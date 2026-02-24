@extends('layouts.admin')
@section('title', 'Thêm Trang mới')

@section('content')
<form action="{{ route('admin.pages.store') }}" method="POST">
    @csrf
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Nội dung trang</h3>
        </div>
        <div class="card-body">
            
            {{-- 1. KHỐI BÁO LỖI TỔNG (Giúp bạn biết ngay có lỗi gì nếu quên hiển thị ở input) --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-ban"></i> Có lỗi xảy ra!</h5>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 2. FIELD TIÊU ĐỀ --}}
            <x-form.translatable-input name="title" label="Tiêu đề trang" required />

            {{-- 3. FIELD SLUG --}}
            <div class="form-group">
                <label>Đường dẫn (Slug - Để trống sẽ tự tạo)</label>
                <input type="text" name="slug" 
                       class="form-control @error('slug') is-invalid @enderror" 
                       value="{{ old('slug') }}" 
                       placeholder="VD: chinh-sach-bao-mat">
                @error('slug')
                    <span class="error invalid-feedback" style="display: block">{{ $message }}</span>
                @enderror
            </div>

            <x-form.translatable-ckeditor name="content" label="Nội dung trang" :value="old('content', [])" />

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="isActive" name="is_active" checked>
                    <label class="custom-control-label" for="isActive">Kích hoạt</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu lại</button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-default">Hủy bỏ</a>
        </div>
    </div>
</form>
@endsection

@push('js')
@endpush
