@extends('layouts.admin')
@section('title', 'Sửa giảng viên')
@section('content_header', 'Sửa giảng viên')

@section('content')
<form action="{{ route('admin.teams.update', $team) }}" method="POST" enctype="multipart/form-data">
  @csrf @method('PUT')
  <div class="card">
    <div class="card-body">
      <x-form.input name="name" label="Họ tên" :value="$team->name" required />
      <x-form.input name="position" label="Vị trí" :value="$team->position" />
      <x-form.input name="hsk_level" label="Trình độ" :value="$team->hsk_level" />
      <x-form.input name="experience" label="Số năm kinh nghiệm" type="number" :value="$team->experience" />
      <x-form.image-input name="image" label="Ảnh đại diện" :value="$team->image" />
      <x-form.textarea name="bio" label="Giới thiệu chi tiết" :value="$team->bio" />
    </div>
    <div class="card-footer">
      <button class="btn btn-primary">Cập nhật</button>
      <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>
  </div>
</form>
@endsection
