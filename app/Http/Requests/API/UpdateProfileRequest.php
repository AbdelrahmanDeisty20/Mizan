<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $userId = auth()->id();

        return [
            'name'              => ['nullable', 'string', 'max:255'],
            'email'             => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'phone'             => ['nullable', 'string', 'max:20', 'unique:users,phone,' . $userId],
            'office_name'       => ['nullable', 'string', 'max:255'],
            'degree_id'         => ['nullable', 'integer', 'exists:degrees,id'],
            'governorate_id'    => ['nullable', 'integer', 'exists:governorates,id'],
            'address'           => ['nullable', 'string', 'max:500'],
            'office_address'    => ['nullable', 'string', 'max:500'],
            'syndicate_card_id' => ['nullable', 'string', 'max:50'],
            'office_phone'      => ['nullable', 'string', 'max:20'],
            'trial_ends_at'     => ['nullable', 'date'],
            'avatar'               => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'syndicate_card_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'image'                => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'type'                 => ['nullable', 'string', 'in:avatar,syndicate_card,syndicate_card_image'],
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
            'name'              => __('validation.attributes.name'),
            'office_name'       => __('validation.attributes.office_name'),
            'degree_id'         => __('validation.attributes.degree_id'),
            'governorate_id'    => __('validation.attributes.governorate_id'),
            'office_address'    => __('validation.attributes.office_address'),
            'address'           => __('validation.attributes.address'),
            'email'             => __('validation.attributes.email'),
            'phone'             => __('validation.attributes.phone'),
            'syndicate_card_id' => __('validation.attributes.syndicate_card_id'),
            'office_phone'      => __('validation.attributes.office_phone'),
            'avatar'               => __('validation.attributes.avatar'),
            'trial_ends_at'     => __('validation.attributes.trial_ends_at'),
            'syndicate_card_image' => __('validation.attributes.syndicate_card_image'),
            'image'                => __('validation.attributes.image'),
            'type'                 => __('validation.attributes.type'),
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
            'trial_ends_at.date'    => __('validation.custom.trial_ends_at.date'),
        ];
    }
}
