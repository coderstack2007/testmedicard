<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreChatRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController
{
    public function index(Request $request)
    {
        $user = $request->user();

        $chats = Chat::where(function ($query) use ($user) {
            $query->where('patient_id', $user->id)
                  ->orWhere('doctor_id', $user->id);
        })
        ->with('patient', 'doctor', 'lastMessage.sender')
        ->latest()
        ->paginate(20);

        return ChatResource::collection($chats);
    }

    public function store(StoreChatRequest $request)
    {
        $validated = $request->validated();

        $chat = Chat::firstOrCreate(
            [
                'patient_id' => $validated['patient_id'],
                'doctor_id'  => $validated['doctor_id'],
            ]
        );

        return response()->json(
            new ChatResource($chat->load('patient', 'doctor', 'lastMessage')),
            201
        );
    }

    public function show(Request $request, int $id)
    {
        $chat = Chat::with('patient', 'doctor')->findOrFail($id);

        // Verify user is part of this chat
        if (!($request->user()->id === $chat->patient_id || $request->user()->id === $chat->doctor_id)) {
            abort(403, 'Unauthorized access to this chat');
        }

        return response()->json(new ChatResource($chat->load('patient', 'doctor', 'messages')));
    }
}
