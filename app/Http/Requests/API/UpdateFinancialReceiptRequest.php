<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinancialReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receipt_number' => ['nullable', 'string', 'max:100', 'unique:financial_receipts,receipt_number,' . $this->financial_receipt?->id],
            'client_id'      => ['nullable', 'integer', 'exists:clients,id'],
            'legal_case_id'  => ['nullable', 'integer', 'exists:legal_cases,id'],
            'user_id'        => ['nullable', 'integer', 'exists:users,id'],
            'amount'         => ['nullable', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'date'           => ['nullable', 'date'],
            'notes'          => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'receipt_number' => __('validation.attributes.receipt_number'),
            'client_id'      => __('validation.attributes.client_id'),
            'legal_case_id'  => __('validation.attributes.legal_case_id'),
            'user_id'        => __('validation.attributes.issuer'),
            'amount'         => __('validation.attributes.amount'),
            'payment_method' => __('validation.attributes.payment_method'),
            'date'           => __('validation.attributes.receipt_date'),
            'notes'          => __('validation.attributes.notes'),
        ];
    }

    public function messages(): array
    {
        return [
            'receipt_number.unique' => __('validation.custom.receipt_number.unique'),
            'client_id.exists'      => __('validation.custom.client_id.exists'),
            'legal_case_id.exists'  => __('validation.custom.legal_case_id.exists'),
            'user_id.exists'        => __('validation.custom.user_id.exists'),
            'amount.min'            => __('validation.custom.amount.min'),
            'date.date'              => __('validation.custom.receipt_date.date'),
        ];
    }
}
