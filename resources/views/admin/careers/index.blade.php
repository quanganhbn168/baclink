@extends('layouts.admin')
@section('title', 'Tuyển dụng')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
        <a href="{{ route('admin.careers.create') }}" class="btn btn-primary">Thêm mới</a>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vị trí</th>
                        <th>Số lượng</th>
                        <th>Hạn nộp</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($careers as $career)
                    <tr>
                        <td>{{ $career->name }}</td>
                        <td>{{ $career->quantity }}</td>
                        <td>{{ $career->deadline ? $career->deadline->format('d/m/Y') : '' }}</td>
                        <td><x-boolean-toggle model="career" :record="$career" field="status" /></td>
                        <td>
                            <a href="{{ route('admin.careers.edit', $career) }}" class="btn btn-warning btn-sm">Sửa</a>
                            <form action="{{ route('admin.careers.destroy', $career) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Chưa có dữ liệu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection