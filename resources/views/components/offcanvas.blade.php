@props([
    'id',
    'title' => 'Tiêu đề',
    'position' => 'left' // Thêm tùy chọn vị trí: 'left' hoặc 'right'
])

<div class="offcanvas-wrapper offcanvas-{{ $position }}" id="{{ $id }}" aria-hidden="true">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">{{ $title }}</h5>
        {{-- Nút đóng này sẽ được JS xử lý một cách tự động --}}
        <a href="#" class="offcanvas-close js-offcanvas-close" aria-label="Close">
            <i class="fa fa-times"></i>
        </a>
    </div>
    <div class="offcanvas-body">
        {{-- Đây là nơi nội dung tùy chỉnh của bạn sẽ được chèn vào --}}
        {{ $slot }}
    </div>
</div>
/* === CSS TỐI ƯU CHO OFF-CANVAS COMPONENT === */

/* Lớp phủ dùng chung cho tất cả */
.offcanvas-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s ease, visibility 0.4s ease;
}

/* Khung Off-canvas chung */
.offcanvas-wrapper {
    position: fixed;
    top: 0;
    width: 300px; /* Hoặc độ rộng bạn muốn */
    max-width: 90%;
    height: 100%;
    background-color: #fff;
    z-index: 1050;
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    transition: transform 0.4s ease;
}

/* Tùy chỉnh vị trí */
.offcanvas-wrapper.offcanvas-left {
    left: 0;
    transform: translateX(-100%); /* Ẩn về bên trái */
}
.offcanvas-wrapper.offcanvas-right {
    right: 0;
    transform: translateX(100%); /* Ẩn về bên phải */
}

/* Header và Body (Giữ nguyên hoặc tùy chỉnh lại) */
.offcanvas-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    background: var(--primary-color);
    color: white;
    flex-shrink: 0;
}
.offcanvas-header .offcanvas-title {
    margin: 0;
    font-size: 16px;
    font-family: var(--font-heading);
}
.offcanvas-header .offcanvas-close {
    font-size: 24px;
    color: white;
    opacity: 0.8;
}
.offcanvas-body {
    flex-grow: 1;
    overflow-y: auto;
    padding: 20px;
}
/* Xóa padding mặc định cho menu */
.offcanvas-body .offcanvas-menu-content {
    margin: -20px; /* Bù lại padding của .offcanvas-body */
}


/* Trạng thái KHI MỞ */
body.offcanvas-is-open .offcanvas-overlay {
    opacity: 1;
    visibility: visible;
}
/* Hiển thị off-canvas có class .is-open */
body.offcanvas-is-open .offcanvas-wrapper.is-open {
    transform: translateX(0);
}

// ... bên trong $(document).ready()

// --- CƠ CHẾ OFF-CANVAS TỔNG QUÁT ---

// 1. Kích hoạt mở off-canvas
$(document).on('click', '[data-toggle="offcanvas"]', function(e) {
    e.preventDefault();
    
    // Lấy ID của off-canvas mục tiêu từ thuộc tính data-target
    const targetId = $(this).data('target');
    const $target = $(targetId);
    
    if ($target.length) {
        // Thêm class vào body để hiện overlay và chặn cuộn
        $('body').addClass('offcanvas-is-open');
        // Thêm class vào đúng off-canvas mục tiêu để nó hiện ra
        $target.addClass('is-open');
    }
});

// 2. Kích hoạt đóng off-canvas (qua nút .js-offcanvas-close)
$(document).on('click', '.js-offcanvas-close', function(e) {
    e.preventDefault();
    
    // Tìm đến off-canvas cha và đóng nó lại
    $(this).closest('.offcanvas-wrapper').removeClass('is-open');
    
    // Kiểm tra xem còn off-canvas nào đang mở không
    // Nếu không, gỡ class khỏi body
    if ($('.offcanvas-wrapper.is-open').length === 0) {
        $('body').removeClass('offcanvas-is-open');
    }
});

// 3. Đóng khi click vào lớp phủ
$('.offcanvas-overlay').on('click', function() {
    // Đóng tất cả off-canvas đang mở
    $('.offcanvas-wrapper.is-open').removeClass('is-open');
    $('body').removeClass('offcanvas-is-open');
});

// --- CODE CŨ VẪN GIỮ LẠI (Chỉ giữ phần xử lý nội dung) ---

// Sao chép menu desktop vào menu mobile
if ($('.offcanvas-menu-content').length && $('.offcanvas-menu-content').is(':empty')) {
    $('.main-menu-desktop').clone().appendTo('.offcanvas-menu-content');
}
// Xử lý submenu trong menu mobile
$('.offcanvas-menu-content').on('click', '.submenu-toggle', function(e) {
    e.preventDefault();
    $(this).parent('.menu-item-has-children').toggleClass('open');
    $(this).siblings('.sub-menu').slideToggle(300);
});

{{-- Đặt ở cuối file layout, trước thẻ đóng </body> --}}

{{-- 1. Off-canvas cho Menu --}}
<x-offcanvas id="menu-offcanvas" title="DANH MỤC" position="left">
    {{-- Nội dung cho menu --}}
    <div class="offcanvas-menu-content">
        {{-- JavaScript sẽ tự động sao chép menu vào đây --}}
    </div>
</x-offcanvas>

{{-- 2. Off-canvas cho Giỏ hàng --}}
<x-offcanvas id="cart-offcanvas" title="GIỎ HÀNG CỦA BẠN" position="right">
    {{-- Đặt toàn bộ HTML của giỏ hàng chi tiết vào đây --}}
    <div class="cart-items-wrapper">
        <div class="cart-item">
            ...
        </div>
    </div>
    <div class="cart-summary">
        ...
    </div>
</x-offcanvas>

{{-- 3. Off-canvas cho Bộ lọc (ví dụ) --}}
{{--
<x-offcanvas id="filter-offcanvas" title="BỘ LỌC" position="left">
    <p>Các tùy chọn bộ lọc theo giá, thương hiệu...</p>
</x-offcanvas>
--}}

{{-- Lớp phủ dùng chung, chỉ cần đặt 1 lần --}}
<div class="offcanvas-overlay"></div>