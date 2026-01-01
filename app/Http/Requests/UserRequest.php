<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        $rules = [
            'name' => 'required|string|max:255',
            // SỐ ĐIỆN THOẠI: Bắt buộc và đúng định dạng 10 số của VN
            'phone' => ['required', 'string', 'regex:/^(0[3|5|7|8|9])([0-9]{8})$/'],
            'address' => 'nullable|string|max:255',
            // EMAIL: Không bắt buộc nữa, nhưng nếu có thì phải là duy nhất
            'email' => [
                'nullable', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'roles' => 'nullable|array',
            'roles.*' => [Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ];

        // Mật khẩu vẫn bắt buộc khi tạo mới
        if ($this->isMethod('POST')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        // Mật khẩu không bắt buộc khi cập nhật
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'required' => 'Vui lòng nhập :attribute.',
            'string'   => ':attribute phải là một chuỗi ký tự.',
            'max'      => ':attribute không được vượt quá :max ký tự.',
            'email'    => ':attribute không phải là một địa chỉ email hợp lệ.',
            'unique'   => ':attribute này đã tồn tại trong hệ thống.',
            'array'    => ':attribute không hợp lệ.',
            'exists'   => ':attribute được chọn không hợp lệ.',
            'min'      => ':attribute phải có ít nhất :min ký tự.',
            'confirmed'=> ':attribute xác nhận không khớp.',
            // Thêm thông báo cho định dạng số điện thoại
            'phone.regex' => ':attribute không đúng định dạng (10 số, bắt đầu bằng 03, 05, 07, 08, 09).',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Họ và tên',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'email' => 'Địa chỉ email',
            'password' => 'Mật khẩu',
            'roles' => 'Vai trò',
            'roles.*' => 'Vai trò',
        ];
    }
}