<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file'          => ['required', 'file', 'max:20480'],
            'title'         => ['required', 'string', 'max:255'],
            'legal_case_id' => ['required', 'exists:legal_cases,id'],
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
            'file'          => __('validation.attributes.file'),
            'title'         => __('validation.attributes.title'),
            'legal_case_id' => __('validation.attributes.legal_case_id'),
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
            'file.required'          => __('validation.custom.file.required'),
            'file.file'              => __('validation.custom.file.file'),
            'file.max'               => __('validation.custom.file.max'),
            'title.required'         => __('validation.custom.title.required'),
            'legal_case_id.required' => __('validation.custom.legal_case_id.required'),
            'legal_case_id.exists'   => __('validation.custom.legal_case_id.exists'),
        ];
    }
}
