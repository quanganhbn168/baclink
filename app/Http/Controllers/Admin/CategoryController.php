<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}
    public function index()
    {
        $categories = $this->categoryService->getAll();
        return view('admin.categories.index', compact('categories'));
    }
    public function create()
    {
        $parentCategories = Category::with('children')->where('parent_id', 0)->get();
        return view('admin.categories.create', compact('parentCategories'));
    }
    public function store(CategoryRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
        
        $this->categoryService->create($validatedData);
        
        return $request->has('save_new')
            ? redirect()->route('admin.categories.create')->with('success', 'Thêm danh mục thành công.')
            : redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
    }
    public function edit(Category $category)
    {
        $parentCategories = Category::with('children')
            ->where('parent_id', 0)
            ->where('id', '!=', $category->id)
            ->get();
        
        $category->load(['parent', 'images']);
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }
    public function update(CategoryRequest $request, Category $category)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        $validatedData['banner_original_path'] = $request->input('banner_original_path');
        
        $this->categoryService->update($category, $validatedData);
        
        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }
    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);
        return redirect()->route('admin.categories.index')->with('success', 'Xóa danh mục thành công.');
    }
}