(function() {
    'use strict';

    // Gallery Widget Handler
    class SofirGallery {
        constructor(element) {
            this.element = element;
            this.lightboxSettings = JSON.parse(element.dataset.lightbox || '{}');
            this.layout = element.dataset.layout;
            this.init();
        }

        init() {
            if (this.lightboxSettings.enable) {
                this.initLightbox();
            }
            
            if (this.layout === 'masonry') {
                this.initMasonry();
            }
        }

        initLightbox() {
            const links = this.element.querySelectorAll('.sofir-gallery-link');
            const images = Array.from(links).map(link => ({
                url: link.href,
                caption: link.dataset.caption || '',
                title: link.dataset.title || ''
            }));

            links.forEach((link, index) => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.openLightbox(images, index);
                });
            });
        }

        initMasonry() {
            setTimeout(() => {
                const items = this.element.querySelectorAll('.sofir-gallery-item');
                items.forEach(item => {
                    item.style.opacity = '1';
                });
            }, 100);
        }

        openLightbox(images, startIndex) {
            const lightbox = new SofirLightbox(images, startIndex, this.lightboxSettings);
            lightbox.open();
        }
    }

    // Slideshow Widget Handler
    class SofirSlideshow {
        constructor(element) {
            this.element = element;
            this.settings = JSON.parse(element.dataset.settings || '{}');
            this.currentIndex = 0;
            this.items = element.querySelectorAll('.sofir-slideshow-item');
            this.isPlaying = false;
            this.autoplayTimer = null;
            this.init();
        }

        init() {
            this.setupNavigation();
            this.setupPagination();
            this.setupKeyboard();
            this.setupTouch();
            
            if (this.settings.pauseOnHover) {
                this.setupHoverPause();
            }

            if (this.settings.autoplay) {
                this.startAutoplay();
            }
        }

        setupNavigation() {
            const prevBtn = this.element.querySelector('.sofir-slideshow-prev');
            const nextBtn = this.element.querySelector('.sofir-slideshow-next');

            if (prevBtn) {
                prevBtn.addEventListener('click', () => this.prev());
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', () => this.next());
            }
        }

        setupPagination() {
            const dots = this.element.querySelectorAll('[class*="sofir-pagination-"]');
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goTo(index));
            });
        }

        setupKeyboard() {
            if (this.settings.keyboard) {
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft') this.prev();
                    if (e.key === 'ArrowRight') this.next();
                });
            }
        }

        setupTouch() {
            if (this.settings.swipe) {
                let touchStartX = 0;
                let touchEndX = 0;

                this.element.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                });

                this.element.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    this.handleSwipe(touchStartX, touchEndX);
                });
            }
        }

        handleSwipe(startX, endX) {
            const diff = startX - endX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    this.next();
                } else {
                    this.prev();
                }
            }
        }

        setupHoverPause() {
            this.element.addEventListener('mouseenter', () => {
                this.stopAutoplay();
            });

            this.element.addEventListener('mouseleave', () => {
                if (this.settings.autoplay) {
                    this.startAutoplay();
                }
            });
        }

        startAutoplay() {
            this.isPlaying = true;
            this.autoplayTimer = setInterval(() => {
                this.next();
            }, this.settings.autoplaySpeed || 3000);
        }

        stopAutoplay() {
            this.isPlaying = false;
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        }

        goTo(index) {
            this.items[this.currentIndex].classList.remove('active');
            
            const dots = this.element.querySelectorAll('[class*="sofir-pagination-"]');
            if (dots[this.currentIndex]) {
                dots[this.currentIndex].classList.remove('active');
            }

            this.currentIndex = index;

            this.items[this.currentIndex].classList.add('active');
            if (dots[this.currentIndex]) {
                dots[this.currentIndex].classList.add('active');
            }
        }

        next() {
            let nextIndex = this.currentIndex + 1;
            if (nextIndex >= this.items.length) {
                nextIndex = this.settings.loop ? 0 : this.currentIndex;
            }
            this.goTo(nextIndex);
        }

        prev() {
            let prevIndex = this.currentIndex - 1;
            if (prevIndex < 0) {
                prevIndex = this.settings.loop ? this.items.length - 1 : 0;
            }
            this.goTo(prevIndex);
        }
    }

    // Filmstrip Gallery Widget Handler
    class SofirFilmstrip {
        constructor(element) {
            this.element = element;
            this.settings = JSON.parse(element.dataset.settings || '{}');
            this.track = element.querySelector('.sofir-filmstrip-track');
            this.items = element.querySelectorAll('.sofir-filmstrip-item');
            this.currentPosition = 0;
            this.itemWidth = 0;
            this.init();
        }

        init() {
            this.calculateItemWidth();
            this.setupNavigation();
            
            if (this.settings.autoplay) {
                this.startAutoplay();
            }

            if (this.settings.pauseOnHover) {
                this.setupHoverPause();
            }

            window.addEventListener('resize', () => this.calculateItemWidth());
        }

        calculateItemWidth() {
            if (this.items.length > 0) {
                const containerWidth = this.element.offsetWidth;
                const itemsToShow = this.getItemsToShow();
                this.itemWidth = containerWidth / itemsToShow;
                
                this.items.forEach(item => {
                    item.style.width = this.itemWidth + 'px';
                });
            }
        }

        getItemsToShow() {
            const width = window.innerWidth;
            if (width <= 768) {
                return this.settings.mobileItems || 2;
            } else if (width <= 1024) {
                return this.settings.tabletItems || 3;
            }
            return this.settings.itemsToShow || 4;
        }

        setupNavigation() {
            const prevBtn = this.element.querySelector('.sofir-filmstrip-prev');
            const nextBtn = this.element.querySelector('.sofir-filmstrip-next');

            if (prevBtn) {
                prevBtn.addEventListener('click', () => this.prev());
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', () => this.next());
            }
        }

        setupHoverPause() {
            this.element.addEventListener('mouseenter', () => {
                this.stopAutoplay();
            });

            this.element.addEventListener('mouseleave', () => {
                if (this.settings.autoplay) {
                    this.startAutoplay();
                }
            });
        }

        startAutoplay() {
            this.autoplayTimer = setInterval(() => {
                this.next();
            }, this.settings.autoplaySpeed || 3000);
        }

        stopAutoplay() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        }

        next() {
            const scrollAmount = this.itemWidth * (this.settings.itemsToScroll || 1);
            const maxPosition = -(this.track.scrollWidth - this.element.offsetWidth);
            
            this.currentPosition -= scrollAmount;
            
            if (this.currentPosition < maxPosition) {
                this.currentPosition = this.settings.loop ? 0 : maxPosition;
            }
            
            this.updatePosition();
        }

        prev() {
            const scrollAmount = this.itemWidth * (this.settings.itemsToScroll || 1);
            
            this.currentPosition += scrollAmount;
            
            if (this.currentPosition > 0) {
                const maxPosition = -(this.track.scrollWidth - this.element.offsetWidth);
                this.currentPosition = this.settings.loop ? maxPosition : 0;
            }
            
            this.updatePosition();
        }

        updatePosition() {
            this.track.style.transform = 'translateX(' + this.currentPosition + 'px)';
        }
    }

    // Album Widget Handler
    class SofirAlbum {
        constructor(element) {
            this.element = element;
            this.enableLightbox = element.dataset.lightbox === 'true';
            this.init();
        }

        init() {
            if (this.enableLightbox) {
                this.setupAlbumClick();
            }
        }

        setupAlbumClick() {
            const albums = this.element.querySelectorAll('.sofir-album-item');
            albums.forEach(album => {
                album.addEventListener('click', (e) => {
                    if (!e.target.closest('a')) {
                        this.openAlbum(album);
                    }
                });
            });
        }

        openAlbum(albumElement) {
            const imageLinks = albumElement.querySelectorAll('.sofir-album-image-link');
            const images = Array.from(imageLinks).map(link => ({
                url: link.href,
                caption: link.dataset.caption || ''
            }));

            if (images.length > 0) {
                const lightbox = new SofirLightbox(images, 0, { enable: true });
                lightbox.open();
            }
        }
    }

    // Advanced Lightbox
    class SofirLightbox {
        constructor(images, startIndex, settings) {
            this.images = images;
            this.currentIndex = startIndex;
            this.settings = settings;
            this.element = null;
        }

        open() {
            this.create();
            this.show(this.currentIndex);
            this.setupEvents();
            
            if (this.settings.autoplay) {
                this.startAutoplay();
            }
        }

        create() {
            this.element = document.createElement('div');
            this.element.className = 'sofir-lightbox';
            this.element.innerHTML = `
                <button class="sofir-lightbox-close" aria-label="Close">&times;</button>
                ${this.settings.counter ? '<div class="sofir-lightbox-counter"></div>' : ''}
                <div class="sofir-lightbox-content">
                    <img class="sofir-lightbox-image" src="" alt="">
                    <div class="sofir-lightbox-caption"></div>
                </div>
                <button class="sofir-lightbox-nav sofir-lightbox-prev" aria-label="Previous">&lsaquo;</button>
                <button class="sofir-lightbox-nav sofir-lightbox-next" aria-label="Next">&rsaquo;</button>
            `;
            document.body.appendChild(this.element);
            
            setTimeout(() => {
                this.element.classList.add('active');
            }, 10);
        }

        show(index) {
            const image = this.images[index];
            const imgElement = this.element.querySelector('.sofir-lightbox-image');
            const captionElement = this.element.querySelector('.sofir-lightbox-caption');
            const counterElement = this.element.querySelector('.sofir-lightbox-counter');

            imgElement.src = image.url;
            
            if (captionElement) {
                captionElement.textContent = image.caption || image.title || '';
                captionElement.style.display = image.caption || image.title ? 'block' : 'none';
            }

            if (counterElement && this.settings.counter) {
                counterElement.textContent = (index + 1) + ' / ' + this.images.length;
            }
        }

        setupEvents() {
            const closeBtn = this.element.querySelector('.sofir-lightbox-close');
            const prevBtn = this.element.querySelector('.sofir-lightbox-prev');
            const nextBtn = this.element.querySelector('.sofir-lightbox-next');

            closeBtn.addEventListener('click', () => this.close());
            prevBtn.addEventListener('click', () => this.prev());
            nextBtn.addEventListener('click', () => this.next());

            this.element.addEventListener('click', (e) => {
                if (e.target === this.element) {
                    this.close();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (!this.element.classList.contains('active')) return;
                
                if (e.key === 'Escape') this.close();
                if (e.key === 'ArrowLeft') this.prev();
                if (e.key === 'ArrowRight') this.next();
            });
        }

        startAutoplay() {
            this.autoplayTimer = setInterval(() => {
                this.next();
            }, this.settings.autoplaySpeed || 3000);
        }

        stopAutoplay() {
            if (this.autoplayTimer) {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = null;
            }
        }

        next() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
            this.show(this.currentIndex);
        }

        prev() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
            this.show(this.currentIndex);
        }

        close() {
            this.stopAutoplay();
            this.element.classList.remove('active');
            
            setTimeout(() => {
                if (this.element && this.element.parentNode) {
                    this.element.parentNode.removeChild(this.element);
                }
            }, 300);
        }
    }

    // Initialize all gallery widgets
    function initGalleryWidgets() {
        // Initialize Gallery widgets
        document.querySelectorAll('.sofir-gallery').forEach(element => {
            new SofirGallery(element);
        });

        // Initialize Slideshow widgets
        document.querySelectorAll('.sofir-slideshow').forEach(element => {
            new SofirSlideshow(element);
        });

        // Initialize Filmstrip widgets
        document.querySelectorAll('.sofir-filmstrip-gallery').forEach(element => {
            new SofirFilmstrip(element);
        });

        // Initialize Album widgets
        document.querySelectorAll('.sofir-album').forEach(element => {
            new SofirAlbum(element);
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initGalleryWidgets);
    } else {
        initGalleryWidgets();
    }

    // Re-initialize on Elementor preview
    if (window.elementorFrontend) {
        window.elementorFrontend.hooks.addAction('frontend/element_ready/sofir-gallery.default', function($scope) {
            new SofirGallery($scope[0].querySelector('.sofir-gallery'));
        });

        window.elementorFrontend.hooks.addAction('frontend/element_ready/sofir-slideshow.default', function($scope) {
            new SofirSlideshow($scope[0].querySelector('.sofir-slideshow'));
        });

        window.elementorFrontend.hooks.addAction('frontend/element_ready/sofir-filmstrip-gallery.default', function($scope) {
            new SofirFilmstrip($scope[0].querySelector('.sofir-filmstrip-gallery'));
        });

        window.elementorFrontend.hooks.addAction('frontend/element_ready/sofir-album.default', function($scope) {
            new SofirAlbum($scope[0].querySelector('.sofir-album'));
        });
    }

})();
