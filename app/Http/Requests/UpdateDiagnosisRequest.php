<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiagnosisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'icd_code'       => 'string|max:50',
            'description'    => 'string',
            'diagnosis_date' => 'date',
            'status'         => 'in:active,completed,archived',
        ];
    }
}
