/**
 * SOFIR Blocks - Frontend JavaScript
 * Handles all block interactions and animations
 */

(function($) {
    'use strict';

    var SofirBlocks = {
        /**
         * Initialize all blocks
         */
        init: function() {
            this.initSliders();
            this.initTestimonialSliders();
            this.initFAQAccordions();
            this.initCountdowns();
            this.initProgressBars();
            this.initSocialShare();
            this.initMobileMenu();
            this.initLazyLoading();
            this.initAccessibility();
        },

        /**
         * Initialize slider blocks
         */
        initSliders: function() {
            $('.sofir-slider').each(function() {
                var $slider = $(this);
                var $track = $slider.find('.sofir-slider-track');
                var $slides = $track.find('.sofir-slider-slide');
                var currentSlide = 0;
                var slideCount = $slides.length;
                var autoplay = $slider.data('autoplay') !== false;
                var interval = $slider.data('interval') || 5000;

                if (slideCount <= 1) return;

                function goToSlide(index) {
                    currentSlide = (index + slideCount) % slideCount;
                    $track.css('transform', 'translateX(-' + (currentSlide * 100) + '%)');
                    $slider.trigger('sofir:slider:change', [currentSlide]);
                }

                $slider.find('.sofir-slider-prev').on('click', function(e) {
                    e.preventDefault();
                    goToSlide(currentSlide - 1);
                });

                $slider.find('.sofir-slider-next').on('click', function(e) {
                    e.preventDefault();
                    goToSlide(currentSlide + 1);
                });

                if (autoplay) {
                    setInterval(function() {
                        goToSlide(currentSlide + 1);
                    }, interval);
                }

                // Touch support
                var touchStartX = 0;
                var touchEndX = 0;

                $slider.on('touchstart', function(e) {
                    touchStartX = e.changedTouches[0].screenX;
                });

                $slider.on('touchend', function(e) {
                    touchEndX = e.changedTouches[0].screenX;
                    if (touchStartX - touchEndX > 50) {
                        goToSlide(currentSlide + 1);
                    } else if (touchEndX - touchStartX > 50) {
                        goToSlide(currentSlide - 1);
                    }
                });
            });
        },

        /**
         * Initialize testimonial sliders
         */
        initTestimonialSliders: function() {
            $('.sofir-testimonial-slider').each(function() {
                var $slider = $(this);
                var $items = $slider.find('.sofir-testimonial-item');
                var currentItem = 0;
                var itemCount = $items.length;

                if (itemCount <= 1) return;

                $items.hide().eq(0).show();

                function showItem(index) {
                    currentItem = (index + itemCount) % itemCount;
                    $items.fadeOut(300).eq(currentItem).fadeIn(300);
                }

                // Auto-rotate every 7 seconds
                setInterval(function() {
                    showItem(currentItem + 1);
                }, 7000);

                // Add navigation dots
                var $dots = $('<div class="sofir-testimonial-dots"></div>');
                for (var i = 0; i < itemCount; i++) {
                    $dots.append('<button class="sofir-testimonial-dot" data-index="' + i + '"></button>');
                }
                $slider.append($dots);

                $dots.find('.sofir-testimonial-dot').on('click', function() {
                    showItem($(this).data('index'));
                });
            });
        },

        /**
         * Initialize FAQ accordions
         */
        initFAQAccordions: function() {
            $('.sofir-faq-question').on('click', function() {
                var $item = $(this).closest('.sofir-faq-item');
                var $answer = $item.find('.sofir-faq-answer');
                var isActive = $item.hasClass('active');

                // Close all other items
                $('.sofir-faq-item.active').not($item).removeClass('active')
                    .find('.sofir-faq-answer').slideUp(300);

                // Toggle current item
                if (isActive) {
                    $item.removeClass('active');
                    $answer.slideUp(300);
                } else {
                    $item.addClass('active');
                    $answer.slideDown(300);
                }
            });
        },

        /**
         * Initialize countdown blocks
         */
        initCountdowns: function() {
            $('.sofir-countdown').each(function() {
                var $countdown = $(this);
                var targetDate = new Date($countdown.data('target')).getTime();

                if (!targetDate) return;

                function updateCountdown() {
                    var now = new Date().getTime();
                    var distance = targetDate - now;

                    if (distance < 0) {
                        $countdown.find('.sofir-countdown-value').text('0');
                        return;
                    }

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $countdown.find('[data-unit="days"] .sofir-countdown-value').text(days);
                    $countdown.find('[data-unit="hours"] .sofir-countdown-value').text(hours);
                    $countdown.find('[data-unit="minutes"] .sofir-countdown-value').text(minutes);
                    $countdown.find('[data-unit="seconds"] .sofir-countdown-value').text(seconds);
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
        },

        /**
         * Initialize progress bars with animation
         */
        initProgressBars: function() {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var $fill = $(entry.target).find('.sofir-progress-fill');
                        var percentage = $fill.data('percentage') || 0;
                        $fill.css('width', percentage + '%');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            $('.sofir-progress-bar').each(function() {
                observer.observe(this);
            });
        },

        /**
         * Initialize social share buttons
         */
        initSocialShare: function() {
            $('.sofir-social-share-button').on('click', function(e) {
                var platform = $(this).data('platform');
                var url = encodeURIComponent(window.location.href);
                var title = encodeURIComponent(document.title);
                var shareUrl = '';

                switch(platform) {
                    case 'facebook':
                        shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
                        break;
                    case 'twitter':
                        shareUrl = 'https://twitter.com/intent/tweet?url=' + url + '&text=' + title;
                        break;
                    case 'linkedin':
                        shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + url;
                        break;
                    case 'whatsapp':
                        shareUrl = 'https://wa.me/?text=' + title + ' ' + url;
                        break;
                }

                if (shareUrl) {
                    e.preventDefault();
                    window.open(shareUrl, 'share', 'width=600,height=400');
                }
            });
        },

        /**
         * Initialize mobile menu toggle
         */
        initMobileMenu: function() {
            $('.sofir-navbar-toggle').on('click', function() {
                $(this).closest('.sofir-navbar').find('.sofir-navbar-menu').slideToggle(300);
            });
        },

        /**
         * Initialize lazy loading for images
         */
        initLazyLoading: function() {
            if ('IntersectionObserver' in window) {
                var imageObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                img.removeAttribute('data-src');
                            }
                            imageObserver.unobserve(img);
                        }
                    });
                });

                $('.sofir-block img[data-src]').each(function() {
                    imageObserver.observe(this);
                });
            }
        },

        /**
         * Initialize accessibility features
         */
        initAccessibility: function() {
            // Keyboard navigation for sliders
            $('.sofir-slider').on('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    $(this).find('.sofir-slider-prev').click();
                } else if (e.key === 'ArrowRight') {
                    $(this).find('.sofir-slider-next').click();
                }
            });

            // ARIA labels for dynamic content
            $('.sofir-faq-question').attr('role', 'button').attr('aria-expanded', 'false');
            $('.sofir-faq-item').on('click', '.sofir-faq-question', function() {
                var expanded = $(this).attr('aria-expanded') === 'true';
                $(this).attr('aria-expanded', !expanded);
            });
        }
    };

    // Initialize on DOM ready
    $(document).ready(function() {
        SofirBlocks.init();
    });

    // Re-initialize on AJAX content load
    $(document).on('sofir:content:loaded', function() {
        SofirBlocks.init();
    });

    // Expose to global scope for external access
    window.SofirBlocks = SofirBlocks;

})(jQuery);
