@extends('layouts.admin')

@section('title', 'Chỉnh sửa danh mục')
@section('content_header', 'Chỉnh sửa danh mục')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Chỉnh sửa danh mục</h3>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="form-horizontal" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card-body">
            <x-form.input
                type="text"
                name="name"
                label="Tên danh mục"
                :value="old('name', $category->name)"
            />

            <x-form.input
                type="text"
                name="slug"
                label="Slug (URL thân thiện)"
                :value="old('slug', $category->slug)"
            />

            <x-form.select
                name="parent_id"
                label="Danh mục cha"
                :options="$categories"
                :selected="old('parent_id', $category->parent_id)"
                placeholder="-- Không có danh mục cha --"
            />

            <x-form.image-input
                name="image"
                label="Ảnh đại diện"
                :value="$category->image"
            />

            <x-form.image-input
                name="banner"
                label="Banner (tuỳ chọn)"
                :value="$category->banner"
            />

            <x-form.switch
                name="status"
                label="Trạng thái"
                :checked="old('status', $category->status)"
            />
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-dark">Quay lại</a>
        </div>
    </form>
</div>
@endsection
