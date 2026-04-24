<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content'  => 'nullable|string',
            'file_url' => 'nullable|string|url',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->input('content') && !$this->input('file_url')) {
                $validator->errors()->add('message', 'Message must have content or file');
            }
        });
    }
}
