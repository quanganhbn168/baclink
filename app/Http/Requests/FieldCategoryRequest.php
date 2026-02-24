<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FieldCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('field_category') ? $this->route('field_category')->id : null;

        return [
            'name' => 'required|array',
            'name.vi' => 'required|string|max:255',
            'name.en' => 'nullable|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('field_categories', 'slug')->ignore($categoryId)
            ],
            'parent_id' => 'nullable|exists:field_categories,id',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'content' => 'nullable|array',
            'content.*' => 'nullable|string',
            'status' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tên danh mục',
            'slug' => 'slug',
            'parent_id' => 'danh mục cha',
            'description' => 'mô tả',
            'content' => 'nội dung',
            'status' => 'trạng thái',
            'order' => 'thứ tự',
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = $this->name;
        if (!$this->slug && $name) {
            $viName = is_array($name) ? ($name['vi'] ?? '') : $name;
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($viName),
            ]);
        }

        $this->merge([
            'status' => (bool) $this->status,
            'order' => $this->order ? (int) $this->order : 0,
        ]);
    }
}