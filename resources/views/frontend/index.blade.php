@extends('layouts.master')
@section('title','Trang chủ - '.$setting->name)
@section('meta_description',$setting->meta_description)
@section('meta_keywords',$setting->meta_keywords)
@section('meta_image',$setting->meta_image ? asset($setting->meta_image) : asset($setting->logo))
@push('css')
    @vite(['resources/css/custom/hero.css', 'resources/css/custom/home.css', 'resources/css/custom/counter.css'])
@endpush
@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "LocalBusiness",
      "@id": "{{ url('/') }}#organization",
      "name": "{{ $setting->name }}",
      "url": "{{ url('/') }}",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ asset($setting->logo) }}"
      },
      "image": "{{ asset($setting->share_image) }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $setting->address }}",
        "addressLocality": "Bắc Ninh",
        "addressCountry": "VN"
      },
      "telephone": "{{ $setting->phone }}",
      "email": "{{ $setting->email }}",
      "priceRange": "$$",
      "sameAs": [
        "{{ $setting->facebook }}",
        "{{ $setting->youtube }}",
        "{{ $setting->zalo }}"
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
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "{{ url('/search') }}?q={search_term_string}"
        },
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

{{-- INTRO SECTION --}}
<section class="section intro-section py-5 bg-white position-relative overflow-hidden intro-home-section bg-pattern-grid" data-aos="fade-up">
    {{-- Decorative SVG Background --}}
    <div class="intro-decoration position-absolute">
       <svg width="600" height="600" viewBox="0 0 600 600" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.1; color: #C5A065;">
            <circle cx="300" cy="300" r="250" stroke="currentColor" stroke-width="2" stroke-dasharray="10 10">
                <animateTransform attributeName="transform" type="rotate" from="0 300 300" to="360 300 300" dur="60s" repeatCount="indefinite"/>
            </circle>
            <circle cx="300" cy="300" r="180" stroke="#1a2a5a" stroke-width="2">
                 <animate attributeName="r" values="180;200;180" dur="10s" repeatCount="indefinite"/>
            </circle>
            <path d="M300 50 L350 150 L450 150 L370 220 L400 320 L300 260 L200 320 L230 220 L150 150 L250 150 Z" stroke="#C5A065" stroke-width="1" fill="none" opacity="0.5"/>
       </svg>
    </div>

    <div class="container container-custom position-relative z-index-1">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="intro-image-wrapper position-relative text-center">
                     @if($intro)
                        {{-- Real Image --}}
                        <img src="{{ optional($intro->mainImage())->url() ?? asset('images/setting/no-image.png') }}" 
                             alt="{{ $intro->title }}" 
                             class="img-fluid rounded shadow-lg position-relative z-index-2"
                             style="max-height: 400px; object-fit: cover;">
                        
                     @endif
                </div>
            </div>
            <div class="col-lg-6">
                @if($intro)
                {{-- <h5 class="text-uppercase text-gold font-weight-bold mb-2">Về chúng tôi</h5> --}}
                <h2 class="section-title text-left mb-4 intro-home-title">{{ $intro->title }}</h2>
                <div class="intro-content text-justify text-muted mb-4">
                     {!! Str::limit(strip_tags($intro->description), 800) !!}
                </div>
                <a href="{{ route('frontend.slug.handle', [$intro->slug ?? 'gioi-thieu']) }}" class="btn btn-gold px-4 py-2 rounded-pill shadow-sm">
                    Tìm hiểu thêm <i class="fas fa-arrow-right ml-2"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</section>


{{-- FIELDS OF ACTIVITY SECTION --}}
@if($activityCategory && $activityCategory->fields->isNotEmpty())
<section class="section fields-section py-5 bg-fixed-parallax" 
         style="background-image: url('{{ asset('images/setting/login-bg.jpg') }}');" 
         data-aos="fade-up" data-aos-delay="100">
    <div class="container container-custom">
        <h2 class="section-title mb-5 text-center text-white"><a href="{{ route('frontend.fields.index') }}" class="text-white">{{ $activityCategory->name }}</a></h2>
        
        <div class="swiper fields-slider position-relative px-2">
            <div class="swiper-wrapper py-3">
                 @foreach($activityCategory->fields as $field)
                <div class="swiper-slide h-auto">
                    <div class="card field-card h-100 shadow-sm border-0">
                        <div class="field-img-container">
                            <a href="{{ route('frontend.slug.handle', $field->slug) }}">
                                <img src="{{ $field->image_url }}" 
                                     alt="{{ $field->name }}">
                            </a>
                        </div>
                        <div class="card-body field-content d-flex flex-column text-center">
                            <h5 class="card-title field-title font-weight-bold mb-3">
                                <a href="{{ route('frontend.slug.handle', $field->slug) }}">{{ $field->name }}</a>
                            </h5>
                            <p class="card-text field-desc mb-0">
                                {{ Str::limit($field->summary, 100) }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="swiper-pagination mt-4"></div>
        </div>
    </div>
</section>
@endif

{{-- NEWS SECTION --}}
<section class="section news-section py-5 bg-texture-noise" data-aos="fade-up" data-aos-delay="100">
    <div class="container container-custom">
        <h2 class="section-title mb-5"><a href="{{ route('frontend.posts.index') }}">HOẠT ĐỘNG CỦA BACLINK</a></h2>
        
        @if($homePosts->count() > 0)
        @php
            $featuredPosts = $homePosts->take(3);
            $verticalPosts = $homePosts->skip(3);
        @endphp
        <div class="row">
            {{-- FEATURED SLIDER (HORIZONTAL) - LEFT --}}
            <div class="col-lg-7 mb-4 mb-lg-0 order-1 order-lg-1">
                <div class="swiper featured-news-slider h-100 rounded overflow-hidden shadow-sm group-hover">
                    <div class="swiper-wrapper">
                        @foreach($featuredPosts as $post)
                        <div class="swiper-slide h-auto">
                            <div class="post-item post-item-big h-100 position-relative">
                                <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="d-block overflow-hidden" style="min-height: 450px; height: 100%;">
                                    <img src="{{ $post->image_url }}" 
                                         alt="{{ $post->title }}" 
                                         class="w-100 h-100 object-fit-cover transition-transform duration-500 hover-scale">
                                </a>
                                {{-- Text Overlay at Bottom --}}
                                <div class="post-content p-4 position-absolute bottom-0 w-100 bg-gradient-dark text-white">
                                    @if($post->category)
                                    <span class="badge badge-gold mb-2">{{ $post->category->name }}</span>
                                    @endif
                                    <h3 class="post-title text-white font-weight-bold mb-2 icon-link-hover font-size-24">
                                        <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="text-white">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    <p class="post-desc text-white-50 mb-0 d-block line-clamp-2">
                                         {{ Str::limit(strip_tags($post->description ?? $post->content), 150) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>

              {{-- NEWS LIST (VERTICAL SLIDER) - RIGHT --}}
            <div class="col-lg-5 order-2 order-lg-2 position-relative">
                 {{-- Nav Buttons Container --}}
                 <div class="slider-nav-buttons position-absolute" style="top: -60px; right: 15px; z-index: 10;">
                    <div class="swiper-button-next-custom d-inline-block text-center mr-2 cursor-pointer text-blue border border-blue rounded-circle" style="width: 30px; height: 30px; line-height: 28px;">
                        <i class="fas fa-chevron-up"></i>
                    </div>
                    <div class="swiper-button-prev-custom d-inline-block text-center cursor-pointer text-blue border border-blue rounded-circle" style="width: 30px; height: 30px; line-height: 28px;">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                 </div>

                 <div class="swiper news-vertical-slider h-100" style="max-height: 500px;">
                    <div class="swiper-wrapper">
                        @foreach($verticalPosts as $post)
                        <div class="swiper-slide news-list-slide pb-3">
                            <div class="post-item post-item-horizontal bg-white rounded overflow-hidden shadow-sm d-flex h-100 align-items-stretch">
                                <div class="post-thumb w-30 flex-shrink-0 overflow-hidden position-relative" style="min-width: 120px;">
                                    <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="d-block h-100">
                                        <img src="{{ $post->image_url }}" 
                                             alt="{{ $post->title }}" 
                                             class="w-100 h-100 object-fit-cover transition-transform duration-500 hover-scale">
                                    </a>
                                </div>
                                <div class="post-content pl-3 pr-2 py-2 w-70 d-flex flex-column">
                                    @if($post->category)
                                    <div class="mb-1"><small class="text-gold font-weight-bold text-uppercase">{{ $post->category->name }}</small></div>
                                    @endif
                                    <h4 class="post-title font-size-15 font-weight-bold mb-1 line-clamp-2">
                                        <a href="{{ route('frontend.slug.handle', $post->slug) }}" class="text-dark">
                                            {{ $post->title }}
                                        </a>
                                    </h4>
                                    <p class="post-desc text-muted mb-2 line-clamp-2 small" style="min-height: 40px;">
                                        {{ Str::limit(strip_tags($post->description ?? $post->content), 120) }}
                                    </p>
                                    <div class="mt-auto">
                                        <span class="text-muted small"><i class="far fa-clock mr-1"></i> {{ $post->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @else
        <p class="text-center">Chưa có tin tức nào.</p>
        @endif
        
        <div class="text-center mt-5">
            <a href="{{ route('frontend.posts.index') }}" class="btn btn-gold px-4 py-2 rounded-pill shadow-sm">
                Xem tất cả tin tức <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

{{-- MEMBERS SECTION --}}
<section class="section member-section py-5 bg-white position-relative bg-curves" data-aos="fade-up" data-aos-delay="100">
    {{-- Optional SVG Overlay --}}
    <div class="position-absolute w-100 h-100 top-0 left-0" style="background: rgba(255,255,255,0.92); z-index: 0;"></div>
    
    <div class="container container-custom position-relative z-index-1">
        <div class="text-center mb-5">
            <h2 class="section-title mb-3"><a href="{{ route('frontend.members.index') }}">HỘI VIÊN TIÊU BIỂU</a></h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                Cộng đồng doanh nghiệp Bắc Ninh cùng kết nối, chia sẻ và phát triển
            </p>
        </div>

        {{-- Grid Layout instead of Slider --}}
        <div class="row px-lg-4">
            @foreach($members as $member)
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="member-card text-center p-4 bg-white rounded shadow-sm h-100 position-relative">
                    <div class="member-avatar-wrapper mb-3 mx-auto" style="width: 100px; height: 100px;">
                        <img src="{{ $member->avatar_url }}" 
                             alt="{{ $member->name }}" 
                             class="w-100 h-100 rounded-circle object-fit-cover border border-3 border-light shadow-sm">
                    </div>
                    <div class="member-info">
                        <h5 class="member-name font-weight-bold mb-1 text-dark text-truncate">{{ $member->name }}</h5>
                        <p class="member-role small text-muted mb-2 text-uppercase text-truncate">
                            {{ optional($member->dealerProfile)->position ?? 'Đại diện doanh nghiệp' }}
                        </p>
                        <p class="member-company text-blue font-weight-bold small line-clamp-2" style="min-height: 40px;">
                            {{ optional($member->dealerProfile)->company_name ?? 'Công ty Hội viên' }}
                        </p>
                    </div>
                    {{-- Hover overlay or link --}}
                    <a href="{{ route('frontend.members.index') }}" class="stretched-link"></a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('frontend.members.index') }}" class="btn btn-gold px-4 py-2 rounded-pill shadow-sm">
                Tìm hiểu thêm <i class="fas fa-arrow-right ml-2"></i>
            </a>
            </div>
    </div>
</section>

{{-- PARTNERS SECTION --}}
<section class="section partner-section py-5 bg-light" data-aos="fade-up" data-aos-delay="100">
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
    </div>
</section>

{{-- Hidden / Commented Out Sections (Products, Exhibition) --}}
{{-- 
<section class="section product-section py-5">
    ...
</section>
--}}
@endsection
    @push('js')
    @endpush
@push('css')
<style>
/* Intro Section */
.intro-image-wrapper {
    position: relative;
    padding: 20px;
}

/* Member Section Decoration */
.member-section {
    position: relative;
    background-color: #fff; /* White base for members */
    z-index: 1;
}

.member-section::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    /* Subtle SVG Pattern (Dot Grid or Curves) */
    background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23003366' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
    opacity: 0.6;
    pointer-events: none;
    z-index: -1;
}

.intro-content {
    font-size: 1.1rem;
    line-height: 1.8;
}

/* News Section Styles */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.font-size-15 { font-size: 15px; }
.font-size-24 { font-size: 24px; }
.w-35 { width: 35% !important; }
.w-65 { width: 65% !important; }

/* Custom Widths for New Layout */
.w-30 { width: 30% !important; }
.w-70 { width: 70% !important; }

.object-fit-cover { object-fit: cover; }
.hover-scale { transition: transform 0.5s ease; }
.group-hover:hover .hover-scale,
.post-item:hover .hover-scale { transform: scale(1.05); }

/* Gradient Overlay */
.bg-gradient-dark {
    background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.5) 60%, transparent 100%);
}

.bottom-0 { bottom: 0 !important; }

.text-gold { color: var(--gold); }
.bg-light-gray { background-color: #f8f9fa; }
</style>
@endpush
