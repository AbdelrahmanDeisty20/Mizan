<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourtRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $courtId = $this->route('court')?->id ?? $this->route('court');

        return [
            'name' => ['nullable', 'string', 'max:255', 'unique:courts,name,' . $courtId],
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
