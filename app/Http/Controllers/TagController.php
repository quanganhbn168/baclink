<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Requests\TagRequest;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected TagService $service;

    public function __construct(TagService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $tags = $this->service->getAll();
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(TagRequest $request)
    {
        $this->service->create($request->validated());
        return to_route('admin.tags.index')->with('success', 'Đã thêm tag');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $this->service->update($tag, $request->validated());
        return to_route('admin.tags.index')->with('success', 'Đã cập nhật tag');
    }

    public function destroy(Tag $tag)
    {
        $this->service->delete($tag);
        return back()->with('success', 'Đã xoá tag');
    }

    public function ajax(Request $request)
    {
        return $this->service->searchAjax($request->get('q', ''));
    }
}
