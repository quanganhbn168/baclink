@extends('layouts.master')
@section('title', 'Thanh toán')

@section('content')
<div class="checkout">
    <h2>Thông tin khách hàng</h2>
    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        <input type="text" name="customer_name" placeholder="Họ tên *" required>
        <input type="email" name="customer_email" placeholder="Email *" required>
        <input type="text" name="customer_phone" placeholder="Số điện thoại *" required>
        <input type="text" name="customer_address" placeholder="Địa chỉ nhận hàng *" required>
        <textarea name="note" placeholder="Ghi chú đơn hàng (nếu có)"></textarea>

        <button type="submit">Đặt hàng</button>
    </form>
</div>
@endsection
