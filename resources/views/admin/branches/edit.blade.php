@extends('layouts.admin')
@section('title', 'Cập nhật chi nhánh')
@section('content_header', 'Cập nhật chi nhánh')

@section('content')
<form action="{{ route('admin.branches.update', $branch) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <x-form.input name="name" label="Tên chi nhánh" :value="$branch->name" required />
            <x-form.input name="address" label="Địa chỉ" :value="$branch->address" required />
            <x-form.input name="phone" label="Số điện thoại" :value="$branch->phone" />
            <x-form.input name="email" label="Email" :value="$branch->email" />

            <x-form.ckeditor name="map_url" label="Bản đồ (iframe)" :value="$branch->map_url" />

            <x-form.switch name="status" label="Hiển thị" :checked="$branch->status" />
        </div>
        <div class="card-footer">
            <button type="submit" name="action" value="update" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
</form>
@endsection
