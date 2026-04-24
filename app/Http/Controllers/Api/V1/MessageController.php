<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController
{
    public function index(Request $request, int $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        // Verify user is part of this chat
        if (!($request->user()->id === $chat->patient_id || $request->user()->id === $chat->doctor_id)) {
            abort(403, 'Unauthorized access');
        }

        $messages = Message::where('chat_id', $chatId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        return MessageResource::collection($messages);
    }

    public function store(StoreMessageRequest $request, int $chatId)
    {
        $chat = Chat::findOrFail($chatId);

        // Verify user is part of this chat
        if (!($request->user()->id === $chat->patient_id || $request->user()->id === $chat->doctor_id)) {
            abort(403, 'Unauthorized access');
        }

        $message = Message::create([
            ...$request->validated(),
            'chat_id'   => $chatId,
            'sender_id' => $request->user()->id,
            'status'    => 'sent',
        ]);

        return response()->json(new MessageResource($message->load('sender')), 201);
    }

    public function markRead(Request $request, int $messageId)
    {
        $message = Message::findOrFail($messageId);

        // Verify user is the receiver
        $chat = $message->chat;
        $isReceiver = ($request->user()->id === $chat->patient_id && $message->sender_id === $chat->doctor_id)
                   || ($request->user()->id === $chat->doctor_id && $message->sender_id === $chat->patient_id);

        if (!$isReceiver) {
            abort(403, 'Unauthorized');
        }

        $message->update(['status' => 'read']);

        return response()->json(new MessageResource($message));
    }
}
