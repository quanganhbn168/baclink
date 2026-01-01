@extends('layouts.admin')
@section('title', 'Cập nhật đơn hàng #' . $order->code)

@section('content')
<div class="container-fluid">
    {{-- Form Submit --}}
    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            {{-- CỘT TRÁI: THÔNG TIN VÀ SẢN PHẨM --}}
            <div class="col-md-8">
                {{-- 1. Thông tin giao hàng --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-user-edit"></i> Thông tin giao hàng</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="customer_name">Tên khách hàng <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" 
                                       class="form-control @error('customer_name') is-invalid @enderror"
                                       value="{{ old('customer_name', $order->customer_name ?? $order->user->name) }}">
                                @error('customer_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="customer_phone">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" id="customer_phone" 
                                       class="form-control @error('customer_phone') is-invalid @enderror"
                                       value="{{ old('customer_phone', $order->customer_phone ?? $order->user->phone) }}">
                                @error('customer_phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-md-12 form-group">
                                <label for="customer_address">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                <input type="text" name="customer_address" id="customer_address" 
                                       class="form-control @error('customer_address') is-invalid @enderror"
                                       value="{{ old('customer_address', $order->customer_address) }}">
                                @error('customer_address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="note">Ghi chú đơn hàng</label>
                                <textarea name="note" id="note" rows="3" class="form-control" placeholder="Ghi chú của khách hàng hoặc Admin...">{{ old('note', $order->note) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- 2. Danh sách sản phẩm (Read-only view) --}}
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Sản phẩm trong đơn (Chỉ xem)</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-right">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold">{{ $item->product_name }}</span>
                                        @if($item->product_variant_id && $item->productVariant)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Phân loại: {{ $item->productVariant->variant_name ?? ($item->productVariant->size ?? '') . ' - ' . ($item->productVariant->color ?? '') }}
                                            </small>
                                        @endif
                                        @if(!$item->product)
                                            <br><span class="badge badge-danger">Đã ngừng kinh doanh</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">{{ $item->quantity }}</td>
                                    <td class="text-right align-middle">{{ number_format($item->product_price, 0, ',', '.') }} ₫</td>
                                    <td class="text-right align-middle font-weight-bold">{{ number_format($item->subtotal, 0, ',', '.') }} ₫</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 3. Tổng kết tài chính (PHẦN BỔ SUNG MỚI) --}}
                <div class="card card-outline card-info mt-3">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Tổng kết đơn hàng</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table">
                            <tr>
                                <td style="width:70%" class="text-right">Tạm tính (Subtotal):</td>
                                <td class="text-right font-weight-bold">{{ number_format($order->subtotal, 0, ',', '.') }} ₫</td>
                            </tr>
                            
                            {{-- Hiển thị chiết khấu nếu có --}}
                            @if($order->discount_amount > 0)
                                <tr>
                                    <td class="text-right text-info">
                                        Chiết khấu đại lý ({{ $order->discount_rate }}%):
                                    </td>
                                    <td class="text-right text-info font-weight-bold">
                                        -{{ number_format($order->discount_amount, 0, ',', '.') }} ₫
                                    </td>
                                </tr>
                            @endif

                            <tr class="bg-light">
                                <td class="text-right text-danger font-weight-bold" style="font-size: 1.1rem;">TỔNG THANH TOÁN:</td>
                                <td class="text-right text-danger font-weight-bold" style="font-size: 1.1rem;">
                                    {{ number_format($order->total_price, 0, ',', '.') }} ₫
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>

            {{-- CỘT PHẢI: TRẠNG THÁI --}}
            <div class="col-md-4">
                <div class="card card-warning card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-edit"></i> Cập nhật trạng thái</h3>
                    </div>
                    <div class="card-body">
                        
                        {{-- 1. Trạng thái Đơn hàng --}}
                        <div class="form-group">
                            <label>Trạng thái đơn hàng</label>
                            <select name="status" class="form-control">
                                @foreach(App\Enums\OrderStatus::options() as $key => $label)
                                    <option value="{{ $key }}" {{ $order->status->value === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Trạng thái Thanh toán --}}
                        <div class="form-group">
                            <label>Trạng thái thanh toán</label>
                            <select name="payment_status" class="form-control">
                                @foreach(App\Enums\PaymentStatus::options() as $key => $label)
                                    <option value="{{ $key }}" {{ $order->payment_status->value === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. Trạng thái Vận chuyển --}}
                        <div class="form-group">
                            <label>Trạng thái vận chuyển</label>
                            <select name="shipping_status" class="form-control">
                                @foreach(App\Enums\ShippingStatus::options() as $key => $label)
                                    <option value="{{ $key }}" {{ $order->shipping_status->value === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <hr>
                        <div class="callout callout-info text-sm">
                            <p><i class="fas fa-info-circle"></i> Chỉ sửa được trạng thái khi đơn chưa hoàn thành.</p>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-default w-100 mt-2">Hủy bỏ</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection