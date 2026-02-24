<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IntroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $introId = $this->route('intro') ? $this->route('intro')->id : null;

        return [
            'title' => 'required|array',
            'title.vi' => 'required|string|max:255',
            'title.en' => 'nullable|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('intros', 'slug')->ignore($introId)
            ],
            'description' => 'nullable|array',
            'description.*' => 'nullable|string',
            'content' => 'nullable|array',
            'content.*' => 'nullable|string',
            'status' => 'boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'tiêu đề',
            'title.vi' => 'tiêu đề (VN)',
            'title.en' => 'tiêu đề (EN)',
            'slug' => 'slug',
            'description' => 'mô tả ngắn',
            'content' => 'nội dung',
            'status' => 'trạng thái',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề',
            'title.vi.required' => 'Vui lòng nhập tiêu đề tiếng Việt',
            'title.vi.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'slug.required' => 'Vui lòng nhập slug',
            'slug.unique' => 'Slug đã tồn tại',
            'status.boolean' => 'Trạng thái không hợp lệ',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Tự động tạo slug từ title (lấy vi) nếu slug trống
        $title = $this->title;
        if (!$this->slug && $title) {
            $viTitle = is_array($title) ? ($title['vi'] ?? '') : $title;
            $this->merge([
                'slug' => \Illuminate\Support\Str::slug($viTitle),
            ]);
        }

        // Đảm bảo status là boolean
        $this->merge([
            'status' => (bool) $this->status,
        ]);
    }
}