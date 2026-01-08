@extends('layouts.admin')
@section('title', 'Quản lý Trang tĩnh')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Danh sách Trang chính sách</h3>
        <div class="card-tools">
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Thêm trang mới
            </a>
        </div>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th style="width: 5%">ID</th>
                    <th>Tiêu đề</th>
                    <th>Đường dẫn (Slug)</th>
                    <th>Trạng thái</th>
                    <th>Ngày cập nhật</th>
                    <th style="width: 15%">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td>{{ $page->id }}</td>
                        <td><strong>{{ $page->title }}</strong></td>
                        <td><a href="{{ route('frontend.slug.handle', $page->slug) }}" target="_blank">{{ $page->slug }} <i class="fas fa-external-link-alt small"></i></a></td>
                        <td>
                            @if($page->is_active)
                                <span class="badge badge-success">Hiện</span>
                            @else
                                <span class="badge badge-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>{{ $page->updated_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa trang này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">Chưa có trang nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $pages->links() }}
    </div>
</div>
@endsection
