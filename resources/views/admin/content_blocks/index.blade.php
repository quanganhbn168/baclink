@extends('layouts.admin')
@section('title', 'Quản lý Khối nội dung')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Danh sách Khối nội dung</h3>
        <div class="card-tools">
            <a href="{{ route('admin.content-blocks.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Thêm mới
            </a>
        </div>
    </div>
    
    {{-- Bộ lọc --}}
    <div class="card-body border-bottom bg-light">
        <form action="" method="GET" class="form-inline">
            <label class="mr-2">Lọc theo loại:</label>
            <select name="section" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                <option value="">-- Tất cả --</option>
                @foreach(App\Enums\ContentSection::cases() as $section)
                    <option value="{{ $section->value }}" {{ request('section') == $section->value ? 'selected' : '' }}>
                        {{ $section->label() }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card-body p-0 table-responsive">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="15%">Khu vực (Section)</th>
                    <th width="10%">Hình/Icon</th>
                    <th>Tiêu đề / Số liệu</th>
                    <th>Mô tả ngắn</th>
                    <th width="5%">Thứ tự</th>
                    <th width="5%">Trạng thái</th>
                    <th width="10%">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blocks as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td><span class="badge badge-info">{{ $item->section->label() }}</span></td>
                        <td class="text-center">
                            @if($item->image)
                                <img src="{{ asset('storage/'.$item->image) }}" style="height: 30px;">
                            @elseif($item->icon)
                                <i class="{{ $item->icon }} text-primary fa-lg"></i>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $item->title ?? '---' }}</strong>
                            @if($item->subtitle)
                                <br><small class="text-danger font-weight-bold">{{ $item->subtitle }}</small>
                            @endif
                        </td>
                        <td>{{ Str::limit($item->description, 50) }}</td>
                        <td class="text-center">{{ $item->sort_order }}</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge badge-success">Hiện</span>
                            @else
                                <span class="badge badge-secondary">Ẩn</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.content-blocks.edit', $item) }}" class="btn btn-sm btn-warning"><i class="fas fa-pencil-alt"></i></a>
                            <form action="{{ route('admin.content-blocks.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa mục này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">Chưa có dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $blocks->links() }}
    </div>
</div>
@endsection
