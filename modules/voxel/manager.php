<?php
namespace Sofir\Voxel;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        \add_action( 'wp_ajax_sofir_voxel_filter_listings', [ $this, 'handle_filter_listings_ajax' ] );
        \add_action( 'wp_ajax_nopriv_sofir_voxel_filter_listings', [ $this, 'handle_filter_listings_ajax' ] );
        \add_action( 'wp_ajax_sofir_location_suggestions', [ $this, 'handle_location_suggestions_ajax' ] );
        \add_action( 'wp_ajax_nopriv_sofir_location_suggestions', [ $this, 'handle_location_suggestions_ajax' ] );

        if ( ! $this->is_voxel_active() ) {
            return;
        }

        \add_filter( 'sofir/cpt/register_args', [ $this, 'enhance_cpt_for_voxel' ], 10, 2 );
        \add_filter( 'sofir/field/meta_config', [ $this, 'map_field_to_voxel' ], 10, 3 );
        \add_action( 'voxel/post-types/register', [ $this, 'register_sofir_cpts_to_voxel' ] );
        \add_filter( 'voxel/templates/available', [ $this, 'add_sofir_templates_to_voxel' ] );
        \add_action( 'admin_notices', [ $this, 'show_compatibility_notice' ] );
    }

    public function is_voxel_active(): bool {
        return defined( 'VOXEL_VERSION' ) || class_exists( '\Voxel\Post_Type' );
    }

    public function enhance_cpt_for_voxel( array $args, string $slug ): array {
        $args['voxel_enabled'] = true;
        $args['voxel_templates'] = true;
        $args['voxel_filters'] = true;
        
        if ( ! isset( $args['supports'] ) ) {
            $args['supports'] = [];
        }
        
        if ( ! in_array( 'custom-fields', $args['supports'], true ) ) {
            $args['supports'][] = 'custom-fields';
        }
        
        return $args;
    }

    public function map_field_to_voxel( array $config, string $field_key, string $post_type ): array {
        $voxel_mapping = [
            'location' => 'location',
            'hours' => 'work-hours',
            'rating' => 'number',
            'status' => 'select',
            'price' => 'number',
            'contact' => 'email',
            'gallery' => 'image',
            'attributes' => 'repeater',
            'event_date' => 'date',
            'event_capacity' => 'number',
            'appointment_datetime' => 'date',
            'appointment_duration' => 'number',
            'appointment_status' => 'select',
        ];

        $sofir_type = $config['type'] ?? 'text';
        $config['voxel_type'] = $voxel_mapping[ $sofir_type ] ?? 'text';
        $config['voxel_searchable'] = ! empty( $config['filterable'] );
        $config['voxel_show_in_card'] = true;
        
        return $config;
    }

    public function register_sofir_cpts_to_voxel(): void {
        $cpt_manager = \Sofir\Cpt\Manager::instance();
        $post_types = $cpt_manager->get_post_types();
        
        foreach ( $post_types as $slug => $definition ) {
            if ( ! post_type_exists( $slug ) ) {
                continue;
            }
            
            $voxel_config = $this->convert_to_voxel_config( $slug, $definition );
            \do_action( 'voxel/register-post-type', $voxel_config );
        }
    }

    public function add_sofir_templates_to_voxel( array $templates ): array {
        $sofir_templates = $this->get_voxel_compatible_templates();
        return array_merge( $templates, $sofir_templates );
    }

    private function convert_to_voxel_config( string $slug, array $definition ): array {
        $args = $definition['args'] ?? [];
        $fields = $definition['fields'] ?? [];
        
        $voxel_config = [
            'key' => $slug,
            'singular' => $args['labels']['singular_name'] ?? ucfirst( $slug ),
            'plural' => $args['labels']['name'] ?? ucfirst( $slug ) . 's',
            'icon' => $this->extract_icon_from_menu_icon( $args['menu_icon'] ?? 'dashicons-admin-post' ),
            'templates' => [
                'single' => 'sofir-single-' . $slug,
                'archive' => 'sofir-archive-' . $slug,
                'card' => 'sofir-card-' . $slug,
            ],
            'fields' => [],
            'search' => [
                'enabled' => true,
                'advanced' => true,
            ],
        ];

        foreach ( $fields as $field_key => $field_config ) {
            $voxel_config['fields'][] = $this->convert_field_to_voxel( $field_key, $field_config );
        }
        
        return $voxel_config;
    }

    private function convert_field_to_voxel( string $key, array $config ): array {
        $type_mapping = [
            'location' => 'location',
            'hours' => 'work-hours',
            'rating' => 'number',
            'status' => 'select',
            'price' => 'number',
            'contact' => 'email',
            'gallery' => 'image',
            'attributes' => 'repeater',
            'event_date' => 'date',
            'event_capacity' => 'number',
            'appointment_datetime' => 'date',
            'appointment_duration' => 'number',
            'appointment_status' => 'select',
        ];

        $voxel_field = [
            'key' => $key,
            'label' => $config['label'] ?? ucfirst( str_replace( '_', ' ', $key ) ),
            'type' => $type_mapping[ $config['type'] ?? 'text' ] ?? 'text',
            'searchable' => ! empty( $config['filterable'] ),
            'show-in-card' => true,
            'show-in-single' => true,
        ];

        if ( isset( $config['options'] ) && is_array( $config['options'] ) ) {
            $voxel_field['choices'] = $config['options'];
        }

        if ( $voxel_field['type'] === 'number' ) {
            $voxel_field['min'] = $config['min'] ?? 0;
            $voxel_field['max'] = $config['max'] ?? 999999;
            $voxel_field['step'] = $config['step'] ?? 1;
        }

        if ( $voxel_field['type'] === 'location' ) {
            $voxel_field['map-format'] = 'address';
            $voxel_field['map-skin'] = 'default';
        }

        return $voxel_field;
    }

    private function extract_icon_from_menu_icon( string $menu_icon ): string {
        if ( strpos( $menu_icon, 'dashicons-' ) === 0 ) {
            return str_replace( 'dashicons-', '', $menu_icon );
        }
        return 'admin-post';
    }

    private function get_voxel_compatible_templates(): array {
        return [
            'sofir-business-directory' => [
                'label' => \__( 'SOFIR Business Directory', 'sofir' ),
                'post_type' => 'listing',
                'icon' => 'location-alt',
                'layouts' => [ 'grid', 'list', 'map' ],
                'features' => [ 'location', 'rating', 'hours', 'contact' ],
            ],
            'sofir-events-calendar' => [
                'label' => \__( 'SOFIR Events Calendar', 'sofir' ),
                'post_type' => 'event',
                'icon' => 'calendar-alt',
                'layouts' => [ 'calendar', 'list', 'grid' ],
                'features' => [ 'event_date', 'event_capacity', 'location', 'registration' ],
            ],
            'sofir-hotel-booking' => [
                'label' => \__( 'SOFIR Hotel Booking', 'sofir' ),
                'post_type' => 'hotel',
                'icon' => 'building',
                'layouts' => [ 'grid', 'list', 'map' ],
                'features' => [ 'price', 'rating', 'gallery', 'location', 'booking' ],
            ],
            'sofir-restaurant-menu' => [
                'label' => \__( 'SOFIR Restaurant Menu', 'sofir' ),
                'post_type' => 'menu_item',
                'icon' => 'food',
                'layouts' => [ 'menu', 'grid', 'list' ],
                'features' => [ 'price', 'gallery', 'attributes', 'orders' ],
            ],
            'sofir-course-catalog' => [
                'label' => \__( 'SOFIR Course Catalog', 'sofir' ),
                'post_type' => 'course',
                'icon' => 'book',
                'layouts' => [ 'grid', 'list' ],
                'features' => [ 'price', 'rating', 'lessons', 'enrollment' ],
            ],
        ];
    }

    public function show_compatibility_notice(): void {
        if ( ! $this->is_voxel_active() ) {
            return;
        }

        $screen = \get_current_screen();
        if ( ! $screen || strpos( $screen->id, 'sofir' ) === false ) {
            return;
        }

        if ( \get_option( 'sofir_voxel_notice_dismissed' ) ) {
            return;
        }

        echo '<div class="notice notice-success is-dismissible" data-dismissible="sofir-voxel-notice">';
        echo '<p><strong>' . \esc_html__( '🎉 Voxel Theme Compatibility Enabled!', 'sofir' ) . '</strong></p>';
        echo '<p>' . \esc_html__( 'SOFIR CPT Library templates are now fully compatible with Voxel theme. All custom post types will work seamlessly with Voxel\'s templates, filters, and Elementor widgets.', 'sofir' ) . '</p>';
        echo '<ul style="list-style: disc; margin-left: 20px;">';
        echo '<li>' . \esc_html__( 'Auto field mapping to Voxel field types', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Native Voxel template support', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Elementor widgets compatible with Voxel', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Advanced search & filters integrated', 'sofir' ) . '</li>';
        echo '</ul>';
        echo '</div>';
    }

    public function get_field_mapping(): array {
        return [
            'location' => [
                'voxel_type' => 'location',
                'supports' => [ 'address', 'latitude', 'longitude', 'map_zoom' ],
                'searchable' => true,
                'card_display' => true,
            ],
            'hours' => [
                'voxel_type' => 'work-hours',
                'supports' => [ 'days', 'hours', 'timezone' ],
                'searchable' => true,
                'card_display' => true,
            ],
            'rating' => [
                'voxel_type' => 'number',
                'supports' => [ 'min', 'max', 'step', 'suffix' ],
                'searchable' => true,
                'card_display' => true,
            ],
            'status' => [
                'voxel_type' => 'select',
                'supports' => [ 'choices', 'multiple' ],
                'searchable' => true,
                'card_display' => true,
            ],
            'price' => [
                'voxel_type' => 'number',
                'supports' => [ 'min', 'max', 'step', 'suffix', 'prefix' ],
                'searchable' => true,
                'card_display' => true,
            ],
            'contact' => [
                'voxel_type' => 'email',
                'supports' => [ 'email', 'phone', 'website' ],
                'searchable' => false,
                'card_display' => true,
            ],
            'gallery' => [
                'voxel_type' => 'image',
                'supports' => [ 'multiple', 'max_count', 'max_size' ],
                'searchable' => false,
                'card_display' => true,
            ],
            'attributes' => [
                'voxel_type' => 'repeater',
                'supports' => [ 'fields', 'max_items' ],
                'searchable' => true,
                'card_display' => false,
            ],
            'event_date' => [
                'voxel_type' => 'date',
                'supports' => [ 'format', 'range' ],
                'searchable' => true,
                'card_display' => true,
            ],
            'event_capacity' => [
                'voxel_type' => 'number',
                'supports' => [ 'min', 'max' ],
                'searchable' => true,
                'card_display' => true,
            ],
            'appointment_datetime' => [
                'voxel_type' => 'date',
                'supports' => [ 'format', 'time', 'range' ],
                'searchable' => true,
                'card_display' => true,
            ],
            'appointment_duration' => [
                'voxel_type' => 'number',
                'supports' => [ 'min', 'max', 'suffix' ],
                'searchable' => false,
                'card_display' => true,
            ],
            'appointment_status' => [
                'voxel_type' => 'select',
                'supports' => [ 'choices' ],
                'searchable' => true,
                'card_display' => true,
            ],
        ];
    }

    public function get_elementor_widget_compatibility(): array {
        return [
            'post-feed' => 'voxel-compatible',
            'term-feed' => 'voxel-compatible',
            'search-form' => 'voxel-native',
            'map' => 'voxel-native',
            'event-list' => 'voxel-compatible',
            'event-calendar' => 'voxel-compatible',
            'booking-form' => 'voxel-compatible',
            'restaurant-menu' => 'voxel-compatible',
            'course-list' => 'voxel-compatible',
        ];
    }

    public function enqueue_assets(): void {
        \wp_enqueue_style(
            'sofir-voxel-integration',
            SOFIR_PLUGIN_URL . 'assets/css/voxel-integration.css',
            [],
            SOFIR_VERSION
        );

        \wp_enqueue_script(
            'sofir-voxel-integration',
            SOFIR_PLUGIN_URL . 'assets/js/voxel-integration.js',
            [ 'jquery' ],
            SOFIR_VERSION,
            true
        );

        \wp_localize_script(
            'sofir-voxel-integration',
            'sofirVoxel',
            [
                'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
                'nonce' => \wp_create_nonce( 'sofir_voxel' ),
                'isVoxelActive' => $this->is_voxel_active(),
            ]
        );
    }

    public function handle_filter_listings_ajax(): void {
        \check_ajax_referer( 'sofir_voxel', 'nonce' );

        $post_type = isset( $_POST['post_type'] ) ? \sanitize_key( $_POST['post_type'] ) : 'listing';
        $settings = isset( $_POST['settings'] ) ? (array) $_POST['settings'] : [];
        
        $query_args = [
            'post_type' => $post_type,
            'posts_per_page' => isset( $_POST['posts_per_page'] ) ? (int) $_POST['posts_per_page'] : 12,
            'orderby' => isset( $_POST['orderby'] ) ? \sanitize_key( $_POST['orderby'] ) : 'date',
            'order' => isset( $_POST['order'] ) ? \sanitize_key( $_POST['order'] ) : 'DESC',
            'post_status' => 'publish',
        ];

        if ( isset( $_POST['paged'] ) ) {
            $query_args['paged'] = (int) $_POST['paged'];
        }

        if ( isset( $_POST['s'] ) && ! empty( $_POST['s'] ) ) {
            $query_args['s'] = \sanitize_text_field( $_POST['s'] );
        }

        $meta_query = [];
        if ( isset( $_POST['sofir_rating'] ) && ! empty( $_POST['sofir_rating'] ) ) {
            $meta_query[] = [
                'key' => 'sofir_rating',
                'value' => (float) $_POST['sofir_rating'],
                'compare' => '>=',
                'type' => 'NUMERIC',
            ];
        }

        if ( ! empty( $meta_query ) ) {
            $query_args['meta_query'] = $meta_query;
        }

        $query = new \WP_Query( $query_args );

        ob_start();
        
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $this->render_listing_card( $settings );
            }
            \wp_reset_postdata();
        } else {
            echo '<div class="sofir-no-results">';
            echo '<p>' . \esc_html__( 'No listings found.', 'sofir' ) . '</p>';
            echo '</div>';
        }

        $html = ob_get_clean();

        $pagination = '';
        if ( $query->max_num_pages > 1 ) {
            $pagination = \paginate_links( [
                'total' => $query->max_num_pages,
                'current' => max( 1, $query_args['paged'] ?? 1 ),
                'prev_text' => \esc_html__( '← Previous', 'sofir' ),
                'next_text' => \esc_html__( 'Next →', 'sofir' ),
                'type' => 'plain',
            ] );
        }

        \wp_send_json_success( [
            'html' => $html,
            'pagination' => $pagination,
            'total' => $query->found_posts,
        ] );
    }

    private function render_listing_card( array $settings ): void {
        echo '<article class="sofir-listing-card">';
        
        if ( \has_post_thumbnail() ) {
            echo '<div class="sofir-card-image">';
            echo '<a href="' . \esc_url( \get_permalink() ) . '">';
            \the_post_thumbnail( 'medium' );
            echo '</a>';
            echo '</div>';
        }
        
        echo '<div class="sofir-card-content">';
        echo '<h3 class="sofir-card-title">';
        echo '<a href="' . \esc_url( \get_permalink() ) . '">' . \esc_html( \get_the_title() ) . '</a>';
        echo '</h3>';
        
        if ( \has_excerpt() ) {
            echo '<div class="sofir-card-excerpt">';
            \the_excerpt();
            echo '</div>';
        }
        
        echo '<div class="sofir-card-meta">';
        $this->render_card_meta();
        echo '</div>';
        
        echo '</div>';
        echo '</article>';
    }

    private function render_card_meta(): void {
        $rating = \get_post_meta( \get_the_ID(), 'sofir_rating', true );
        if ( $rating ) {
            echo '<span class="sofir-rating">⭐ ' . \esc_html( $rating ) . '</span>';
        }

        $location = \get_post_meta( \get_the_ID(), 'sofir_location', true );
        if ( $location ) {
            echo '<span class="sofir-location">📍 ' . \esc_html( $location ) . '</span>';
        }

        $price = \get_post_meta( \get_the_ID(), 'sofir_price', true );
        if ( $price ) {
            echo '<span class="sofir-price">💰 ' . \esc_html( $price ) . '</span>';
        }
    }

    public function handle_location_suggestions_ajax(): void {
        \check_ajax_referer( 'sofir_voxel', 'nonce' );

        $query = isset( $_POST['query'] ) ? \sanitize_text_field( $_POST['query'] ) : '';
        
        if ( strlen( $query ) < 3 ) {
            \wp_send_json_error( [ 'message' => \__( 'Query too short', 'sofir' ) ] );
        }

        $suggestions = $this->get_location_suggestions( $query );

        \wp_send_json_success( [ 'suggestions' => $suggestions ] );
    }

    private function get_location_suggestions( string $query ): array {
        $suggestions = [];

        $args = [
            'post_type' => 'any',
            'posts_per_page' => 10,
            'meta_query' => [
                [
                    'key' => 'sofir_location',
                    'value' => $query,
                    'compare' => 'LIKE',
                ],
            ],
        ];

        $query_obj = new \WP_Query( $args );

        if ( $query_obj->have_posts() ) {
            while ( $query_obj->have_posts() ) {
                $query_obj->the_post();
                $location = \get_post_meta( \get_the_ID(), 'sofir_location', true );
                if ( $location && ! in_array( $location, $suggestions, true ) ) {
                    $suggestions[] = $location;
                }
            }
            \wp_reset_postdata();
        }

        return array_unique( $suggestions );
    }
}
