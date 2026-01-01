@extends('frontend.dashboard.layout')

@section('title', 'Chi tiết đơn hàng #' . $order->code)

@section('dashboard_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Chi tiết đơn hàng #{{ $order->code }}</h3>
        <a href="{{ route('user.orders') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h5>Thông tin đơn hàng</h5>
            <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>Trạng thái:</strong> <span class="badge badge-{{ $order->status->color() }}" style="font-size: 0.9rem;">{{ $order->status->label() }}</span></p>
            <p><strong>Tổng tiền:</strong> <strong class="text-danger">{{ number_format($order->total_price) }}đ</strong></p>
        </div>
        <div class="col-md-6">
            <h5>Thông tin giao hàng</h5>
            <p><strong>Người nhận:</strong> {{ $order->customer_name }}</p>
            <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
            <p><strong>Địa chỉ:</strong> {{ $order->customer_address }}</p>
        </div>
    </div>

    <hr>
    <h4 class="mt-4 mb-3">Các sản phẩm trong đơn</h4>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderItems as $item)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ optional($item->product->mainImage())->url() }}" alt="{{ $item->product->name }}" style="width: 80px; height: 80px; object-fit: cover; margin-right: 15px;">
                            <div>
                                <a href="{{ route('frontend.product.show', $item->product->slugValue) }}" class="fw-bold">{{ $item->product->name }}</a>
                            </div>
                        </div>
                    </td>
                    <td class="text-end">{{ number_format($item->product_price) }}đ</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end fw-bold">{{ number_format($item->product_price * $item->quantity) }}đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection