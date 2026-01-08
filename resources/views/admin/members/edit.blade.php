@extends('layouts.admin')

@section('title', 'Chỉnh sửa Hội viên')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 font-weight-bold">Chỉnh sửa thông tin: {{ $member->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.members.update', $member) }}" method="POST">
            @method('PUT')
            @include('admin.members._form')
        </form>
    </div>
</div>
@endsection
