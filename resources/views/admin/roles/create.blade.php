@extends('layouts.admin')

@section('title', 'Tạo Vai trò mới')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Tạo Vai trò mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @include('admin.roles._form')
        </form>
    </div>
</div>
@endsection
