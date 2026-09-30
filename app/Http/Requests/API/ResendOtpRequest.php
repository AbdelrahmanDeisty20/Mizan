<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ResendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => __('validation.attributes.email'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => __('validation.custom.email.exists'),
        ];
    }
}
