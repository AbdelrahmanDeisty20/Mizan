<?php

namespace App\Http\Requests\API;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreHearingRequest extends FormRequest
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
            'legal_case_id'      => ['required', 'integer', 'exists:legal_cases,id'],
            'assigned_lawyer_id' => ['nullable', 'integer', 'exists:users,id'],
            'hearing_date'       => ['required', 'date'],
            'hearing_type'       => ['required', 'string', 'max:255'],
            'court_room'         => ['nullable', 'string', 'max:255'],
            'roll_number'        => ['nullable', 'string', 'max:100'],
            'decision'           => ['nullable', 'string'],
            'requirements'       => ['nullable', 'string'],
            'status'             => ['nullable', 'string', 'max:50'],
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
            'legal_case_id'      => __('validation.attributes.legal_case_id'),
            'assigned_lawyer_id' => __('validation.attributes.assigned_lawyer_id'),
            'hearing_date'       => __('validation.attributes.hearing_date'),
            'hearing_type'       => __('validation.attributes.hearing_type'),
            'court_room'         => __('validation.attributes.court_room'),
            'roll_number'        => __('validation.attributes.roll_number'),
            'decision'           => __('validation.attributes.decision'),
            'requirements'       => __('validation.attributes.requirements'),
            'status'             => __('validation.attributes.status'),
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
            'legal_case_id.required'      => __('validation.custom.legal_case_id.required'),
            'legal_case_id.exists'        => __('validation.custom.legal_case_id.exists'),
            'assigned_lawyer_id.exists'   => __('validation.custom.assigned_lawyer_id.exists'),
            'hearing_date.required'       => __('validation.custom.hearing_date.required'),
            'hearing_date.date'           => __('validation.custom.hearing_date.date'),
        ];
    }
}
