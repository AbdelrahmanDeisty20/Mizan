<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email', 'exists:users,email'],
            'token'    => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()],
        ];
    }

    public function attributes(): array
    {
        return [
            'email'                 => __('validation.attributes.email'),
            'token'                 => __('validation.attributes.token'),
            'password'              => __('validation.attributes.password'),
            'password_confirmation' => __('validation.attributes.password_confirmation'),
        ];
    }

    public function messages(): array
    {
        return [
            'email.exists'       => __('validation.custom.email.exists'),
            'password.confirmed' => __('validation.custom.password.confirmed'),
            'password.letters'   => __('validation.custom.password.letters'),
        ];
    }
}
