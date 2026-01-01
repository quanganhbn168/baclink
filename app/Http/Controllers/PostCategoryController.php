<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use App\Services\PostCategoryService;
use Illuminate\Http\Request;

class PostCategoryController extends Controller
{
    public function __construct(protected PostCategoryService $postCategoryService) {}

    public function index()
    {
        $categories = $this->postCategoryService->getAll();

        return view('admin.post_categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = $this->postCategoryService->getParentOptions();
        return view('admin.post_categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->postCategoryService->create($request);
        return $request->input('action') === 'save_new'
            ? redirect()->route('admin.post-categories.create')->with('success', 'Đã thêm danh mục, tiếp tục thêm mới.')
            : redirect()->route('admin.post-categories.index')->with('success', 'Đã thêm danh mục.');
    }

    public function edit(PostCategory $postCategory)
    {
        $categories = $this->postCategoryService->getParentOptions($postCategory->id);
        return view('admin.post_categories.edit', compact('postCategory', 'categories'));
    }

    public function update(Request $request, PostCategory $postCategory)
    {
        $this->postCategoryService->update($postCategory, $request);
        return redirect()->route('admin.post-categories.index')->with('success', 'Cập nhật danh mục bài viết thành công.');
    }

    public function destroy(PostCategory $postCategory)
    {
        $this->postCategoryService->delete($postCategory);
        return redirect()->route('admin.post-categories.index')->with('success', 'Xoá danh mục bài viết thành công.');
    }
}
