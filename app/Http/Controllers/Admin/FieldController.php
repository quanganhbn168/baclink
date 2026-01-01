<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\FieldCategory;
use App\Http\Requests\FieldRequest;
use App\Services\FieldService;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function __construct(
        protected FieldService $fieldService
    ) {}

    /** Index: lọc + phân trang (chuẩn với view index mới) */
    public function index(Request $request)
    {
        [$fields, $filterCategories] = $this->fieldService->list($request);
        return view('admin.fields.index', compact('fields', 'filterCategories'));
    }

    public function create()
    {
        $categories = FieldCategory::pluck("name","id");
        return view('admin.fields.create', compact('categories'));
    }

    public function store(FieldRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        
        $this->fieldService->create($validatedData);
        
        return $request->has('save_new')
            ? redirect()->route('admin.fields.create')->with('success', 'Thêm lĩnh vực thành công.')
            : redirect()->route('admin.fields.index')->with('success', 'Thêm lĩnh vực thành công.');
    }

    public function edit(Field $field)
    {   
        $categories = FieldCategory::pluck("name","id");
        $field->load(['category', 'images']);
        return view('admin.fields.edit', compact('field','categories'));
    }

    public function update(FieldRequest $request, Field $field)
    {
        $validatedData = $request->validated();
        $validatedData['image_original_path'] = $request->input('image_original_path');
        
        $this->fieldService->update($field, $validatedData);
        
        return redirect()->route('admin.fields.index')->with('success', 'Cập nhật lĩnh vực thành công.');
    }

    public function destroy(Field $field)
    {
        $this->fieldService->delete($field);
        return redirect()->route('admin.fields.index')->with('success', 'Xóa lĩnh vực thành công.');
    }
}