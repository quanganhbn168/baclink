<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('sort_order')->latest()->paginate(20);
        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/partners'), $fileName);
            $validated['image'] = 'uploads/partners/' . $fileName;
        }

        Partner::create($validated);

        return redirect()->route('admin.partners.index')->with('success', 'Thêm đối tác thành công!');
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'sort_order' => 'nullable|integer',
            'status' => 'boolean',
        ]);

        // Xử lý upload ảnh mới nếu có
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if (File::exists(public_path($partner->image))) {
                File::delete(public_path($partner->image));
            }
            
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/partners'), $fileName);
            $validated['image'] = 'uploads/partners/' . $fileName;
        }

        $partner->update($validated);

        return redirect()->route('admin.partners.index')->with('success', 'Cập nhật đối tác thành công!');
    }

    public function destroy(Partner $partner)
    {
        // Xóa ảnh liên quan
        if (File::exists(public_path($partner->image))) {
            File::delete(public_path($partner->image));
        }

        $partner->delete();

        return back()->with('success', 'Xóa đối tác thành công!');
    }
}