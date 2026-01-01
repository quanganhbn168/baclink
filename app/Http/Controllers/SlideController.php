<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SlideRequest;
use App\Models\Slide;
use App\Services\SlideService;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    public function __construct(
        protected SlideService $slideService
    ) {}

    /**
     * Danh sách slide + bộ lọc & phân trang
     * Filters: keyword (title/link), status
     * Params: per_page (mặc định 20)
     */
    public function index(Request $request)
    {
        [$slides] = $this->slideService->list($request);
        return view('admin.slides.index', compact('slides'));
    }

    /** Form tạo */
    public function create()
    {
        return view('admin.slides.create');
    }

    /** Lưu tạo */
    public function store(SlideRequest $request)
    {
        $data = $request->validated();
        // media-input trả về path ở input hidden "image_original_path"
        $data['image_original_path'] = $request->input('image_original_path');

        $this->slideService->create($data);

        return $request->has('save_new')
            ? redirect()->route('admin.slides.create')->with('success', 'Thêm slide thành công.')
            : redirect()->route('admin.slides.index')->with('success', 'Thêm slide thành công.');
    }

    /** Form sửa */
    public function edit(Slide $slide)
    {
        // để view prefill media-input
        $slide->load('images');
        return view('admin.slides.edit', compact('slide'));
    }

    /** Cập nhật */
    public function update(SlideRequest $request, Slide $slide)
    {
        $data = $request->validated();
        $data['image_original_path'] = $request->input('image_original_path');

        $this->slideService->update($slide, $data);

        return redirect()->route('admin.slides.index')->with('success', 'Cập nhật slide thành công.');
    }

    /** Xoá */
    public function destroy(Slide $slide)
    {
        $this->slideService->delete($slide);
        return redirect()->route('admin.slides.index')->with('success', 'Đã xoá slide.');
    }
}
