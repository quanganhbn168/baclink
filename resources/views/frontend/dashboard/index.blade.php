@extends('frontend.dashboard.layout')

@section('title', 'Bảng điều khiển')

@section('dashboard_content')
    <h3 class="mb-4">Bảng điều khiển</h3>
    <p>Xin chào, <strong>{{ Auth::user()->name }}</strong>!</p>
    <p>Từ trang quản lý này, bạn có thể xem các đơn hàng gần đây, quản lý thông tin cá nhân và nhiều hơn nữa.</p>

    <hr>

    <h4 class="mt-4 mb-3">Đơn hàng gần đây</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->code }}</td>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        <td>{{ number_format($order->total_price) }}đ</td>
                        <td>
                            <span class="badge badge-{{ $order->status->color() }}" style="font-size: 0.9rem;">
        {{ $order->status->label() }}
    </span>
                        </td>
                        <td>
                            <a href="{{ route('user.order.detail', $order->id) }}" class="btn btn-sm btn-primary">
                                Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Bạn chưa có đơn hàng nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection