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
    <div class="main-header">
        <div class="container-fluid">
            <div class="main-header-inner">
                <div class="header-col-left">
                    <div class="mobile-menu-toggle d-lg-none">
                        <a href="#" aria-label="Toggle Menu"><i class="fa fa-bars"></i></a>
                    </div>
                    <div class="logo d-none d-lg-block">
                        <a href="{{ url('/') }}">
                            <img src="{{asset($setting->logo)}}" alt="Logo">
                        </a>
                    </div>
                </div>
                <div class="header-col-center">
                    <div class="logo text-center d-lg-none">
                        <a href="{{ url('/') }}">
                            <img src="{{asset($setting->logo)}}" alt="Logo">
                        </a>
                    </div>
                    <ul class="main-menu-desktop d-none d-lg-flex" id="main-menu-desktop-source">
                        @foreach($headerMenu as $menuItem)
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
                    </ul>
                </div>
                <div class="header-col-right d-none d-lg-flex">
                    <a href="{{ route('register') }}" class="btn btn-red-cta">ĐĂNG KÝ HỘI VIÊN</a>
                </div>
                </div>
                <div class="header-col-right">
                    <ul class="box-header d-flex d-md-none justify-content-center align-items-center mb-0">
                        <li>
    @auth
        {{-- Nội dung chỉ hiện khi ĐÃ đăng nhập --}}
        <a href="{{ route('user.dashboard') }}">
            <i class="fa-regular fa-user"></i> 
        </a>
    @endauth

    @guest
        {{-- Nội dung chỉ hiện khi CHƯA đăng nhập (Khách) --}}
        <a href="{{ route('login') }}">
            <i class="fa-regular fa-user"></i>
        </a>
    @endguest
</li>
                        <li class="cart-icon-wrapper position-relative">
                            <a href="{{ route('cart.page') }}">
                                <i class="fa-solid fa-bag-shopping"></i>
                                <span class="cart-count" data-role="cart-count" data-place="mobile" aria-hidden="true">0</span>
                            </a>
                        </li>
                    </ul>
                    <div class="d-none d-lg-block">
                        <a href="{{ route('register') }}" class="text-white">Đăng ký tài khoản</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mobile-search-container d-lg-none">
        <div class="search-box">
            <form action="/search" method="get">
                <input type="text" class="form-control" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit"><i class="fa fa-search"></i></button>
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
<div class="cart-offcanvas-wrapper">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Giỏ Hàng Của Bạn</h5>
        <a href="#" class="offcanvas-close js-close-cart"><i class="fa fa-times"></i></a>
    </div>
    @auth('web')
        <div class="offcanvas-body">
            @forelse($cartItems as $item)
            <div class="cart-item cart-item-auth">
                <div class="cart-item_image">
                    <img src="{{ asset($item->product->image ?? 'https://placehold.co/100x100') }}" alt="{{ $item->product->name }}">
                </div>
                <div class="cart-item_info">
                    <a href="{{ route('frontend.product.show', $item->product->slug) }}" class="item-name">{{ $item->product->name }}</a>
                    <div class="item-meta">
                        <span class="item-price">{{ number_format($item->product->price) }}đ</span>
                        <span class="item-quantity">x {{ $item->quantity }}</span>
                    </div>
                </div>
                <a href="#" class="item-remove" title="Xóa sản phẩm" data-item-id="{{ $item->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
            @empty
            <p class="text-center p-4">Giỏ hàng của bạn đang trống.</p>
            @endforelse
        </div>
        <div class="offcanvas-footer">
            <div class="cart-summary">
                <span>Tổng cộng:</span>
                <span class="total-price">{{ number_format($cartTotal ?? 0) }}đ</span>
            </div>
            <a href="/cart" class="btn btn-dark w-100">Xem Giỏ Hàng</a>
            <a href="/checkout" class="btn btn-primary w-100 mt-2">Thanh Toán</a>
        </div>
    @else
        <div id="guest-cart-body" class="offcanvas-body">
            <p class="text-center p-4">Giỏ hàng của bạn đang trống.</p>
        </div>
        <div id="guest-cart-footer" class="offcanvas-footer" style="display: none;">
            <div class="cart-summary">
                <span>Tổng cộng:</span>
                <span id="guest-cart-total" class="total-price">0đ</span>
            </div>
            <a href="/cart" class="btn btn-dark w-100">Xem Giỏ Hàng</a>
            <a href="/checkout" class="btn bg-main w-100 mt-2">Thanh Toán</a>
        </div>
    @endauth   
</div>
<template id="guest-cart-item-template">
    <div class="cart-item">
        <div class="cart-item_image">
            <img src="__IMAGE__" alt="__NAME__">
        </div>
        <div class="cart-item_info">
            <a href="__URL__" class="item-name">__NAME__</a>
            <div class="item-variant text-muted small">__VARIANT__</div>
            <div class="item-meta">
                <span class="item-price">__PRICE__đ</span>
                <span class="item-quantity">x __QUANTITY__</span>
            </div>
        </div>
        <a href="#" class="item-remove" title="Xóa sản phẩm" data-item-id="__ID__">
            <i class="fa fa-trash"></i>
        </a>
    </div>
</template>
<div class="offcanvas-overlay"></div>
@push('js')
<script>
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
        if ($('.offcanvas-menu-content #main-menu-desktop-source').length === 0) {
    // Chỉ clone menu gốc có ID là "main-menu-desktop-source"
                $('#main-menu-desktop-source').clone()
            .removeAttr('id') // Xóa ID để tránh bị trùng lặp
            .removeClass('d-none d-lg-flex') // Xóa class ẩn/hiện của desktop
            .appendTo('.offcanvas-menu-content');
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
    // Bấm ra ngoài khu vực menu thì sẽ đóng menu lại
    $(document).on('click', function() {
        $('.header-actions .frame-fix').removeClass('active');
        $('#desktop-search-dropdown').addClass('d-none'); // Đóng search dropdown
    });
    
    // Desktop Search Toggle
    $('#desktop-search-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#desktop-search-dropdown').toggleClass('d-none');
        if (!$('#desktop-search-dropdown').hasClass('d-none')) {
            $('#desktop-search-dropdown input').focus();
        }
    });

    $('#desktop-search-dropdown').on('click', function(e) {
        e.stopPropagation(); // Prevent closing when clicking inside
    });
</script>
@endpush