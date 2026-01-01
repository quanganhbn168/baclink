<?php

namespace App\Services;

use App\Models\ServiceCategory;
use App\Traits\UploadImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class ServiceCategoryService
{
    use UploadImageTrait;

    public function create(Request $request): ServiceCategory
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:service_categories,slug',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'parent_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $value = (int) $value;

                    if ($value !== 0 && !ServiceCategory::where('id', $value)->exists()) {
                        $fail('Danh mục cha không tồn tại.');
                    }
                },
            ],
            'status' => 'nullable|boolean',
        ]);
        $data['slug'] = Str::slug($data['name']);
        
        $data['image'] = $this->uploadImage($request->file('image'), 'uploads/service_categories', 800,800, true,'',false);

        if ($request->hasFile('banner')) {
            $data['banner'] = $this->uploadImage($request->file('banner'), 'uploads/service_categories', 1920,300, false,'',false);
        }

        return ServiceCategory::create($data);
    }

    public function update(Request $request, ServiceCategory $category): ServiceCategory
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('service_categories', 'slug')->ignore($category->id)
            ],
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'parent_id' => [
                'nullable',
                'integer',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value == $category->id) {
                        $fail('Danh mục không thể là cha của chính nó.');
                    }
                }
            ],
            'status' => 'nullable|boolean',
        ]);
        $data['slug'] = Str::slug($data['name']);
        if ($request->hasFile('image')) {
            $this->deleteImage($category->image);
            $data['image'] = $this->uploadImage($request->file('image'), 'uploads/service_categories', 800,800, true,'',false);
        }

        if ($request->hasFile('banner')) {
            $this->deleteImage($category->banner);
            $data['banner'] = $this->uploadImage($request->file('banner'), 'uploads/service_categories', 1920,300, false,'',false);
        }

        $category->update($data);
        return $category;
    }


    public function delete(ServiceCategory $category): void
    {
        $this->deleteImage($category->image);
        $this->deleteImage($category->banner);
        $category->delete();
    }
}
