@extends('layouts.admin')
@section('title', 'Danh sách bài viết')
@section('content_header', 'Danh sách bài viết')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Danh sách bài viết</h3>
        <div class="card-tools">
            <x-admin.bulk-action-bar model="post" />
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Thêm bài viết
            </a>
        </div>
    </div>
    <div class="card-body">
        {{-- Bộ lọc --}}
        <div class="row mb-3">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.posts.index') }}" class="form-inline">
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
                        <th class="text-center" width="40">
                            <input type="checkbox" id="checkAll" class="custom-checkbox">
                        </th>
                        <th>Ảnh</th>
                        <th>Tên danh mục</th>
                        <th>Danh mục cha</th>
                        <th class="text-center">Home</th>
                        <th class="text-center">Trạng thái</th>
                        <th style="width: 120px" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $key => $post)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="custom-checkbox check-item" value="{{ $post->id }}">
                        </td>
                        <td>
                            @if($post->mainImage())
                                <img src="{{ Storage::url($post->mainImage()->main_path) }}" alt="{{ $post->name }}" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $post->title }}</strong>
                            @if($post->slug)
                                <br><small class="text-muted">{{ $post->slug }}</small>
                            @endif
                        </td>
                        <td>{{ $post->category->name ?? '—' }}</td>
                        {{-- Các toggle --}}
                        <td class="text-center">
                            <x-boolean-toggle model="PostCategory" :record="$post" field="is_home" />
                        </td>
                        <td class="text-center">
                            <x-boolean-toggle model="PostCategory" :record="$post" field="status" />
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('admin.posts.edit', $post) }}" 
                                   class="btn btn-sm btn-warning" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <x-admin.duplicate-button 
                                    model="post_categories" 
                                    :id="$post->id"
                                    label="" 
                                    icon="fas fa-copy" 
                                    confirm="Nhân bản danh mục này?" 
                                    class="btn btn-sm btn-info" 
                                />
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline">
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
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                            Chưa có danh mục nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Phân trang --}}
        @if($posts->hasPages())
        <div class="card-footer clearfix">
            {{ $posts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
