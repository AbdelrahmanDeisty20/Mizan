<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
            'code'  => ['required', 'string', 'digits:6'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => __('validation.attributes.email'),
            'code'  => __('validation.attributes.code'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists' => __('validation.custom.email.exists'),
        ];
    }
}
