@extends('layouts.admin')

@section('title', 'Danh mục Lĩnh vực')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">@yield('title')</h1>
        <a href="{{ route('admin.field-categories.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Thêm mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">STT</th>
                            <th>Tên danh mục</th>
                            <th>Danh mục cha</th>
                            <th width="10%">Trạng thái</th>
                            <th width="10%">Thứ tự</th>
                            <th width="15%">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($category->parent_id)
                                        <span class="text-muted">&mdash;</span> 
                                    @endif
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    {{ $category->parent->name ?? '—' }}
                                </td>
                                <td>
                                    {{-- ĐÂY LÀ PHẦN THAY ĐỔI --}}
                                    {{-- Sử dụng component boolean-toggle mới --}}
                                    <x-boolean-toggle
                                        model="field_category"
                                        :record="$category"
                                        field="status"
                                        onText="Hoạt động"
                                        offText="Tạm ẩn"
                                    />
                                </td>
                                <td>{{ $category->order ?? 0 }}</td>
                                <td>
                                    <a href="{{ route('admin.field-categories.edit', $category) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.field-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này? Hành động này không thể hoàn tác.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Chưa có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection