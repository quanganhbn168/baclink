<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\BrandRequest;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    protected BrandService $service;

    public function __construct(BrandService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $brands = $this->service->getAll();
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(BrandRequest $request)
    {
        $this->service->create($request->validated());

        return to_route('admin.brands.index')->with('success', 'Đã thêm thương hiệu');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $this->service->update($brand, $request->validated());

        return to_route('admin.brands.index')->with('success', 'Đã cập nhật thương hiệu');
    }

    public function destroy(Brand $brand)
    {
        $this->service->delete($brand);

        return back()->with('success', 'Đã xoá thương hiệu');
    }

    public function ajax(Request $request)
    {
        if ($request->isMethod('post')) {
            $brand = Brand::firstOrCreate(
                ['name' => $request->name],
                ['slug' => Str::slug($request->name)]
            );

            return response()->json([
                'id' => $brand->id,
                'text' => $brand->name,
            ]);
        }

        return $this->service->searchAjax($request->get('q', ''));
    }

}
