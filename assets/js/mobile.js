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
            $(document).trigger('sofir:mobile-menu:open');
        });

        function closeMenu() {
            mobileMenu.removeClass('is-active');
            $('body').removeClass('sofir-mobile-menu-open');
            $(document).trigger('sofir:mobile-menu:close');
        }

        menuClose.on('click', closeMenu);
        menuOverlay.on('click', closeMenu);

        $('.sofir-mobile-nav a').on('click', function() {
            closeMenu();
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenu.hasClass('is-active')) {
                closeMenu();
            }
        });

        var bottomNav = $('.sofir-bottom-navbar');
        var lastScrollTop = 0;
        var currentUrl = window.location.href;

        $('.sofir-bottom-nav-item').each(function() {
            var itemUrl = $(this).attr('href');
            if (itemUrl && currentUrl.indexOf(itemUrl) !== -1 && itemUrl !== '#') {
                $(this).addClass('is-current');
            }
        });

        $(window).on('scroll', function() {
            var scrollTop = $(this).scrollTop();

            if (scrollTop > lastScrollTop && scrollTop > 100) {
                if (!bottomNav.hasClass('is-hidden')) {
                    bottomNav.addClass('is-hidden');
                    $(document).trigger('sofir:bottom-nav:hide');
                }
            } else {
                if (bottomNav.hasClass('is-hidden')) {
                    bottomNav.removeClass('is-hidden');
                    $(document).trigger('sofir:bottom-nav:show');
                }
            }

            lastScrollTop = scrollTop;
        });
    });
})(jQuery);
