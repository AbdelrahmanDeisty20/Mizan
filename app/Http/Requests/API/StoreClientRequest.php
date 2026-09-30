<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['required', 'string', 'max:20', 'unique:clients,phone'],
            'password'       => ['required', 'string', 'min:6'],
            'national_id'    => ['nullable', 'string', 'max:50'],
            'whatsapp'       => ['nullable', 'string', 'max:20'],
            'governorate_id' => ['nullable', 'integer', 'exists:governorates,id'],
            'address'        => ['nullable', 'string'],
            'notes'          => ['nullable', 'string'],
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
            'name'           => __('validation.attributes.name'),
            'phone'          => __('validation.attributes.phone'),
            'password'       => __('validation.attributes.password'),
            'national_id'    => __('validation.attributes.national_id'),
            'whatsapp'       => __('validation.attributes.whatsapp'),
            'governorate_id' => __('validation.attributes.governorate_id'),
            'address'        => __('validation.attributes.address'),
            'notes'          => __('validation.attributes.notes'),
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
            'phone.unique'          => __('validation.custom.phone.unique'),
            'governorate_id.exists' => __('validation.custom.governorate_id.exists'),
        ];
    }
}
