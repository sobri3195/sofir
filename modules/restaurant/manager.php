<?php
namespace Sofir\Restaurant;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'wp_ajax_sofir_create_order', [ $this, 'handle_create_order' ] );
        \add_action( 'wp_ajax_nopriv_sofir_create_order', [ $this, 'handle_create_order' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_shortcode( 'sofir_restaurant_menu', [ $this, 'render_menu_shortcode' ] );
        \add_shortcode( 'sofir_order_form', [ $this, 'render_order_form' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/restaurant/orders',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_orders' ],
                'permission_callback' => function () {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/restaurant/orders/(?P<id>\d+)',
            [
                'methods' => 'PATCH',
                'callback' => [ $this, 'rest_update_order_status' ],
                'permission_callback' => function () {
                    return \current_user_can( 'edit_posts' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/restaurant/menu',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_menu' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function register_assets(): void {
        \wp_register_script(
            'sofir-restaurant',
            SOFIR_ASSETS_URL . 'js/restaurant.js',
            [ 'wp-api-fetch' ],
            SOFIR_VERSION,
            true
        );

        \wp_localize_script(
            'sofir-restaurant',
            'SOFIR_RESTAURANT_DATA',
            [
                'restRoot' => \esc_url_raw( \rest_url() ),
                'nonce' => \wp_create_nonce( 'wp_rest' ),
            ]
        );

        \wp_register_style(
            'sofir-restaurant',
            SOFIR_ASSETS_URL . 'css/restaurant.css',
            [],
            SOFIR_VERSION
        );
    }

    public function handle_create_order(): void {
        $order_type = isset( $_POST['order_type'] ) ? \sanitize_key( $_POST['order_type'] ) : 'dine_in';
        $items = isset( $_POST['items'] ) ? json_decode( \stripslashes( $_POST['items'] ), true ) : [];
        $customer_name = isset( $_POST['customer_name'] ) ? \sanitize_text_field( $_POST['customer_name'] ) : '';
        $customer_phone = isset( $_POST['customer_phone'] ) ? \sanitize_text_field( $_POST['customer_phone'] ) : '';
        $customer_address = isset( $_POST['customer_address'] ) ? \sanitize_textarea_field( $_POST['customer_address'] ) : '';
        $table_number = isset( $_POST['table_number'] ) ? \sanitize_text_field( $_POST['table_number'] ) : '';
        $notes = isset( $_POST['notes'] ) ? \sanitize_textarea_field( $_POST['notes'] ) : '';

        if ( empty( $customer_name ) || empty( $customer_phone ) ) {
            \wp_send_json_error( \__( 'Customer name and phone are required.', 'sofir' ) );
            return;
        }

        if ( empty( $items ) || ! is_array( $items ) ) {
            \wp_send_json_error( \__( 'Order items are required.', 'sofir' ) );
            return;
        }

        if ( $order_type === 'delivery' && empty( $customer_address ) ) {
            \wp_send_json_error( \__( 'Delivery address is required.', 'sofir' ) );
            return;
        }

        $total = 0;
        $items_text = [];
        foreach ( $items as $item ) {
            if ( ! isset( $item['name'] ) || ! isset( $item['price'] ) || ! isset( $item['quantity'] ) ) {
                continue;
            }
            $subtotal = (float) $item['price'] * (int) $item['quantity'];
            $total += $subtotal;
            $items_text[] = sprintf(
                '%s x %d = %s',
                $item['name'],
                $item['quantity'],
                \number_format_i18n( $subtotal, 0 )
            );
        }

        $order_title = sprintf(
            '%s - %s - %s',
            $order_type === 'dine_in' ? \__( 'Dine In', 'sofir' ) : \__( 'Delivery', 'sofir' ),
            $customer_name,
            \current_time( 'H:i' )
        );

        $order_content = implode( "\n", $items_text );
        if ( $notes ) {
            $order_content .= "\n\n" . \__( 'Notes:', 'sofir' ) . ' ' . $notes;
        }

        $post_data = [
            'post_title'   => $order_title,
            'post_content' => $order_content,
            'post_type'    => 'restaurant_order',
            'post_status'  => 'publish',
        ];

        if ( \is_user_logged_in() ) {
            $post_data['post_author'] = \get_current_user_id();
        }

        $post_id = \wp_insert_post( $post_data );

        if ( \is_wp_error( $post_id ) ) {
            \wp_send_json_error( \__( 'Failed to create order.', 'sofir' ) );
            return;
        }

        \update_post_meta( $post_id, 'sofir_order_type', $order_type );
        \update_post_meta( $post_id, 'sofir_order_status', 'pending' );
        \update_post_meta( $post_id, 'sofir_customer_name', $customer_name );
        \update_post_meta( $post_id, 'sofir_customer_phone', $customer_phone );
        \update_post_meta( $post_id, 'sofir_customer_address', $customer_address );
        \update_post_meta( $post_id, 'sofir_table_number', $table_number );
        \update_post_meta( $post_id, 'sofir_order_items', $items );
        \update_post_meta( $post_id, 'sofir_order_total', $total );
        \update_post_meta( $post_id, 'sofir_order_datetime', \current_time( 'mysql' ) );

        \do_action( 'sofir/restaurant/order_created', $post_id, $order_type );

        \wp_send_json_success( [
            'order_id' => $post_id,
            'order_number' => sprintf( 'ORD-%05d', $post_id ),
            'message' => \__( 'Order created successfully!', 'sofir' ),
        ] );
    }

    public function rest_get_orders( \WP_REST_Request $request ): \WP_REST_Response {
        $status = $request->get_param( 'status' );
        $type = $request->get_param( 'type' );
        
        $args = [
            'post_type' => 'restaurant_order',
            'posts_per_page' => 50,
            'orderby' => 'date',
            'order' => 'DESC',
        ];

        if ( $status ) {
            $args['meta_query'] = [
                [
                    'key' => 'sofir_order_status',
                    'value' => \sanitize_key( $status ),
                ],
            ];
        }

        if ( $type ) {
            $args['meta_query'][] = [
                'key' => 'sofir_order_type',
                'value' => \sanitize_key( $type ),
            ];
        }

        $query = new \WP_Query( $args );
        $orders = [];

        foreach ( $query->posts as $post ) {
            $orders[] = [
                'id' => $post->ID,
                'title' => $post->post_title,
                'type' => \get_post_meta( $post->ID, 'sofir_order_type', true ),
                'status' => \get_post_meta( $post->ID, 'sofir_order_status', true ),
                'customer_name' => \get_post_meta( $post->ID, 'sofir_customer_name', true ),
                'customer_phone' => \get_post_meta( $post->ID, 'sofir_customer_phone', true ),
                'total' => (float) \get_post_meta( $post->ID, 'sofir_order_total', true ),
                'datetime' => \get_post_meta( $post->ID, 'sofir_order_datetime', true ),
                'items' => \get_post_meta( $post->ID, 'sofir_order_items', true ),
            ];
        }

        return \rest_ensure_response( $orders );
    }

    public function rest_update_order_status( \WP_REST_Request $request ): \WP_REST_Response {
        $order_id = (int) $request->get_param( 'id' );
        $status = \sanitize_key( (string) $request->get_param( 'status' ) );

        if ( ! in_array( $status, [ 'pending', 'preparing', 'ready', 'completed', 'cancelled' ], true ) ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Invalid status', 'sofir' ) ], 400 );
        }

        \update_post_meta( $order_id, 'sofir_order_status', $status );

        \do_action( 'sofir/restaurant/order_status_changed', $order_id, $status );

        return \rest_ensure_response( [
            'status' => 'success',
            'order_id' => $order_id,
            'new_status' => $status,
        ] );
    }

    public function rest_get_menu(): \WP_REST_Response {
        $args = [
            'post_type' => 'menu_item',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ];

        $query = new \WP_Query( $args );
        $menu = [];

        foreach ( $query->posts as $post ) {
            $menu[] = [
                'id' => $post->ID,
                'name' => $post->post_title,
                'description' => $post->post_excerpt,
                'price' => (float) \get_post_meta( $post->ID, 'sofir_menu_price', true ),
                'category' => \get_post_meta( $post->ID, 'sofir_menu_category', true ),
                'image' => \get_the_post_thumbnail_url( $post->ID, 'medium' ),
                'available' => (bool) \get_post_meta( $post->ID, 'sofir_menu_available', true ),
            ];
        }

        return \rest_ensure_response( $menu );
    }

    public function render_menu_shortcode( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'category' => '',
                'columns' => 3,
            ],
            $atts,
            'sofir_restaurant_menu'
        );

        \wp_enqueue_style( 'sofir-restaurant' );

        $args = [
            'post_type' => 'menu_item',
            'posts_per_page' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ];

        if ( $atts['category'] ) {
            $args['meta_query'] = [
                [
                    'key' => 'sofir_menu_category',
                    'value' => \sanitize_text_field( $atts['category'] ),
                ],
            ];
        }

        $query = new \WP_Query( $args );

        if ( ! $query->have_posts() ) {
            return '<p>' . \esc_html__( 'No menu items found.', 'sofir' ) . '</p>';
        }

        ob_start();
        echo '<div class="sofir-restaurant-menu" style="display: grid; grid-template-columns: repeat(' . \absint( $atts['columns'] ) . ', 1fr); gap: 20px;">';

        while ( $query->have_posts() ) {
            $query->the_post();
            $price = (float) \get_post_meta( \get_the_ID(), 'sofir_menu_price', true );
            $available = (bool) \get_post_meta( \get_the_ID(), 'sofir_menu_available', true );

            echo '<div class="sofir-menu-item" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px;">';
            if ( \has_post_thumbnail() ) {
                echo '<div class="menu-item-image" style="margin-bottom: 15px;">';
                \the_post_thumbnail( 'medium', [ 'style' => 'width: 100%; height: 200px; object-fit: cover; border-radius: 8px;' ] );
                echo '</div>';
            }
            echo '<h3 style="margin: 0 0 10px 0;">' . \esc_html( \get_the_title() ) . '</h3>';
            if ( \get_the_excerpt() ) {
                echo '<p style="color: #666; margin: 0 0 10px 0;">' . \esc_html( \get_the_excerpt() ) . '</p>';
            }
            echo '<div class="menu-item-price" style="font-size: 18px; font-weight: bold; color: #0073aa;">';
            echo 'Rp ' . \number_format_i18n( $price, 0 );
            echo '</div>';
            if ( ! $available ) {
                echo '<div style="color: #dc3232; margin-top: 10px;">' . \esc_html__( 'Not Available', 'sofir' ) . '</div>';
            }
            echo '</div>';
        }

        echo '</div>';
        \wp_reset_postdata();

        return (string) ob_get_clean();
    }

    public function render_order_form( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'type' => 'dine_in',
            ],
            $atts,
            'sofir_order_form'
        );

        \wp_enqueue_script( 'sofir-restaurant' );
        \wp_enqueue_style( 'sofir-restaurant' );

        ob_start();
        echo '<div class="sofir-order-form" data-order-type="' . \esc_attr( $atts['type'] ) . '">';
        echo '<h3>' . \esc_html__( 'Place Your Order', 'sofir' ) . '</h3>';
        echo '<form id="sofir-restaurant-order-form">';
        echo '<input type="hidden" name="order_type" value="' . \esc_attr( $atts['type'] ) . '" />';
        
        echo '<div class="form-field">';
        echo '<label>' . \esc_html__( 'Name', 'sofir' ) . ' <span style="color: red;">*</span></label>';
        echo '<input type="text" name="customer_name" required />';
        echo '</div>';

        echo '<div class="form-field">';
        echo '<label>' . \esc_html__( 'Phone', 'sofir' ) . ' <span style="color: red;">*</span></label>';
        echo '<input type="tel" name="customer_phone" required />';
        echo '</div>';

        if ( $atts['type'] === 'delivery' ) {
            echo '<div class="form-field">';
            echo '<label>' . \esc_html__( 'Delivery Address', 'sofir' ) . ' <span style="color: red;">*</span></label>';
            echo '<textarea name="customer_address" required></textarea>';
            echo '</div>';
        } else {
            echo '<div class="form-field">';
            echo '<label>' . \esc_html__( 'Table Number', 'sofir' ) . '</label>';
            echo '<input type="text" name="table_number" />';
            echo '</div>';
        }

        echo '<div class="form-field">';
        echo '<label>' . \esc_html__( 'Notes', 'sofir' ) . '</label>';
        echo '<textarea name="notes"></textarea>';
        echo '</div>';

        echo '<div id="order-items-container"></div>';

        echo '<button type="submit" class="button button-primary">' . \esc_html__( 'Submit Order', 'sofir' ) . '</button>';
        echo '</form>';
        echo '</div>';

        return (string) ob_get_clean();
    }
}
