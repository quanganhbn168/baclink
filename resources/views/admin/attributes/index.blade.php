{{-- resources/views/admin/attributes/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Danh sách thuộc tính')
@section('content_header_title', 'Quản lý thuộc tính')

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Danh sách thuộc tính</h3>
            <div class="card-tools">
                <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Thêm mới
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible m-3">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h5><i class="icon fas fa-check"></i> Thành công!</h5>
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-striped projects">
                <thead>
                    <tr>
                        <th style="width: 5%">ID</th>
                        <th style="width: 20%">Tên thuộc tính</th>
                        <th style="width: 15%">Loại</th>
                        <th style="width: 15%">Dùng cho biến thể</th>
                        <th style="width: 30%">Giá trị (Preview)</th>
                        <th style="width: 15%" class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attributes as $attr)
                        <tr>
                            <td>{{ $attr->id }}</td>
                            <td>
                                <strong>{{ $attr->name }}</strong>
                                <br>
                                <small class="text-muted">Created {{ $attr->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                @if($attr->type == 'color')
                                    <span class="badge badge-info"><i class="fas fa-palette"></i> Màu sắc</span>
                                @elseif($attr->type == 'image')
                                    <span class="badge badge-warning"><i class="fas fa-image"></i> Hình ảnh</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-font"></i> Văn bản</span>
                                @endif
                            </td>
                            <td>
                                @if($attr->is_variant_defining)
                                    <span class="badge badge-success">Có</span>
                                @else
                                    <span class="badge badge-light">Không</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap">
                                    @foreach($attr->values->take(4) as $val)
                                        @if($attr->type == 'color' && $val->color_code)
                                            <span class="badge mr-1 border" style="background-color: {{ $val->color_code }}; color: #fff; text-shadow: 0 0 2px #000;">
                                                {{ $val->value }}
                                            </span>
                                        @else
                                            <span class="badge badge-light border mr-1">{{ $val->value }}</span>
                                        @endif
                                    @endforeach
                                    @if($attr->values->count() > 4)
                                        <span class="badge badge-light text-muted">...+{{ $attr->values->count() - 4 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="project-actions text-center">
                                <a class="btn btn-info btn-sm" href="{{ route('admin.attributes.edit', $attr->id) }}">
                                    <i class="fas fa-pencil-alt"></i> Sửa
                                </a>
                                <form action="{{ route('admin.attributes.destroy', $attr->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thuộc tính này? Toàn bộ giá trị con cũng sẽ bị xóa!');">
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i><br>
                                Chưa có thuộc tính nào được tạo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            {{ $attributes->links() }}
        </div>
    </div>
@endsection