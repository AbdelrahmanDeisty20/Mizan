<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proposed_fee' => ['required', 'numeric', 'min:0'],
            'notes'        => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'proposed_fee' => __('validation.attributes.proposed_fee'),
            'notes'        => __('validation.attributes.notes'),
        ];
    }

    public function messages(): array
    {
        return [
            'proposed_fee.required' => __('validation.custom.proposed_fee.required'),
            'proposed_fee.min'      => __('validation.custom.proposed_fee.min'),
        ];
    }
}
