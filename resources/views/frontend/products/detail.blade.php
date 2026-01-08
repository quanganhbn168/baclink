@extends('layouts.master')
@section('title', $product->name)
@section('meta_description', $product->meta_description ?? Str::limit(strip_tags($product->description), 160))
@section('meta_keywords', $product->meta_keywords ?? '')
@section('meta_image', optional($product->mainImage())->url() ?? (optional($product->bannerImage())->url() ??
    asset('images/default-product.png')))
@push('css')
    @vite(['resources/css/custom/product.css'])
@endpush
@push('schema')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Product",
      "name": "{{ $product->name }}",
      "image": [
        "{{ optional($product->mainImage())->url() ?? asset('images/default-product.png') }}"
      ],
      "description": "{{ Str::limit(strip_tags($product->description ?? $product->content), 160) }}",
      "sku": "{{ $product->code ?? $product->id }}",
      "mpn": "{{ $product->code ?? $product->id }}",
      "brand": {
        "@type": "Brand",
        "name": "{{ $product->brand->name ?? $setting->name ?? config('app.name') }}"
      },
      "review": {
        "@type": "Review",
        "reviewRating": {
          "@type": "Rating",
          "ratingValue": "5",
          "bestRating": "5"
        },
        "author": {
          "@type": "Person",
          "name": "Admin"
        }
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "5",
        "reviewCount": "1"
      },
      "offers": [
        @if($product->variants->count() > 0)
            @foreach($product->variants as $index => $variant)
            {
                "@type": "Offer",
                "name": "{{ $product->name }} - {{ $variant->variant_name }}",
                "url": "{{ url()->current() }}?variant={{ $variant->id }}",
                "priceCurrency": "VND",
                "price": "{{ $variant->price }}",
                "sku": "{{ $variant->sku }}",
                "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
                "itemCondition": "https://schema.org/NewCondition",
                "availability": "https://schema.org/{{ ($variant->stock > 0) ? 'InStock' : 'OutOfStock' }}",
                "seller": {
                    "@type": "Organization",
                    "name": "{{ $setting->name ?? config('app.name') }}"
                }
            }{{ $index < $product->variants->count() - 1 ? ',' : '' }}
            @endforeach
        @else
            {
                "@type": "Offer",
                "url": "{{ url()->current() }}",
                "priceCurrency": "VND",
                "price": "{{ $product->price_discount > 0 ? $product->price_discount : $product->price }}",
                "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
                "itemCondition": "https://schema.org/NewCondition",
                "availability": "https://schema.org/{{ ($product->stock > 0 || $product->stock === null) ? 'InStock' : 'OutOfStock' }}",
                "seller": {
                    "@type": "Organization",
                    "name": "{{ $setting->name ?? config('app.name') }}"
                }
            }
        @endif
      ]
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Trang chủ",
          "item": "{{ url('/') }}"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "{{ $product->category->name }}",
          "item": "{{ route('frontend.slug.handle', $product->category->slug) }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ $product->name }}",
          "item": "{{ url()->current() }}"
        }
      ]
    }
  ]
}
</script>
@endpush

@section('content')
    <div id="toasts" aria-live="polite" aria-atomic="true"></div>
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-light px-3 py-2">
            <li class="breadcrumb-item">
                <a href="{{ url('/') }}"><i class="fas fa-home"></i> Trang chủ</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $product->category->name }}
            </li>
        </ol>
    </nav>
    <div id="product-detail">
        <div class="container-custom">
            <section class="section section-product__detail">
                <div class="row">
                    {{-- ===== LEFT: GALLERY ===== --}}
                    <div class="col-lg-6 product-images">
                        <div class="product-image-block">
                            <div class="gallery-top">
                                <div class="swiper main-slider">
                                    <div class="swiper-wrapper">
                                        @if ($product->gallery && $product->gallery->isNotEmpty())
                                            @foreach ($product->gallery as $image)
                                                <div class="swiper-slide">
                                                    <img src="{{ optional($image)->url() }}" class="img-fluid"
                                                        alt="{{ $image->alt ?? $product->name }}" loading="lazy">
                                                </div>
                                            @endforeach
                                        @elseif(optional($product->mainImage())->url())
                                            <div class="swiper-slide">
                                                <img src="{{ optional($product->mainImage())->url() }}" class="img-fluid"
                                                    alt="{{ $product->name }}" loading="lazy">
                                            </div>
                                        @elseif(optional($product->bannerImage())->url())
                                            <div class="swiper-slide">
                                                <img src="{{ optional($product->bannerImage())->url() }}" class="img-fluid"
                                                    alt="{{ $product->name }}" loading="lazy">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="swiper gallery-thumbs">
                                <div class="swiper-wrapper">
                                    @if ($product->gallery && $product->gallery->isNotEmpty())
                                        @foreach ($product->gallery as $image)
                                            <div class="swiper-slide">
                                                <img src="{{ optional($image)->url() }}" class="img-fluid"
                                                    alt="{{ $image->alt ?? $product->name }}" loading="lazy">
                                            </div>
                                        @endforeach
                                    @elseif(optional($product->mainImage())->url())
                                        <div class="swiper-slide">
                                            <img src="{{ optional($product->mainImage())->url() }}" class="img-fluid"
                                                alt="{{ $product->name }}" loading="lazy">
                                        </div>
                                    @elseif(optional($product->bannerImage())->url())
                                        <div class="swiper-slide">
                                            <img src="{{ optional($product->bannerImage())->url() }}" class="img-fluid"
                                                alt="{{ $product->name }}" loading="lazy">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ===== RIGHT: INFO ===== --}}
                    <div class="col-lg-6">
                        <div class="details-pro">
                            <h1 class="title-product">{{ $product->name }}</h1>
                            <div class="product-top">
                                {{-- Logic giá PHP mặc định (dùng khi chưa chọn biến thể hoặc load trang) --}}
                                @php
                                    $hasDiscount =
                                        $product->price_discount && $product->price > $product->price_discount;
                                    $displayPrice = $hasDiscount ? $product->price_discount : $product->price;
                                    $isContact = (int) $displayPrice <= 0;
                                    $percentOff = $hasDiscount
                                        ? round(100 - ($product->price_discount / max(1, $product->price)) * 100)
                                        : 0;
                                @endphp
                                <div class="sku-product">
                                    <span>Mã sản phẩm:</span>
                                    <strong
                                        class="a-sku">{{ $product->code ?? 'SP' . str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                </div>
                                <div class="status-product">
                                    <span>Tình trạng:</span>
                                    <strong class="text-success">Sản phẩm có sẵn</strong>
                                </div>
                                {{-- KHU VỰC GIÁ (Có ID để JS cập nhật) --}}
                                <div class="price-product" id="price-container">
                                    @if ($isContact)
                                        <span class="price-main">Liên hệ</span>
                                    @else
                                        <span class="price-main">{{ number_format($displayPrice, 0, ',', '.') }}đ</span>
                                        @if ($hasDiscount)
                                            <span
                                                class="price-compare">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                                            <span class="badge-sale">-{{ $percentOff }}%</span>
                                        @endif
                                    @endif
                                </div>
                                <div class="product-des">
                                    {!! nl2br($product->description) !!}
                                </div>
                                <hr>
                                {{-- ========================================== --}}
                                {{-- KHU VỰC CHỌN BIẾN THỂ (MỚI THÊM) --}}
                                {{-- ========================================== --}}
                                @if (isset($attributes) && count($attributes) > 0)
                                    <div class="product-variants mb-3">
                                        @foreach ($attributes as $attrId => $attr)
                                            <div class="variant-group mb-3">
                                                <label class="font-weight-bold mb-1 d-block">{{ $attr['name'] }}:</label>
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($attr['values'] as $val)
                                                        <label>
                                                            <input type="radio" name="attr_{{ $attrId }}"
                                                                value="{{ $val['id'] }}" class="variant-input"
                                                                data-attr-id="{{ $attrId }}">
                                                            {{-- Xử lý hiển thị Color hoặc Text --}}
                                                            @if ($attr['type'] === 'color')
                                                                {{-- Nếu là Color: Hiện ô màu --}}
                                                                <span class="variant-label is-color"
                                                                    style="background-color: {{ $val['color_code'] ?? '#000' }};"
                                                                    title="{{ $val['value'] }}">
                                                                </span>
                                                            @else
                                                                {{-- Nếu là Text: Hiện chữ --}}
                                                                <span class="variant-label">
                                                                    {{ $val['value'] }}
                                                                </span>
                                                            @endif
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                {{-- SỐ LƯỢNG --}}
                                <div class="qty-wrap">
                                    <div class="qty-title">Số lượng</div>
                                    <div class="qty-group">
                                        <button type="button" class="btn-qty" data-act="dec">−</button>
                                        <input type="number" name="qty" id="qtyInput" value="1" min="1">
                                        <button type="button" class="btn-qty" data-act="inc">+</button>
                                    </div>
                                </div>
                                {{-- NÚT HÀNH ĐỘNG --}}
                                <div class="d-flex flex-wrap" style="gap:12px" id="action-buttons">
                                    {{-- Nhóm nút Mua (Hiện nếu có giá) --}}
                                    <div id="group-buy-buttons" class="d-flex"
                                        style="gap:12px; {{ $isContact ? 'display:none !important' : '' }}">
                                        <button type="button" class="btn btn-primary-cart" id="btnAddToCart"
                                            data-product-id="{{ $product->id }}" data-url="{{ route('cart.add') }}">
                                            THÊM VÀO GIỎ
                                        </button>
                                        <button type="button" class="btn btn-buy-now" id="btnBuyNowAjax"
                                            data-url="{{ route('cart.buy-now') }}"
                                            data-product-id="{{ $product->id }}">
                                            MUA NGAY
                                        </button>
                                    </div>
                                    {{-- Nút Liên hệ (Hiện nếu giá = 0) --}}
                                    <button type="button" class="btn btn-primary-cart" id="btnContact"
                                        data-toggle="modal" data-target="#modalContact"
                                        style="{{ $isContact ? 'display:inline-block' : '' }}">
                                        <i class="fas fa-phone-alt mr-2"></i> LIÊN HỆ BÁO GIÁ
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        {{-- ===== SECOND CONTAINER: TABS DESCRIPTION & SPECS ===== --}}
<div class="container-custom mt-4 mb-5">
    <div class="product-detail-tabs">
        {{-- 1. THANH ĐIỀU HƯỚNG TAB --}}
        <ul class="nav nav-tabs product-nav-tabs" id="productTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab"
                    aria-controls="description" aria-selected="true">
                    Mô tả sản phẩm
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="specs-tab" data-toggle="tab" href="#specs" role="tab"
                    aria-controls="specs" aria-selected="false">
                    Thông số kỹ thuật
                </a>
            </li>
            {{-- Mở rộng: Tab đánh giá (nếu có sau này) --}}
            {{-- <li class="nav-item"><a class="nav-link" href="#reviews" ...>Đánh giá</a></li> --}}
        </ul>

        {{-- 2. NỘI DUNG TAB --}}
        <div class="tab-content product-tab-content p-4 border border-top-0 bg-white" id="productTabContent">
            {{-- TAB 1: MÔ TẢ --}}
            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                <div class="description-wrapper collapsed" id="descWrapper">
        <div class="ck-content product-description-wrapper">
            @if(!empty($product->content))
                {!! $product->content !!}
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-edit mb-2"></i><br>
                    Nội dung mô tả đang được cập nhật.
                </div>
            @endif
        </div>
        
        {{-- Lớp phủ mờ (Gradient) tạo hiệu ứng cắt dở --}}
        <div class="desc-gradient" id="descGradient"></div>
    </div>

    {{-- Nút Xem thêm / Thu gọn --}}
    <div class="text-center mt-3">
        <button class="btn btn-outline-primary btn-sm d-none" id="btnToggleDesc">
            Xem thêm nội dung <i class="fas fa-chevron-down ml-1"></i>
        </button>
    </div>
            </div>

            {{-- TAB 2: THÔNG SỐ --}}
            <div class="tab-pane fade" id="specs" role="tabpanel" aria-labelledby="specs-tab">
                <div class="ck-content">
                    @if(!empty($product->specifications))
                        {!! $product->specifications !!}
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-tools mb-2"></i><br>
                            Thông số kỹ thuật đang được cập nhật.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
        {{-- ===== SECOND CONTAINER: DESCRIPTION & SPECS ===== --}}
        <div class="container-custom">
            {{-- ===== THANK YOU ===== --}}
            <div class="thankyou-info my-4 p-3 rounded" style="background:#f8fafc;border:1px solid #e5e7eb;">
                <span class="d-block mb-1 font-weight-bold text-uppercase">TRÂN TRỌNG CẢM ƠN QUÝ KHÁCH ĐÃ QUAN TÂM, MỌI CHI
                    TIẾT XIN LIÊN HỆ:</span>
                <p class="mb-1">{{ $setting->name ?? config('app.name') }}</p>
                <p class="mb-1">Địa chỉ: {{ $setting->address ?? 'Đang cập nhật' }}</p>
                <p class="mb-1">Hotline: {{ $setting->phone ?? 'Đang cập nhật' }}</p>
                <p class="mb-0">Email: {{ $setting->email ?? 'Đang cập nhật' }}</p>
            </div>
            {{-- ===== RELATED PRODUCTS ===== --}}
            @if (!empty($relatedProducts) && count($relatedProducts))
                <section class="section related-product">
                    <h2 class="custom-section-title mb-3">Sản phẩm liên quan</h2>
                    <div class="container-custom">
                        <div class="category-slider swiper">
                            <div class="swiper-wrapper">
                                @foreach ($relatedProducts as $key => $product)
                                    <div class="swiper-slide">
                                        @include('partials.frontend.product_item', ['product' => $product])
                                    </div>
                                @endforeach
                            </div>
                            {{-- Navigation for this slider only --}}
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>
                </section>
            @endif
        </div> {{-- /container-custom --}}
    </div> {{-- /#product-detail --}}
    {{-- ========================================== --}}
    {{-- MODAL LIÊN HỆ --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalContact" tabindex="-1" aria-labelledby="modalContactLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalContactLabel">Liên hệ tư vấn sản phẩm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Sản phẩm: <strong>{{ $product->name }}</strong></p>
                    <p>Vui lòng liên hệ với chúng tôi qua Hotline để nhận báo giá tốt nhất.</p>
                    <div class="text-center my-3">
                        <a href="tel:{{ $setting->phone ?? '0123456789' }}" class="btn btn-danger btn-lg">
                            <i class="fas fa-phone"></i> {{ $setting->phone ?? '0123 456 789' }}
                        </a>
                    </div>
                    <p class="mb-0">Hoặc để lại thông tin, chúng tôi sẽ gọi lại sau.</p>
                    {{-- Form liên hệ tùy ý --}}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- PHẦN 1: LOGIC SLIDER ẢNH SẢN PHẨM ---
        const root = document.querySelector('#product-detail');
        if (root) {
            const thumbsEl = root.querySelector('.gallery-thumbs');
            const mainEl = root.querySelector('.main-slider');
            
            if (thumbsEl && mainEl) {
                const thumbs = new Swiper(thumbsEl, {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                    breakpoints: {
                        576: { slidesPerView: 5 },
                        768: { slidesPerView: 6 },
                        1200: { slidesPerView: 7 }
                    }
                });
                
                new Swiper(mainEl, {
                    spaceBetween: 10,
                    thumbs: {
                        swiper: thumbs
                    },
                });
            }

            // Slider sản phẩm liên quan
            root.querySelectorAll('.category-slider').forEach(function(slider) {
                new Swiper(slider, {
                    loop: false,
                    slidesPerView: 2,
                    spaceBetween: 15,
                    breakpoints: {
                        576: { slidesPerView: 2, spaceBetween: 20 },
                        768: { slidesPerView: 3, spaceBetween: 20 },
                        992: { slidesPerView: 4, spaceBetween: 24 },
                    },
                });
            });
        }

        // --- PHẦN 2: LOGIC XEM THÊM / THU GỌN MÔ TẢ ---
        const wrapper = document.getElementById('descWrapper');
        const btn = document.getElementById('btnToggleDesc');
        const gradient = document.getElementById('descGradient');
        const limitHeight = 500; // Phải khớp với max-height trong CSS

        if (wrapper && btn) {
            // Kiểm tra chiều cao thực tế
            const actualHeight = wrapper.scrollHeight;

            if (actualHeight > limitHeight) {
                // Nội dung dài -> Hiện nút, hiện gradient
                btn.classList.remove('d-none');
                if (gradient) gradient.style.display = 'block';
            } else {
                // Nội dung ngắn -> Mở full, ẩn nút
                wrapper.classList.remove('collapsed');
                wrapper.classList.add('expanded');
                if (gradient) gradient.style.display = 'none';
                btn.classList.add('d-none');
            }

            // Xử lý sự kiện click
            btn.addEventListener('click', function() {
                const isCollapsed = wrapper.classList.contains('collapsed');

                if (isCollapsed) {
                    // MỞ RỘNG
                    wrapper.classList.remove('collapsed');
                    wrapper.classList.add('expanded');
                    btn.innerHTML = 'Thu gọn <i class="fas fa-chevron-up ml-1"></i>';
                    if (gradient) gradient.style.display = 'none';
                } else {
                    // THU GỌN
                    wrapper.classList.remove('expanded');
                    wrapper.classList.add('collapsed');
                    btn.innerHTML = 'Xem thêm nội dung <i class="fas fa-chevron-down ml-1"></i>';
                    if (gradient) gradient.style.display = 'block';
                    
                    // Cuộn lên đầu tab mô tả
                    const tabElement = document.getElementById('productTab');
                    if(tabElement) {
                        tabElement.scrollIntoView({behavior: 'smooth', block: 'start'});
                    }
                }
            });
        }
    });
</script>
<script>
        // Pass PHP data to JavaScript global scope
        window.productData = {
            variants: @json($variantsJson ?? []),
            cartCount: {{ $cartCount ?? 0 }}
        };
    </script>
@endpush
