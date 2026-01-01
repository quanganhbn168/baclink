@extends('layouts.admin')
@section('title', 'Thêm slide mới')
@section('content_header', 'Thêm slide mới')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Thêm slide mới</h3>
    </div>

    <form action="{{ route('admin.slides.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="card-body">

            {{-- Tiêu đề --}}
            <x-form.input 
                type="text" 
                name="title" 
                label="Tiêu đề slide" 
                :value="old('title')" 
            />

            {{-- Link --}}
            <x-form.input 
                type="text" 
                name="link" 
                label="Link bài / Link chuyển hướng" 
                :value="old('link')" 
            />

            {{-- Thứ tự hiển thị --}}
            <x-form.input 
                type="number" 
                name="position" 
                label="Thứ tự hiển thị" 
                :value="old('position', 0)" 
            />

            {{-- Trạng thái --}}
            <x-form.switch 
                name="status" 
                label="Hiển thị" 
                :checked="old('status', true)" 
            />

            <hr>

            {{-- Ảnh slide --}}
            <x-admin.form.media-input
                name="image_original_path"
                label="Ảnh slide (chuẩn: 1920×600px)"
                :multiple="false"
                :value="old('image_original_path')"
            />

        </div>

        <div class="card-footer text-right">
            <button type="submit" name="action" value="save" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Lưu
            </button>
            <button type="submit" name="action" value="save_new" class="btn btn-success">
                <i class="fas fa-plus-circle mr-1"></i> Lưu & Thêm mới
            </button>
            <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>

    </form>
</div>
@endsection
