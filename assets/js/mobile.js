(function($) {
    'use strict';

    $(document).ready(function() {
        var mobileMenu = $('#sofir-mobile-menu');
        var menuToggle = $('.sofir-mobile-menu-toggle');
        var menuClose = $('.sofir-mobile-menu-close');
        var menuOverlay = $('.sofir-mobile-menu-overlay');

        menuToggle.on('click', function(e) {
            e.preventDefault();
            mobileMenu.addClass('is-active');
            $('body').addClass('sofir-mobile-menu-open');
        });

        function closeMenu() {
            mobileMenu.removeClass('is-active');
            $('body').removeClass('sofir-mobile-menu-open');
        }

        menuClose.on('click', closeMenu);
        menuOverlay.on('click', closeMenu);

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenu.hasClass('is-active')) {
                closeMenu();
            }
        });

        var bottomNav = $('.sofir-bottom-navbar');
        var lastScrollTop = 0;

        $(window).on('scroll', function() {
            var scrollTop = $(this).scrollTop();

            if (scrollTop > lastScrollTop && scrollTop > 100) {
                bottomNav.addClass('is-hidden');
            } else {
                bottomNav.removeClass('is-hidden');
            }

            lastScrollTop = scrollTop;
        });
    });
})(jQuery);
