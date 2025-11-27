/**
 * SOFIR Voxel Theme Integration JavaScript
 * Handles AJAX filtering, search, and Voxel-specific interactions
 */

(function ($) {
    'use strict';

    /**
     * Voxel Listings Widget Handler
     */
    class VoxelListingsWidget {
        constructor(element) {
            this.$element = $(element);
            this.$grid = this.$element.find('.sofir-listings-grid');
            this.$toolbar = this.$element.find('.sofir-listings-toolbar');
            this.settings = this.$element.data('ajax') || {};
            this.isLoading = false;

            this.init();
        }

        init() {
            this.bindEvents();
            this.initFilters();
        }

        bindEvents() {
            // Search input
            this.$toolbar.find('.sofir-search-input').on('keyup', $.proxy(this.debounce(this.handleSearch, 500), this));

            // Filter selects
            this.$toolbar.find('.sofir-filter-select').on('change', $.proxy(this.handleFilter, this));

            // Sorting
            this.$toolbar.find('.sofir-sort-select').on('change', $.proxy(this.handleSort, this));

            // Pagination
            this.$element.on('click', '.sofir-pagination a', $.proxy(this.handlePagination, this));
        }

        initFilters() {
            // Initialize active filters from URL
            const urlParams = new URLSearchParams(window.location.search);
            
            urlParams.forEach((value, key) => {
                const $input = this.$toolbar.find('[name="' + key + '"]');
                if ($input.length) {
                    $input.val(value);
                }
            });
        }

        handleSearch(e) {
            const keyword = $(e.target).val();
            this.updateListings({ s: keyword });
        }

        handleFilter(e) {
            const $select = $(e.target);
            const taxonomy = $select.data('taxonomy');
            const value = $select.val();

            const filters = {};
            filters[taxonomy] = value;

            this.updateListings(filters);
        }

        handleSort(e) {
            const value = $(e.target).val();
            const [orderby, order] = value.split('-');

            this.updateListings({
                orderby: orderby,
                order: order.toUpperCase()
            });
        }

        handlePagination(e) {
            e.preventDefault();
            const url = $(e.currentTarget).attr('href');
            const urlObj = new URL(url);
            const paged = urlObj.searchParams.get('paged') || 1;

            this.updateListings({ paged: paged });

            // Scroll to top of listings
            $('html, body').animate({
                scrollTop: this.$element.offset().top - 100
            }, 500);
        }

        updateListings(params) {
            if (this.isLoading) {
                return;
            }

            if (!this.settings.post_type) {
                return;
            }

            this.isLoading = true;
            this.$grid.css('opacity', '0.5');

            // Build query parameters
            const queryParams = $.extend({
                action: 'sofir_voxel_filter_listings',
                nonce: sofirVoxel.nonce,
                post_type: this.settings.post_type,
                settings: this.settings.settings
            }, params);

            $.ajax({
                url: sofirVoxel.ajaxUrl,
                type: 'POST',
                data: queryParams,
                success: $.proxy(function (response) {
                    if (response.success) {
                        this.$grid.html(response.data.html);
                        
                        // Update pagination
                        const $pagination = this.$element.find('.sofir-pagination');
                        if (response.data.pagination) {
                            $pagination.html(response.data.pagination);
                        } else {
                            $pagination.empty();
                        }

                        // Update URL without reload
                        this.updateURL(params);

                        // Trigger custom event
                        this.$element.trigger('sofir:listings:updated', [response.data]);
                    }
                }, this),
                complete: $.proxy(function () {
                    this.isLoading = false;
                    this.$grid.css('opacity', '1');
                }, this)
            });
        }

        updateURL(params) {
            const url = new URL(window.location);

            Object.keys(params).forEach(function (key) {
                if (params[key]) {
                    url.searchParams.set(key, params[key]);
                } else {
                    url.searchParams.delete(key);
                }
            });

            window.history.pushState({}, '', url);
        }

        debounce(func, wait) {
            let timeout;
            return function executedFunction() {
                const context = this;
                const args = arguments;
                const later = function () {
                    timeout = null;
                    func.apply(context, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }

    /**
     * Voxel Search Form Handler
     */
    class VoxelSearchForm {
        constructor(element) {
            this.$form = $(element);
            this.init();
        }

        init() {
            this.initLocationAutocomplete();
            this.bindEvents();
        }

        bindEvents() {
            this.$form.on('submit', $.proxy(this.handleSubmit, this));
        }

        initLocationAutocomplete() {
            const $locationInput = this.$form.find('.sofir-location-autocomplete');

            if ($locationInput.length && typeof google !== 'undefined' && google.maps) {
                const autocomplete = new google.maps.places.Autocomplete($locationInput[0], {
                    types: ['geocode']
                });

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    if (place.geometry) {
                        $locationInput.val(place.formatted_address);
                    }
                });
            } else if ($locationInput.length) {
                // Fallback to simple autocomplete
                $locationInput.on('input', $.proxy(this.debounce(this.loadLocationSuggestions, 300), this));
            }
        }

        loadLocationSuggestions(e) {
            const $input = $(e.target);
            const query = $input.val();

            if (query.length < 3) {
                return;
            }

            $.ajax({
                url: sofirVoxel.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'sofir_location_suggestions',
                    nonce: sofirVoxel.nonce,
                    query: query
                },
                success: function (response) {
                    if (response.success && response.data.suggestions) {
                        // Display suggestions
                        // Implementation depends on UI design
                        console.log(response.data.suggestions);
                    }
                }
            });
        }

        handleSubmit(e) {
            // Allow form to submit normally
            // Or implement AJAX search here if needed

            // Validate required fields
            const $requiredFields = this.$form.find('[required]');
            let isValid = true;

            $requiredFields.each(function () {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('sofir-error');
                } else {
                    $(this).removeClass('sofir-error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            return true;
        }

        debounce(func, wait) {
            let timeout;
            return function executedFunction() {
                const context = this;
                const args = arguments;
                const later = function () {
                    timeout = null;
                    func.apply(context, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    }

    /**
     * Voxel Integration Helper
     */
    const VoxelIntegration = {
        init: function () {
            this.initWidgets();
            this.bindGlobalEvents();
        },

        initWidgets: function () {
            // Initialize Listings Widgets
            $('.sofir-voxel-listings').each(function () {
                new VoxelListingsWidget(this);
            });

            // Initialize Search Forms
            $('.sofir-voxel-search-form').each(function () {
                new VoxelSearchForm(this);
            });
        },

        bindGlobalEvents: function () {
            // Handle Voxel theme events if present
            $(document).on('voxel:ready', function () {
                console.log('SOFIR: Voxel theme detected and ready');
            });

            // Refresh widgets after Elementor preview update
            $(document).on('elementor/frontend/init', function () {
                elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
                    if ($scope.find('.sofir-voxel-listings').length) {
                        new VoxelListingsWidget($scope.find('.sofir-voxel-listings')[0]);
                    }
                    if ($scope.find('.sofir-voxel-search-form').length) {
                        new VoxelSearchForm($scope.find('.sofir-voxel-search-form')[0]);
                    }
                });
            });
        }
    };

    /**
     * Initialize on document ready
     */
    $(function () {
        VoxelIntegration.init();
    });

    // Expose to global scope for external access
    window.SofirVoxel = VoxelIntegration;

})(jQuery);
