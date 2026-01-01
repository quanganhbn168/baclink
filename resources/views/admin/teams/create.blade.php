@extends('layouts.admin')
@section('title', 'Thêm giảng viên')
@section('content_header', 'Thêm giảng viên')

@section('content')
<form action="{{ route('admin.teams.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="card">
    <div class="card-body">
      <x-form.input name="name" label="Họ tên" required />
      <x-form.input name="position" label="Vị trí" />
      <x-form.input name="hsk_level" label="Trình độ" />
      <x-form.input name="experience" label="Số năm kinh nghiệm" type="number" />
      <x-form.image-input name="image" label="Ảnh đại diện" />
      <x-form.textarea name="bio" label="Giới thiệu chi tiết" />
    </div>
    <div class="card-footer">
      <button class="btn btn-primary">Lưu</button>
      <button class="btn btn-success" name="save_new" value="1">Lưu & thêm mới</button>
      <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
  </div>
</form>
@endsection
