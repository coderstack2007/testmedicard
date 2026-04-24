<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id'  => 'exists:departments,id',
            'specialization' => 'string',
            'phone'          => 'nullable|string',
        ];
    }
}
