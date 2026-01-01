<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Services\TestimonialService;

class TestimonialController extends Controller
{
    public function __construct(protected TestimonialService $testimonialService) {}

    public function index()
    {
        $testimonials = $this->testimonialService->getAll();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $this->testimonialService->create($request);

        $route = $request->has('save_new')
            ? route('admin.testimonials.create')
            : route('admin.testimonials.index');

        return redirect($route)->with('success', 'Thêm cảm nhận thành công.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $this->testimonialService->update($request, $testimonial);
        return redirect()->route('admin.testimonials.index')->with('success', 'Cập nhật cảm nhận thành công.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $this->testimonialService->delete($testimonial);
        return redirect()->route('admin.testimonials.index')->with('success', 'Xoá cảm nhận thành công.');
    }
}
