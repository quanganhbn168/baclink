@extends('layouts.admin')

@section('title', $pageTitle)
@section('content_header_title', $pageTitle)

@section('content')
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <a href="{{ route('admin.agents.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Thêm Đại lý
            </a>
        </h3>
        <div class="card-tools">
            <form action="{{ route('admin.agents.index') }}" method="GET">
                <div class="input-group input-group-sm" style="width: 300px;">
                    <input type="text" name="keyword" class="form-control float-right" 
                           placeholder="Tên, Email, SĐT, Công ty..." 
                           value="{{ request('keyword') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped text-nowrap">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th>Đại lý / Liên hệ</th>
                    <th>Thông tin Công ty</th>
                    <th>Tài chính</th>
                    <th class="text-right">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        <div class="user-block">
                            {{-- Avatar ngẫu nhiên theo tên --}}
                            <img class="img-circle img-bordered-sm" src="https://ui-avatars.com/api/?name={{ urlencode($item->name) }}&background=random" alt="User Image">
                            <span class="username">
                                <a href="{{ route('admin.agents.show', $item->id) }}">{{ $item->name }}</a>
                            </span>
                            <span class="description">
                                <i class="fas fa-phone-alt text-xs"></i> {{ $item->phone }} <br>
                                <i class="fas fa-envelope text-xs"></i> {{ $item->email }}
                            </span>
                        </div>
                    </td>
                    <td>
                        <strong class="text-primary">{{ $item->dealerProfile->company_name ?? '---' }}</strong><br>
                        <small>{{ $item->dealerProfile->address ?? '' }}</small>
                    </td>
                    <td>
                        <div>Số dư: <b class="text-success">{{ number_format($item->dealerProfile->wallet_balance ?? 0) }} đ</b></div>
                        <div>Chiết khấu: <span class="badge badge-warning">{{ $item->dealerProfile->discount_rate ?? 0 }}%</span></div>
                    </td>
                    <td class="text-right">
                        <div class="btn-group">
                            <a href="{{ route('admin.agents.show', $item->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết & Nạp tiền">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.agents.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Sửa thông tin">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.agents.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa đại lý này? Dữ liệu không thể khôi phục!');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu đại lý nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $agents->withQueryString()->links() }}
    </div>
</div>
@endsection
