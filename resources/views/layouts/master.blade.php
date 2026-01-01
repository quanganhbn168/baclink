{{-- resources/views/layouts/master.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Basic --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- CSRF --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Title & SEO --}}
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta_description', $setting->meta_description)">
    <meta name="keywords" content="@yield('meta_keywords', $setting->meta_keywords)">
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">
    {{-- Canonical --}}
    <link rel="canonical" href="{{ url()->current() }}" />
    {{-- Open Graph --}}
    <meta property="og:type"        content="@yield('og_type','website')" />
    <meta property="og:title"       content="@yield('title', config('app.name')) " />
    <meta property="og:description" content="@yield('meta_description', $setting->meta_description)" />
    <meta property="og:url"         content="{{ url()->current() }}" />
    <meta property="og:site_name"   content="{{ $setting->name }}" />
    <meta property="og:image"       content="@yield('meta_image', $setting->share_image)" />
    {{-- Twitter Card --}}
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:title"       content="@yield('title', config('app.name'))" />
    <meta name="twitter:description" content="@yield('meta_description')" />
    <meta name="twitter:image"       content="@yield('meta_image', $setting->share_image)" />
    {{-- Fonts, Favicons --}}
    <link rel="icon" href="{{ asset($setting->favicon) }}" type="image/x-icon" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset($setting->favicon) }}" />
    {{-- CSS & JS --}}
    <link rel="stylesheet" href="{{asset('vendor/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/fontawesome/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendor/swiper/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/sweetalert2/bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/slide.css') }}?v={{ filemtime(public_path('css/slide.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}?v={{ filemtime(public_path('css/global.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}?v={{ filemtime(public_path('css/responsive.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/project-slider.css') }}?v={{ filemtime(public_path('css/project-slider.css')) }}">

    @stack('css')
    {!!$setting->head_script!!}
    @stack('conversion_script')
    @yield('google_tag')
    @stack('schema')
</head>
<body class="{{ Auth::check() ? 'logged-in' : '' }}">
    {!!$setting->body_script!!}
    @include('partials.frontend.header')
    @yield('content')
    @include('frontend.modal.contact')
    @include('frontend.modal.branch')
    @include('partials.frontend.footer')
    {{-- KHỐI CÁC NÚT HÀNH ĐỘNG CỐ ĐỊNH Ở GÓC MÀN HÌNH --}}
    <div class="contact-pills">

        {{-- Nút gọi điện (với hiệu ứng rung) --}}
        <a href="tel:{{ $setting->phone }}" class="contact-pill phone-pill">
            <div class="phone-icon-wrapper is-animating">
               <i class="fas fa-phone-alt"></i>
           </div>
       </a>

       {{-- Nút Zalo --}}
       <a href="{{ $setting->zalo }}" target="_blank" rel="nofollow" class="contact-pill zalo-pill">
            <i class="fas fa-comment-dots"></i>
        </a>
        
        {{-- Nút Messenger --}}
        <a href="{{ $setting->mess }}" target="_blank" rel="nofollow" class="contact-pill messenger-pill">
            <i class="fab fa-facebook-messenger"></i>
        </a>
        
        {{-- Nút Lên đầu trang (Back to top) --}}
        <a href="#" class="contact-pill back-to-top" id="js-back-to-top">
            <i class="fas fa-arrow-up"></i>
        </a>

    </div>
    <script src="{{asset('/js/jquery-3.7.1.min.js')}}?{{time()}}"></script>
    <script src="{{asset('/vendor/bootstrap/popper.min.js')}}?{{time()}}"></script>
    <script src="{{asset('/vendor/bootstrap/js/bootstrap.min.js')}}?{{time()}}"></script>
    <script src="{{asset('/vendor/swiper/swiper-bundle.min.js')}}?{{time()}}"></script>
    <script src="{{asset('plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{ asset('js/cart.js') }}"></script>
    <script src="{{ asset('js/counter.js') }}"></script>
    <script src="{{ asset('js/TabbedSwiperHandler.js') }}?v={{ filemtime(public_path('js/TabbedSwiperHandler.js')) }}"></script>

    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: @json(session('success')),
            confirmButtonText: 'OK'
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: @json(session('error')),
            confirmButtonText: 'OK'
        });
    </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const backToTopButton = document.getElementById('js-back-to-top');

    if (backToTopButton) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        backToTopButton.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
    </script>
    @push('js')
    <script>
    $(function () {
        const $badges = $('[data-role="cart-count"]');
        if (!$badges.length) return;

        function renderQty(qty) {
            qty = Number(qty || 0);
            $badges.each(function () {
                const $b = $(this);
                $b.text(qty);

                if (qty > 0) {
                    // Luôn là flex theo yêu cầu
                    $b.css('display', 'flex').attr('aria-hidden', 'false');
                } else {
                    $b.css('display', 'none').attr('aria-hidden', 'true');
                }
            });
        }

        // Lấy count ban đầu
        $.ajax({
            url: "{{ route('cart.index') }}",
            method: "GET",
            headers: { "Accept": "application/json" }
        }).done(function (data) {
            renderQty(data?.total_quantity);
        }).fail(function () {
            console.warn("Could not load cart count.");
        });

        // Nếu nơi khác cần cập nhật realtime, chỉ việc bắn event này:
        // $(document).trigger('cart:count-refresh', { qty: 5 });
        $(document).on('cart:count-refresh', function (e, payload) {
            renderQty(payload?.qty);
        });
    });
    </script>
    @endpush


    @stack('js')
</body>
</html>