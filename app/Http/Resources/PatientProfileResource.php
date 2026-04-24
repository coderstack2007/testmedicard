<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'branch_id'      => $this->branch_id,
            'date_of_birth'  => $this->date_of_birth,
            'blood_type'     => $this->blood_type,
            'user'           => new UserResource($this->whenLoaded('user')),
            'branch'         => new BranchResource($this->whenLoaded('branch')),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
