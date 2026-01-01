<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use App\Http\Requests\PostCategoryRequest;
use App\Services\PostCategoryService;

class PostCategoryController extends Controller
{
    public function __construct(
        protected PostCategoryService $postCategoryService
    ) {}

    public function index()
    {
        $categories = $this->postCategoryService->getAll();
        return view('admin.post_categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = PostCategory::with('children')->whereNull('parent_id')->get();
        return view('admin.post_categories.create', compact('parentCategories'));
    }

    public function store(PostCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
        
        $this->postCategoryService->create($validatedData);
        
        return $request->has('save_new')
            ? redirect()->route('admin.post-categories.create')->with('success', 'Thêm danh mục bài viết thành công.')
            : redirect()->route('admin.post-categories.index')->with('success', 'Thêm danh mục bài viết thành công.');
    }

    public function edit(PostCategory $postCategory)
    {
        $parentCategories = PostCategory::with('children')
            ->whereNull('parent_id')
            ->where('id', '!=', $postCategory->id)
            ->get();
        
        $postCategory->load(['parent', 'images']);
        return view('admin.post_categories.edit', compact('postCategory', 'parentCategories'));
    }

    public function update(PostCategoryRequest $request, PostCategory $postCategory)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
        
        $this->postCategoryService->update($postCategory, $validatedData);
        
        return redirect()->route('admin.post-categories.index')->with('success', 'Cập nhật danh mục bài viết thành công.');
    }

    public function destroy(PostCategory $postCategory)
    {
        $this->postCategoryService->delete($postCategory);
        return redirect()->route('admin.post-categories.index')->with('success', 'Xóa danh mục bài viết thành công.');
    }
}   