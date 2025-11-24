<?php
namespace Sofir\Ecommerce;

class EDD {
    private static ?EDD $instance = null;

    public static function instance(): EDD {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init(): void {
        \add_action( 'sofir/cpt/fields_catalog', [ $this, 'add_download_fields' ] );
        \add_filter( 'sofir/directory/post_types', [ $this, 'add_download_post_type' ] );
        \add_action( 'sofir/webhooks/edd', [ $this, 'trigger_webhooks' ], 10, 2 );
        
        \add_action( 'edd_complete_purchase', [ $this, 'handle_complete_purchase' ] );
        \add_action( 'edd_insert_payment', [ $this, 'handle_new_payment' ] );
        \add_action( 'edd_update_payment_status', [ $this, 'handle_payment_status_change' ], 10, 3 );
    }

    public function add_download_fields( array $fields ): array {
        $fields['edd_price'] = [
            'name' => \__( 'EDD Price', 'sofir' ),
            'type' => 'price',
            'meta_key' => 'edd_price',
        ];

        $fields['edd_sales'] = [
            'name' => \__( 'EDD Sales', 'sofir' ),
            'type' => 'number',
            'meta_key' => '_edd_download_sales',
        ];

        $fields['edd_earnings'] = [
            'name' => \__( 'EDD Earnings', 'sofir' ),
            'type' => 'price',
            'meta_key' => '_edd_download_earnings',
        ];

        return $fields;
    }

    public function add_download_post_type( array $post_types ): array {
        $post_types[] = 'download';
        return $post_types;
    }

    public function trigger_webhooks( string $event, array $data ): void {
        \do_action( 'sofir/webhooks/trigger', 'edd_' . $event, $data );
    }

    public function handle_complete_purchase( int $payment_id ): void {
        $payment = \edd_get_payment( $payment_id );
        
        if ( ! $payment ) {
            return;
        }

        $data = [
            'payment_id' => $payment_id,
            'payment_number' => $payment->number,
            'total' => $payment->total,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'customer_email' => $payment->email,
            'customer_name' => $payment->first_name . ' ' . $payment->last_name,
            'downloads' => [],
        ];

        $cart_items = \edd_get_payment_meta_cart_details( $payment_id );
        if ( is_array( $cart_items ) ) {
            foreach ( $cart_items as $item ) {
                $data['downloads'][] = [
                    'download_id' => $item['id'],
                    'download_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'] ?? 1,
                ];
            }
        }

        $this->trigger_webhooks( 'purchase_completed', $data );

        \do_action( 'sofir/loyalty/award_points', $payment->user_id, 'purchase', [
            'payment_id' => $payment_id,
            'amount' => $payment->total,
        ] );
    }

    public function handle_new_payment( int $payment_id ): void {
        $payment = \edd_get_payment( $payment_id );
        
        if ( ! $payment ) {
            return;
        }

        $data = [
            'payment_id' => $payment_id,
            'total' => $payment->total,
            'status' => $payment->status,
            'created_date' => $payment->date,
        ];

        $this->trigger_webhooks( 'new_payment', $data );
    }

    public function handle_payment_status_change( int $payment_id, string $new_status, string $old_status ): void {
        $payment = \edd_get_payment( $payment_id );
        
        if ( ! $payment ) {
            return;
        }

        $data = [
            'payment_id' => $payment_id,
            'new_status' => $new_status,
            'old_status' => $old_status,
            'total' => $payment->total,
        ];

        $this->trigger_webhooks( 'payment_status_changed', $data );
    }
}
