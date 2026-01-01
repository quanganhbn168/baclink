<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IntroRequest; // nếu chưa có, dùng FormRequest riêng cho Intro
use App\Models\Intro;
use App\Services\IntroService;
use Illuminate\Http\Request;

class IntroController extends Controller
{
    public function __construct(
        protected IntroService $introService
    ) {}

    /** Danh sách + bộ lọc + phân trang */
    public function index(Request $request)
    {
        [$intros] = $this->introService->list($request);
        return view('admin.intros.index', compact('intros'));
    }

    /** Form tạo */
    public function create()
    {
        return view('admin.intros.create');
    }

    /** Lưu tạo */
    public function store(IntroRequest $request)
    {
        $data = $request->validated();
        // media-input trả về hidden inputs
        $data['image_original_path']  = $request->input('image_original_path');
        $data['banner_original_path'] = $request->input('banner_original_path');

        $this->introService->create($data);

        return $request->has('save_new')
            ? redirect()->route('admin.intros.create')->with('success', 'Thêm Intro thành công.')
            : redirect()->route('admin.intros.index')->with('success', 'Thêm Intro thành công.');
    }

    /** Form sửa */
    public function edit(Intro $intro)
    {
        // để prefill media-input
        $intro->load('images');
        return view('admin.intros.edit', compact('intro'));
    }

    /** Cập nhật */
    public function update(IntroRequest $request, Intro $intro)
    {
        $data = $request->validated();
        $data['image_original_path']  = $request->input('image_original_path');
        $data['banner_original_path'] = $request->input('banner_original_path');

        $this->introService->update($intro, $data);

        return redirect()->route('admin.intros.index')->with('success', 'Cập nhật Intro thành công.');
    }

    /** Xoá */
    public function destroy(Intro $intro)
    {
        $this->introService->delete($intro);
        return redirect()->route('admin.intros.index')->with('success', 'Đã xoá Intro.');
    }
}
