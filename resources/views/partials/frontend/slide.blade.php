<div class="swiper main-slider h-100">
    <div class="swiper-wrapper">
        @foreach($slides as $i => $slide)
            <div class="swiper-slide">
                <img src="{{ optional($slide->mainImage())->url() }}"
                     alt="{{ $slide->name }}"
                     title="{{ $slide->name }}"
                     decoding="async"
                     @if($i>0) loading="lazy" @endif>
                <div class="slide-overlay">
                    <div class="container container-custom h-100 d-flex flex-column justify-content-center align-items-start">
                        <div class="slide-content">
                            <h2 class="slide-subtext text-red mb-2" style="font-family: var(--font-secondary); font-weight: 700; text-transform: uppercase; font-size: 1.5rem;">CÔNG NGHIỆP CHỦ LỰC</h2>
                            <h1 class="slide-title text-white mb-4" style="font-family: var(--font-secondary); font-weight: 700; font-size: clamp(2rem, 1rem + 5vw, 4rem); line-height: 1.1;">
                                {{ $slide->title }}
                            </h1>
                            <a href="{{ route('register') }}" class="btn btn-red-cta" style="padding: 15px 40px; font-size: 1.1rem;">ĐĂNG KÝ HỘI VIÊN <i class="fa fa-arrow-right ml-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>

@push('js')
<script>
(function () {
  if (!window.Swiper) return;
  new Swiper('.main-slider', {
    loop: false,
    rewind: true,
    speed: 800,
    effect: 'fade',
    fadeEffect: { crossFade: true },
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.main-slider .swiper-pagination',
      clickable: true
    },
    navigation: {
      nextEl: '.main-slider .swiper-button-next',
      prevEl: '.main-slider .swiper-button-prev'
    }
  });
})();
</script>
@endpush