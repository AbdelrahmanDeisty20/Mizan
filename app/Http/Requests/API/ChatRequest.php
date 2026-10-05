<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt'     => ['required', 'string', 'max:2000'],
            'history'    => ['nullable', 'array'],
            'session_id' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'prompt'  => __('validation.attributes.prompt'),
            'history' => __('validation.attributes.history'),
        ];
    }
}
