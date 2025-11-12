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
            showRecent: { type: 'boolean', default: true }
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
                })
            );
        });

        // Gallery Block
        createServerBlock('gallery', __('Gallery', 'sofir'), 'format-gallery', {
            imageIds: { type: 'array', default: [] },
            columns: { type: 'number', default: 3 }
        }, function(props) {
            return el(PanelBody, { title: __('Gallery Settings', 'sofir') },
                el(RangeControl, {
                    label: __('Columns', 'sofir'),
                    value: props.attributes.columns,
                    onChange: function(val) { props.setAttributes({ columns: val }); },
                    min: 1,
                    max: 6
                })
            );
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
            height: { type: 'string', default: '400px' }
        }, function(props) {
            return el(PanelBody, { title: __('Map Settings', 'sofir') },
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
                    onChange: function(val) { props.setAttributes({ height: val }); }
                })
            );
        });

        // Messages Block
        createServerBlock('messages', __('Messages', 'sofir'), 'email');

        // Navbar Block
        createServerBlock('navbar', __('Navigation Bar', 'sofir'), 'menu', {
            menuId: { type: 'number', default: 0 },
            mobileBreakpoint: { type: 'number', default: 768 }
        });

        // Order Block
        createServerBlock('order', __('Order Details', 'sofir'), 'cart', {
            orderId: { type: 'number', default: 0 }
        });

        // Popup Kit Block
        createServerBlock('popup-kit', __('Popup Kit', 'sofir'), 'welcome-view-site', {
            triggerText: { type: 'string', default: 'Open Popup' },
            popupTitle: { type: 'string', default: '' },
            popupContent: { type: 'string', default: '' }
        }, function(props) {
            return el(PanelBody, { title: __('Popup Settings', 'sofir') },
                el(TextControl, {
                    label: __('Trigger Text', 'sofir'),
                    value: props.attributes.triggerText,
                    onChange: function(val) { props.setAttributes({ triggerText: val }); }
                }),
                el(TextControl, {
                    label: __('Popup Title', 'sofir'),
                    value: props.attributes.popupTitle,
                    onChange: function(val) { props.setAttributes({ popupTitle: val }); }
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
            vendorId: { type: 'number', default: 0 }
        });

        // Product Price Block
        createServerBlock('product-price', __('Product Price', 'sofir'), 'tag', {
            productId: { type: 'number', default: 0 }
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
            postType: { type: 'string', default: 'listing' },
            averageRating: { type: 'boolean', default: true },
            totalReviews: { type: 'boolean', default: true }
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
                })
            );
        });

        // Ring Chart Block
        createServerBlock('ring-chart', __('Ring Chart', 'sofir'), 'chart-pie', {
            data: { type: 'string', default: '[]' },
            title: { type: 'string', default: 'Chart' }
        }, function(props) {
            return el(PanelBody, { title: __('Chart Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
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
        createServerBlock('cta-banner', __('CTA Banner', 'sofir'), 'megaphone', {
            title: { type: 'string', default: 'Call to Action' },
            buttonText: { type: 'string', default: 'Get Started' },
            buttonUrl: { type: 'string', default: '' }
        }, function(props) {
            return el(PanelBody, { title: __('CTA Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
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
                })
            );
        });

        // Feature Box Block
        createServerBlock('feature-box', __('Feature Box', 'sofir'), 'lightbulb', {
            title: { type: 'string', default: 'Feature' },
            icon: { type: 'string', default: 'star-filled' }
        }, function(props) {
            return el(PanelBody, { title: __('Feature Settings', 'sofir') },
                el(TextControl, {
                    label: __('Title', 'sofir'),
                    value: props.attributes.title,
                    onChange: function(val) { props.setAttributes({ title: val }); }
                })
            );
        });

        // Contact Form Block
        createServerBlock('contact-form', __('Contact Form', 'sofir'), 'email-alt', {
            emailTo: { type: 'string', default: '' },
            showPhone: { type: 'boolean', default: true }
        }, function(props) {
            return el(PanelBody, { title: __('Form Settings', 'sofir') },
                el(TextControl, {
                    label: __('Send To Email', 'sofir'),
                    value: props.attributes.emailTo,
                    onChange: function(val) { props.setAttributes({ emailTo: val }); }
                }),
                el(ToggleControl, {
                    label: __('Show Phone Field', 'sofir'),
                    checked: props.attributes.showPhone,
                    onChange: function(val) { props.setAttributes({ showPhone: val }); }
                })
            );
        });

        // Social Share Block
        createServerBlock('social-share', __('Social Share', 'sofir'), 'share', {
            platforms: { type: 'array', default: ['facebook', 'twitter', 'linkedin'] }
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
            providerId: { type: 'number', default: 0 }
        });

        // Dynamic Data Block
        createServerBlock('dynamic-data', __('Dynamic Data', 'sofir'), 'database', {
            dataSource: { type: 'string', default: 'post_title' },
            format: { type: 'string', default: 'text' },
            fallback: { type: 'string', default: '' }
        }, function(props) {
            return el(PanelBody, { title: __('Data Settings', 'sofir') },
                el(SelectControl, {
                    label: __('Data Source', 'sofir'),
                    value: props.attributes.dataSource,
                    onChange: function(val) { props.setAttributes({ dataSource: val }); },
                    options: [
                        { label: __('Post Title', 'sofir'), value: 'post_title' },
                        { label: __('Post Content', 'sofir'), value: 'post_content' },
                        { label: __('Post Date', 'sofir'), value: 'post_date' },
                        { label: __('Post Author', 'sofir'), value: 'post_author' },
                        { label: __('Custom Field', 'sofir'), value: 'custom_field' },
                        { label: __('User Data', 'sofir'), value: 'user_data' }
                    ]
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
