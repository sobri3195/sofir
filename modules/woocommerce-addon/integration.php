<?php
namespace Sofir\WooCommerceAddon;

class Integration {
    private static ?Integration $instance = null;

    public static function instance(): Integration {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init(): void {
        $this->register_hooks();
        $this->register_ajax_handlers();
    }

    private function register_hooks(): void {
        \add_filter( 'sofir/admin/tabs', [ $this, 'add_admin_tab' ] );
        \add_action( 'sofir_admin_tab_woocommerce_addon', [ $this, 'render_admin_tab' ] );
        \add_filter( 'sofir/ecommerce/post_types', [ $this, 'enhance_post_types' ] );
        \add_action( 'woocommerce_product_set_stock', [ $this, 'on_stock_change' ] );
        \add_action( 'woocommerce_new_order', [ $this, 'on_new_order' ] );
    }

    private function register_ajax_handlers(): void {
        \add_action( 'wp_ajax_sofir_fetch_code_snippet', [ $this, 'fetch_code_snippet' ] );
        \add_action( 'wp_ajax_sofir_save_snippet', [ $this, 'save_snippet' ] );
        \add_action( 'wp_ajax_sofir_toggle_addon', [ $this, 'toggle_addon' ] );
        \add_action( 'wp_ajax_sofir_get_addon_status', [ $this, 'get_addon_status' ] );
    }

    public function add_admin_tab( array $tabs ): array {
        $tabs['woocommerce_addon'] = \__( 'WooCommerce', 'sofir' );
        return $tabs;
    }

    public function render_admin_tab(): void {
        Admin::instance()->render_admin_tab();
    }

    public function enhance_post_types( array $post_types ): array {
        if ( ! in_array( 'product', $post_types, true ) ) {
            $post_types[] = 'product';
        }
        return $post_types;
    }

    public function on_stock_change( $product ): void {
        if ( ! $product instanceof \WC_Product ) {
            return;
        }

        $is_enabled = \get_option( 'sofir_woocommerce_addon_enabled', false );
        if ( ! $is_enabled ) {
            return;
        }

        \do_action( 'sofir/woocommerce/stock_changed', [
            'product_id' => $product->get_id(),
            'product_name' => $product->get_name(),
            'stock_quantity' => $product->get_stock_quantity(),
            'stock_status' => $product->get_stock_status(),
        ] );
    }

    public function on_new_order( int $order_id ): void {
        $is_enabled = \get_option( 'sofir_woocommerce_addon_enabled', false );
        if ( ! $is_enabled ) {
            return;
        }

        $order = \wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        \do_action( 'sofir/woocommerce/new_order', [
            'order_id' => $order_id,
            'order_number' => $order->get_order_number(),
            'total' => $order->get_total(),
            'status' => $order->get_status(),
            'customer_email' => $order->get_billing_email(),
        ] );
    }

    public function fetch_code_snippet(): void {
        check_ajax_referer( 'sofir_wc_addon_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( \__( 'Unauthorized', 'sofir' ) );
        }

        $snippet_id = isset( $_POST['snippet_id'] ) ? sanitize_key( $_POST['snippet_id'] ) : '';
        if ( ! $snippet_id ) {
            wp_send_json_error( \__( 'Invalid snippet ID', 'sofir' ) );
        }

        $snippet = Snippets::instance()->get_snippet( $snippet_id );
        if ( ! $snippet ) {
            wp_send_json_error( \__( 'Snippet not found', 'sofir' ) );
        }

        wp_send_json_success( $snippet );
    }

    public function save_snippet(): void {
        check_ajax_referer( 'sofir_wc_addon_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( \__( 'Unauthorized', 'sofir' ) );
        }

        $snippet_data = isset( $_POST['snippet'] ) ? sanitize_text_field( $_POST['snippet'] ) : '';
        $snippet_name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';

        if ( ! $snippet_data || ! $snippet_name ) {
            wp_send_json_error( \__( 'Invalid data', 'sofir' ) );
        }

        $saved = Snippets::instance()->save_snippet( $snippet_name, $snippet_data );

        if ( $saved ) {
            wp_send_json_success( [
                'message' => \__( 'Snippet saved successfully', 'sofir' ),
            ] );
        } else {
            wp_send_json_error( \__( 'Failed to save snippet', 'sofir' ) );
        }
    }

    public function toggle_addon(): void {
        check_ajax_referer( 'sofir_wc_addon_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( \__( 'Unauthorized', 'sofir' ) );
        }

        $enabled = isset( $_POST['enabled'] ) ? rest_sanitize_boolean( $_POST['enabled'] ) : false;
        \update_option( 'sofir_woocommerce_addon_enabled', $enabled );

        wp_send_json_success( [
            'enabled' => $enabled,
            'message' => $enabled ? \__( 'WooCommerce addon activated', 'sofir' ) : \__( 'WooCommerce addon deactivated', 'sofir' ),
        ] );
    }

    public function get_addon_status(): void {
        check_ajax_referer( 'sofir_wc_addon_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( \__( 'Unauthorized', 'sofir' ) );
        }

        $enabled = \get_option( 'sofir_woocommerce_addon_enabled', false );
        $product_count = wp_count_posts( 'product' );
        $order_count = wp_count_posts( 'shop_order' );

        $orders_count = 0;
        if ( isset( $order_count->{'wc-completed'} ) ) {
            $orders_count = $order_count->{'wc-completed'};
        }

        wp_send_json_success( [
            'enabled' => (bool) $enabled,
            'products' => isset( $product_count->publish ) ? $product_count->publish : 0,
            'orders' => $orders_count,
            'version' => \get_option( 'woocommerce' ) ? '3.0+' : 'unknown',
        ] );
    }
}
