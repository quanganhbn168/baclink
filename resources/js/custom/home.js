/* resources/js/custom/home.js */

document.addEventListener('DOMContentLoaded', function () {
    // Custom phone validation
    if ($.validator) {
        $.validator.addMethod("phoneVN", function (value, element) {
            return this.optional(element) || /^(0[3|5|7|8|9])[0-9]{8}$|^\+84[3|5|7|8|9][0-9]{8}$/.test(value);
        }, "Số điện thoại không hợp lệ");
    }

    // Contact Form Validation
    const contactForm = $('#contact-form');
    if (contactForm.length) {
        contactForm.validate({
            rules: {
                name: { required: true, minlength: 2 },
                phone: { required: true, phoneVN: true },
                email: { email: true },
                message: { maxlength: 1000 }
            },
            messages: {
                name: { required: "Vui lòng nhập họ và tên", minlength: "Tên quá ngắn" },
                phone: { required: "Vui lòng nhập số điện thoại", phoneVN: "Số điện thoại không hợp lệ (ví dụ: 098xxxxxxx)" },
                email: { email: "Email không hợp lệ" },
                message: { maxlength: "Ý kiến không vượt quá 1000 ký tự" }
            },
            errorElement: 'small',
            errorClass: 'text-danger',
            highlight: function (element) { $(element).addClass('is-invalid'); },
            unhighlight: function (element) { $(element).removeClass('is-invalid'); }
        });
    }

    // Swiper for products
    if (document.querySelector('.product-slider')) {
        new Swiper('.product-slider', {
            loop: true, spaceBetween: 25, slidesPerView: 1, autoplay: { delay: 5000 },
            breakpoints: { 576: { slidesPerView: 2 }, 768: { slidesPerView: 3 }, 992: { slidesPerView: 4 } },
            pagination: { el: '.product-slider .swiper-pagination', clickable: true },
            navigation: { nextEl: '.product-slider .swiper-button-next', prevEl: '.product-slider .swiper-button-prev' },
        });
    }

    // Featured News Slider (Horizontal - Left)
    if (document.querySelector('.featured-news-slider')) {
        new Swiper('.featured-news-slider', {
            loop: true, autoplay: { delay: 5000 },
            pagination: { el: '.featured-news-slider .swiper-pagination', clickable: true },
        });
    }

    // Vertical News Slider (Right)
    if (document.querySelector('.news-vertical-slider')) {
        new Swiper('.news-vertical-slider', {
            direction: 'vertical', slidesPerView: 2.5, spaceBetween: 20, loop: true,
            autoplay: { delay: 3000, pauseOnMouseEnter: true },
            navigation: { nextEl: '.swiper-button-next-custom', prevEl: '.swiper-button-prev-custom' },
            breakpoints: {
                320: { slidesPerView: 2.5, spaceBetween: 10 },
                768: { slidesPerView: 2.5, spaceBetween: 15 },
                1024: { slidesPerView: 2.5, spaceBetween: 20 }
            }
        });
    }

    // Partner Marquee
    document.querySelectorAll('.partner-marquee-single').forEach(el => {
        new Swiper(el, {
            slidesPerView: 2, spaceBetween: 30, loop: true, speed: 5000, allowTouchMove: false,
            autoplay: { delay: 0, disableOnInteraction: false },
            breakpoints: { 576: { slidesPerView: 3 }, 768: { slidesPerView: 4 }, 1024: { slidesPerView: 6 } },
        });
    });

    // Event Photo Slider
    if (document.querySelector('.event-photo-slider')) {
        new Swiper('.event-photo-slider', {
            effect: "coverflow", grabCursor: true, centeredSlides: true, slidesPerView: "auto", loop: true,
            coverflowEffect: { rotate: 0, stretch: 0, depth: 100, modifier: 2.5, slideShadows: true },
            pagination: { el: ".event-photo-slider .swiper-pagination", clickable: true },
            autoplay: { delay: 3000, disableOnInteraction: false },
        });
    }

    // Fields of Activity Slider
    if (document.querySelector('.fields-slider')) {
        new Swiper('.fields-slider', {
            loop: true, slidesPerView: 1, spaceBetween: 20, autoplay: { delay: 4000 },
            pagination: { el: '.fields-slider .swiper-pagination', clickable: true },
            breakpoints: { 576: { slidesPerView: 2, spaceBetween: 20 }, 768: { slidesPerView: 3, spaceBetween: 25 }, 1024: { slidesPerView: 4, spaceBetween: 30 } }
        });
    }
});
