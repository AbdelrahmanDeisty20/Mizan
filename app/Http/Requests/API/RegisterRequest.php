<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name'              => ['required', 'string', 'max:255'],
            'office_name'       => ['required', 'string', 'max:255'],
            'degree_id'         => ['required', 'integer', 'exists:degrees,id'],
            'governorate_id'    => ['required', 'integer', 'exists:governorates,id'],
            'office_address'    => ['required', 'string', 'max:500'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'          => ['required', 'string', 'confirmed', Password::min(8)->letters()],
            'phone'             => ['required', 'string', 'max:20','unique:users,phone'],
            'syndicate_card_id' => ['required', 'string', 'max:50'],
            'office_phone'      => ['required', 'string', 'max:20'],
            'avatar'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'syndicate_card_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
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
            'name'                  => __('validation.attributes.name'),
            'office_name'           => __('validation.attributes.office_name'),
            'degree_id'             => __('validation.attributes.degree_id'),
            'governorate_id'        => __('validation.attributes.governorate_id'),
            'office_address'        => __('validation.attributes.office_address'),
            'email'                 => __('validation.attributes.email'),
            'password'              => __('validation.attributes.password'),
            'password_confirmation' => __('validation.attributes.password_confirmation'),
            'phone'                 => __('validation.attributes.phone'),
            'syndicate_card_id'     => __('validation.attributes.syndicate_card_id'),
            'office_phone'          => __('validation.attributes.office_phone'),
            'role'                  => __('validation.attributes.role'),
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
            'degree_id.exists'      => __('validation.custom.degree_id.exists'),
            'governorate_id.exists' => __('validation.custom.governorate_id.exists'),
            'email.unique'          => __('validation.custom.email.unique'),
            'phone.unique'          => __('validation.custom.phone.unique'),
            'password.confirmed'    => __('validation.custom.password.confirmed'),
            'password.letters'      => __('validation.custom.password.letters'),
            'role.in'               => __('validation.custom.role.in'),
        ];
    }
}
