<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class ReplyConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reply'   => ['required', 'string'],
            'status'  => ['nullable', 'string', 'in:pending,confirmed,completed,cancelled'],
            'fee'     => ['nullable', 'numeric', 'min:0'],
            'is_paid' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'reply'   => __('validation.attributes.reply'),
            'status'  => __('validation.attributes.status'),
            'fee'     => __('validation.attributes.fee'),
            'is_paid' => __('validation.attributes.is_paid'),
        ];
    }
}
