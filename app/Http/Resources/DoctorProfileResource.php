<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'branch_id'        => $this->branch_id,
            'department_id'    => $this->department_id,
            'specialization'   => $this->specialization,
            'phone'            => $this->phone,
            'user'             => new UserResource($this->whenLoaded('user')),
            'branch'           => new BranchResource($this->whenLoaded('branch')),
            'department'       => new DepartmentResource($this->whenLoaded('department')),
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
