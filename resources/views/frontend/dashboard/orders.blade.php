@extends('frontend.dashboard.layout')

@section('title', 'Lịch sử đơn hàng')

@section('dashboard_content')
    <h3 class="mb-4">Lịch sử đơn hàng</h3>
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
                @forelse ($orders as $order)
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
                                Xem chi tiết
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
    
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links() }}
    </div>
@endsection