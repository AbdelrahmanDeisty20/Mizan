<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'consultation_method' => ['nullable', 'string'],
            'preferred_date'      => ['nullable', 'date'],
            'preferred_time'      => ['nullable', 'string'],
            'subject'             => ['nullable', 'string'],
            'status'              => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'reply'               => ['nullable', 'string'],
            'fee'                 => ['nullable', 'numeric', 'min:0'],
            'is_paid'             => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'consultation_method' => __('validation.attributes.consultation_method'),
            'preferred_date'      => __('validation.attributes.preferred_date'),
            'preferred_time'      => __('validation.attributes.preferred_time'),
            'subject'             => __('validation.attributes.subject'),
            'status'              => __('validation.attributes.status'),
            'reply'               => __('validation.attributes.reply'),
        ];
    }
}
