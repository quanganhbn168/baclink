<?php

namespace App\Http\Requests\Admin\Agent;

class UpdateAgentRequest extends BaseAgentRequest
{
    public function rules(): array
    {
        // 1. Lấy luật chung từ Base
        $rules = $this->commonRules();

        // 2. Lấy ID đại lý từ URL (route: admin/agents/{agent})
        $agentId = $this->route('agent'); 

        // 3. Thêm luật riêng cho UPDATE
        // Email unique nhưng trừ cái ID hiện tại ra
        $rules['email']    = 'required|email|max:255|unique:users,email,' . $agentId;
        
        // Password để trống nghĩa là không đổi -> nullable
        $rules['password'] = 'nullable|string|min:6';

        return $rules;
    }
}