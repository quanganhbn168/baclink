@extends('layouts.admin')
@section('title', 'Chỉnh sửa tin tuyển dụng')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">@yield('title')</h1>
    <form action="{{ route('admin.careers.update', $career) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @csrf
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <x-form.translatable-input name="name" label="Tên vị trí" :value="$career->getTranslations('name')" required />
                <div class="form-group">
                    <label>Đường dẫn tĩnh (Slug)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $career->slug) }}">
                </div>
                <div class="form-group">
                    <x-form.translatable-ckeditor name="description" label="Mô tả công việc" :value="$career->getTranslations('description')" />
                </div>
                <div class="form-group">
                    <x-form.translatable-ckeditor name="requirements" label="Yêu cầu ứng viên" :value="$career->getTranslations('requirements')" />
                </div>
                <div class="form-group">
                    <x-form.translatable-ckeditor name="benefits" label="Quyền lợi" :value="$career->getTranslations('benefits')" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $career->quantity) }}">
                </div>
                <div class="form-group">
                    <label>Mức lương</label>
                    <input type="text" name="salary" class="form-control" value="{{ old('salary', $career->salary) }}" placeholder="VD: Thỏa thuận">
                </div>
                <div class="form-group">
                    <label>Kinh nghiệm</label>
                    <input type="text" name="experience" class="form-control" value="{{ old('experience', $career->experience) }}" placeholder="VD: 2 năm">
                </div>
                <div class="form-group">
                    <label>Hạn nộp hồ sơ</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline', $career->deadline ? $career->deadline->format('Y-m-d') : '') }}">
                </div>
                <hr>
                <x-form.switch name="status" label="Trạng thái" :checked="old('status', $career->status)" />
                <hr>
                <x-form.image-input name="image" label="Ảnh minh họa" :value="$career->image ?? ''" />
            </div>
        </div>
    </div>
</div>
        <div class="text-right mt-3">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.careers.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection
