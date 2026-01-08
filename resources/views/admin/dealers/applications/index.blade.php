@extends('layouts.admin')

@section('title', 'Danh sách Đăng ký Đại lý')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Quản lý Đăng ký Đại lý</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Đăng ký Đại lý</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        
        {{-- Thông báo thành công --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-check"></i> Thông báo!</h5>
                {{ session('success') }}
            </div>
        @endif

        {{-- Card Tìm kiếm & Lọc --}}
        <div class="card card-outline card-info collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter"></i> Bộ lọc tìm kiếm</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body" style="display: none;"> {{-- Mặc định ẩn cho gọn, bấm dấu + để hiện --}}
                <form action="{{ route('admin.dealer-applications.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <label>Từ khóa</label>
                            <input type="text" name="keyword" class="form-control" 
                                   placeholder="Tên, SĐT, Email..." 
                                   value="{{ request('keyword') }}">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label>Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="">-- Tất cả --</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đã duyệt</option>
                                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Tìm kiếm
                            </button>
                        </div>
                        <div class="col-md-2 mb-2 d-flex align-items-end">
                             <a href="{{ route('admin.dealer-applications.index') }}" class="btn btn-default w-100">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Card Danh sách --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Danh sách yêu cầu</h3>
            </div>
            
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 25%">Thông tin khách hàng</th>
                            <th style="width: 25%">Công ty / Địa chỉ</th>
                            <th style="width: 25%">Lời nhắn</th>
                            <th style="width: 10%" class="text-center">Trạng thái</th>
                            <th style="width: 10%" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <div class="user-block">
                                    <span class="username" style="margin-left: 0px !important;">
                                        <a href="#">{{ $item->name }}</a>
                                    </span>
                                    <span class="description" style="margin-left: 0px !important;">
                                        <i class="fas fa-phone-alt"></i> {{ $item->phone }} <br>
                                        <i class="fas fa-envelope"></i> {{ $item->email }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $item->company ?? '---' }}</strong><br>
                                <span class="text-muted text-wrap" style="font-size: 0.9em; display: block; max-width: 250px; white-space: normal;">
                                    {{ $item->address }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted text-wrap" style="font-size: 0.9em; display: block; max-width: 300px; white-space: normal;">
                                    {{ Str::limit($item->message, 80) }}
                                </span>
                                <small class="badge badge-light mt-1 border">
                                    <i class="far fa-clock"></i> {{ $item->created_at->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td class="text-center align-middle">
                                @if($item->status == 0)
                                    <span class="badge badge-warning">Chờ xử lý</span>
                                @elseif($item->status == 1)
                                    <span class="badge badge-success">Đã duyệt</span>
                                @else
                                    <span class="badge badge-danger">Đã hủy</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <a href="{{ route('admin.dealer-applications.edit', $item->id) }}" class="btn btn-info btn-sm" title="Chi tiết/Sửa">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- Chỉ hiện nút Duyệt/Hủy nếu đang chờ xử lý --}}
                                    @if($item->status == 0)
                                        <button type="button" class="btn btn-success btn-sm" 
                                                onclick="if(confirm('Duyệt hồ sơ này?')) { document.getElementById('approve-form-{{$item->id}}').submit(); }" 
                                                title="Duyệt">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <form id="approve-form-{{$item->id}}" action="{{ route('admin.dealer-applications.status', $item->id) }}" method="POST" style="display: none;">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="1">
                                        </form>
                                    @endif

                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="if(confirm('Xóa vĩnh viễn hồ sơ này?')) { document.getElementById('delete-form-{{$item->id}}').submit(); }" 
                                            title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{$item->id}}" action="{{ route('admin.dealer-applications.destroy', $item->id) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                Không tìm thấy dữ liệu nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $applications->withQueryString()->links() }} {{-- Nếu dùng Bootstrap 4 pagination thì thêm 'pagination::bootstrap-4' --}}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
