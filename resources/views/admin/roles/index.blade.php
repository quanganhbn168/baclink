@extends('layouts.admin') {{-- Thay đổi tùy theo layout của anh --}}

@section('title', 'Quản lý Vai trò')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Danh sách Vai trò</h5>
    </div>
    <div class="card-body">
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary mb-3">Thêm vai trò mới</a>

        {{-- Hiển thị thông báo thành công hoặc lỗi --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên vai trò</th>
                        <th>Các quyền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                @foreach ($role->permissions->take(5) as $permission)
                                    <span class="badge bg-info me-1">{{ $permission->name }}</span>
                                @endforeach
                                @if ($role->permissions->count() > 5)
                                    <span class="badge bg-secondary">...</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Anh có chắc chắn muốn xóa vai trò này không? Hành động này không thể hoàn tác.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Chưa có vai trò nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        <div class="mt-3">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection