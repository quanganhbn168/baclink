/* resources/js/app.js */

import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

import '@popperjs/core';
import 'bootstrap';
import Swiper from 'swiper/bundle';
window.Swiper = Swiper;

import AOS from 'aos';
window.AOS = AOS;

import Swal from 'sweetalert2';
window.Swal = Swal;

import 'jquery-validation';

// Custom Scripts
import './custom/counter.js';
import './custom/TabbedSwiperHandler.js';
import './custom/home.js';
import './custom/product.js';

// Initialize AOS
document.addEventListener('DOMContentLoaded', function () {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false
    });
});

// Back to top logic
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
