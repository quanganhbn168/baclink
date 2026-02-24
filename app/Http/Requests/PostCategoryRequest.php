<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('post_category') ? $this->route('post_category')->id : null;

        return [
            'parent_id' => 'nullable|exists:post_categories,id',
            'name' => 'required|array',
            'name.vi' => 'required|string|max:255',
            'name.en' => 'nullable|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('post_categories', 'slug')->ignore($categoryId)
            ],
            'status' => 'boolean',
            'is_home' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => 'danh mục cha',
            'name' => 'tên danh mục',
            'name.vi' => 'tên danh mục (Tiếng Việt)',
            'name.en' => 'tên danh mục (English)',
            'slug' => 'slug',
            'status' => 'trạng thái',
            'is_home' => 'hiển thị trang chủ',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục',
            'name.vi.required' => 'Vui lòng nhập tên danh mục (Tiếng Việt)',
            'name.vi.max' => 'Tên danh mục không được vượt quá 255 ký tự',
            'slug.required' => 'Vui lòng nhập slug',
            'slug.unique' => 'Slug đã tồn tại',
            'parent_id.exists' => 'Danh mục cha không tồn tại',
            'status.boolean' => 'Trạng thái không hợp lệ',
            'is_home.boolean' => 'Hiển thị trang chủ không hợp lệ',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Tự động tạo slug từ name.vi nếu slug trống
        $name = $this->name;
        $nameVi = is_array($name) ? ($name['vi'] ?? '') : $name;

        if (!$this->slug && $nameVi) {
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($nameVi),
            ]);
        }

        // Đảm bảo các trường boolean
        $this->merge([
            'status' => (bool) $this->status,
            'is_home' => (bool) $this->is_home,
        ]);

        // Xử lý parent_id: nếu trống hoặc '' thì set thành null
        if ($this->parent_id === null || $this->parent_id === '') {
            $this->merge([
                'parent_id' => null,
            ]);
        }
    }
}