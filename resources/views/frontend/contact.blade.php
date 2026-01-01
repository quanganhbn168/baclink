@extends('layouts.master')
@section('title','Liên hệ')
@push('css')
<style>
	.map iframe{
		width: 100%!important;
	}
</style>
@endpush
@section('content')
<div id="contact">
	<div class="container">
		<x-frontend.breadcrumb :items="[
			['label' => 'Liên hệ'],
		]"/>
		<h2 class="text-center mt-3">Liên hệ</h2>
		<div class="row">
			<div class="col-12 col-md-6 col-sm-12 order-2 order-md-1">
				<h3>
					<strong>Liên hệ với {{$setting->name}}</strong>
				</h3>
				<ul>
					<li class="mt-3"><strong>Địa chỉ:</strong> {{$setting->address}}</li>
					@foreach($branches as $key => $branch)
					<li class="mt-3"> <strong>{{$branch->name}}</strong> : {{$branch->address}}</li>
					@endforeach
					<li class="mt-3">
						<a href="tel:{{ preg_replace('/\s+/', '', $setting->phone) }}">
							<strong>Điện thoại:</strong> {{ $setting->phone }}</a>
					</li>
					<li class="mt-3">
						<a href="mailto:{{ trim($setting->email) }}">
							<strong>Email:</strong> {{ $setting->email }}
						</a>
					</li>
				</ul>
			</div>
			<div class="col-12 col-md-6 col-sm-12 order-1 order-md-2">
				<div class="card text-bg-light">
					<div class="card-header">
						<div class="card-title">
							<h3 class="strong text-uppercase">Liên hệ ngay với chúng tôi</h3>
						</div>
					</div>
					<div class="card-body bg-light">
						<form id="contact-form" action="{{route('contact.store')}}" method="POST">
							@csrf
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for="name">Họ và tên *</label>
										<input type="text" class="form-control" id="name" name="name" autocomplete>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for="address">Địa chỉ</label>
										<input type="text" class="form-control" id="address" name="address" autocomplete>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for="phone">Số điện thoại *</label>
										<input type="text" class="form-control" id="phone" name="phone" autocomplete>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for="email">Email</label>
										<input type="text" class="form-control" id="email" name="email" autocomplete>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="message">Ý kiến</label>
								<textarea name="message" id="message" class="form-control" autocomplete></textarea>
							</div>
							<button class="btn btn-dark d-block w-100">Gửi ý kiến</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div class="map text-center py-4">
					{!!$setting->map!!}
				</div>
			</div>			
		</div>
	</div>
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
    // Custom rule kiểm tra số điện thoại Việt Nam
    $.validator.addMethod("phoneVN", function (value, element) {
        return this.optional(element) || /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/.test(value);
    }, "Số điện thoại không hợp lệ");
    $(document).ready(function () {
        $('#contact-form').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                phone: {
                    required: true,
                    phoneVN: true
                },
                email: {
                    email: true
                },
                message: {
                	required: true,
                    maxlength: 1000
                }
            },
            messages: {
                name: {
                    required: "Vui lòng nhập họ và tên",
                    minlength: "Tên quá ngắn"
                },
                phone: {
                    required: "Vui lòng nhập số điện thoại",
                    phoneVN: "Số điện thoại không hợp lệ (ví dụ: 098xxxxxxx)"
                },
                email: {
                    email: "Email không hợp lệ"
                },
                message: {
                	required: "Vui lòng nhập nội dung",
                    maxlength: "Ý kiến không vượt quá 1000 ký tự"
                }
            },
            errorElement: 'small',
            errorClass: 'text-danger',
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>
@endpush