<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ClientLoginRequest extends FormRequest
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
            'phone'       => ['required', 'string', 'exists:clients,phone'],
            'access_code' => ['required', 'string', 'max:50'],
            'password'    => ['required', 'string'],
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
            'phone'       => __('validation.attributes.phone'),
            'access_code' => __('validation.attributes.access_code'),
            'password'    => __('validation.attributes.password'),
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
            'phone.required'       => __('validation.custom.phone.required'),
            'phone.exists'         => __('validation.custom.phone.not_found'),
            'access_code.required' => __('validation.custom.access_code.required'),
            'password.required'    => __('validation.custom.password.required'),
        ];
    }
}
