@extends('layouts.admin')

@section('title', 'Thêm Hội viên mới')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 font-weight-bold">Thông tin Hội viên mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.members.store') }}" method="POST">
            @include('admin.members._form')
        </form>
    </div>
</div>
@endsection
