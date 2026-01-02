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
        <div class="container">
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