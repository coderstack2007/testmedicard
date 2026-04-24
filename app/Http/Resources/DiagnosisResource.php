<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiagnosisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'patient_id'      => $this->patient_id,
            'doctor_id'       => $this->doctor_id,
            'icd_code'        => $this->icd_code,
            'description'     => $this->description,
            'status'          => $this->status,
            'diagnosis_date'  => $this->diagnosis_date,
            'patient'         => new UserResource($this->whenLoaded('patient')),
            'doctor'          => new UserResource($this->whenLoaded('doctor')),
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
