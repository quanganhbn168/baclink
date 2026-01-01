<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CareerRequest;
use App\Models\Career;
use App\Services\CareerService;

class CareerController extends Controller
{
    public function __construct(protected CareerService $service) {}

    public function index()
    {
        return view('admin.careers.index', ['careers' => $this->service->getAll()]);
    }

    public function create()
    {
        return view('admin.careers.create', ['career' => new Career()]);
    }

    public function store(CareerRequest $request)
    {
        $this->service->store($request);
        return redirect()->route('admin.careers.index')->with('success', 'Thêm tin tuyển dụng thành công.');
    }

    public function edit(Career $career)
    {
        return view('admin.careers.edit', compact('career'));
    }

    public function update(CareerRequest $request, Career $career)
    {
        $this->service->update($request, $career);
        return redirect()->route('admin.careers.index')->with('success', 'Cập nhật tin tuyển dụng thành công.');
    }

    public function destroy(Career $career)
    {
        $this->service->destroy($career);
        return redirect()->route('admin.careers.index')->with('success', 'Xóa tin tuyển dụng thành công.');
    }
}