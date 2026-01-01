@extends('layouts.master')
@section('title', 'Tra cứu thông tin bảo hành')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center mb-0">TRA CỨU THÔNG TIN BẢO HÀNH</h3>
                </div>
                <div class="card-body">
                    <p class="text-center text-muted">
                        Tra cứu bằng <strong>Số điện thoại</strong> hoặc <strong>Mã đơn</strong> (ví dụ: TP-250813-0001).
                    </p>

                    {{-- Form tìm kiếm --}}
                    <form action="{{ route('order.tracking') }}" method="GET" class="row g-2 mb-4">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="phone"
                                   placeholder="Nhập số điện thoại..."
                                   value="{{ $phone_searched ?? '' }}">
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="code"
                                   placeholder="Nhập mã đơn (bỏ dấu #)..."
                                   value="{{ $code_searched ?? '' }}">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button class="btn btn-primary" type="submit">Tra cứu</button>
                        </div>
                    </form>

                    <hr>

                    @php
                        $hasQuery = (isset($phone_searched) && $phone_searched !== '') || (isset($code_searched) && $code_searched !== '');
                    @endphp

                    @if($hasQuery)
                        @if(isset($orders) && $orders->count() > 0)
                            @if(isset($user) && ($phone_searched ?? '') !== '')
                                <p>Khách hàng: <strong>{{ $user->name }}</strong> — SĐT: <strong>{{ $phone_searched }}</strong></p>
                            @endif

                            @foreach($orders as $order)
                                @php $cid = 'order-'.$order->id; @endphp
                                <div class="card mb-3">
                                    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center">
                                        <div class="mb-2 mb-md-0">
                                            <span class="me-3">Mã đơn: <strong>{{ $order->code }}</strong></span>
                                            <span class="me-3">
                                                Ngày lắp đặt: {{ $order->installed_date_for_view }}
                                            </span>
                                            <span>Trạng thái: <span class="badge bg-success text-white">{{ $order->status }}</span></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ route('warranty.code.qr', $order->code) }}" alt="QR" width="48" height="48" class="mr-2">
                                            {{-- BS4: dùng data-toggle/data-target --}}
                                            <button
                                                class="btn btn-sm btn-outline-primary"
                                                type="button"
                                                data-toggle="collapse"
                                                data-target="#{{ $cid }}"
                                                aria-expanded="false"
                                                aria-controls="{{ $cid }}"
                                            >
                                                Xem chi tiết
                                            </button>
                                        </div>
                                    </div>

                                    <div id="{{ $cid }}" class="collapse">
                                        <div class="card-body">
                                            <h6 class="mb-3">Sản phẩm trong đơn</h6>
                                            <ul class="list-group">
                                                @forelse($order->orderItems as $item)
                                                    <li class="list-group-item">
                                                        <div class="fw-bold">
                                                            {{ $item->product->name ?? $item->product_name }}
                                                            <span class="text-muted">× {{ $item->quantity }}</span>
                                                        </div>
                                                        <div class="small text-muted">
                                                            @if($item->warranty_expires_at)
                                                                Ngày hết hạn: {{ $item->warranty_expires_at_for_view }}
                                                                ({{ $item->warranty_remaining_text }})
                                                            @else
                                                                Không áp dụng bảo hành
                                                            @endif
                                                            @if($item->warranty_months)
                                                                — Thời hạn: {{ $item->warranty_months }} tháng
                                                            @endif
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="list-group-item text-muted">Đơn hàng chưa có sản phẩm.</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-danger text-center">
                                Không tìm thấy đơn hàng phù hợp với thông tin đã nhập.
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
