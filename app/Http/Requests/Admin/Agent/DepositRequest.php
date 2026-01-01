<?php

namespace App\Http\Requests\Admin\Agent;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Min 10k, Max 1 tỷ (ví dụ)
            'amount' => 'required|numeric|min:10000|max:1000000000',
            'note'   => 'required|string|max:500',
        ];
    }
}