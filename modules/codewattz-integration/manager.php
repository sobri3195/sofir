<?php
namespace Sofir\CodeWattzIntegration;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        // Enqueue assets
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

        // Load sub-modules
        require_once __DIR__ . '/advanced-fields.php';
        Advanced_Fields::instance()->boot();
        
        // Load calendar integration
        require_once __DIR__ . '/calendar-integration.php';
        Calendar_Integration::instance()->boot();
        
        // Load review system
        require_once __DIR__ . '/review-system.php';
        Review_System::instance()->boot();
        
        // Load payment gateways
        require_once __DIR__ . '/payment-gateways.php';
        Payment_Gateways::instance()->boot();

        // Integration hooks
        add_filter( 'sofir/cpt/register_args', [ $this, 'enhance_cpt_for_codewattz' ], 15, 2 );
        add_filter( 'sofir/field/meta_config', [ $this, 'map_field_to_codewattz' ], 15, 3 );
        add_action( 'voxel/post-types/register', [ $this, 'register_codewattz_features' ] );
        
        // Elementor widget registration
        add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widgets' ] );
        
        // Shortcodes
        add_action( 'init', [ $this, 'register_shortcodes' ] );
        
        // REST API endpoints
        add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
    }

    public function enhance_cpt_for_codewattz( array $args, string $slug ): array {
        if ( ! defined( 'VOXEL_VERSION' ) || ! class_exists( '\Voxel\Post_Type' ) ) {
            return $args;
        }

        $args['codewattz_enabled'] = true;
        $args['codewattz_calendar'] = true;
        $args['codewattz_reviews'] = true;
        $args['codewattz_payments'] = true;
        
        if ( ! isset( $args['supports'] ) ) {
            $args['supports'] = [];
        }
        
        if ( ! in_array( 'custom-fields', $args['supports'], true ) ) {
            $args['supports'][] = 'custom-fields';
        }
        
        if ( ! isset( $args['show_in_menu'] ) ) {
            $args['show_in_menu'] = true;
        }
        
        if ( ! isset( $args['show_ui'] ) ) {
            $args['show_ui'] = true;
        }
        
        if ( ! isset( $args['public'] ) ) {
            $args['public'] = true;
        }
        
        return $args;
    }

    public function map_field_to_codewattz( array $config, string $field_key, string $post_type ): array {
        $codewattz_mapping = [
            'business_hours' => 'enhanced-hours',
            'advanced_rating' => 'rating-criteria',
            'price_range' => 'range-slider',
            'location_plus' => 'geo-location',
            'gallery_plus' => 'enhanced-gallery',
            'contact_form' => 'voxel-form',
            'booking_calendar' => 'calendar-picker',
            'reviews_enabled' => 'review-system',
        ];

        $sofir_type = $config['type'] ?? 'text';
        $config['codewattz_type'] = $codewattz_mapping[ $sofir_type ] ?? 'text';
        $config['codewattz_searchable'] = ! empty( $config['filterable'] );
        $config['codewattz_show_in_card'] = true;
        $config['codewattz_voxel_compatible'] = true;
        
        return $config;
    }

    public function register_codewattz_features(): void {
        // Register CodeWattz features with Voxel
        if ( class_exists( 'Voxel\Features' ) ) {
            Voxel\Features::register( 'codewattz-calendar', [
                'label' => __( 'CodeWattz Calendar', 'sofir' ),
                'description' => __( 'Advanced calendar integration for events and appointments', 'sofir' ),
            ]);
            
            Voxel\Features::register( 'codewattz-reviews', [
                'label' => __( 'CodeWattz Reviews', 'sofir' ),
                'description' => __( 'Enhanced review system with photos and criteria', 'sofir' ),
            ]);
            
            Voxel\Features::register( 'codewattz-payments', [
                'label' => __( 'CodeWattz Payments', 'sofir' ),
                'description' => __( 'PayPal and enhanced payment gateways', 'sofir' ),
            ]);
        }
    }

    public function register_elementor_widgets(): void {
        if ( ! did_action( 'elementor/loaded' ) ) {
            return;
        }

        require_once __DIR__ . '/elementor-widgets/calendar-widget.php';
        require_once __DIR__ . '/elementor-widgets/review-widget.php';
        require_once __DIR__ . '/elementor-widgets/advanced-form-widget.php';
        require_once __DIR__ . '/elementor-widgets/payment-form-widget.php';

        \Elementor\Plugin::instance()->widgets_manager->register( new SofirCodeWattzWidgets\Calendar_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register( new SofirCodeWattzWidgets\Review_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register( new SofirCodeWattzWidgets\Advanced_Form_Widget() );
        \Elementor\Plugin::instance()->widgets_manager->register( new SofirCodeWattzWidgets\Payment_Form_Widget() );
    }

    public function register_shortcodes(): void {
        add_shortcode( 'sofir_codewattz_calendar', [ $this, 'render_calendar_shortcode' ] );
        add_shortcode( 'sofir_codewattz_reviews', [ $this, 'render_reviews_shortcode' ] );
        add_shortcode( 'sofir_codewattz_booking', [ $this, 'render_booking_shortcode' ] );
    }

    public function render_calendar_shortcode( $atts ): string {
        $atts = shortcode_atts([
            'view' => 'month',
            'post_type' => 'appointment',
            'category' => '',
            'limit' => 30,
            'show_filters' => 'yes',
        ], $atts );

        wp_enqueue_style( 'sofir-codewattz-calendar' );
        wp_enqueue_script( 'sofir-codewattz-calendar' );

        ob_start();
        ?>
        <div class="sofir-codewattz-calendar" 
             data-view="<?php echo esc_attr( $atts['view'] ); ?>" 
             data-post-type="<?php echo esc_attr( $atts['post_type'] ); ?>"
             data-category="<?php echo esc_attr( $atts['category'] ); ?>"
             data-limit="<?php echo esc_attr( $atts['limit'] ); ?>"
             data-show-filters="<?php echo esc_attr( $atts['show_filters'] ); ?>">
            <div class="calendar-loading">
                <div class="spinner"></div>
                <?php esc_html_e( 'Loading calendar...', 'sofir' ); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_reviews_shortcode( $atts ): string {
        $atts = shortcode_atts([
            'post_id' => get_the_ID(),
            'show_photos' => 'yes',
            'show_form' => 'yes',
            'per_page' => 10,
        ], $atts );

        wp_enqueue_style( 'sofir-codewattz-reviews' );
        wp_enqueue_script( 'sofir-codewattz-reviews' );

        ob_start();
        ?>
        <div class="sofir-codewattz-reviews" 
             data-post-id="<?php echo esc_attr( $atts['post_id'] ); ?>"
             data-show-photos="<?php echo esc_attr( $atts['show_photos'] ); ?>"
             data-show-form="<?php echo esc_attr( $atts['show_form'] ); ?>"
             data-per-page="<?php echo esc_attr( $atts['per_page'] ); ?>">
            <div class="reviews-loading">
                <div class="spinner"></div>
                <?php esc_html_e( 'Loading reviews...', 'sofir' ); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_booking_shortcode( $atts ): string {
        $atts = shortcode_atts([
            'service' => '',
            'show_calendar' => 'yes',
            'time_slots' => '30',
            'redirect_url' => '',
        ], $atts );

        wp_enqueue_style( 'sofir-codewattz-booking' );
        wp_enqueue_script( 'sofir-codewattz-booking' );

        ob_start();
        ?>
        <div class="sofir-codewattz-booking" 
             data-service="<?php echo esc_attr( $atts['service'] ); ?>"
             data-show-calendar="<?php echo esc_attr( $atts['show_calendar'] ); ?>"
             data-time-slots="<?php echo esc_attr( $atts['time_slots'] ); ?>"
             data-redirect-url="<?php echo esc_attr( $atts['redirect_url'] ); ?>">
            <div class="booking-loading">
                <div class="spinner"></div>
                <?php esc_html_e( 'Loading booking form...', 'sofir' ); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function register_rest_routes(): void {
        register_rest_route( 'sofir/v1', '/codewattz/calendar', [
            'methods' => 'GET',
            'callback' => [ $this, 'get_calendar_data' ],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route( 'sofir/v1', '/codewattz/reviews', [
            'methods' => 'GET',
            'callback' => [ $this, 'get_reviews_data' ],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route( 'sofir/v1', '/codewattz/booking', [
            'methods' => 'POST',
            'callback' => [ $this, 'process_booking' ],
            'permission_callback' => function() {
                return current_user_can( 'read' );
            },
        ]);
    }

    public function get_calendar_data( \WP_REST_Request $request ): \WP_REST_Response {
        $post_type = $request->get_param( 'post_type' );
        $start_date = $request->get_param( 'start_date' );
        $end_date = $request->get_param( 'end_date' );

        $calendar_integration = Calendar_Integration::instance();
        $events = $calendar_integration->get_events( $post_type, $start_date, $end_date );

        return new \WP_REST_Response( $events, 200 );
    }

    public function get_reviews_data( \WP_REST_Request $request ): \WP_REST_Response {
        $post_id = $request->get_param( 'post_id' );
        $page = $request->get_param( 'page' ) ?? 1;

        $review_system = Review_System::instance();
        $reviews = $review_system->get_reviews( $post_id, $page );

        return new \WP_REST_Response( $reviews, 200 );
    }

    public function process_booking( \WP_REST_Request $request ): \WP_REST_Response {
        $data = $request->get_json_params();
        
        $calendar_integration = Calendar_Integration::instance();
        $result = $calendar_integration->create_booking( $data );
        
        if ( $result['success'] ) {
            return new \WP_REST_Response( $result, 200 );
        } else {
            return new \WP_REST_Response( $result, 400 );
        }
    }

    public function enqueue_assets(): void {
        // Calendar assets
        wp_enqueue_style(
            'sofir-codewattz-calendar',
            SOFIR_PLUGIN_URL . '/modules/codewattz-integration/assets/css/calendar.css',
            [],
            SOFIR_VERSION
        );
        
        wp_enqueue_script(
            'sofir-codewattz-calendar',
            SOFIR_PLUGIN_URL . '/modules/codewattz-integration/assets/js/calendar.js',
            [ 'jquery', 'moment' ],
            SOFIR_VERSION,
            true
        );

        // Reviews assets
        wp_enqueue_style(
            'sofir-codewattz-reviews',
            SOFIR_PLUGIN_URL . '/modules/codewattz-integration/assets/css/reviews.css',
            [],
            SOFIR_VERSION
        );
        
        wp_enqueue_script(
            'sofir-codewattz-reviews',
            SOFIR_PLUGIN_URL . '/modules/codewattz-integration/assets/js/reviews.js',
            [ 'jquery' ],
            SOFIR_VERSION,
            true
        );

        // Booking assets
        wp_enqueue_style(
            'sofir-codewattz-booking',
            SOFIR_PLUGIN_URL . '/modules/codewattz-integration/assets/css/booking.css',
            [],
            SOFIR_VERSION
        );
        
        wp_enqueue_script(
            'sofir-codewattz-booking',
            SOFIR_PLUGIN_URL . '/modules/codewattz-integration/assets/js/booking.js',
            [ 'jquery' ],
            SOFIR_VERSION,
            true
        );

        // Localize scripts
        wp_localize_script( 'sofir-codewattz-calendar', 'sofirCodewattz', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'restUrl' => rest_url( 'sofir/v1/codewattz/' ),
            'nonce' => wp_create_nonce( 'sofir_codewattz_nonce' ),
            'strings' => [
                'loading' => __( 'Loading...', 'sofir' ),
                'error' => __( 'An error occurred. Please try again.', 'sofir' ),
                'noEvents' => __( 'No events found.', 'sofir' ),
                'noReviews' => __( 'No reviews yet.', 'sofir' ),
                'bookingSuccess' => __( 'Booking successful!', 'sofir' ),
                'bookingError' => __( 'Booking failed. Please try again.', 'sofir' ),
            ],
        ]);
    }

    public function enqueue_admin_assets(): void {
        wp_enqueue_style(
            'sofir-codewattz-admin',
            SOFIR_PLUGIN_URL . '/modules/codewattz-integration/assets/css/admin.css',
            [],
            SOFIR_VERSION
        );
        
        wp_enqueue_script(
            'sofir-codewattz-admin',
            SOFIR_PLUGIN_URL . '/modules/codewattz-integration/assets/js/admin.js',
            [ 'jquery' ],
            SOFIR_VERSION,
            true
        );
    }
}