@extends('layouts.admin')

@section('title', 'Danh mục dự án')
@section('content_header', 'Danh mục dự án')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách danh mục dự án</h3>
        <div class="card-tools">
            <a href="{{ route('admin.project-categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Thêm danh mục
            </a>
        </div>
    </div>

    <div class="card-body">
        {{-- Bộ lọc --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.project-categories.index') }}" class="form-inline">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Tìm kiếm...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th style="width: 50px">#</th>
                        <th>Ảnh</th>
                        <th>Tên danh mục</th>
                        <th>Danh mục cha</th>
                        <th class="text-center">Home</th>
                        <th class="text-center">Menu</th>
                        <th class="text-center">Footer</th>
                        <th class="text-center">Trạng thái</th>
                        <th style="width: 120px" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $key => $category)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>
                            @if($category->mainImage())
                                <img src="{{ Storage::url($category->mainImage()->main_path) }}" alt="{{ $category->name }}" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $category->name }}</strong>
                            @if($category->slug)
                                <br><small class="text-muted">{{ $category->slug }}</small>
                            @endif
                        </td>
                        <td>{{ $category->parent->name ?? '—' }}</td>
                        
                        {{-- Các toggle --}}
                        <td class="text-center">
                            <x-boolean-toggle model="ProjectCategory" :record="$category" field="is_home" />
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="ProjectCategory" :record="$category" field="is_menu" />
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="ProjectCategory" :record="$category" field="is_footer" />
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="ProjectCategory" :record="$category" field="status" />
                        </td>

                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.project-categories.edit', $category) }}" 
                                   class="btn btn-sm btn-warning" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <x-admin.duplicate-button 
                                    model="project_categories" 
                                    :id="$category->id"
                                    label="" 
                                    icon="fas fa-copy" 
                                    confirm="Nhân bản danh mục này?" 
                                    class="btn btn-sm btn-info" 
                                />

                                <form action="{{ route('admin.project-categories.destroy', $category) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                            Chưa có danh mục nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if($categories->hasPages())
        <div class="card-footer clearfix">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('css')
@endpush

@push('js')
<script>
// Xác nhận xóa với SweetAlert2
function confirmDelete(form) {
    event.preventDefault();
    Swal.fire({
        title: 'Bạn có chắc muốn xoá?',
        text: "Hành động này không thể hoàn tác!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Xoá',
        cancelButtonText: 'Huỷ',
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
</script>
@endpush
