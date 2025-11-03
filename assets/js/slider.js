(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var sliders = document.querySelectorAll('.sofir-slider');

        sliders.forEach(function(slider) {
            var track = slider.querySelector('.sofir-slider-track');
            var slides = track.querySelectorAll('.sofir-slide');
            var prevBtn = slider.querySelector('.sofir-slider-prev');
            var nextBtn = slider.querySelector('.sofir-slider-next');
            var currentIndex = 0;
            var autoplay = slider.dataset.autoplay === 'true';
            var interval = parseInt(slider.dataset.interval) || 5000;
            var autoplayTimer;

            function goToSlide(index) {
                currentIndex = (index + slides.length) % slides.length;
                track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';
            }

            function nextSlide() {
                goToSlide(currentIndex + 1);
            }

            function prevSlide() {
                goToSlide(currentIndex - 1);
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    prevSlide();
                    if (autoplay) {
                        clearInterval(autoplayTimer);
                        startAutoplay();
                    }
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    nextSlide();
                    if (autoplay) {
                        clearInterval(autoplayTimer);
                        startAutoplay();
                    }
                });
            }

            function startAutoplay() {
                if (autoplay) {
                    autoplayTimer = setInterval(nextSlide, interval);
                }
            }

            startAutoplay();
        });
    });
})();
