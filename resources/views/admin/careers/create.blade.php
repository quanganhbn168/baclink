@extends('layouts.admin')
@section('title', 'Thêm tin tuyển dụng')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">@yield('title')</h1>
    <form action="{{ route('admin.careers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <x-form.translatable-input name="name" label="Tên vị trí" :value="old('name', [])" required />
                <div class="form-group">
                    <label>Đường dẫn tĩnh (Slug)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                </div>
                <div class="form-group">
                    <x-form.translatable-ckeditor name="description" label="Mô tả công việc" :value="old('description', [])" />
                </div>
                <div class="form-group">
                    <x-form.translatable-ckeditor name="requirements" label="Yêu cầu ứng viên" :value="old('requirements', [])" />
                </div>
                <div class="form-group">
                    <x-form.translatable-ckeditor name="benefits" label="Quyền lợi" :value="old('benefits', [])" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}">
                </div>
                <div class="form-group">
                    <label>Mức lương</label>
                    <input type="text" name="salary" class="form-control" value="{{ old('salary') }}" placeholder="VD: Thỏa thuận">
                </div>
                <div class="form-group">
                    <label>Kinh nghiệm</label>
                    <input type="text" name="experience" class="form-control" value="{{ old('experience') }}" placeholder="VD: 2 năm">
                </div>
                <div class="form-group">
                    <label>Hạn nộp hồ sơ</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                </div>
                <hr>
                <x-form.switch name="status" label="Trạng thái" :checked="old('status', true)" />
                <hr>
                <x-form.image-input name="image" label="Ảnh minh họa" :required="true" />
            </div>
        </div>
    </div>
</div>
        <div class="text-right mt-3">
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.careers.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection
