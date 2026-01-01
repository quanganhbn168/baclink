<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        
        $attribute = $this->route('attribute');

        return [
            'value' => 'required|string|max:255',
            
            'color_code' => [
                'nullable',
                'string',
                'max:7', 
                Rule::requiredIf(fn () => $attribute?->type === 'color_swatch'),
            ],
            
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:1024', 
                Rule::requiredIf(fn () => $attribute?->type === 'image_swatch'),
            ],
        ];
    }
}