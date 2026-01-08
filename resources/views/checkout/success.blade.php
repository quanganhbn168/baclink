@extends('layouts.master')
@section('title', 'Đặt hàng thành công')
@section('google_tag')
<!-- Google tag (gtag.js) event -->
<script>
  gtag('event', 'conversion_event_purchase', {
    // <event_parameters>
  });
</script>
@endsection
@section('content')

<div class="container py-5 text-center">
    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
    <h2>Đặt hàng thành công!</h2>
    <p>Cảm ơn bạn đã mua hàng. Mã đơn hàng của bạn là <strong>#{{ $order->code }}</strong>.</p>
    <p>Chúng tôi sẽ liên hệ với bạn để xác nhận đơn hàng trong thời gian sớm nhất.</p>

    {{-- Hiển thị QR Code nếu là chuyển khoản --}}
    @if($order->payment_method == 'bank_transfer')
        @php
            $setting = \App\Models\Setting::first();
            $bankId = $setting->bank_name ?? '';
            $accountNo = $setting->bank_account_no ?? '';
            $accountName = $setting->bank_account_name ?? '';
            $template = $setting->bank_qr_template ?? 'compact';
            $amount = $order->total_price;
            $note = "NLMT " . $order->code; 
        @endphp

        @if($bankId && $accountNo)
            <div class="mt-5 p-4 border rounded" style="max-width: 450px; margin: auto;">
                @php
                    $qrCodeUrl = "https://api.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo=" . urlencode($note) . "&accountName=" . urlencode($accountName);
                @endphp
                <h4>Quét mã QR để thanh toán</h4>
                <p class="text-muted">Ngân hàng: <strong>{{ $bankId }}</strong> - STK: <strong>{{ $accountNo }}</strong></p>
                @if($accountName)
                    <p class="text-muted">Chủ TK: <strong>{{ $accountName }}</strong></p>
                @endif
                <p class="mb-1"><strong>Nội dung:</strong> <span class="text-danger">{{ $note }}</span></p>
                <p><strong>Số tiền:</strong> <span class="text-danger">{{ number_format($amount) }}đ</span></p>
                <img src="{{ $qrCodeUrl }}" alt="Mã QR thanh toán" class="img-fluid">
            </div>
        @endif
    @endif

    <a href="/" class="btn bg-main mt-4">Tiếp tục mua sắm</a>
</div>

@endsection
