<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CareerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $careerId = $this->route('career')?->id;
        $imageRule = $this->isMethod('POST') ? 'required|image' : 'nullable|image';

        return [
            'name'          => 'required|string|max:255',
            'slug'          => ['nullable', 'string', Rule::unique('careers', 'slug')->ignore($careerId)],
            'image'         => $imageRule,
            'quantity'      => 'nullable|integer',
            'salary'        => 'nullable|string|max:255',
            'experience'    => 'nullable|string|max:255',
            'deadline'      => 'nullable|date',
            'description'   => 'nullable|string',
            'requirements'  => 'nullable|string',
            'benefits'      => 'nullable|string',
            'status'        => 'required|boolean',
        ];
    }
}