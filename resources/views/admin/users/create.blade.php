@extends('layouts.admin')

@section('title', 'Tạo Người dùng mới')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Thông tin Người dùng mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            {{--
                Biến $user sẽ không tồn tại ở trang create,
                nên file _form sẽ tự động hiển thị form ở trạng thái "tạo mới".
            --}}
            @include('admin.users._form')
        </form>
    </div>
</div>
@endsection