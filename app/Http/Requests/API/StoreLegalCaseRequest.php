<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLegalCaseRequest extends FormRequest
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
            'client_id'       => ['required', 'integer', 'exists:clients,id'],
            'client_role'     => ['required', 'string', 'in:plaintiff,defendant,appellant,appellee,petitioner,respondent,intervener'],
            'case_number'     => ['required', 'string', 'max:100'],
            'year'            => ['required', 'integer', 'min:1900', 'max:2100'],
            'court_id'        => ['required', 'integer', 'exists:courts,id'],
            'degree'          => ['required', 'string'],
            'case_type'       => ['required', 'string'],
            'status'          => ['required', 'string', 'in:active,archived,closed,won,lost'],
            'opponent_name'   => ['required', 'string', 'max:255'],
            'opponent_lawyer' => ['nullable', 'string', 'max:255'],
            'total_fees'      => ['required', 'numeric', 'min:0'],
            'paid_fees'       => ['required', 'numeric', 'min:0'],
            'notes'           => ['required', 'string'],
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
            'client_id'       => __('validation.attributes.client_id'),
            'client_role'     => __('validation.attributes.client_role'),
            'case_number'     => __('validation.attributes.case_number'),
            'year'            => __('validation.attributes.year'),
            'court_id'        => __('validation.attributes.court_id'),
            'degree'          => __('validation.attributes.degree'),
            'case_type'       => __('validation.attributes.case_type'),
            'status'          => __('validation.attributes.status'),
            'opponent_name'   => __('validation.attributes.opponent_name'),
            'opponent_lawyer' => __('validation.attributes.opponent_lawyer'),
            'total_fees'      => __('validation.attributes.total_fees'),
            'paid_fees'       => __('validation.attributes.paid_fees'),
            'notes'           => __('validation.attributes.notes'),
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
            'client_id.required'      => __('validation.custom.client_id.required'),
            'client_id.exists'        => __('validation.custom.client_id.exists'),
            'client_role.in'          => __('validation.custom.client_role.in'),
            'year.min'                => __('validation.custom.year.min'),
            'year.max'                => __('validation.custom.year.max'),
            'total_fees.min'          => __('validation.custom.total_fees.min'),
            'paid_fees.min'           => __('validation.custom.paid_fees.min'),
            'opponent_name.required'  => __('validation.custom.opponent_name.required'),
            'total_fees.required'     => __('validation.custom.total_fees.required'),
            'paid_fees.required'      => __('validation.custom.paid_fees.required'),
            'notes.required'          => __('validation.custom.notes.required'),
        ];
    }
}
