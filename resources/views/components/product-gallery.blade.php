{{-- resources/views/components/product-gallery.blade.php --}}

@props(['images' => []])

{{-- Chỉ hiển thị component nếu có ảnh --}}
@if (!empty($images))
<div class="product-gallery-container">
    <div class="swiper swiper-thumbs">
        <div class="swiper-wrapper">
            @foreach ($images as $image)
                <div class="swiper-slide">
                    <img src="{{ $image }}" alt="Product Thumbnail">
                </div>
            @endforeach
        </div>
    </div>

    <div class="swiper swiper-main">
        <div class="swiper-wrapper">
            @foreach ($images as $image)
                <div class="swiper-slide">
                    <img src="{{ $image }}" alt="Product Image">
                </div>
            @endforeach
        </div>

        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
</div>


<style>
    /* CSS cho toàn bộ component */
    .product-gallery-container {
        display: flex;
        flex-direction: row;
        gap: 10px;
        height: 500px; /* Điều chỉnh chiều cao theo ý bạn */
    }

    /* Thumbs slider bên trái */
    .swiper-thumbs {
        height: 100%;
        width: 100px; /* Chiều rộng của cột thumbnail */
        padding: 5px 0;
    }

    .swiper-thumbs .swiper-slide {
        width: 100%;
        height: 100px !important; /* Chiều cao mỗi thumbnail */
        opacity: 0.6;
        cursor: pointer;
        border: 2px solid transparent;
        border-radius: 5px;
        overflow: hidden;
        transition: border 0.3s, opacity 0.3s;
    }
    
    .swiper-thumbs .swiper-slide:hover {
        opacity: 1;
    }

    /* Style cho thumbnail đang được chọn */
    .swiper-thumbs .swiper-slide-thumb-active {
        opacity: 1;
        border-color: #007aff; /* Màu viền của thumbnail active */
    }

    .swiper-thumbs .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Main slider bên phải */
    .swiper-main {
        width: calc(100% - 110px); /* Lấy phần còn lại của container */
        height: 100%;
        border-radius: 5px;
    }

    .swiper-main .swiper-slide {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .swiper-main .swiper-slide img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    
    /* Tùy chỉnh nút next/prev nếu bạn dùng */
    .swiper-main .swiper-button-next,
    .swiper-main .swiper-button-prev {
        color: #007aff;
    }
</style>


<script>
    // Đảm bảo script chỉ chạy sau khi DOM đã tải xong
    document.addEventListener('DOMContentLoaded', function () {
        // Khởi tạo Swiper cho thumbnails
        const swiperThumbs = new Swiper('.swiper-thumbs', {
            direction: 'vertical', // Chuyển slider thành dạng dọc
            spaceBetween: 10,
            slidesPerView: 'auto',
            freeMode: true,
            watchSlidesProgress: true,
        });

        // Khởi tạo Swiper cho slider chính và liên kết với thumbnails
        const swiperMain = new Swiper('.swiper-main', {
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            thumbs: {
                swiper: swiperThumbs, // Liên kết với swiperThumbs
            },
        });
    });
</script>
@endif