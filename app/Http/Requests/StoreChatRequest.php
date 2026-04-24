<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:users,id',
            'doctor_id'  => 'required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Patient is required',
            'doctor_id.required'  => 'Doctor is required',
        ];
    }
}
