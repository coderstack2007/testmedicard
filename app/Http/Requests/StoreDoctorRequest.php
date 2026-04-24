<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'        => 'required|exists:users,id',
            'branch_id'      => 'required|exists:branches,id',
            'department_id'  => 'required|exists:departments,id',
            'specialization' => 'nullable|string',
            'phone'          => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'       => 'User is required',
            'branch_id.required'     => 'Branch is required',
            'department_id.required' => 'Department is required',
        ];
    }
}
