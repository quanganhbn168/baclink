<?php

namespace App\Http\Requests\Admin\Agent;

class StoreAgentRequest extends BaseAgentRequest
{
    public function rules(): array
    {
        // 1. Lấy luật chung từ Base
        $rules = $this->commonRules();

        // 2. Thêm luật riêng cho STORE
        $rules['email']    = 'required|email|max:255|unique:users,email';
        $rules['password'] = 'required|string|min:6';

        return $rules;
    }
}