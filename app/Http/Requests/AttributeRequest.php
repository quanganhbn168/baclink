<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_variant_defining' => $this->boolean('is_variant_defining'),
        ]);
    }
    public function rules(): array
    {
        $attributeId = $this->route('attribute')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('attributes')->ignore($attributeId),
            ],
            'type' => [
                'required',
                'string',
                Rule::in(['dropdown', 'button', 'color_swatch', 'image_swatch']),
            ],
            'is_variant_defining' => [
                'required',
                'boolean',
            ],
        ];
    }
}