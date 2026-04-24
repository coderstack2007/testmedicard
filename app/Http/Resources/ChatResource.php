<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'patient_id'   => $this->patient_id,
            'doctor_id'    => $this->doctor_id,
            'patient'      => new UserResource($this->whenLoaded('patient')),
            'doctor'       => new UserResource($this->whenLoaded('doctor')),
            'last_message' => new MessageResource($this->whenLoaded('lastMessage')),
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
