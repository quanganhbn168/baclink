@section('content')
@extends('layouts.master')
@section('title',$category->name)
@push('css')
<link rel="stylesheet" href="{{asset('css/product.css')}}">
@endpush
@section('content')
	<div id="category-wrapper" class="bg-light">
		<div class="category-banner">
			<img src="{{ asset($category->banner) }}" alt="{{ $category->title }}" width="1920" height="300">
			<div class="category-banner_overlay"></div>
			<div class="category-banner_info">
				<h1 class="category-banner_title">{{ $category->name }}</h1>
				<div class="category-banner_meta">
					<i class="fas fa-user"></i> Admin
					<span class="dot">•</span>
					<i class="fas fa-clock"></i> {{ $category->updated_at->format('d/m/Y') }}
				</div>
			</div>
		</div>
		<div class="breadcrumb-wrapper bg-white">
			<div class="container">
				<ul class="breadcrumb">
					<li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
					<li class="breadcrumb-item active">{{ $category->name }}</li>
				</ul>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-12 col-md-9 bg-white">
					<h1 class="text-primary">{{$category->name}}</h1>
					<div class="description">
						{!!$category->content!!}
					</div>
					<div class="services-wrapper">
						<h3 class="services-title text-center">Các dịch vụ</h3>
						<div class="row services-list">
							@foreach($services as $key => $service)
							<div class="col-md-4 col-sm-6 mb-4">
								<div class="services-list_item">
									<a href="{{route('slug.resolve',$service->slug)}}">
										<div class="item-image">
											<img src="{{ asset($service->image) }}" alt="{{ $service->name }}">
										</div>
										<div class="item-description">
											{{ $service->name }}
										</div>
									</a>
								</div>
							</div>
							@endforeach
						</div>
						<h3 class="services-title text-center mt-4">Bảng giá</h3>
						
							<h3>{{$category->name}}</h3>
							<div class="banggia">
							    <table class="table table-bordered banggia-table">
							        <thead class="thead-primary bg-primary text-white">
							            <tr>
							                <th scope="col">STT</th>
							                <th scope="col">Tên dịch vụ</th>
							                <th scope="col">Đơn vị tính</th>
							                <th scope="col">Giá tiền</th>
							            </tr>
							        </thead>
							        <tbody>
							            @foreach($services as $serviceKey => $child)
							            <tr>
							                <td>{{ $serviceKey + 1 }}</td>
							                <td>
							                    <a href="{{route('slug.resolve',$child->slug)}}">
							                        {{ $child->name }}
							                    </a>
							                </td>
							                <td>{{ $child->unit->name ?? '' }}</td>
							                <td>{{ number_format($child->price, 0, ',', '.') }}₫</td>
							            </tr>
							            @endforeach
							        </tbody>
							    </table>
							</div>          
							
					</div>
		
					<div class="social-share">
						<span class="social-share_label">Chia sẻ:</span>
						<a href="#" class="social-share_item facebook" title="Chia sẻ Facebook"><i class="fab fa-facebook-f"></i></a>
						<a href="#" class="social-share_item twitter" title="Chia sẻ Twitter"><i class="fab fa-twitter"></i></a>
						<a href="#" class="social-share_item linkedin" title="Chia sẻ LinkedIn"><i class="fab fa-linkedin-in"></i></a>
					</div>

				</div>
				@include('partials.frontend.aside')
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
                        message: {
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
