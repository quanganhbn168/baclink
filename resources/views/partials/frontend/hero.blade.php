<section class="hero-slider-section">
    <div class="swiper hero-slider">
        <div class="swiper-wrapper">
            @forelse($slides as $slide)
            <div class="swiper-slide hero-slide">
                {{-- Background Image --}}
                <img src="{{ $slide->image_url ?? asset('images/herosection/background.jpg') }}" 
                     alt="{{ $slide->title ?? $setting->name }}" 
                     class="hero__bg">
                
                <div class="hero__overlay"></div>

                <div class="hero__container">
                    <div class="hero__content">
                        <h2 class="hero__title animated-text" style="animation-delay: 0.2s;">
                            {!! $slide->title ?? 'Cộng đồng các Doanh nghiệp sản xuất<br>sản phẩm công nghiệp chủ lực<br>Thành phố Bắc Ninh' !!}
                        </h2>
                        
                        <p class="hero__subtitle animated-text" style="animation-delay: 0.4s;">
                            {{ $slide->description ?? 'Gắn kết – Tiên phong' }}
                        </p>

                        <a href="{{ $slide->link ?? route('register') }}" class="hero__btn animated-text" style="animation-delay: 0.6s;">
                            <div class="hero__btn-icon">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="hero__btn-text">
                                <span class="hero__btn-main">XEM CHI TIẾT</span>
                                <span class="hero__btn-sub">Hội công nghiệp chủ lực Bắc Ninh</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            {{-- Fallback Static Slide if no slides --}}
            <div class="swiper-slide hero-slide">
                <img src="{{ asset('images/herosection/background.jpg') }}" alt="{{ $setting->name }}" class="hero__bg">
                <div class="hero__overlay"></div>
                <div class="hero__container">
                    <div class="hero__content">
                        <h2 class="hero__title">
                            Cộng đồng các Doanh nghiệp sản xuất<br>
                            sản phẩm công nghiệp chủ lực<br>
                            Thành phố Bắc Ninh
                        </h2>
                        <p class="hero__subtitle">Gắn kết – Tiên phong</p>
                        <a href="{{ route('register') }}" class="hero__btn">
                            <div class="hero__btn-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="hero__btn-text">
                                <span class="hero__btn-main">ĐĂNG KÝ HỘI VIÊN</span>
                                <span class="hero__btn-sub">Hội công nghiệp chủ lực Bắc Ninh</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        {{-- Navigation & Pagination --}}
        <div class="swiper-button-next d-none d-md-flex"></div>
        <div class="swiper-button-prev d-none d-md-flex"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>

@push('css')
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Swiper('.hero-slider', {
            loop: true,
            effect: 'fade', // Fade effect cho hero
            fadeEffect: {
                crossFade: true
            },
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.hero-slider .swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.hero-slider .swiper-button-next',
                prevEl: '.hero-slider .swiper-button-prev',
            },
        });
    });
</script>
@endpush
