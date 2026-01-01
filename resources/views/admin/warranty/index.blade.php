@extends('layouts.admin')

@section('title', 'Tra cứu Bảo hành')

@section('content')
    {{-- PHẦN 1: FORM TÌM KIẾM (Giữ nguyên) --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Tra cứu thông tin bảo hành theo Mã Đơn hàng</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.warranty.search') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-6">
                    <label for="order_code" class="form-label">Nhập mã đơn hàng (ví dụ: TP-250811-0001)</label>
                    <input type="text" class="form-control" id="order_code" name="order_code" value="{{ $order->code ?? old('order_code') }}" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tra cứu
                    </button>
                </div>
            </form>

            @if (session('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    {{-- PHẦN 2: HIỂN THỊ KẾT QUẢ (Đã cập nhật logic) --}}
    @if (isset($order))
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Kết quả cho Đơn hàng: {{ $order->code }}</h5>
        </div>
        <div class="card-body">
            {{-- Thông tin chung --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Khách hàng của đơn:</strong> {{ $order->customer_name ?? $order->user->name ?? 'N/A' }}<br>
                    <strong>Điện thoại:</strong> {{ $order->customer_phone ?? $order->user->phone ?? 'N/A' }}
                </div>
                <div class="col-md-6">
                    {{-- Hiển thị thông tin thợ nếu có và khác với khách hàng --}}
                    @if($order->technician)
                        <strong>Thợ lắp đặt được gán:</strong> {{ $order->technician->name }}<br>
                    @endif
                    <strong>Ngày tạo đơn (Bắt đầu BH):</strong> {{ $order->created_at->format('d/m/Y') }}
                </div>
            </div>

            {{-- Bảng chi tiết sản phẩm và bảo hành --}}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Thời hạn BH</th>
                            <th>Ngày hết hạn (Dự kiến)</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>{{ $item->warranty_months }} tháng</td>
                                <td>
                                    {{-- TÍNH BẢO HÀNH DỰA TRÊN NGÀY TẠO ĐƠN HÀNG --}}
                                    @if($item->warranty_months > 0)
                                        @php
                                            $expiryDate = $order->created_at->copy()->addMonths($item->warranty_months);
                                        @endphp
                                        {{ $expiryDate->format('d/m/Y') }}
                                    @else
                                        Không áp dụng
                                    @endif
                                </td>
                                <td>
                                    @if(isset($expiryDate) && now()->isBefore($expiryDate))
                                        <span class="badge bg-success">Còn bảo hành</span>
                                    @elseif($item->warranty_months > 0)
                                        <span class="badge bg-danger">Hết bảo hành</span>
                                    @else
                                        <span class="badge bg-secondary">Không có</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

@endsection