@extends('layouts.admin')
@section('title', 'Quản lý Đơn hàng')


@section('content')
<section class="content">
    <div class="container-fluid">
        {{-- Form bao quanh để submit khi lọc --}}
        <form action="{{ route('admin.orders.index') }}" method="GET">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i> Dữ liệu đơn hàng
                    </h3>

                    {{-- CARD TOOLS: Chứa ô tìm kiếm Search Text --}}
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="search" class="form-control float-right" placeholder="Tìm tên, SĐT, mã đơn..." value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Hàng bộ lọc Dropdown & Nút Thao tác --}}
                    <div class="row mb-3">
                        <div class="col-12 mb-2">
                             <x-admin.bulk-action-bar model="order" />
                        </div>

                        {{-- 1. Lọc Trạng thái Đơn --}}
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="form-group mb-0">
                                <label class="small text-muted mb-1">Trạng thái đơn</label>
                                <select class="form-control form-control-sm" name="status" onchange="this.form.submit()">
                                    <option value="">-- Tất cả --</option>
                                    @foreach(App\Enums\OrderStatus::options() as $key => $label)
                                        <option value="{{ $key }}" {{ (string)request('status') === (string)$key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 2. Lọc Thanh toán --}}
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="form-group mb-0">
                                <label class="small text-muted mb-1">Thanh toán</label>
                                <select class="form-control form-control-sm" name="payment_status" onchange="this.form.submit()">
                                    <option value="">-- Tất cả --</option>
                                    @foreach(App\Enums\PaymentStatus::options() as $key => $label)
                                        <option value="{{ $key }}" {{ (string)request('payment_status') === (string)$key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 3. Lọc Vận chuyển --}}
                        <div class="col-md-3 col-sm-6 mb-2">
                            <div class="form-group mb-0">
                                <label class="small text-muted mb-1">Vận chuyển</label>
                                <select class="form-control form-control-sm" name="shipping_status" onchange="this.form.submit()">
                                    <option value="">-- Tất cả --</option>
                                    @foreach(App\Enums\ShippingStatus::options() as $key => $label)
                                        <option value="{{ $key }}" {{ (string)request('shipping_status') === (string)$key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 4. Các nút hành động --}}
                        <div class="col-md-3 col-sm-6 mb-2 d-flex align-items-end">
                            <div class="btn-group w-100">
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-default btn-sm" title="Reset bộ lọc">
                                    <i class="fas fa-sync-alt"></i>
                                </a>
                                <a href="{{ route('admin.orders.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tạo đơn mới
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Bảng dữ liệu --}}
                    <div class="table-responsive p-0">
                        <table class="table table-hover text-nowrap table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 10px">
                                        <input type="checkbox" id="checkAll">
                                    </th>
                                    <th style="width: 5%">Mã</th>
                                    <th style="width: 20%">Khách hàng</th>
                                    <th style="width: 15%">Tổng tiền</th>
                                    <th style="width: 15%" class="text-center">Trạng thái</th>
                                    <th style="width: 15%" class="text-center">Thanh toán & Ship</th>
                                    <th style="width: 15%">Ngày tạo</th>
                                    <th style="width: 15%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="check-item" value="{{ $order->id }}">
                                        </td>
                                        <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->code }}</a></td>
                                        <td>
                                            <div class="user-block">
                                                <span class="username ml-0 text-sm">
                                                    {{ $order->customer_name ?? $order->user->name ?? 'Khách lẻ' }}
                                                </span>
                                                <span class="description ml-0">
                                                    {{ $order->customer_phone ?? $order->user->phone ?? '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="font-weight-bold text-danger">
                                            {{ number_format($order->total_price, 0, ',', '.') }} ₫
                                        </td>
                                        
                                        <td class="text-center align-middle">
                                            {{-- Badge chuẩn Bootstrap 4 --}}
                                            <span class="badge badge-{{ $order->status->color() }}" style="font-size: 90%;">
                                                {{ $order->status->label() }}
                                            </span>
                                        </td>

                                        <td class="text-center align-middle">
                                            <div class="d-flex flex-column gap-1">
                                                <small class="badge badge-{{ $order->payment_status->color() }} font-weight-normal">
                                                    {{ $order->payment_status->label() }}
                                                </small>
                                                <small class="badge badge-{{ $order->shipping_status->color() }} font-weight-normal mt-1">
                                                    <i class="fas fa-truck"></i> {{ $order->shipping_status->label() }}
                                                </small>
                                            </div>
                                        </td>

                                        <td class="align-middle">
                                            {{ $order->created_at->format('d/m/Y') }}<br>
                                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                        </td>
                                        
                                        <td class="text-center align-middle">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-info btn-sm" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($order->status->canEdit())
        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning btn-sm" title="Sửa">
            <i class="fas fa-pencil-alt"></i>
        </a>
    @endif

                                            @if($order->status->canDelete())
        <button type="button" class="btn btn-danger btn-sm" title="Xóa" 
            onclick="confirmDelete({{ $order->id }})">
            <i class="fas fa-trash"></i>
        </button>

        <form id="delete-{{ $order->id }}" action="{{ route('admin.orders.destroy', $order) }}" method="POST" style="display: none;">
            @csrf 
            @method('DELETE')
        </form>
    @else
        {{-- (Tùy chọn) Hiển thị nút xóa bị mờ (disabled) để họ biết là có chức năng này nhưng đang bị khóa --}}
        <button class="btn btn-secondary btn-sm disabled" title="Không thể xóa đơn hàng đang xử lý hoặc đã hoàn thành" disabled>
            <i class="fas fa-trash"></i>
        </button>
    @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3"></i><br>
                                            Không tìm thấy dữ liệu phù hợp.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer clearfix">
                    <div class="float-right">
                        {{ $orders->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
@push('js')
<script>
    function confirmDelete(orderId) {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Đơn hàng #" + orderId + " sẽ bị xóa vĩnh viễn và không thể khôi phục!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Vâng, xóa nó!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-' + orderId).submit();
            }
        })
    }
</script>
@endpush
