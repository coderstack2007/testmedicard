<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'icd_code'       => 'required|string|max:50',
            'description'    => 'required|string',
            'diagnosis_date' => 'required|date',
            'status'         => 'in:active,completed,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'icd_code.required'       => 'ICD code is required',
            'description.required'    => 'Description is required',
            'diagnosis_date.required' => 'Diagnosis date is required',
        ];
    }
}
