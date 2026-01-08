@extends('layouts.admin')

@section('title', 'Quản lý Người dùng')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Danh sách Người dùng</h5>
    </div>
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Thêm người dùng mới
            </a>
            {{-- Optional: Search Form --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex">
                <input type="text" class="form-control me-2" name="search" placeholder="Tìm kiếm tên, email..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-secondary">Tìm</button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Họ và tên</th>
                        <th scope="col">Email / Điện thoại</th>
                        <th scope="col">Vai trò</th>
                        <th scope="col" style="width: 15%;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $key => $user)
                        <tr>
                            <th scope="row">{{ $key + 1 }}</th>
                            <td>{{ $user->name }}</td>
                            <td>
                                <div>{{ $user->email }}</div>
                                <small class="text-muted">{{ $user->phone }}</small>
                            </td>
                            <td>
                                @foreach($user->getRoleNames() as $roleName)
                                    <span class="badge bg-info">{{ $roleName }}</span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Sửa
                                </a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Anh có chắc chắn muốn xóa người dùng này không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Không tìm thấy người dùng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
