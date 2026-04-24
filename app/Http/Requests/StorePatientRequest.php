<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'       => 'required|exists:users,id',
            'branch_id'     => 'required|exists:branches,id',
            'date_of_birth' => 'nullable|date',
            'blood_type'    => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'   => 'User is required',
            'branch_id.required' => 'Branch is required',
        ];
    }
}
