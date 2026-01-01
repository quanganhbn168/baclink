<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Enums\ContentSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;

class ContentBlockController extends Controller
{
    public function index(Request $request)
    {
        $query = ContentBlock::query();

        // Bộ lọc theo Section
        if ($request->filled('section')) {
            $query->where('section', $request->section);
        }

        $blocks = $query->orderBy('section')->orderBy('sort_order')->paginate(20);

        return view('admin.content_blocks.index', compact('blocks'));
    }

    public function create()
    {
        return view('admin.content_blocks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'section'     => ['required', new Enum(ContentSection::class)],
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:100', // Ví dụ: fas fa-home
            'url'         => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data['is_active'] = $request->has('is_active');

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/blocks', 'public');
        }

        ContentBlock::create($data);

        return redirect()->route('admin.content-blocks.index')
            ->with('success', 'Thêm khối nội dung thành công!');
    }

    public function edit(ContentBlock $contentBlock)
    {
        return view('admin.content_blocks.edit', compact('contentBlock'));
    }

    public function update(Request $request, ContentBlock $contentBlock)
    {
        $data = $request->validate([
            'section'     => ['required', new Enum(ContentSection::class)],
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'icon'        => 'nullable|string|max:100',
            'url'         => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data['is_active'] = $request->has('is_active');

        // Xử lý ảnh: Nếu up ảnh mới thì xóa ảnh cũ
        if ($request->hasFile('image')) {
            if ($contentBlock->image) {
                Storage::disk('public')->delete($contentBlock->image);
            }
            $data['image'] = $request->file('image')->store('uploads/blocks', 'public');
        }

        $contentBlock->update($data);

        return redirect()->route('admin.content-blocks.index')
            ->with('success', 'Cập nhật thành công!');
    }

    public function destroy(ContentBlock $contentBlock)
    {
        if ($contentBlock->image) {
            Storage::disk('public')->delete($contentBlock->image);
        }
        $contentBlock->delete();

        return redirect()->back()->with('success', 'Đã xóa khối nội dung.');
    }
}