@extends('layouts.admin')
@section('title', 'Chi tiết đơn hàng #' . $order->code)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <img src="{{ asset($setting->logo) }}" alt="{{ $setting->name }}" height="50" width="auto">
                    </div>
                </div>
                
                <div class="row invoice-info mt-4">
                    {{-- Cột 1: Người gửi (Lấy từ biến $setting) --}}
                    <div class="col-sm-4 invoice-col">
                        Người gửi
                        <address>
                            <strong>{{ $setting->name ?? 'Ekokemika Việt Nam' }}</strong><br>
                            {{ $setting->address ?? 'Số 123 Đường ABC, Hà Nội' }}<br>
                            Phone: {{ $setting->phone ?? '(84) 123-456-7890' }}<br>
                            Email: {{ $setting->email ?? 'info@ekokemika.com' }}
                        </address>
                    </div>
                    
                    {{-- Cột 2: Người nhận --}}
                    <div class="col-sm-4 invoice-col">
                        Người nhận
                        <address>
                            <strong>{{ $order->customer_name }}</strong><br>
                            {{ $order->customer_address ?? 'Nhận tại cửa hàng' }}<br>
                            Phone: {{ $order->customer_phone }}<br>
                            @if($order->user)
                                Email: {{ $order->user->email }} <br>
                                {{-- Hiển thị thẻ Đại lý nếu có --}}
                                @if($order->discount_rate > 0)
                                    <span class="badge badge-info mt-1">Đại lý (Chiết khấu {{ $order->discount_rate }}%)</span>
                                @endif
                            @endif
                        </address>
                    </div>
                    
                    {{-- Cột 3: Thông tin đơn --}}
                    <div class="col-sm-4 invoice-col">
                        <b>Mã đơn: #{{ $order->code }}</b><br>
                        <br>
                        <b>Trạng thái:</b> <span class="badge badge-{{ $order->status->color() }}">{{ $order->status->label() }}</span><br>
                        <b>Thanh toán:</b> <span class="badge badge-{{ $order->payment_status->color() }}">{{ $order->payment_status->label() }}</span><br>
                        <b>Vận chuyển:</b> <span class="badge badge-{{ $order->shipping_status->color() }}">{{ $order->shipping_status->label() }}</span>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="font-weight-bold">{{ $item->product_name }}</span>
                                        
                                        {{-- Hiển thị biến thể nếu có --}}
                                        @if($item->product_variant_id && $item->productVariant)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Phân loại: {{ $item->productVariant->variant_name ?? ($item->productVariant->size . ' - ' . $item->productVariant->color) }}
                                            </small>
                                        @endif

                                        @if(!$item->product)
                                            <br><span class="badge badge-danger">Sản phẩm đã xóa</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right">{{ number_format($item->product_price, 0, ',', '.') }} ₫</td>
                                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    {{-- Cột Ghi chú --}}
                    <div class="col-6">
                        <p class="lead">Ghi chú:</p>
                        <p class="text-muted well well-sm shadow-none" style="margin-top: 10px; background: #f9f9f9; padding: 10px; border-radius: 5px;">
                            {{ $order->note ?? 'Không có ghi chú.' }}
                        </p>
                    </div>
                    
                    {{-- Cột Tổng tiền (Đã thêm phần Chiết khấu) --}}
                    <div class="col-6">
                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th style="width:50%">Tạm tính (Subtotal):</th>
                                    <td class="text-right font-weight-bold">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</td>
                                </tr>
                                
                                {{-- Chỉ hiển thị dòng chiết khấu nếu có giảm giá --}}
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <th class="text-info">
                                            Chiết khấu đại lý ({{ $order->discount_rate }}%):
                                        </th>
                                        <td class="text-right text-info font-weight-bold">
                                            -{{ number_format($order->discount_amount, 0, ',', '.') }} ₫
                                        </td>
                                    </tr>
                                @endif

                                {{-- Nếu có phí ship thì thêm vào đây (hiện tại em đang hardcode là 0) --}}
                                {{-- <tr>
                                    <th>Phí vận chuyển:</th>
                                    <td class="text-right">0 ₫</td>
                                </tr> --}}

                                <tr class="border-top">
                                    <th>Tổng thanh toán:</th>
                                    <td class="text-right">
                                        <h4 class="text-danger font-weight-bold mb-0">
                                            {{ number_format($order->total_price, 0, ',', '.') }} ₫
                                        </h4>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="row no-print mt-4">
                    <div class="col-12">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        
                        @if(method_exists($order->status, 'canEdit') && $order->status->canEdit())
                            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-warning float-right" style="margin-right: 5px;">
                                <i class="fas fa-pencil-alt"></i> Sửa đơn hàng
                            </a>
                        @endif
                        
                        <button type="button" class="btn btn-primary float-right mr-2" onclick="window.print()">
                            <i class="fas fa-print"></i> In hóa đơn
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection