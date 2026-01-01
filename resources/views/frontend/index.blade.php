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
<section id="slider">
    @include("partials.frontend.slide")
</section>
<section class="section section-intro">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <a href="{{route('frontend.slug.handle',$introMain->slug)}}">
                    <img src="{{ optional($introMain->mainImage())->url() }}" alt="{{$introMain->title}}">
                </a>
            </div>
            <div class="col-12 col-md-6">
                <h2 class="">{{$introMain->title}}</h2>
                <div>
                    {!! $introMain->description !!}
                </div>
                <div class="intro-action">
                    <a href="{{route('frontend.slug.handle',$introMain->slug)}}" class="btn btn-outline-primary rounded-pill btn-crossover">
                        <span class="btn-crossover-text">Xem chi tiết</span>
                        <span class="btn-crossover-icon">
                            <i class="fa-solid fa-arrow-right-long"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>              
<section class="section product-section">
  <h2 class="section-title">Sản phẩm</h2>
  <div class="swiper product-slider">
    <div class="swiper-wrapper">
      @foreach($homeProducts as $product)
        <div class="swiper-slide">
          @include('partials.frontend.product_item', ['product' => $product])
        </div>
      @endforeach
    </div>
    
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>
</section>
<section class="section daily-section">
    <div class="container">
        <div class="daily-box">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="daily-image">
                        <img src="{{ asset('images/setting/Anh-keu-goi-dang-ky-dai-ly.jpg') }}" alt="Ảnh kêu gọi đăng ký đại lý">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="daily-text">
                        <h2 class="text-bold">
                            Phát Triển Công Việc Kinh Doanh Của Bạn
                        </h2>
                        <div class="daily-text_sumary">
                            Bạn mong muốn nâng tầm vị thế và tối đa hóa lợi nhuận cho trung tâm chăm sóc xe hoặc đơn vị kinh doanh của mình? Hãy cùng Ekokemika Việt Nam kiến tạo thành công bền vững. Chúng tôi không chỉ tìm kiếm nhà phân phối, chúng tôi tìm kiếm những đối tác chiến lược để cùng nhau thống lĩnh thị trường!
                        </div>
                        <a href="/dang-ky-dai-ly">Đăng ký ngay</a>
                    </div>
                </div>              
            </div>
        </div>       
    </div>
</section>
<section class="section slogan-section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-4 order-2 order-md-1">
                <h3 class="slogan-text">Tiết kiệm thời gian và tiền bạc</h3>
            </div>
            <div class="col-12 col-md-8 order-1 order-md-2">
                <x-counters :items="[
    ['icon'=>'fa-solid fa-calendar-days','to'=>1000000,   'suffix'=>'+', 'label'=>'SẢN PHẨM ĐÃ BÁN'],
    ['icon'=>'fa-solid fa-house-flag','to'=>150,  'suffix'=>'+', 'label'=>'ĐỐI TÁC'],
    ['icon'=>'fa-solid fa-circle-check',   'to'=>50000, 'suffix'=>'+', 'label'=>'KHÁCH HÀNG HÀI LÒNG'],
]" col="col-12 col-lg-4" />
            </div>
        </div>
    </div>
</section>
<section class="section section-news">
    <h2 class="section-title"><a href="#">Tin tức</a></h2>
    <div class="container-fluid">
        <div class="row">
            @foreach($homePosts as $post)
                <div class="col-12 col-md-3 mb-5"> {{-- 2 bài / hàng trên desktop --}}
                    @include("partials.frontend.post_item",["post" => $post])
                </div>
            @endforeach
        </div>
    </div>
</section>
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
  const SLIDE_COUNT = {{ count($homeProducts ?? []) }};
  const productSlider = new Swiper('.product-slider', {
    center:true,
    rewind: true,
    spaceBetween: 20,
    slidesPerView: 1,
    breakpoints: {
      576: { slidesPerView: 2, spaceBetween: 20 },
      768: { slidesPerView: 3, spaceBetween: 25 },
      992: { slidesPerView: 4, spaceBetween: 30 }
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
});
</script>
<script src="{{ asset('js/counter.js') }}"></script>
@endpush