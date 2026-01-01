@extends('layouts.admin')

@section('title', 'Danh sách chi nhánh')
@section('content_header', 'Danh sách chi nhánh')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm chi nhánh
        </a>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tên</th>
                    <th>Địa chỉ</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($branches as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td>{{ $branch->address }}</td>
                        <td>{{ $branch->phone}}</td>
                        <td>{{ $branch->email }}</td>
                        <td>
                            <x-boolean-toggle model="branch" :record="$branch" field="status" />
                        </td>
                        <td>
                            <a href="{{ route('admin.branches.edit', $branch) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" class="d-inline-block" onsubmit="return confirmDelete(this)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('js')
<script>
    function confirmDelete(form) {
        event.preventDefault();
        Swal.fire({
            title: 'Bạn có chắc muốn xoá?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xoá',
            cancelButtonText: 'Huỷ',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>
@endpush
