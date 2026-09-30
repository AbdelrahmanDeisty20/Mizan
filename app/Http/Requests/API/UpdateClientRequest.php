<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client')?->id ?? $this->route('client');

        return [
            'name'           => ['sometimes', 'string', 'max:255'],
            'phone'          => ['sometimes', 'string', 'max:20', 'unique:clients,phone,' . $clientId],
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
