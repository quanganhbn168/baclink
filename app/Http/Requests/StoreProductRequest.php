<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Hoặc check quyền của user
    }

    public function rules(): array
    {
        return [
            'category_id'    => 'required|exists:categories,id',
            'name'           => 'required|string|max:255',
            'slug'           => 'nullable|string|max:255|unique:products,slug',
            'price'          => 'nullable|numeric',
            'price_discount' => 'nullable|numeric',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status'         => 'boolean',
            'is_home'        => 'boolean',
            'gallery'        => 'nullable|array',
            'gallery.*'      => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'attributes'     => 'nullable|array', // Thêm validation cho attributes
        ];
    }
}