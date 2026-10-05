<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consultation_method' => ['required', 'string'], // office, phone, online / بمقر المكتب، مكالمة هاتفية، اجتماع أونلاين
            'preferred_date'      => ['required', 'date'],
            'preferred_time'      => ['required', 'string'],
            'subject'             => ['required', 'string'],
            'user_id'             => ['nullable', 'integer', 'exists:users,id'],
            'office_id'           => ['nullable', 'integer', 'exists:offices,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'consultation_method' => __('validation.attributes.consultation_method'),
            'preferred_date'      => __('validation.attributes.preferred_date'),
            'preferred_time'      => __('validation.attributes.preferred_time'),
            'subject'             => __('validation.attributes.subject'),
            'user_id'             => __('validation.attributes.user_id'),
            'office_id'           => __('validation.attributes.office_id'),
        ];
    }

    public function messages(): array
    {
        return [
            'consultation_method.required' => __('validation.custom.consultation_method.required'),
            'preferred_date.required'      => __('validation.custom.preferred_date.required'),
            'preferred_date.date'          => __('validation.custom.preferred_date.date'),
            'preferred_time.required'      => __('validation.custom.preferred_time.required'),
            'subject.required'             => __('validation.custom.subject.required'),
        ];
    }
}
