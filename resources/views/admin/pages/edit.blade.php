@extends('layouts.admin')
@section('title', 'Sửa Trang')

@section('content')
<form action="{{ route('admin.pages.update', $page) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card card-warning card-outline">
        <div class="card-header">
            <h3 class="card-title">Cập nhật trang: {{ $page->title }}</h3>
        </div>
        <div class="card-body">
            <x-form.translatable-input name="title" label="Tiêu đề trang" :value="$page->getTranslations('title')" required />

            <div class="form-group">
                <label>Đường dẫn (Slug)</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}">
            </div>

            <x-form.translatable-ckeditor name="content" label="Nội dung trang" :value="$page->getTranslations('content')" />

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" {{ $page->is_active ? 'checked' : '' }}>
                    <label class="custom-control-label" for="isActive">Kích hoạt</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Cập nhật</button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-default">Hủy bỏ</a>
        </div>
    </div>
</form>
@endsection

@push('js')

@endpush
