@extends('layouts.master')
@section('title', 'Thông tin bảo hành')
@push('head')
<meta name="robots" content="noindex,nofollow">
@endpush

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-0">Mã đơn: <strong>{{ $order->code }}</strong></h5>
            <small class="text-muted">Ngày lắp đặt: {{ \Carbon\Carbon::parse($order->installed_at)->format('d/m/Y') }}</small>
          </div>
          <img src="{{ route('warranty.code.qr', $order->code) }}" alt="QR" width="64" height="64">
        </div>
        <div class="card-body">
          <ul class="list-group">
            @foreach($order->orderItems as $item)
              <li class="list-group-item">
                <div class="fw-bold">
                  {{ $item->product->name ?? $item->product_name }}
                  <span class="text-muted">× {{ $item->quantity }}</span>
                </div>
                <div class="small text-muted">
                  @if($item->warranty_expires_at)
                    Hạn bảo hành: {{ \Carbon\Carbon::parse($item->warranty_expires_at)->format('d/m/Y') }}
                    ({{ $item->warranty_remaining_text }})
                  @else
                    Không áp dụng bảo hành
                  @endif
                  @if($item->warranty_months)
                    — Thời hạn: {{ $item->warranty_months }} tháng
                  @endif
                </div>
              </li>
            @endforeach
          </ul>

          @if($order->technician)
            <div class="mt-3 small text-muted">Thợ phụ trách: {{ $order->technician->name }}</div>
          @endif

          <div class="text-center mt-3">
            <a href="{{ route('order.tracking', ['code' => $order->code]) }}" class="btn btn-outline-primary">
              Xem trên trang tra cứu
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
