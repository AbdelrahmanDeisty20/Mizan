<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'governorate_id' => ['nullable', 'integer', 'exists:governorates,id'],
            'court_id'       => ['nullable', 'integer', 'exists:courts,id'],
            'case_number'    => ['nullable', 'string', 'max:100'],
            'due_date'       => ['nullable', 'date'],
            'offered_fee'    => ['nullable', 'numeric', 'min:0'],
            'status'         => ['nullable', 'string', 'in:open,assigned,completed,cancelled'],
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
            'status'         => __('validation.attributes.status'),
            'contact_phone'  => __('validation.attributes.phone'),
        ];
    }

    public function messages(): array
    {
        return [
            'governorate_id.exists' => __('validation.custom.governorate_id.exists'),
            'court_id.exists'       => __('validation.custom.court_id.exists'),
            'due_date.date'         => __('validation.custom.due_date.date'),
            'offered_fee.min'       => __('validation.custom.offered_fee.min'),
        ];
    }
}
