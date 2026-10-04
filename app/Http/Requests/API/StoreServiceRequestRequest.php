<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string'],
            'governorate_id' => ['required', 'integer', 'exists:governorates,id'],
            'court_id'       => ['required', 'integer', 'exists:courts,id'],
            'case_number'    => ['nullable', 'string', 'max:100'],
            'due_date'       => ['required', 'date'],
            'offered_fee'    => ['required', 'numeric', 'min:0'],
            'contact_phone'  => ['nullable', 'string', 'max:50'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'          => __('validation.attributes.service_title'),
            'description'    => __('validation.attributes.service_description'),
            'governorate_id' => __('validation.attributes.governorate_id'),
            'court_id'       => __('validation.attributes.court_id'),
            'case_number'    => __('validation.attributes.case_number'),
            'due_date'       => __('validation.attributes.due_date'),
            'offered_fee'    => __('validation.attributes.offered_fee'),
            'contact_phone'  => __('validation.attributes.phone'),
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'          => __('validation.custom.service_title.required'),
            'description.required'    => __('validation.custom.service_description.required'),
            'governorate_id.required' => __('validation.custom.governorate_id.required'),
            'governorate_id.exists'   => __('validation.custom.governorate_id.exists'),
            'court_id.required'       => __('validation.custom.court_id.required'),
            'court_id.exists'         => __('validation.custom.court_id.exists'),
            'due_date.required'       => __('validation.custom.due_date.required'),
            'due_date.date'           => __('validation.custom.due_date.date'),
            'offered_fee.required'    => __('validation.custom.offered_fee.required'),
            'offered_fee.min'         => __('validation.custom.offered_fee.min'),
        ];
    }
}
