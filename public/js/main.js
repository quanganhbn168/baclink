$(document).ready(function () {
    // === Sticky Header ===
    const headerMain = $('.header-main');
    const stickyOffset = headerMain.offset().top;
    let lastScrollTop = 0;

    $(window).on('scroll', function () {
        let scrollTop = $(this).scrollTop();

        if (scrollTop > stickyOffset) {
            headerMain.addClass('is-sticky');
        } else {
            headerMain.removeClass('is-sticky is-hidden');
        }

        if (scrollTop > lastScrollTop && scrollTop > stickyOffset + 50) {
            headerMain.addClass('is-hidden');
        } else if (scrollTop < lastScrollTop) {
            headerMain.removeClass('is-hidden');
        }

        lastScrollTop = scrollTop;

        // Go to top button toggle
        if (scrollTop > 200) {
            $('.gotop').addClass('show');
        } else {
            $('.gotop').removeClass('show');
        }

        // Trigger stats counter animation
        const statsSection = $('.section-stats');
        const sectionTop = statsSection.offset()?.top - window.innerHeight + 100;
        if (sectionTop && !statsSection.hasClass('counted') && scrollTop > sectionTop) {
            statsSection.addClass('counted');
            $('.stats-number').each(function () {
                const $el = $(this);
                const target = parseInt($el.data('target'));
                $({ countNum: 0 }).animate({ countNum: target }, {
                    duration: 1500,
                    easing: 'swing',
                    step: function () {
                        $el.text(Math.floor(this.countNum));
                    },
                    complete: function () {
                        $el.text(target.toLocaleString() + '+');
                    }
                });
            });
        }
    });

    // === Go to top click ===
    $('.gotop').on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 600);
    });

    // === Mobile nav toggle ===
    $('.menu-toggle').on('click', function () {
        $('#mobile-nav').addClass('active');
    });

    $('.close-nav').on('click', function () {
        $('#mobile-nav').removeClass('active');
    });

    // === Mobile submenu toggle ===
    $('.mobile-menu .toggle-sub').on('click', function (e) {
        e.preventDefault();
        var parent = $(this).closest('.has-sub');
        parent.toggleClass('open');
        $(this).text(parent.hasClass('open') ? '−' : '+');
    });

    // === Close mobile nav when click outside ===
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#mobile-nav, .menu-toggle').length) {
            $('#mobile-nav').removeClass('active');
        }
    });
});
