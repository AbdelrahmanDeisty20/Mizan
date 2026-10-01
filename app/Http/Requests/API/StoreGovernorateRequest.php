<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreGovernorateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255', 'unique:governorates,name_ar'],
            'name_en' => ['nullable', 'string', 'max:255', 'unique:governorates,name_en'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_ar' => __('validation.attributes.name_ar'),
            'name_en' => __('validation.attributes.name_en'),
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.unique' => __('validation.custom.name_ar.unique'),
            'name_en.unique' => __('validation.custom.name_en.unique'),
        ];
    }
}
