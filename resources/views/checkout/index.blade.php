@extends('layouts.master')
@section('title', 'Thanh toán')
@section('content')
<div class="container py-5">
    <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form" novalidate>
        @csrf
        <div class="row">
            <div class="col-md-7">
                <h4>Thông tin giao hàng</h4>
                <hr>
                @auth('web')
                    <div class="alert alert-info">
                        Đang đặt hàng với tài khoản: <strong>{{ auth('web')->user()->name }}</strong>
                        (<a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>)
                    </div>
                @endauth
                <div class="mb-3">
                    <label for="customer_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ auth('web')->user()->name ?? old('customer_name') }}" required>
                </div>
                <div class="mb-3">
                    <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="customer_phone" name="customer_phone" value="{{ auth('web')->user()->phone ?? old('customer_phone') }}" required>
                </div>
                <div class="mb-3">
                    <label for="customer_address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="customer_address" name="customer_address" value="{{ auth('web')->user()->address ?? old('customer_address') }}" required>
                </div>
                 <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú đơn hàng (tùy chọn)</label>
                    <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title">Đơn hàng của bạn</h4>
                        <ul class="list-group list-group-flush mt-3" id="order-summary-list">
                        </ul>
                        <hr>
                        <ul class="list-group list-group-flush">
                             <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                                Tạm tính
                                <span id="summary-subtotal">0đ</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                                <div><strong>Tổng cộng</strong></div>
                                <span><strong id="summary-total">0đ</strong></span>
                            </li>
                        </ul>
                        <hr>
                        <h5>Phương thức thanh toán</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cod" checked>
                            <label class="form-check-label" for="payment_cod">
                                Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payment_bank" value="bank_transfer">
                            <label class="form-check-label" for="payment_bank">
                                Chuyển khoản ngân hàng (VietQR)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-main w-100 mt-3">ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="cart_data" id="cart_data_input">
    </form>
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
$(function () {
  const ITEMS = {!! json_encode($cartItems ?? []) !!};
  const $list = $('#order-summary-list');
  const $btn  = $('#checkout-form button[type="submit"]');

  const fmt = n => new Intl.NumberFormat('vi-VN', {style:'currency', currency:'VND'}).format(Number(n||0));

  function render(){
    $list.empty();

    if (!Array.isArray(ITEMS) || ITEMS.length === 0){
      $list.html('<li class="list-group-item">Giỏ hàng trống</li>');
      $('#summary-subtotal, #summary-total').text(fmt(0));
      $btn.prop('disabled', true).addClass('disabled');
      return;
    }

    let total = 0;
    for (const it of ITEMS){
      total += Number(it.subtotal || 0);
      $list.append(`
        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
          <div>
            ${it.name || 'Sản phẩm'}
            ${it.variant_text ? `<small class="d-block text-muted">${it.variant_text}</small>` : ``}
            <small class="d-block text-muted">SL: ${it.quantity || 1}</small>
          </div>
          <span class="item-total">${fmt(it.subtotal || 0)}</span>
        </li>
      `);
    }
    $('#summary-subtotal').text(fmt(total));
    $('#summary-total').text(fmt(total));
    $btn.prop('disabled', false).removeClass('disabled');
  }

  render();

  // validate form
  $.validator.addMethod("phoneVN", (v)=>/^(0[3|5|7|8|9])[0-9]{8}$/.test(v), "Số điện thoại không hợp lệ.");
  $('#checkout-form').validate({
    rules: {
      customer_name:    { required: true, minlength: 2 },
      customer_phone:   { required: true, phoneVN: true },
      customer_address: { required: true, minlength: 10 }
    },
    messages: {
      customer_name:    { required: "Vui lòng nhập họ tên.", minlength: "Họ tên quá ngắn." },
      customer_phone:   { required: "Vui lòng nhập số điện thoại hợp lệ." },
      customer_address: { required: "Vui lòng nhập địa chỉ.", minlength: "Địa chỉ quá ngắn." }
    },
    errorElement: 'small',
    errorClass: 'text-danger',
    highlight:   (el)=> $(el).addClass('is-invalid'),
    unhighlight: (el)=> $(el).removeClass('is-invalid')
  });
});
</script>
@endpush
