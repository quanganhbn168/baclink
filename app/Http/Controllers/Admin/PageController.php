<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    // 1. Danh sách
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    // 2. Form thêm mới
    public function create()
    {
        return view('admin.pages.create');
    }

    // 3. Lưu dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'slug'    => 'nullable|string|max:255|unique:pages,slug', // Slug có thể để trống, ta sẽ tự tạo
            'content' => 'required',
        ]);

        $data = $request->all();

        // Tự tạo slug nếu không nhập
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        } else {
            $data['slug'] = Str::slug($data['slug']); // Chuẩn hóa slug nếu người dùng nhập có dấu
        }
        
        // Check trùng slug lần nữa sau khi generate
        if (Page::where('slug', $data['slug'])->exists()) {
             return back()->withInput()->withErrors(['slug' => 'Đường dẫn (Slug) này đã tồn tại, vui lòng chọn tiêu đề khác.']);
        }

        $data['is_active'] = $request->has('is_active');

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Tạo trang mới thành công!');
    }

    // 4. Form sửa
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    // 5. Cập nhật
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'slug'    => ['nullable', 'string', 'max:255', Rule::unique('pages')->ignore($page->id)],
            'content' => 'required',
        ]);

        $data = $request->all();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }
        
        // Check trùng slug (trừ chính nó)
        if (Page::where('slug', $data['slug'])->where('id', '!=', $page->id)->exists()) {
             return back()->withInput()->withErrors(['slug' => 'Đường dẫn (Slug) này đã tồn tại.']);
        }

        $data['is_active'] = $request->has('is_active');

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Cập nhật trang thành công!');
    }

    // 6. Xóa
    public function destroy(Page $page)
    {
        $page->delete();
        return back()->with('success', 'Đã xóa trang.');
    }
}