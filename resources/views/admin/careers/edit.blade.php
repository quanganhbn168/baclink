@extends('layouts.admin')
@section('title', 'Chỉnh sửa tin tuyển dụng')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">@yield('title')</h1>
    <form action="{{ route('admin.careers.update', $career) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.careers._form')
        <div class="text-right mt-3">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
        </div>
    </form>
</div>
@endsection
