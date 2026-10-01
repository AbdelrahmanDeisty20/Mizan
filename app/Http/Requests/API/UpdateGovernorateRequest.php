<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGovernorateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $governorateId = $this->route('governorate')?->id ?? $this->route('governorate');

        return [
            'name_ar' => ['nullable', 'string', 'max:255', 'unique:governorates,name_ar,' . $governorateId],
            'name_en' => ['nullable', 'string', 'max:255', 'unique:governorates,name_en,' . $governorateId],
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
