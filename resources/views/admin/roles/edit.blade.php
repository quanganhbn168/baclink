@extends('layouts.admin')

@section('title', 'Chỉnh sửa Vai trò')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Chỉnh sửa: {{ $role->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @method('PUT')
            @include('admin.roles._form')
        </form>
    </div>
</div>
@endsection
