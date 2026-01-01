@extends('layouts.admin')
@section('title', 'Danh sách giảng viên')
@section('content_header', 'Đội ngũ giảng viên')

@section('content')
<div class="card">
  <div class="card-header">
    <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">Thêm mới</a>
  </div>

  <div class="card-body table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Ảnh</th>
          <th>Họ tên</th>
          <th>Vị trí</th>
          <th>Trình độ</th>
          <th>Kinh nghiệm</th>
          <th>Ngày tạo</th>
          <th width="120">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @foreach($teams as $team)
        <tr>
          <td><img src="{{ asset($team->image ?? 'images/setting/no-image.png') }}" width="50" height="50" class="rounded-circle" /></td>
          <td>{{ $team->name }}</td>
          <td>{{ $team->position }}</td>
          <td>{{ $team->level }}</td>
          <td>{{ $team->experience }} năm</td>
          <td>{{ $team->created_at->format('d/m/Y') }}</td>
          <td>
            <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <form action="{{ route('admin.teams.destroy', $team) }}" method="POST" class="d-inline" onsubmit="return confirm('Xoá giảng viên này?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{ $teams->links() }}
  </div>
</div>
@endsection
