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
            'name'          => 'required|array',
            'name.vi'       => 'required|string|max:255',
            'name.en'       => 'nullable|string|max:255',
            'slug'          => ['nullable', 'string', Rule::unique('careers', 'slug')->ignore($careerId)],
            'image'         => $imageRule,
            'quantity'      => 'nullable|integer',
            'salary'        => 'nullable|string|max:255',
            'experience'    => 'nullable|string|max:255',
            'deadline'      => 'nullable|date',
            'description'   => 'nullable|array',
            'description.*' => 'nullable|string',
            'requirements'  => 'nullable|array',
            'requirements.*'=> 'nullable|string',
            'benefits'      => 'nullable|array',
            'benefits.*'    => 'nullable|string',
            'status'        => 'required|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = $this->name;
        if (!$this->slug && $name) {
            $viName = is_array($name) ? ($name['vi'] ?? '') : $name;
            $this->merge(['slug' => \Illuminate\Support\Str::slug($viName)]);
        }
        $this->merge(['status' => (bool) $this->status]);
    }
}