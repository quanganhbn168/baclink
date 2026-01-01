@extends('layouts.admin')
@section('title', 'Thêm chi nhánh')
@section('content_header', 'Thêm chi nhánh')

@section('content')
<form action="{{ route('admin.branches.store') }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-body">
            <x-form.input name="name" label="Tên chi nhánh" required />
            <x-form.input name="address" label="Địa chỉ" />
            <x-form.input name="phone" label="Số điện thoại" />
            <x-form.input name="email" label="Email" />

            <x-form.ckeditor name="map_url" label="Bản đồ (iframe)" />

            <x-form.switch name="status" label="Hiển thị" :checked="true" />
        </div>
        <div class="card-footer">
            <button type="submit" name="action" value="save" class="btn btn-primary">Lưu</button>
            <button type="submit" name="action" value="save_new" class="btn btn-success">Lưu & thêm mới</button>
            <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
@endsection
