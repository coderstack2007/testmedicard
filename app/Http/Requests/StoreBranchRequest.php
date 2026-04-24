<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'address'      => 'nullable|string',
            'moderator_id' => 'nullable|exists:users,id',
            'is_active'    => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Branch name is required',
            'moderator_id.exists' => 'Selected moderator does not exist',
        ];
    }
}
