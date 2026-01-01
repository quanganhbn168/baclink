<?php

namespace App\Http\Requests\Admin\Agent;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseAgentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Định nghĩa các luật chung (Shared Rules)
     * Dùng cho cả Thêm mới và Cập nhật
     */
    protected function commonRules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20', // SĐT bắt buộc
            'company_name'  => 'nullable|string|max:255',
            'tax_id'        => 'nullable|string|max:50',
            'address'       => 'nullable|string|max:255',
            'facebook_id'   => 'nullable|string|max:255',
            'zalo_phone'    => 'nullable|string|max:20',
            'discount_rate' => 'nullable|integer|min:0|max:100',
            'admin_note'    => 'nullable|string|max:1000',
        ];
    }
}