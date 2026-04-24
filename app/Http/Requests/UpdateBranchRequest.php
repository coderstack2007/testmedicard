<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'string|max:255',
            'address'      => 'nullable|string',
            'moderator_id' => 'nullable|exists:users,id',
            'is_active'    => 'boolean',
        ];
    }
}
