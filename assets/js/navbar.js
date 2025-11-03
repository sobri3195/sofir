(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var navbar = document.querySelector('.sofir-navbar');
        if (!navbar) return;

        var toggle = navbar.querySelector('.sofir-mobile-toggle');
        var menu = navbar.querySelector('.sofir-navbar-menu');

        if (toggle) {
            toggle.addEventListener('click', function() {
                menu.classList.toggle('is-active');
                toggle.classList.toggle('is-active');
            });
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                menu.classList.remove('is-active');
                toggle.classList.remove('is-active');
            }
        });
    });
})();
