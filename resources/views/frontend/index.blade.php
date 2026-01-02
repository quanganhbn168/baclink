@extends('layouts.master')
@section('title','Trang chủ - '.$setting->name)
@section('meta_description',$setting->meta_description)
@section('meta_keywords',$setting->meta_keywords)
@section('meta_image',$setting->meta_image ? asset($setting->meta_image) : asset($setting->logo))
@push('css')
<link rel="stylesheet" href="{{ asset('css/counter.css') }}">
@endpush
@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "{{ url('/') }}#organization",
      "name": "{{ $setting->name }}", 
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ asset($setting->logo) }}", 
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "{{ $setting->phone}}", 
        "contactType": "customer service",
        "areaServed": "VN",
        "availableLanguage": "Vietnamese"
      },
      "sameAs": [
        "{{$setting->mess}}",
        "{{$setting->youtube}}",
        "{{$setting->zalo}}"
      ]
    },
    {
      "@type": "WebSite",
      "@id": "{{ url('/') }}#website",
      "url": "{{ url('/') }}",
      "name": "{{ $setting->name }}", 
      "publisher": {
        "@id": "{{ url('/') }}#organization"
      },
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ url('/search') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
  ]
}
</script>
@endpush
@section("content")
<div id="hero">
    @include("partials.frontend.hero")
</div>

<section class="section product-section py-5">
    <div class="container container-custom">
        <h2 class="section-title"><a href="{{ route('products.index') }}">SẢN PHẨM CHỦ LỰC</a></h2>
        <div class="swiper product-slider mt-4">
            <div class="swiper-wrapper">
                @foreach($homeProducts as $product)
                    <div class="swiper-slide">
                        @include('partials.frontend.product_item', ['product' => $product, 'hide_info' => true])
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination mt-4"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
    </div>
</section>

{{-- Marquee Divider (Single Row) --}}
<div class="partner-marquee-divider py-4 bg-white">
    <div class="container container-custom">
        <div class="swiper partner-marquee-single">
            <div class="swiper-wrapper">
                @foreach($brands as $brand)
                    <div class="swiper-slide">
                        <div class="partner-item-simple">
                            <img src="{{ optional($brand->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $brand->name }}">
                        </div>
                    </div>
                @endforeach
                {{-- Loop again for smooth marquee --}}
                @foreach($brands as $brand)
                    <div class="swiper-slide">
                        <div class="partner-item-simple">
                            <img src="{{ optional($brand->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $brand->name }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Banner 1 --}}
<div class="section-banner py-4">
    <div class="container container-custom text-center">
        <img src="{{ asset('images/setting/danh-cho-quang-cao.png') }}" 
             class="img-fluid w-100" 
             style="max-height: 250px; object-fit: cover;" 
             alt="Quảng cáo">
    </div>
</div>

<section class="section news-section py-5 bg-light-gray">
    <div class="container container-custom">
        <h2 class="section-title"><a href="{{ route('frontend.posts.index') }}">HOẠT ĐỘNG CỦA BACLINK</a></h2>
        <div class="row">
            @foreach($homePosts as $post)
                <div class="col-12 col-md-4 mb-4">
                    @include("partials.frontend.post_item", ["post" => $post])
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Banner 2 --}}
<div class="section-banner py-4">
    <div class="container container-custom text-center">
        <img src="{{ asset('images/setting/danh-cho-quang-cao.png') }}" 
             class="img-fluid w-100" 
             style="max-height: 250px; object-fit: cover;" 
             alt="Quảng cáo">
    </div>
</div>

<section class="section member-section py-5 bg-white">
    <div class="container container-custom">
        <h2 class="section-title"><a href="{{ route('frontend.members.index') }}">HỘI VIÊN BACLINK</a></h2>
        <div class="row align-items-center mt-5">
            <!-- Left: Industry Wheel -->
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="industry-wheel-container">
                    <div class="industry-wheel">
                        <div class="wheel-center">
                            <img src="{{ asset('images/setting/logo-t.png') }}" alt="Baclink">
                        </div>
                        <div class="wheel-items">
                            @foreach($industries as $index => $industry)
                                <div class="wheel-item industry-{{ $index + 1 }}" style="--index: {{ $index }}">
                                    <div class="wheel-item-inner">
                                        <div class="wheel-icon">
                                            <i class="fas {{ $industry['icon'] }}"></i>
                                        </div>
                                        <span class="industry-name">{{ $industry['name'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Member List -->
            <div class="col-lg-6">
                <div class="featured-member-list">
                    @foreach($members as $member)
                        <div class="featured-member-item d-flex align-items-center p-3 mb-3">
                            <div class="member-logo me-4">
                                <img src="{{ $member->avatar ?? asset('images/setting/no-image.png') }}" alt="{{ optional($member->dealerProfile)->company_name }}">
                            </div>
                            <div class="member-details">
                                <h5 class="company-name mb-1">{{ optional($member->dealerProfile)->company_name ?? 'Công ty Hội viên' }}</h5>
                                <p class="representative-info mb-0">
                                    <span class="rep-name font-weight-bold">{{ $member->name }}</span>
                                    <span class="mx-2">|</span>
                                    <span class="rep-role text-muted small">Phó Tổng giám đốc</span>
                                </p>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('frontend.members.index') }}" class="btn btn-red px-5 rounded-px">Tìm hiểu thêm ></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Banner 3 --}}
<div class="section-banner py-4">
    <div class="container container-custom text-center">
        <img src="{{ asset('images/setting/danh-cho-quang-cao.png') }}" 
             class="img-fluid w-100" 
             style="max-height: 250px; object-fit: cover;" 
             alt="Quảng cáo">
    </div>
</div>

<section class="section exhibition-section py-5 bg-dark text-white" style="background: var(--bg-dark);">
    <div class="container container-custom text-center">
        <h2 class="section-title text-white">TRIỂN LÃM BACLINK</h2>
        <div class="py-5">
            <p class="fs-4 italic text-gold">Sắp ra mắt - Không gian trưng bày sản phẩm trực tuyến hàng đầu.</p>
        </div>
    </div>
</section>

<section class="section partner-section py-5 bg-light">
    <div class="container container-custom">
        <h2 class="section-title">ĐỐI TÁC BACLINK</h2>
        
        <!-- Logo Marquee (Single Row) -->
        <div class="swiper partner-marquee-single mt-5 mb-5">
            <div class="swiper-wrapper">
                @foreach($brands as $brand)
                    <div class="swiper-slide">
                        <div class="partner-item-simple">
                            <img src="{{ optional($brand->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $brand->name }}">
                        </div>
                    </div>
                @endforeach
                {{-- Loop again for smooth marquee --}}
                @foreach($brands as $brand)
                    <div class="swiper-slide">
                        <div class="partner-item-simple">
                            <img src="{{ optional($brand->mainImage())->url() ?? asset('images/setting/no-image.png') }}" alt="{{ $brand->name }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Event Photo Slider (Coverflow) -->
        <div class="swiper event-photo-slider mt-4">
            <div class="swiper-wrapper">
                @foreach($eventPhotos as $photo)
                    <div class="swiper-slide">
                        <div class="event-photo-item">
                            <img src="{{ $photo }}" alt="Event Photo">
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination mt-4"></div>
        </div>
    </div>
</section>

{{-- Banner 4 --}}
<div class="section-banner py-4">
    <div class="container container-custom text-center">
        <img src="{{ asset('images/setting/danh-cho-quang-cao.png') }}" 
             class="img-fluid w-100" 
             style="max-height: 250px; object-fit: cover;" 
             alt="Quảng cáo">
    </div>
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
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
<script>
  document.addEventListener('DOMContentLoaded', function () {
  // Swiper for products
  const productSlider = new Swiper('.product-slider', {
    loop: true,
    spaceBetween: 25,
    slidesPerView: 1,
    autoplay: {
      delay: 5000,
    },
    breakpoints: {
      576: { slidesPerView: 2 },
      768: { slidesPerView: 3 },
      992: { slidesPerView: 4 }
    },
    pagination: {
      el: '.product-slider .swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.product-slider .swiper-button-next',
      prevEl: '.product-slider .swiper-button-prev',
    },
  });

  const memberSlider = new Swiper('.member-slider', {
      loop: true,
      spaceBetween: 25,
      slidesPerView: 1,
      autoplay: {
        delay: 3000,
      },
      breakpoints: {
        576: { slidesPerView: 2 },
        768: { slidesPerView: 3 },
        992: { slidesPerView: 4 }
      },
      pagination: {
        el: '.member-slider .swiper-pagination',
        clickable: true,
      },
    });

    // Partner Marquee Single (Multiple Instances)
    document.querySelectorAll('.partner-marquee-single').forEach(el => {
      new Swiper(el, {
        slidesPerView: 2,
        spaceBetween: 30,
        loop: true,
        speed: 5000,
        allowTouchMove: false,
        autoplay: {
          delay: 0,
          disableOnInteraction: false,
        },
        breakpoints: {
          576: { slidesPerView: 3 },
          768: { slidesPerView: 4 },
          1024: { slidesPerView: 6 },
        },
      });
    });

    // Event Photo Slider (Coverflow)
    new Swiper('.event-photo-slider', {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",
      loop: true,
      coverflowEffect: {
        rotate: 0,
        stretch: 0,
        depth: 100,
        modifier: 2.5,
        slideShadows: true,
      },
      pagination: {
        el: ".event-photo-slider .swiper-pagination",
        clickable: true,
      },
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
    });
  });
</script>
<script src="{{ asset('js/counter.js') }}"></script>
@endpush