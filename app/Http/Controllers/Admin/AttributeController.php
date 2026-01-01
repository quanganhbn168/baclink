<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    /**
     * Danh sách thuộc tính
     */
    public function index()
    {
        // Lấy danh sách, kèm theo relation values để đếm số lượng cho view
        $attributes = Attribute::with('values')->latest()->paginate(10);
        
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Form tạo mới
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Xử lý lưu mới (Attribute + AttributeValues)
     */
    public function store(Request $request)
    {
        // 1. Validate trực tiếp tại đây
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
            'type' => 'required|in:text,color,image',
            'is_variant_defining' => 'nullable', // Checkbox có thể null
            
            // Validate mảng values
            'values' => 'required|array|min:1',
            'values.*.value' => 'required|string|max:255',
            'values.*.color_code' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Tên thuộc tính không được để trống.',
            'name.unique' => 'Tên thuộc tính đã tồn tại.',
            'values.required' => 'Phải nhập ít nhất 1 giá trị.',
            'values.*.value.required' => 'Tên giá trị không được để trống.',
        ]);

        // Xử lý checkbox (nếu không tick thì là 0)
        $validated['is_variant_defining'] = $request->has('is_variant_defining') ? 1 : 0;

        try {
            DB::beginTransaction();

            // 2. Tạo Attribute
            $attribute = Attribute::create([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'is_variant_defining' => $validated['is_variant_defining'],
            ]);

            // 3. Tạo các Attribute Values
            // Mảng values từ form gửi lên dạng: [ 0 => [...], 1 => [...] ]
            foreach ($request->values as $item) {
                $attribute->values()->create([
                    'value' => $item['value'],
                    'color_code' => $item['color_code'] ?? null,
                    // 'image' => ... (xử lý upload ảnh nếu cần sau này)
                ]);
            }

            DB::commit();
            return redirect()->route('admin.attributes.index')->with('success', 'Tạo thuộc tính thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Lỗi hệ thống: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(Attribute $attribute)
    {
        // Load sẵn values để hiển thị trong form edit
        $attribute->load('values');
        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Xử lý cập nhật
     */
    public function update(Request $request, Attribute $attribute)
    {
        // 1. Validate
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('attributes', 'name')->ignore($attribute->id)],
            'type' => 'required|in:text,color,image',
            'is_variant_defining' => 'nullable',
            
            'values' => 'required|array|min:1',
            'values.*.id' => 'nullable|integer', // ID của value (nếu sửa cái cũ)
            'values.*.value' => 'required|string|max:255',
            'values.*.color_code' => 'nullable|string|max:20',
        ]);

        $validated['is_variant_defining'] = $request->has('is_variant_defining') ? 1 : 0;

        try {
            DB::beginTransaction();

            // 2. Update Attribute cha
            $attribute->update([
                'name' => $validated['name'],
                'type' => $validated['type'],
                'is_variant_defining' => $validated['is_variant_defining'],
            ]);

            // 3. Xử lý Attribute Values (Khó nhất là đoạn này: Thêm/Sửa/Xóa)
            
            // A. Lấy danh sách ID các value được gửi lên từ form
            $submittedIds = collect($request->values)->pluck('id')->filter()->toArray();

            // B. Xóa các value cũ trong DB mà KHÔNG có trong danh sách gửi lên (nghĩa là người dùng đã xóa dòng đó trên giao diện)
            $attribute->values()->whereNotIn('id', $submittedIds)->delete();

            // C. Loop qua danh sách gửi lên để Thêm mới hoặc Cập nhật
            foreach ($request->values as $item) {
                // Sử dụng updateOrCreate
                // Nếu có 'id' thì update, nếu không thì create mới
                $attribute->values()->updateOrCreate(
                    ['id' => $item['id'] ?? null], // Điều kiện tìm
                    [
                        'attribute_id' => $attribute->id, // Gán ID cha để chắc chắn
                        'value' => $item['value'],
                        'color_code' => $item['color_code'] ?? null,
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.attributes.index')->with('success', 'Cập nhật thuộc tính thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Lỗi cập nhật: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Xóa thuộc tính
     */
    public function destroy(Attribute $attribute)
    {
        try {
            // Xóa các giá trị con trước (nếu chưa set cascade trong database)
            $attribute->values()->delete();
            
            // Xóa cha
            $attribute->delete();
            
            return redirect()->route('admin.attributes.index')->with('success', 'Xóa thuộc tính thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Không thể xóa: ' . $e->getMessage()]);
        }
    }
}