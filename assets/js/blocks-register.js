/**
 * SOFIR Blocks Registration
 * Register all SOFIR blocks for Gutenberg editor
 */

(function() {
    'use strict';

    var registerBlockType = wp.blocks.registerBlockType;
    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var ToggleControl = wp.components.ToggleControl;
    var SelectControl = wp.components.SelectControl;
    var RangeControl = wp.components.RangeControl;
    var ServerSideRender = wp.serverSideRender;

    // Helper function to create a server-rendered block
    function createServerBlock(name, title, icon, attributes, inspectorControls) {
        registerBlockType('sofir/' + name, {
            title: title,
            icon: icon || 'star-filled',
            category: 'sofir',
            attributes: attributes || {},
            edit: function(props) {
                var controls = inspectorControls ? inspectorControls(props) : null;
                
                return el(
                    'div',
                    { className: 'sofir-block-editor-wrapper' },
                    controls ? el(InspectorControls, {}, controls) : null,
                    el(ServerSideRender, {
                        block: 'sofir/' + name,
                        attributes: props.attributes
                    })
                );
            },
            save: function() {
                return null; // Dynamic block, rendered by PHP
            }
        });
    }

    // Register all SOFIR blocks
    wp.domReady(function() {
        
        // Action Block
        createServerBlock('action', __('Action Button', 'sofir'), 'button', {
            actionType: { type: 'string', default: 'button' },
            actionLabel: { type: 'string', default: 'Click Me' },
            actionUrl: { type: 'string', default: '' },
            actionClass: { type: 'string', default: '' },
            buttonStyle: { type: 'string', default: 'filled' },
            rounded: { type: 'boolean', default: false }
        }, function(props) {
            return el(PanelBody, { title: __('Action Settings', 'sofir') },
                el(TextControl, {
                    label: __('Label', 'sofir'),
                    value: props.attributes.actionLabel,
                    onChange: function(val) { props.setAttributes({ actionLabel: val }); }
                }),
                el(TextControl, {
                    label: __('URL', 'sofir'),
                    value: props.attributes.actionUrl,
                    onChange: function(val) { props.setAttributes({ actionUrl: val }); }
                }),
                el(SelectControl, {
                    label: __('Button Style', 'sofir'),
                    value: props.attributes.buttonStyle,
                    onChange: function(val) { props.setAttributes({ buttonStyle: val }); },
                    options: [
                        { label: __('Filled', 'sofir'), value: 'filled' },
                        { label: __('Outline', 'sofir'), value: 'outline' }
                    ]
                }),
                el(ToggleControl, {
                    label: __('Rounded', 'sofir'),
                    checked: props.attributes.rounded,
                    onChange: function(val) { props.setAttributes({ rounded: val }); }
                })
            );
        });

        // Cart Summary Block
        createServerBlock('cart-summary', __('Cart Summary', 'sofir'), 'cart');

        // Countdown Block
        createServerBlock('countdown', __('Countdown Timer', 'sofir'), 'clock', {
            targetDate: { type: 'string', default: '' },
            format: { type: 'string', default: 'dhms' }
        }, function(props) {
            return el(PanelBody, { title: __('Countdown Settings', 'sofir') },
                el(TextControl, {
                    label: __('Target Date', 'sofir'),
                    value: props.attributes.targetDate,
                    onChange: function(val) { props.setAttributes({ targetDate: val }); }
                })
            );
        });

        // Create Post Block
        createServerBlock('create-post', __('Create Post Form', 'sofir'), 'edit', {
            postType: { type: 'string', default: 'post' },
            buttonLabel: { type: 'string', default: 'Create Post' }
        }, function(props) {
            return el(PanelBody, { title: __('Form Settings', 'sofir') },
                el(TextControl, {
                    label: __('Post Type', 'sofir'),
                    value: props.attributes.postType,
                    onChange: function(val) { props.setAttributes({ postType: val }); }
                }),
                el(TextControl, {
                    label: __('Button Label', 'sofir'),
                    value: props.attributes.buttonLabel,
                    onChange: function(val) { props.setAttributes({ buttonLabel: val }); }
                })
            );
        });

        // Dashboard Block
        createServerBlock('dashboard', __('User Dashboard', 'sofir'), 'dashboard-icon', {
            title: { type: 'string', default: 'Dashboard' },
            showStats: { type: 'boolean', default: true },
            showRecent: { type: 'boolean', default: true },
            recentPostsCount: { type: 'number', default: 5 }
        }, function(props) {
            return el(PanelBody, { title: __('Dashboard Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Statistics', 'sofir'),
                    checked: props.attributes.showStats,
                    onChange: function(val) { props.setAttributes({ showStats: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Recent Posts', 'sofir'),
                    checked: props.attributes.showRecent,
                    onChange: function(val) { props.setAttributes({ showRecent: val }); }
                }),
                el(RangeControl, {
                    label: __('Recent Posts Count', 'sofir'),
                    value: props.attributes.recentPostsCount,
                    onChange: function(val) { props.setAttributes({ recentPostsCount: val }); },
                    min: 1,
                    max: 20
                })
            );
        });

        // Gallery Block
        var MediaUpload = wp.blockEditor.MediaUpload || wp.editor.MediaUpload;
        var MediaPlaceholder = wp.blockEditor.MediaPlaceholder || wp.editor.MediaPlaceholder;
        var Button = wp.components.Button;
        
        registerBlockType('sofir/gallery', {
            title: __('Gallery', 'sofir'),
            icon: 'format-gallery',
            category: 'sofir',
            attributes: {
                imageIds: { type: 'array', default: [] },
                columns: { type: 'number', default: 3 }
            },
            edit: function(props) {
                var imageIds = props.attributes.imageIds || [];
                var columns = props.attributes.columns || 3;
                
                function onSelectImages(images) {
                    var ids = images.map(function(img) { return img.id; });
                    props.setAttributes({ imageIds: ids });
                }
                
                function removeImage(index) {
                    var newIds = imageIds.filter(function(id, i) { return i !== index; });
                    props.setAttributes({ imageIds: newIds });
                }
                
                var inspectorControls = el(InspectorControls, {},
                    el(PanelBody, { title: __('Gallery Settings', 'sofir') },
                        el(RangeControl, {
                            label: __('Columns', 'sofir'),
                            value: columns,
                            onChange: function(val) { props.setAttributes({ columns: val }); },
                            min: 1,
                            max: 6
                        })
                    )
                );
                
                if (imageIds.length === 0) {
                    return el(
                        'div',
                        { className: 'sofir-gallery-placeholder' },
                        inspectorControls,
                        el(MediaPlaceholder, {
                            icon: 'format-gallery',
                            labels: {
                                title: __('Gallery', 'sofir'),
                                instructions: __('Upload images or select from media library', 'sofir')
                            },
                            onSelect: onSelectImages,
                            accept: 'image/*',
                            allowedTypes: ['image'],
                            multiple: true
                        })
                    );
                }
                
                return el(
                    'div',
                    { className: 'sofir-block-editor-wrapper' },
                    inspectorControls,
                    el('div', { className: 'sofir-gallery sofir-gallery-columns-' + columns },
                        imageIds.map(function(id, index) {
                            return el('div', { key: id, className: 'sofir-gallery-item' },
                                el('img', {
                                    src: wp.data.select('core').getMedia(id) ? 
                                        wp.data.select('core').getMedia(id).source_url : '',
                                    className: 'sofir-gallery-image'
                                }),
                                el(Button, {
                                    className: 'sofir-gallery-remove',
                                    icon: 'no-alt',
                                    onClick: function() { removeImage(index); }
                                })
                            );
                        }),
                        el(MediaUpload, {
                            onSelect: onSelectImages,
                            allowedTypes: ['image'],
                            multiple: true,
                            value: imageIds,
                            render: function(obj) {
                                return el(Button, {
                                    className: 'sofir-gallery-add button button-large',
                                    onClick: obj.open
                                }, __('Add More Images', 'sofir'));
                            }
                        })
                    )
                );
            },
            save: function() {
                return null;
            }
        });

        // Login/Register Block
        createServerBlock('login-register', __('Login / Register Form', 'sofir'), 'admin-users', {
            showRegister: { type: 'boolean', default: true },
            redirectUrl: { type: 'string', default: '' }
        }, function(props) {
            return el(PanelBody, { title: __('Form Settings', 'sofir') },
                el(ToggleControl, {
                    label: __('Show Registration', 'sofir'),
                    checked: props.attributes.showRegister,
                    onChange: function(val) { props.setAttributes({ showRegister: val }); }
                }),
                el(TextControl, {
                    label: __('Redirect URL', 'sofir'),
                    value: props.attributes.redirectUrl,
                    onChange: function(val) { props.setAttributes({ redirectUrl: val }); }
                })
            );
        });

        // Map Block
        createServerBlock('map', __('Interactive Map', 'sofir'), 'location', {
            postType: { type: 'string', default: 'listing' },
            zoom: { type: 'number', default: 12 },
            height: { type: 'string', default: '400px' },
            mapProvider: { type: 'string', default: 'google' }
        }, function(props) {
            return el(PanelBody, { title: __('Map Settings', 'sofir') },
                el(SelectControl, {
                    label: __('Map Provider', 'sofir'),
                    value: props.attributes.mapProvider,
                    onChange: function(val) { props.setAttributes({ mapProvider: val }); },
                    options: [
                        { label: __('Google Maps', 'sofir'), value: 'google' },
                        { label: __('Mapbox', 'sofir'), value: 'mapbox' },
                        { label: __('OpenStreetMap', 'sofir'), value: 'osm' }
                    ]
                }),
                el(TextControl, {
                    label: __('Post Type', 'sofir'),
                    value: props.attributes.postType,
                    onChange: function(val) { props.setAttributes({ postType: val }); }
                }),
                el(RangeControl, {
                    label: __('Zoom Level', 'sofir'),
                    value: props.attributes.zoom,
                    onChange: function(val) { props.setAttributes({ zoom: val }); },
                    min: 1,
                    max: 20
                }),
                el(TextControl, {
                    label: __('Height', 'sofir'),
                    value: props.attributes.height,
                    onChange: function(val) { props.setAttributes({ height: val }); },
                    help: __('e.g., 400px or 50vh', 'sofir')
                })
            );
        });

        // Messages Block
        createServerBlock('messages', __('Messages', 'sofir'), 'email', {
            recipientId: { type: 'number', default: 0 },
            showRecipientSelector: { type: 'boolean', default: true },
            maxHeight: { type: 'string', default: '400px' }
        }, function(props) {
            return el(PanelBody, { title: __('Messages Settings', 'sofir') },
                el(ToggleControl, {
                    label: __('Show Recipient Selector', 'sofir'),
                    checked: props.attributes.showRecipientSelector,
                    onChange: function(val) { props.setAttributes({ showRecipientSelector: val }); }
                }),
                el(TextControl, {
                    label: __('Max Height', 'sofir'),
                    value: props.attributes.maxHeight,
                    onChange: function(val) { props.setAttributes({ maxHeight: val }); },
                    help: __('e.g., 400px or 50vh', 'sofir')
                })
            );
        });

        // Navbar Block
        createServerBlock('navbar', __('Navigation Bar', 'sofir'), 'menu', {
            menuId: { type: 'number', default: 0 },
            mobileBreakpoint: { type: 'number', default: 768 },
            sticky: { type: 'boolean', default: false }
        }, function(props) {
            return el(PanelBody, { title: __('Navbar Settings', 'sofir') },
                el(TextControl, {
                    label: __('Menu ID', 'sofir'),
                    value: props.attributes.menuId,
                    onChange: function(val) { props.setAttributes({ menuId: parseInt(val) || 0 }); },
                    help: __('Enter the menu ID to display', 'sofir')
                }),
                el(RangeControl, {
                    label: __('Mobile Breakpoint (px)', 'sofir'),
                    value: props.attributes.mobileBreakpoint,
                    onChange: function(val) { props.setAttributes({ mobileBreakpoint: val }); },
                    min: 320,
                    max: 1024
                }),
                el(ToggleControl, {
                    label: __('Sticky Navigation', 'sofir'),
                    checked: props.attributes.sticky,
                    onChange: function(val) { props.setAttributes({ sticky: val }); }
                })
            );
        });

        // Order Block
        createServerBlock('order', __('Order Details', 'sofir'), 'cart', {
            orderId: { type: 'number', default: 0 }
        });

        // Popup Kit Block
        var TextareaControl = wp.components.TextareaControl;
        createServerBlock('popup-kit', __('Popup Kit', 'sofir'), 'welcome-view-site', {
            triggerText: { type: 'string', default: 'Open Popup' },
            popupTitle: { type: 'string', default: 'Popup Title' },
            popupContent: { type: 'string', default: 'Popup content goes here...' },
            triggerStyle: { type: 'string', default: 'button' },
            popupWidth: { type: 'string', default: '600px' }
        }, function(props) {
            return el(PanelBody, { title: __('Popup Settings', 'sofir') },
                el(TextControl, {
                    label: __('Trigger Text', 'sofir'),
                    value: props.attributes.triggerText,
                    onChange: function(val) { props.setAttributes({ triggerText: val }); }
                }),
                el(SelectControl, {
                    label: __('Trigger Style', 'sofir'),
                    value: props.attributes.triggerStyle,
                    onChange: function(val) { props.setAttributes({ triggerStyle: val }); },
                    options: [
                        { label: __('Button', 'sofir'), value: 'button' },
                        { label: __('Link', 'sofir'), value: 'link' },
                        { label: __('Image', 'sofir'), value: 'image' }
                    ]
                }),
                el(TextControl, {
                    label: __('Popup Title', 'sofir'),
                    value: props.attributes.popupTitle,
                    onChange: function(val) { props.setAttributes({ popupTitle: val }); }
                }),
                el(TextareaControl, {
                    label: __('Popup Content', 'sofir'),
                    value: props.attributes.popupContent,
                    onChange: function(val) { props.setAttributes({ popupContent: val }); },
                    rows: 4
                }),
                el(TextControl, {
                    label: __('Popup Width', 'sofir'),
                    value: props.attributes.popupWidth,
                    onChange: function(val) { props.setAttributes({ popupWidth: val }); },
                    help: __('e.g., 600px or 80%', 'sofir')
                })
            );
        });

        // Post Feed Block
        createServerBlock('post-feed', __('Post Feed', 'sofir'), 'grid-view', {
            postType: { type: 'string', default: 'post' },
            postsPerPage: { type: 'number', default: 10 },
            layout: { type: 'string', default: 'grid' },
            columns: { type: 'number', default: 3 },
            showExcerpt: { type: 'boolean', default: true },
            showMeta: { type: 'boolean', default: true }
        }, function(props) {
            return el(PanelBody, { title: __('Feed Settings', 'sofir') },
                el(TextControl, {
                    label: __('Post Type', 'sofir'),
                    value: props.attributes.postType,
                    onChange: function(val) { props.setAttributes({ postType: val }); }
                }),
                el(RangeControl, {
                    label: __('Posts Per Page', 'sofir'),
                    value: props.attributes.postsPerPage,
                    onChange: function(val) { props.setAttributes({ postsPerPage: val }); },
                    min: 1,
                    max: 50
                }),
                el(SelectControl, {
                    label: __('Layout', 'sofir'),
                    value: props.attributes.layout,
                    onChange: function(val) { props.setAttributes({ layout: val }); },
                    options: [
                        { label: __('Grid', 'sofir'), value: 'grid' },
                        { label: __('List', 'sofir'), value: 'list' }
                    ]
                }),
                el(RangeControl, {
                    label: __('Columns', 'sofir'),
                    value: props.attributes.columns,
                    onChange: function(val) { props.setAttributes({ columns: val }); },
                    min: 1,
                    max: 6
                }),
                el(ToggleControl, {
                    label: __('Show Excerpt', 'sofir'),
                    checked: props.attributes.showExcerpt,
                    onChange: function(val) { props.setAttributes({ showExcerpt: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Meta', 'sofir'),
                    checked: props.attributes.showMeta,
                    onChange: function(val) { props.setAttributes({ showMeta: val }); }
                })
            );
        });

        // Print Template Block
        createServerBlock('print-template', __('Print Template', 'sofir'), 'printer');

        // Product Form Block
        createServerBlock('product-form', __('Product Form', 'sofir'), 'products', {
            vendorId: { type: 'number', default: 0 },
            showDescription: { type: 'boolean', default: true },
            showImage: { type: 'boolean', default: true },
            submitText: { type: 'string', default: 'Add Product' }
        }, function(props) {
            return el(PanelBody, { title: __('Form Settings', 'sofir') },
                el(ToggleControl, {
                    label: __('Show Description', 'sofir'),
                    checked: props.attributes.showDescription,
                    onChange: function(val) { props.setAttributes({ showDescription: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Image Upload', 'sofir'),
                    checked: props.attributes.showImage,
                    onChange: function(val) { props.setAttributes({ showImage: val }); }
                }),
                el(TextControl, {
                    label: __('Submit Button Text', 'sofir'),
                    value: props.attributes.submitText,
                    onChange: function(val) { props.setAttributes({ submitText: val }); }
                })
            );
        });

        // Product Price Block
        createServerBlock('product-price', __('Product Price', 'sofir'), 'tag', {
            productId: { type: 'number', default: 0 },
            showCurrency: { type: 'boolean', default: true },
            format: { type: 'string', default: 'default' }
        }, function(props) {
            return el(PanelBody, { title: __('Price Settings', 'sofir') },
                el(TextControl, {
                    label: __('Product ID', 'sofir'),
                    value: props.attributes.productId,
                    onChange: function(val) { props.setAttributes({ productId: parseInt(val) || 0 }); },
                    help: __('Leave 0 to use current post', 'sofir')
                }),
                el(ToggleControl, {
                    label: __('Show Currency', 'sofir'),
                    checked: props.attributes.showCurrency,
                    onChange: function(val) { props.setAttributes({ showCurrency: val }); }
                })
            );
        });

        // Quick Search Block
        createServerBlock('quick-search', __('Quick Search', 'sofir'), 'search', {
            placeholder: { type: 'string', default: 'Search...' },
            postType: { type: 'string', default: 'post' }
        }, function(props) {
            return el(PanelBody, { title: __('Search Settings', 'sofir') },
                el(TextControl, {
                    label: __('Placeholder', 'sofir'),
                    value: props.attributes.placeholder,
                    onChange: function(val) { props.setAttributes({ placeholder: val }); }
                }),
                el(TextControl, {
                    label: __('Post Type', 'sofir'),
                    value: props.attributes.postType,
                    onChange: function(val) { props.setAttributes({ postType: val }); }
                })
            );
        });

        // Review Stats Block
        createServerBlock('review-stats', __('Review Statistics', 'sofir'), 'star-filled', {
            postId: { type: 'number', default: 0 },
            postType: { type: 'string', default: 'listing' },
            averageRating: { type: 'boolean', default: true },
            totalReviews: { type: 'boolean', default: true },
            showStars: { type: 'boolean', default: true }
        }, function(props) {
            return el(PanelBody, { title: __('Review Settings', 'sofir') },
                el(TextControl, {
                    label: __('Post Type', 'sofir'),
                    value: props.attributes.postType,
                    onChange: function(val) { props.setAttributes({ postType: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Average Rating', 'sofir'),
                    checked: props.attributes.averageRating,
                    onChange: function(val) { props.setAttributes({ averageRating: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Total Reviews', 'sofir'),
                    checked: props.attributes.totalReviews,
                    onChange: function(val) { props.setAttributes({ totalReviews: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Stars', 'sofir'),
                    checked: props.attributes.showStars,
                    onChange: function(val) { props.setAttributes({ showStars: val }); }
                })
            );
        });

        // Ring Chart Block
        createServerBlock('ring-chart', __('Ring Chart', 'sofir'), 'chart-pie', {
            data: { type: 'string', default: '[{"label":"Item 1","value":30,"color":"#4CAF50"},{"label":"Item 2","value":45,"color":"#2196F3"},{"label":"Item 3","value":25,"color":"#FFC107"}]' },
            title: { type: 'string', default: 'Chart' }
        }, function(props) {
            return el(PanelBody, { title: __('Chart Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                }),
                el(TextControl, {
                    label: __('Chart Data (JSON)', 'sofir'),
                    value: props.attributes.data,
                    onChange: function(val) { props.setAttributes({ data: val }); },
                    help: __('Format: [{"label":"Name","value":30,"color":"#4CAF50"}]', 'sofir')
                })
            );
        });

        // Sales Chart Block
        createServerBlock('sales-chart', __('Sales Chart', 'sofir'), 'chart-line', {
            period: { type: 'string', default: '30' },
            title: { type: 'string', default: 'Sales' }
        }, function(props) {
            return el(PanelBody, { title: __('Chart Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                }),
                el(SelectControl, {
                    label: __('Period', 'sofir'),
                    value: props.attributes.period,
                    onChange: function(val) { props.setAttributes({ period: val }); },
                    options: [
                        { label: __('7 Days', 'sofir'), value: '7' },
                        { label: __('30 Days', 'sofir'), value: '30' },
                        { label: __('90 Days', 'sofir'), value: '90' }
                    ]
                })
            );
        });

        // Search Form Block
        createServerBlock('search-form', __('Search Form', 'sofir'), 'search', {
            placeholder: { type: 'string', default: 'Search...' },
            showFilters: { type: 'boolean', default: false },
            advancedFilters: { type: 'boolean', default: false },
            postType: { type: 'string', default: 'listing' }
        }, function(props) {
            return el(PanelBody, { title: __('Search Settings', 'sofir') },
                el(TextControl, {
                    label: __('Placeholder', 'sofir'),
                    value: props.attributes.placeholder,
                    onChange: function(val) { props.setAttributes({ placeholder: val }); }
                }),
                el(TextControl, {
                    label: __('Post Type', 'sofir'),
                    value: props.attributes.postType,
                    onChange: function(val) { props.setAttributes({ postType: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Filters', 'sofir'),
                    checked: props.attributes.showFilters || props.attributes.advancedFilters,
                    onChange: function(val) { 
                        props.setAttributes({ showFilters: val, advancedFilters: val }); 
                    }
                })
            );
        });

        // Slider Block
        createServerBlock('slider', __('Image Slider', 'sofir'), 'images-alt2', {
            imageIds: { type: 'array', default: [] },
            autoplay: { type: 'boolean', default: true },
            interval: { type: 'number', default: 5000 }
        }, function(props) {
            return el(PanelBody, { title: __('Slider Settings', 'sofir') },
                el(ToggleControl, {
                    label: __('Autoplay', 'sofir'),
                    checked: props.attributes.autoplay,
                    onChange: function(val) { props.setAttributes({ autoplay: val }); }
                }),
                el(RangeControl, {
                    label: __('Interval (ms)', 'sofir'),
                    value: props.attributes.interval,
                    onChange: function(val) { props.setAttributes({ interval: val }); },
                    min: 1000,
                    max: 10000,
                    step: 500
                })
            );
        });

        // Term Feed Block
        createServerBlock('term-feed', __('Term Feed', 'sofir'), 'category', {
            taxonomy: { type: 'string', default: 'category' },
            showCount: { type: 'boolean', default: true },
            numberOfTerms: { type: 'number', default: 10 },
            layout: { type: 'string', default: 'grid' }
        }, function(props) {
            return el(PanelBody, { title: __('Term Settings', 'sofir') },
                el(TextControl, {
                    label: __('Taxonomy', 'sofir'),
                    value: props.attributes.taxonomy,
                    onChange: function(val) { props.setAttributes({ taxonomy: val }); }
                }),
                el(RangeControl, {
                    label: __('Number of Terms', 'sofir'),
                    value: props.attributes.numberOfTerms,
                    onChange: function(val) { props.setAttributes({ numberOfTerms: val }); },
                    min: 1,
                    max: 50
                }),
                el(ToggleControl, {
                    label: __('Show Count', 'sofir'),
                    checked: props.attributes.showCount,
                    onChange: function(val) { props.setAttributes({ showCount: val }); }
                })
            );
        });

        // Timeline Block
        createServerBlock('timeline', __('Timeline', 'sofir'), 'backup', {
            items: { type: 'array', default: [] }
        });

        // Timeline Style Kit Block
        createServerBlock('timeline-style-kit', __('Timeline Style Kit', 'sofir'), 'backup');

        // User Bar Block
        createServerBlock('user-bar', __('User Bar', 'sofir'), 'admin-users', {
            showAvatar: { type: 'boolean', default: true },
            showName: { type: 'boolean', default: true }
        }, function(props) {
            return el(PanelBody, { title: __('User Bar Settings', 'sofir') },
                el(ToggleControl, {
                    label: __('Show Avatar', 'sofir'),
                    checked: props.attributes.showAvatar,
                    onChange: function(val) { props.setAttributes({ showAvatar: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Name', 'sofir'),
                    checked: props.attributes.showName,
                    onChange: function(val) { props.setAttributes({ showName: val }); }
                })
            );
        });

        // Visit Chart Block
        createServerBlock('visit-chart', __('Visit Chart', 'sofir'), 'chart-bar', {
            period: { type: 'string', default: '7' },
            title: { type: 'string', default: 'Visits' }
        }, function(props) {
            return el(PanelBody, { title: __('Chart Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                }),
                el(SelectControl, {
                    label: __('Period', 'sofir'),
                    value: props.attributes.period,
                    onChange: function(val) { props.setAttributes({ period: val }); },
                    options: [
                        { label: __('7 Days', 'sofir'), value: '7' },
                        { label: __('30 Days', 'sofir'), value: '30' },
                        { label: __('90 Days', 'sofir'), value: '90' }
                    ]
                })
            );
        });

        // Work Hours Block
        createServerBlock('work-hours', __('Work Hours', 'sofir'), 'clock', {
            postId: { type: 'number', default: 0 }
        });

        // Testimonial Slider Block
        createServerBlock('testimonial-slider', __('Testimonial Slider', 'sofir'), 'format-quote', {
            autoplay: { type: 'boolean', default: true },
            interval: { type: 'number', default: 5000 }
        }, function(props) {
            return el(PanelBody, { title: __('Slider Settings', 'sofir') },
                el(ToggleControl, {
                    label: __('Autoplay', 'sofir'),
                    checked: props.attributes.autoplay,
                    onChange: function(val) { props.setAttributes({ autoplay: val }); }
                })
            );
        });

        // Pricing Table Block
        createServerBlock('pricing-table', __('Pricing Table', 'sofir'), 'money-alt', {
            columns: { type: 'number', default: 3 }
        }, function(props) {
            return el(PanelBody, { title: __('Pricing Settings', 'sofir') },
                el(RangeControl, {
                    label: __('Columns', 'sofir'),
                    value: props.attributes.columns,
                    onChange: function(val) { props.setAttributes({ columns: val }); },
                    min: 1,
                    max: 4
                })
            );
        });

        // Team Grid Block
        createServerBlock('team-grid', __('Team Grid', 'sofir'), 'groups', {
            columns: { type: 'number', default: 3 }
        }, function(props) {
            return el(PanelBody, { title: __('Grid Settings', 'sofir') },
                el(RangeControl, {
                    label: __('Columns', 'sofir'),
                    value: props.attributes.columns,
                    onChange: function(val) { props.setAttributes({ columns: val }); },
                    min: 1,
                    max: 6
                })
            );
        });

        // FAQ Accordion Block
        createServerBlock('faq-accordion', __('FAQ Accordion', 'sofir'), 'list-view');

        // CTA Banner Block
        var ColorPicker = wp.components.ColorPicker || null;
        var PanelColorSettings = wp.blockEditor.PanelColorSettings || wp.editor.PanelColorSettings || null;
        
        createServerBlock('cta-banner', __('CTA Banner', 'sofir'), 'megaphone', {
            title: { type: 'string', default: 'Ready to Get Started?' },
            description: { type: 'string', default: '' },
            buttonText: { type: 'string', default: 'Get Started' },
            buttonUrl: { type: 'string', default: '#' },
            backgroundColor: { type: 'string', default: '#0073aa' },
            textColor: { type: 'string', default: '#ffffff' },
            alignment: { type: 'string', default: 'center' },
            backgroundImage: { type: 'string', default: '' }
        }, function(props) {
            return el(PanelBody, { title: __('CTA Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                }),
                el(TextControl, {
                    label: __('Description', 'sofir'),
                    value: props.attributes.description,
                    onChange: function(val) { props.setAttributes({ description: val }); }
                }),
                el(TextControl, {
                    label: __('Button Text', 'sofir'),
                    value: props.attributes.buttonText,
                    onChange: function(val) { props.setAttributes({ buttonText: val }); }
                }),
                el(TextControl, {
                    label: __('Button URL', 'sofir'),
                    value: props.attributes.buttonUrl,
                    onChange: function(val) { props.setAttributes({ buttonUrl: val }); }
                }),
                el(SelectControl, {
                    label: __('Alignment', 'sofir'),
                    value: props.attributes.alignment,
                    onChange: function(val) { props.setAttributes({ alignment: val }); },
                    options: [
                        { label: __('Left', 'sofir'), value: 'left' },
                        { label: __('Center', 'sofir'), value: 'center' },
                        { label: __('Right', 'sofir'), value: 'right' }
                    ]
                }),
                el(TextControl, {
                    label: __('Background Color', 'sofir'),
                    value: props.attributes.backgroundColor,
                    onChange: function(val) { props.setAttributes({ backgroundColor: val }); },
                    help: __('Hex color code, e.g., #0073aa', 'sofir')
                }),
                el(TextControl, {
                    label: __('Text Color', 'sofir'),
                    value: props.attributes.textColor,
                    onChange: function(val) { props.setAttributes({ textColor: val }); },
                    help: __('Hex color code, e.g., #ffffff', 'sofir')
                }),
                el(TextControl, {
                    label: __('Background Image URL', 'sofir'),
                    value: props.attributes.backgroundImage,
                    onChange: function(val) { props.setAttributes({ backgroundImage: val }); }
                })
            );
        });

        // Feature Box Block
        createServerBlock('feature-box', __('Feature Box', 'sofir'), 'lightbulb', {
            icon: { type: 'string', default: '⭐' },
            title: { type: 'string', default: 'Feature Title' },
            description: { type: 'string', default: 'Feature description goes here.' },
            iconPosition: { type: 'string', default: 'top' },
            alignment: { type: 'string', default: 'center' }
        }, function(props) {
            return el(PanelBody, { title: __('Feature Settings', 'sofir') },
                el(TextControl, {
                    label: __('Icon (emoji or HTML)', 'sofir'),
                    value: props.attributes.icon,
                    onChange: function(val) { props.setAttributes({ icon: val }); }
                }),
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                }),
                el(TextControl, {
                    label: __('Description', 'sofir'),
                    value: props.attributes.description,
                    onChange: function(val) { props.setAttributes({ description: val }); }
                }),
                el(SelectControl, {
                    label: __('Icon Position', 'sofir'),
                    value: props.attributes.iconPosition,
                    onChange: function(val) { props.setAttributes({ iconPosition: val }); },
                    options: [
                        { label: __('Top', 'sofir'), value: 'top' },
                        { label: __('Left', 'sofir'), value: 'left' },
                        { label: __('Right', 'sofir'), value: 'right' }
                    ]
                }),
                el(SelectControl, {
                    label: __('Alignment', 'sofir'),
                    value: props.attributes.alignment,
                    onChange: function(val) { props.setAttributes({ alignment: val }); },
                    options: [
                        { label: __('Left', 'sofir'), value: 'left' },
                        { label: __('Center', 'sofir'), value: 'center' },
                        { label: __('Right', 'sofir'), value: 'right' }
                    ]
                })
            );
        });

        // Contact Form Block
        createServerBlock('contact-form', __('Contact Form', 'sofir'), 'email-alt', {
            title: { type: 'string', default: 'Contact Us' },
            showSubject: { type: 'boolean', default: true },
            showPhone: { type: 'boolean', default: false },
            submitText: { type: 'string', default: 'Send Message' },
            emailTo: { type: 'string', default: '' },
            successMessage: { type: 'string', default: 'Thank you! Your message has been sent.' }
        }, function(props) {
            return el(PanelBody, { title: __('Form Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                }),
                el(TextControl, {
                    label: __('Send To Email', 'sofir'),
                    value: props.attributes.emailTo,
                    onChange: function(val) { props.setAttributes({ emailTo: val }); },
                    help: __('Leave blank to use site admin email', 'sofir')
                }),
                el(TextControl, {
                    label: __('Submit Button Text', 'sofir'),
                    value: props.attributes.submitText,
                    onChange: function(val) { props.setAttributes({ submitText: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Subject Field', 'sofir'),
                    checked: props.attributes.showSubject,
                    onChange: function(val) { props.setAttributes({ showSubject: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Phone Field', 'sofir'),
                    checked: props.attributes.showPhone,
                    onChange: function(val) { props.setAttributes({ showPhone: val }); }
                })
            );
        });

        // Social Share Block
        var CheckboxControl = wp.components.CheckboxControl;
        createServerBlock('social-share', __('Social Share', 'sofir'), 'share', {
            title: { type: 'string', default: 'Share this:' },
            platforms: { type: 'array', default: ['facebook', 'twitter', 'linkedin', 'whatsapp'] },
            layout: { type: 'string', default: 'horizontal' }
        }, function(props) {
            var platforms = props.attributes.platforms || ['facebook', 'twitter', 'linkedin', 'whatsapp'];
            
            function togglePlatform(platform) {
                var newPlatforms = platforms.slice();
                var index = newPlatforms.indexOf(platform);
                if (index > -1) {
                    newPlatforms.splice(index, 1);
                } else {
                    newPlatforms.push(platform);
                }
                props.setAttributes({ platforms: newPlatforms });
            }
            
            return el(PanelBody, { title: __('Social Share Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                }),
                el(SelectControl, {
                    label: __('Layout', 'sofir'),
                    value: props.attributes.layout,
                    onChange: function(val) { props.setAttributes({ layout: val }); },
                    options: [
                        { label: __('Horizontal', 'sofir'), value: 'horizontal' },
                        { label: __('Vertical', 'sofir'), value: 'vertical' }
                    ]
                }),
                el('p', { style: { fontWeight: 'bold', marginTop: '10px' } }, __('Select Platforms:', 'sofir')),
                el(ToggleControl, {
                    label: 'Facebook',
                    checked: platforms.indexOf('facebook') > -1,
                    onChange: function() { togglePlatform('facebook'); }
                }),
                el(ToggleControl, {
                    label: 'Twitter',
                    checked: platforms.indexOf('twitter') > -1,
                    onChange: function() { togglePlatform('twitter'); }
                }),
                el(ToggleControl, {
                    label: 'LinkedIn',
                    checked: platforms.indexOf('linkedin') > -1,
                    onChange: function() { togglePlatform('linkedin'); }
                }),
                el(ToggleControl, {
                    label: 'WhatsApp',
                    checked: platforms.indexOf('whatsapp') > -1,
                    onChange: function() { togglePlatform('whatsapp'); }
                })
            );
        });

        // Breadcrumb Block
        createServerBlock('breadcrumb', __('Breadcrumb', 'sofir'), 'admin-links', {
            separator: { type: 'string', default: '/' }
        }, function(props) {
            return el(PanelBody, { title: __('Breadcrumb Settings', 'sofir') },
                el(TextControl, {
                    label: __('Separator', 'sofir'),
                    value: props.attributes.separator,
                    onChange: function(val) { props.setAttributes({ separator: val }); }
                })
            );
        });

        // Progress Bar Block
        createServerBlock('progress-bar', __('Progress Bar', 'sofir'), 'minus', {
            percentage: { type: 'number', default: 50 },
            label: { type: 'string', default: 'Progress' }
        }, function(props) {
            return el(PanelBody, { title: __('Progress Settings', 'sofir') },
                el(TextControl, {
                    label: __('Label', 'sofir'),
                    value: props.attributes.label,
                    onChange: function(val) { props.setAttributes({ label: val }); }
                }),
                el(RangeControl, {
                    label: __('Percentage', 'sofir'),
                    value: props.attributes.percentage,
                    onChange: function(val) { props.setAttributes({ percentage: val }); },
                    min: 0,
                    max: 100
                })
            );
        });

        // Appointment Booking Block
        createServerBlock('appointment-booking', __('Appointment Booking', 'sofir'), 'calendar-alt', {
            serviceId: { type: 'number', default: 0 },
            providerId: { type: 'number', default: 0 },
            buttonText: { type: 'string', default: 'Book Appointment' },
            showCalendar: { type: 'boolean', default: true }
        }, function(props) {
            return el(PanelBody, { title: __('Booking Settings', 'sofir') },
                el(TextControl, {
                    label: __('Button Text', 'sofir'),
                    value: props.attributes.buttonText,
                    onChange: function(val) { props.setAttributes({ buttonText: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Calendar Picker', 'sofir'),
                    checked: props.attributes.showCalendar,
                    onChange: function(val) { props.setAttributes({ showCalendar: val }); }
                })
            );
        });

        // Dynamic Data Block
        createServerBlock('dynamic-data', __('Dynamic Data', 'sofir'), 'database', {
            dataSource: { type: 'string', default: 'post_meta' },
            metaKey: { type: 'string', default: '' },
            postId: { type: 'number', default: 0 },
            userId: { type: 'number', default: 0 },
            optionKey: { type: 'string', default: '' },
            format: { type: 'string', default: 'text' },
            fallback: { type: 'string', default: '' },
            prefix: { type: 'string', default: '' },
            suffix: { type: 'string', default: '' },
            dateFormat: { type: 'string', default: 'F j, Y' },
            imageSize: { type: 'string', default: 'medium' }
        }, function(props) {
            var dataSource = props.attributes.dataSource;
            var showMetaKey = ['post_meta', 'user_meta', 'post_field', 'user_field', 'cpt_field'].indexOf(dataSource) > -1;
            var showOptionKey = dataSource === 'site_option';
            
            return el(PanelBody, { title: __('Data Settings', 'sofir') },
                el(SelectControl, {
                    label: __('Data Source', 'sofir'),
                    value: props.attributes.dataSource,
                    onChange: function(val) { props.setAttributes({ dataSource: val }); },
                    options: [
                        { label: __('Post Meta', 'sofir'), value: 'post_meta' },
                        { label: __('Post Field', 'sofir'), value: 'post_field' },
                        { label: __('User Meta', 'sofir'), value: 'user_meta' },
                        { label: __('User Field', 'sofir'), value: 'user_field' },
                        { label: __('Site Option', 'sofir'), value: 'site_option' },
                        { label: __('CPT Field', 'sofir'), value: 'cpt_field' }
                    ]
                }),
                showMetaKey ? el(TextControl, {
                    label: __('Meta/Field Key', 'sofir'),
                    value: props.attributes.metaKey,
                    onChange: function(val) { props.setAttributes({ metaKey: val }); },
                    help: __('Enter the meta key or field name', 'sofir')
                }) : null,
                showOptionKey ? el(TextControl, {
                    label: __('Option Key', 'sofir'),
                    value: props.attributes.optionKey,
                    onChange: function(val) { props.setAttributes({ optionKey: val }); }
                }) : null,
                el(SelectControl, {
                    label: __('Format', 'sofir'),
                    value: props.attributes.format,
                    onChange: function(val) { props.setAttributes({ format: val }); },
                    options: [
                        { label: __('Text', 'sofir'), value: 'text' },
                        { label: __('HTML', 'sofir'), value: 'html' },
                        { label: __('URL', 'sofir'), value: 'url' },
                        { label: __('Email', 'sofir'), value: 'email' },
                        { label: __('Phone', 'sofir'), value: 'phone' },
                        { label: __('Date', 'sofir'), value: 'date' },
                        { label: __('Number', 'sofir'), value: 'number' },
                        { label: __('Currency', 'sofir'), value: 'currency' },
                        { label: __('Image', 'sofir'), value: 'image' }
                    ]
                }),
                el(TextControl, {
                    label: __('Prefix', 'sofir'),
                    value: props.attributes.prefix,
                    onChange: function(val) { props.setAttributes({ prefix: val }); }
                }),
                el(TextControl, {
                    label: __('Suffix', 'sofir'),
                    value: props.attributes.suffix,
                    onChange: function(val) { props.setAttributes({ suffix: val }); }
                }),
                el(TextControl, {
                    label: __('Fallback Text', 'sofir'),
                    value: props.attributes.fallback,
                    onChange: function(val) { props.setAttributes({ fallback: val }); }
                })
            );
        });

        console.log('[SOFIR] All blocks registered successfully!');
    });

})();
