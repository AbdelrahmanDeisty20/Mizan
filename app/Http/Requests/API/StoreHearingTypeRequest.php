<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class StoreHearingTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:hearing_types,name'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.hearing_type_name'),
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('validation.custom.hearing_type_name.unique'),
        ];
    }
}
