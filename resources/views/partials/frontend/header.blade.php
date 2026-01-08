<div class="header-top" role="region" aria-label="Announcement bar">
  <div class="header-top__inner">
    <div class="header-top__contacts">
        <a href="tel:{{ $setting->phone }}"><i class="fa fa-phone"></i> {{ $setting->phone }}</a>
        <a href="mailto:{{ $setting->email }}"><i class="fa fa-envelope"></i> {{ $setting->email }}</a>
    </div>
    <div class="header-top__right">
        <div class="language-switcher">
            <a href="#" class="active">VN</a> | <a href="#">EN</a>
        </div>
        <div class="header-top__search d-none d-lg-block">
            <form action="{{ route('frontend.search') }}" method="GET">
                <input type="text" name="q" placeholder="Tìm kiếm...">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </div>
  </div>
</div>
<header class="header">
    <!-- Header Top -->
    <div class="main-header" style="overflow: visible;">
        <div class="container">
            <div class="main-header-inner row align-items-center position-relative">
                
                {{-- Mobile Toggle --}}
                <div class="mobile-menu-toggle d-lg-none col-2 text-left">
                    <a href="#" aria-label="Toggle Menu"><i class="fa fa-bars"></i></a>
                </div>

                {{-- Logo Mobile --}}
                <div class="logo text-center d-lg-none col-8 mx-auto">
                    <a href="{{ url('/') }}" class="d-inline-block">
                        <img src="{{asset($setting->logo)}}" alt="Logo" class="img-fluid" style="max-height: 50px; margin: 0 auto;">
                    </a>
                </div>
                
                {{-- Mobile Search Toggle --}}
                <div class="mobile-search-toggle d-lg-none col-2 text-right">
                    <a href="#" aria-label="Toggle Search" id="mobile-search-trigger"><i class="fa fa-search"></i></a>
                </div>

                @php
                    $menuItems = collect($headerMenu);
                    $midPoint = ceil($menuItems->count() / 2);
                    $leftMenu = $menuItems->slice(0, $midPoint);
                    $rightMenu = $menuItems->slice($midPoint);
                @endphp

                {{-- LEFT MENU --}}
                <div class="col-lg-5 d-none d-lg-flex align-items-center justify-content-end pr-4">
                    <ul class="main-menu-desktop d-flex justify-content-end mb-0">
                        @foreach($leftMenu as $menuItem)
                        <li class="{{ !empty($menuItem['children']) ? 'menu-item-has-children' : '' }}">
                            <a href="{{ $menuItem['url'] }}" target="{{ $menuItem['target'] ?? '_self' }}">{{ $menuItem['title'] }}</a>
                            @if(!empty($menuItem['children']))
                            <span class="submenu-toggle"><i class="fa fa-angle-down"></i></span>
                            <ul class="sub-menu">
                                @foreach($menuItem['children'] as $childItem)
                                <li class="{{ !empty($childItem['children']) ? 'menu-item-has-children' : '' }}">
                                    <a href="{{ $childItem['url'] }}" target="{{ $childItem['target'] ?? '_self' }}">{{ $childItem['title'] }}</a>
                                    @if(!empty($childItem['children']))
                                    <span class="submenu-toggle"><i class="fa fa-angle-right"></i></span>
                                    <ul class="sub-menu">
                                        @foreach($childItem['children'] as $grandChildItem)
                                        <li><a href="{{ $grandChildItem['url'] }}" target="{{ $grandChildItem['target'] ?? '_self' }}">{{ $grandChildItem['title'] }}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- CENTER LOGO (Standard) --}}
                <div class="col-lg-2 d-none d-lg-flex justify-content-center align-items-center">
                     <div class="logo text-center">
                         <a href="{{ url('/') }}" class="d-block">
                             <img src="{{asset($setting->logo)}}" alt="Logo" class="img-fluid">
                         </a>
                     </div>
                </div>

                {{-- RIGHT MENU --}}
                <div class="col-lg-5 d-none d-lg-flex align-items-center justify-content-start pl-4 flex-nowrap">
                    <ul class="main-menu-desktop d-flex justify-content-start mb-0">
                        @foreach($rightMenu as $menuItem)
                        <li class="{{ !empty($menuItem['children']) ? 'menu-item-has-children' : '' }}">
                            <a href="{{ $menuItem['url'] }}" target="{{ $menuItem['target'] ?? '_self' }}">{{ $menuItem['title'] }}</a>
                            @if(!empty($menuItem['children']))
                            <span class="submenu-toggle"><i class="fa fa-angle-down"></i></span>
                            <ul class="sub-menu">
                                @foreach($menuItem['children'] as $childItem)
                                <li class="{{ !empty($childItem['children']) ? 'menu-item-has-children' : '' }}">
                                    <a href="{{ $childItem['url'] }}" target="{{ $childItem['target'] ?? '_self' }}">{{ $childItem['title'] }}</a>
                                    @if(!empty($childItem['children']))
                                    <span class="submenu-toggle"><i class="fa fa-angle-right"></i></span>
                                    <ul class="sub-menu">
                                        @foreach($childItem['children'] as $grandChildItem)
                                        <li><a href="{{ $grandChildItem['url'] }}" target="{{ $grandChildItem['target'] ?? '_self' }}">{{ $grandChildItem['title'] }}</a></li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    
                    {{-- CTA Button --}}
                    <div class="ml-3">
                        <a href="{{ route('register') }}" class="btn btn-gold-cta btn-sm text-nowrap px-3">ĐĂNG KÝ HỘI VIÊN</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- Mobile Search Dropdown (Hidden by default) --}}
    <div id="mobile-search-dropdown" class="d-none animate__animated animate__fadeInDown" style="position: absolute; top: 100%; left: 0; width: 100%; background: #fff; padding: 15px; box-shadow: 0 10px 15px rgba(0,0,0,0.1); z-index: 1000;">
        <div class="search-box">
            <form action="{{ route('frontend.search') }}" method="get">
                <input type="text" name="q" class="form-control" placeholder="nhập từ khóa tìm kiếm" style="border: 1px solid var(--gold); border-radius: 4px;">
                <button type="submit" style="background: var(--gold); color: #fff; border: none; padding: 0 15px; border-radius: 0 4px 4px 0;"><i class="fa fa-search"></i></button>
            </form>
        </div>
    </div>
    <nav class="main-nav-container d-none"></nav>
</header>
<div class="offcanvas-menu-wrapper">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">MENU</h5>
        <a href="#" class="offcanvas-close"><i class="fa fa-times"></i></a>
    </div>
    <div class="offcanvas-menu-content">
        </div>
</div>
<div class="offcanvas-overlay"></div>
@push('js')
<script type="module">
    $(document).ready(function() {
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            const scrollPosition = window.scrollY;
            if (scrollPosition > 50) { 
                header.classList.add('header-scrolled');
                header.classList.remove('is-unsticking'); 
            } else {
                if (header.classList.contains('header-scrolled')) {
                    header.classList.remove('header-scrolled');
                    header.classList.add('is-unsticking');
                    setTimeout(function() {
                        header.classList.remove('is-unsticking');
                    }, 20);
                }
            }
        });
        if ($('.offcanvas-menu-content .main-menu-mobile').length === 0) {
            const $mobileMenu = $('<ul class="main-menu-mobile"></ul>');
            $('.main-menu-desktop').each(function() {
                $(this).children().clone().appendTo($mobileMenu);
            });
            $mobileMenu.appendTo('.offcanvas-menu-content');
        }
        $('.mobile-menu-toggle a').on('click', function(e) {
            e.preventDefault();
            $('body').addClass('show-offcanvas');
        });
        $('.offcanvas-menu-content').on('click', '.submenu-toggle', function(e) {
            e.preventDefault();
            $(this).parent('.menu-item-has-children').toggleClass('open');
            $(this).siblings('.sub-menu').slideToggle(300);
        });
        $('.cart-action > a').on('click', function(e) {
            e.preventDefault(); 
            $('body').addClass('show-cart-offcanvas');
        });
        $('.offcanvas-menu-wrapper .offcanvas-close').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('show-offcanvas');
        });
        $('.cart-offcanvas-wrapper .js-close-cart').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('show-cart-offcanvas');
        });
        $('.offcanvas-overlay').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('show-offcanvas show-cart-offcanvas');
        });
    });
    $('.header-actions .frame-fix').on('click', function(event) {
    // Ngăn sự kiện click lan ra ngoài, tránh việc tự đóng ngay lập tức
        event.stopPropagation(); 
    // Thêm/xóa class 'active' trên chính nó để bật/tắt menu
        $(this).toggleClass('active'); 
    });
    // Mobile Search Toggle
    $('#mobile-search-trigger').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#mobile-search-dropdown').toggleClass('d-none');
        if (!$('#mobile-search-dropdown').hasClass('d-none')) {
            $('#mobile-search-dropdown input').focus();
        }
    });

    // Desktop Search Toggle (Keep for safety or consolidate)
    $('#desktop-search-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#desktop-search-dropdown').toggleClass('d-none');
        if (!$('#desktop-search-dropdown').hasClass('d-none')) {
            $('#desktop-search-dropdown input').focus();
        }
    });

    // Close logic
    $(document).on('click', function() {
        $('.header-actions .frame-fix').removeClass('active');
        $('#mobile-search-dropdown').addClass('d-none'); 
        $('#desktop-search-dropdown').addClass('d-none');
    });

    $('#mobile-search-dropdown, #desktop-search-dropdown').on('click', function(e) {
        e.stopPropagation(); 
    });

    $('#desktop-search-dropdown').on('click', function(e) {
        e.stopPropagation(); // Prevent closing when clicking inside
    });
</script>
@endpush
