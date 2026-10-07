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
            'title'         => ['nullable', 'string', 'max:255'],
            'legal_case_id' => ['nullable', 'exists:legal_cases,id'],
            'client_id'     => ['nullable', 'exists:clients,id'],
        ];
    }
}
