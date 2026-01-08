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
    {{-- CSS & JS handled by Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --site-favicon: url('{{ asset($setting->favicon) }}');
        }
    </style>

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

    @if(session('success'))
    <script type="module">
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: @json(session('success')),
            confirmButtonText: 'OK'
        });
    </script>
    @endif
    @if(session('error'))
    <script type="module">
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: @json(session('error')),
            confirmButtonText: 'OK'
        });
    </script>
    @endif

    @stack('js')
</body>
</html>
