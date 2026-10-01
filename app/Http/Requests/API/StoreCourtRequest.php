<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:courts,name'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.court_name'),
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('validation.custom.court_name.unique'),
        ];
    }
}
