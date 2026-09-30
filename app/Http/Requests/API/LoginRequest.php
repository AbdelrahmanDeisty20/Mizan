<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login'             => ['required_without_all:email,phone,syndicate_card_id', 'nullable', 'string', 'max:255'],
            'email'             => ['required_without_all:login,phone,syndicate_card_id', 'nullable', 'string', 'max:255'],
            'phone'             => ['required_without_all:login,email,syndicate_card_id', 'nullable', 'string', 'max:20'],
            'syndicate_card_id' => ['required_without_all:login,email,phone', 'nullable', 'string', 'max:50'],
            'password'          => ['required', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'login'             => __('validation.attributes.login'),
            'email'             => __('validation.attributes.email'),
            'phone'             => __('validation.attributes.phone'),
            'syndicate_card_id' => __('validation.attributes.syndicate_card_id'),
            'password'          => __('validation.attributes.password'),
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'login.required_without_all'             => __('validation.custom.login.required_without_all'),
            'email.required_without_all'             => __('validation.custom.login.required_without_all'),
            'phone.required_without_all'             => __('validation.custom.login.required_without_all'),
            'syndicate_card_id.required_without_all' => __('validation.custom.login.required_without_all'),
        ];
    }
}
