@extends('layouts.admin')

@section('title', 'Chỉnh sửa Người dùng')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Chỉnh sửa Người dùng: {{ $user->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @method('PUT')
            {{--
                Controller đã truyền biến $user và $userRoles vào view này,
                nên file _form sẽ tự động hiển thị dữ liệu và các vai trò đã chọn.
            --}}
            @include('admin.users._form')
        </form>
    </div>
</div>
@endsection