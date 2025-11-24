<?php
namespace Sofir\Ecommerce;

class NorthCommerce {
    private static ?NorthCommerce $instance = null;

    public static function instance(): NorthCommerce {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init(): void {
        \add_action( 'sofir/cpt/fields_catalog', [ $this, 'add_product_fields' ] );
        \add_filter( 'sofir/directory/post_types', [ $this, 'add_product_post_type' ] );
        \add_action( 'sofir/webhooks/north', [ $this, 'trigger_webhooks' ], 10, 2 );
        
        \add_action( 'north_commerce_new_order', [ $this, 'handle_new_order' ], 10, 1 );
        \add_action( 'north_commerce_order_completed', [ $this, 'handle_order_completed' ], 10, 1 );
        \add_action( 'north_commerce_stock_changed', [ $this, 'handle_stock_change' ], 10, 2 );
    }

    public function add_product_fields( array $fields ): array {
        $fields['nc_price'] = [
            'name' => \__( 'North Commerce Price', 'sofir' ),
            'type' => 'price',
            'meta_key' => '_nc_price',
        ];

        $fields['nc_sale_price'] = [
            'name' => \__( 'North Commerce Sale Price', 'sofir' ),
            'type' => 'price',
            'meta_key' => '_nc_sale_price',
        ];

        $fields['nc_stock'] = [
            'name' => \__( 'North Commerce Stock', 'sofir' ),
            'type' => 'number',
            'meta_key' => '_nc_stock',
        ];

        $fields['nc_sku'] = [
            'name' => \__( 'North Commerce SKU', 'sofir' ),
            'type' => 'text',
            'meta_key' => '_nc_sku',
        ];

        return $fields;
    }

    public function add_product_post_type( array $post_types ): array {
        $post_types[] = 'nc_product';
        return $post_types;
    }

    public function trigger_webhooks( string $event, array $data ): void {
        \do_action( 'sofir/webhooks/trigger', 'north_' . $event, $data );
    }

    public function handle_new_order( $order_data ): void {
        if ( ! is_array( $order_data ) ) {
            return;
        }

        $data = [
            'order_id' => $order_data['id'] ?? 0,
            'total' => $order_data['total'] ?? 0,
            'status' => $order_data['status'] ?? 'pending',
            'customer_email' => $order_data['email'] ?? '',
            'customer_name' => $order_data['name'] ?? '',
            'items' => $order_data['items'] ?? [],
        ];

        $this->trigger_webhooks( 'new_order', $data );

        $user_id = $order_data['user_id'] ?? 0;
        if ( $user_id > 0 ) {
            \do_action( 'sofir/loyalty/award_points', $user_id, 'purchase', [
                'order_id' => $data['order_id'],
                'amount' => $data['total'],
            ] );
        }
    }

    public function handle_order_completed( $order_data ): void {
        if ( ! is_array( $order_data ) ) {
            return;
        }

        $data = [
            'order_id' => $order_data['id'] ?? 0,
            'total' => $order_data['total'] ?? 0,
            'completed_date' => current_time( 'mysql' ),
        ];

        $this->trigger_webhooks( 'order_completed', $data );
    }

    public function handle_stock_change( int $product_id, int $new_stock ): void {
        $product = \get_post( $product_id );
        
        if ( ! $product || 'nc_product' !== $product->post_type ) {
            return;
        }

        $data = [
            'product_id' => $product_id,
            'product_name' => $product->post_title,
            'stock_quantity' => $new_stock,
        ];

        $this->trigger_webhooks( 'stock_changed', $data );

        if ( $new_stock <= 5 ) {
            $this->trigger_webhooks( 'low_stock', $data );
        }
    }
}
