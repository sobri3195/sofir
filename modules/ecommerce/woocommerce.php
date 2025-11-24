<?php
namespace Sofir\Ecommerce;

class WooCommerce {
    private static ?WooCommerce $instance = null;

    public static function instance(): WooCommerce {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init(): void {
        \add_action( 'sofir/cpt/fields_catalog', [ $this, 'add_product_fields' ] );
        \add_filter( 'sofir/directory/post_types', [ $this, 'add_product_post_type' ] );
        \add_action( 'sofir/webhooks/woocommerce', [ $this, 'trigger_webhooks' ], 10, 2 );
        
        \add_action( 'woocommerce_new_order', [ $this, 'handle_new_order' ] );
        \add_action( 'woocommerce_order_status_completed', [ $this, 'handle_order_completed' ] );
        \add_action( 'woocommerce_product_set_stock', [ $this, 'handle_stock_change' ] );
    }

    public function add_product_fields( array $fields ): array {
        $fields['wc_price'] = [
            'name' => \__( 'WooCommerce Price', 'sofir' ),
            'type' => 'price',
            'meta_key' => '_price',
        ];

        $fields['wc_sale_price'] = [
            'name' => \__( 'WooCommerce Sale Price', 'sofir' ),
            'type' => 'price',
            'meta_key' => '_sale_price',
        ];

        $fields['wc_stock'] = [
            'name' => \__( 'WooCommerce Stock', 'sofir' ),
            'type' => 'number',
            'meta_key' => '_stock',
        ];

        $fields['wc_sku'] = [
            'name' => \__( 'WooCommerce SKU', 'sofir' ),
            'type' => 'text',
            'meta_key' => '_sku',
        ];

        return $fields;
    }

    public function add_product_post_type( array $post_types ): array {
        $post_types[] = 'product';
        return $post_types;
    }

    public function trigger_webhooks( string $event, array $data ): void {
        \do_action( 'sofir/webhooks/trigger', 'woocommerce_' . $event, $data );
    }

    public function handle_new_order( int $order_id ): void {
        $order = \wc_get_order( $order_id );
        
        if ( ! $order ) {
            return;
        }

        $data = [
            'order_id' => $order_id,
            'order_number' => $order->get_order_number(),
            'total' => $order->get_total(),
            'currency' => $order->get_currency(),
            'status' => $order->get_status(),
            'customer_email' => $order->get_billing_email(),
            'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'items' => [],
        ];

        foreach ( $order->get_items() as $item ) {
            $data['items'][] = [
                'product_id' => $item->get_product_id(),
                'product_name' => $item->get_name(),
                'quantity' => $item->get_quantity(),
                'subtotal' => $item->get_subtotal(),
            ];
        }

        $this->trigger_webhooks( 'new_order', $data );

        \do_action( 'sofir/loyalty/award_points', $order->get_customer_id(), 'purchase', [
            'order_id' => $order_id,
            'amount' => $order->get_total(),
        ] );
    }

    public function handle_order_completed( int $order_id ): void {
        $order = \wc_get_order( $order_id );
        
        if ( ! $order ) {
            return;
        }

        $data = [
            'order_id' => $order_id,
            'order_number' => $order->get_order_number(),
            'total' => $order->get_total(),
            'completed_date' => $order->get_date_completed(),
        ];

        $this->trigger_webhooks( 'order_completed', $data );
    }

    public function handle_stock_change( $product ): void {
        if ( ! $product instanceof \WC_Product ) {
            return;
        }

        $data = [
            'product_id' => $product->get_id(),
            'product_name' => $product->get_name(),
            'stock_quantity' => $product->get_stock_quantity(),
            'stock_status' => $product->get_stock_status(),
        ];

        $this->trigger_webhooks( 'stock_changed', $data );

        if ( $product->get_stock_quantity() <= \get_option( 'woocommerce_notify_low_stock_amount', 2 ) ) {
            $this->trigger_webhooks( 'low_stock', $data );
        }
    }
}
