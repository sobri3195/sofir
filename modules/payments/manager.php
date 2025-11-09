<?php
namespace Sofir\Payments;

class Manager {
    private const OPTION_SETTINGS = 'sofir_payment_settings';
    private const OPTION_GATEWAYS = 'sofir_payment_gateways';

    private static ?Manager $instance = null;

    private array $settings = [];
    private array $gateways = [];

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->settings = $this->load_settings();
        $this->gateways = $this->load_gateways();
    }

    public function boot(): void {
        \add_action( 'admin_post_sofir_save_payment_settings', [ $this, 'handle_save_settings' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        \add_shortcode( 'sofir_payment_form', [ $this, 'render_payment_form' ] );
    }

    public function get_settings(): array {
        return $this->settings;
    }

    public function get_gateways(): array {
        return $this->gateways;
    }

    public function handle_save_settings(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_payment_settings', '_sofir_nonce' );

        $currency = isset( $_POST['payment_currency'] ) ? \sanitize_text_field( \wp_unslash( $_POST['payment_currency'] ) ) : 'IDR';
        $manual_enabled = isset( $_POST['enable_manual'] );
        
        $duitku_merchant = isset( $_POST['duitku_merchant_code'] ) ? \sanitize_text_field( \wp_unslash( $_POST['duitku_merchant_code'] ) ) : '';
        $duitku_api = isset( $_POST['duitku_api_key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['duitku_api_key'] ) ) : '';
        $duitku_enabled = isset( $_POST['enable_duitku'] );
        
        $xendit_api = isset( $_POST['xendit_api_key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['xendit_api_key'] ) ) : '';
        $xendit_enabled = isset( $_POST['enable_xendit'] );
        
        $midtrans_server = isset( $_POST['midtrans_server_key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['midtrans_server_key'] ) ) : '';
        $midtrans_client = isset( $_POST['midtrans_client_key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['midtrans_client_key'] ) ) : '';
        $midtrans_sandbox = isset( $_POST['midtrans_sandbox'] );
        $midtrans_enabled = isset( $_POST['enable_midtrans'] );

        $this->settings = [
            'currency' => $currency,
            'manual_enabled' => $manual_enabled,
            'duitku_merchant_code' => $duitku_merchant,
            'duitku_api_key' => $duitku_api,
            'duitku_enabled' => $duitku_enabled,
            'xendit_api_key' => $xendit_api,
            'xendit_enabled' => $xendit_enabled,
            'midtrans_server_key' => $midtrans_server,
            'midtrans_client_key' => $midtrans_client,
            'midtrans_sandbox' => $midtrans_sandbox,
            'midtrans_enabled' => $midtrans_enabled,
        ];

        \update_option( self::OPTION_SETTINGS, $this->settings );

        \wp_safe_redirect(
            \add_query_arg(
                [
                    'page' => 'sofir-dashboard',
                    'tab' => 'payments',
                    'sofir_notice' => 'payment_settings_saved',
                ],
                \admin_url( 'admin.php' )
            )
        );
        exit;
    }

    public function render_payment_form( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'amount' => 0,
                'item_name' => '',
                'return_url' => '',
            ],
            $atts,
            'sofir_payment_form'
        );

        $amount = (float) $atts['amount'];
        $item_name = $atts['item_name'];
        $return_url = $atts['return_url'] ?: \home_url();

        if ( $amount <= 0 ) {
            return '<p>' . \esc_html__( 'Invalid payment amount.', 'sofir' ) . '</p>';
        }

        \wp_enqueue_script( 'sofir-payments' );

        ob_start();
        echo '<div class="sofir-payment-form" data-amount="' . \esc_attr( $amount ) . '" data-item="' . \esc_attr( $item_name ) . '" data-return="' . \esc_url( $return_url ) . '">';
        echo '<h3>' . \esc_html__( 'Select Payment Method', 'sofir' ) . '</h3>';

        if ( $this->settings['manual_enabled'] ?? false ) {
            echo '<label class="sofir-payment-option">';
            echo '<input type="radio" name="payment_gateway" value="manual" />';
            echo '<span>' . \esc_html__( 'Manual Payment', 'sofir' ) . '</span>';
            echo '</label>';
        }

        if ( $this->settings['duitku_enabled'] ?? false ) {
            echo '<label class="sofir-payment-option">';
            echo '<input type="radio" name="payment_gateway" value="duitku" />';
            echo '<span>Duitku</span>';
            echo '</label>';
        }

        if ( $this->settings['xendit_enabled'] ?? false ) {
            echo '<label class="sofir-payment-option">';
            echo '<input type="radio" name="payment_gateway" value="xendit" />';
            echo '<span>Xendit</span>';
            echo '</label>';
        }

        if ( $this->settings['midtrans_enabled'] ?? false ) {
            echo '<label class="sofir-payment-option">';
            echo '<input type="radio" name="payment_gateway" value="midtrans" />';
            echo '<span>Midtrans</span>';
            echo '</label>';
        }

        echo '<div class="sofir-payment-total">';
        echo '<strong>' . \esc_html__( 'Total:', 'sofir' ) . '</strong> ';
        echo '<span>' . \esc_html( $this->format_price( $amount ) ) . '</span>';
        echo '</div>';

        echo '<button type="button" class="button button-primary sofir-payment-submit">' . \esc_html__( 'Proceed to Payment', 'sofir' ) . '</button>';
        echo '</div>';

        return (string) ob_get_clean();
    }

    public function register_assets(): void {
        if ( ! \wp_script_is( 'sofir-payments', 'registered' ) ) {
            \wp_register_script(
                'sofir-payments',
                SOFIR_ASSETS_URL . 'js/payments.js',
                [ 'wp-api-fetch' ],
                SOFIR_VERSION,
                true
            );

            \wp_localize_script(
                'sofir-payments',
                'SOFIR_PAYMENTS_DATA',
                [
                    'restRoot' => \esc_url_raw( \rest_url() ),
                    'nonce' => \wp_create_nonce( 'wp_rest' ),
                    'currency' => $this->settings['currency'] ?? 'IDR',
                ]
            );
        }
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/payments/create',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_create_payment' ],
                'permission_callback' => function () {
                    return \is_user_logged_in();
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/payments/webhook/duitku',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'webhook_duitku' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/payments/webhook/xendit',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'webhook_xendit' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/payments/webhook/midtrans',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'webhook_midtrans' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/payments/transactions',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_transactions' ],
                'permission_callback' => function () {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );
    }

    public function rest_create_payment( \WP_REST_Request $request ): \WP_REST_Response {
        $gateway = \sanitize_key( (string) $request->get_param( 'gateway' ) );
        $amount = (float) $request->get_param( 'amount' );
        $item_name = \sanitize_text_field( (string) $request->get_param( 'item_name' ) );

        if ( ! $amount || $amount <= 0 ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Invalid amount', 'sofir' ) ], 400 );
        }

        $transaction_id = $this->create_transaction( $gateway, $amount, $item_name );

        switch ( $gateway ) {
            case 'manual':
                return $this->create_manual_payment( $transaction_id, $amount, $item_name );
            case 'duitku':
                return $this->create_duitku_payment( $transaction_id, $amount, $item_name );
            case 'xendit':
                return $this->create_xendit_payment( $transaction_id, $amount, $item_name );
            case 'midtrans':
                return $this->create_midtrans_payment( $transaction_id, $amount, $item_name );
            default:
                return new \WP_REST_Response( [ 'message' => \__( 'Invalid gateway', 'sofir' ) ], 400 );
        }
    }

    private function create_transaction( string $gateway, float $amount, string $item_name ): string {
        $transaction_id = 'TRX-' . \wp_rand( 100000, 999999 ) . '-' . \time();
        
        $transaction = [
            'id' => $transaction_id,
            'gateway' => $gateway,
            'amount' => $amount,
            'item_name' => $item_name,
            'status' => 'pending',
            'user_id' => \get_current_user_id(),
            'created_at' => \current_time( 'mysql' ),
        ];

        $transactions = \get_option( 'sofir_payment_transactions', [] );
        $transactions[ $transaction_id ] = $transaction;
        \update_option( 'sofir_payment_transactions', $transactions );

        return $transaction_id;
    }

    private function create_manual_payment( string $transaction_id, float $amount, string $item_name ): \WP_REST_Response {
        return \rest_ensure_response( [
            'status' => 'success',
            'payment_method' => 'manual',
            'transaction_id' => $transaction_id,
            'instructions' => \__( 'Please transfer to our bank account and send proof of payment.', 'sofir' ),
        ] );
    }

    private function create_duitku_payment( string $transaction_id, float $amount, string $item_name ): \WP_REST_Response {
        $merchant_code = $this->settings['duitku_merchant_code'] ?? '';
        $api_key = $this->settings['duitku_api_key'] ?? '';

        if ( ! $merchant_code || ! $api_key ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Duitku not configured', 'sofir' ) ], 400 );
        }

        $payload = [
            'merchantCode' => $merchant_code,
            'paymentAmount' => $amount,
            'merchantOrderId' => $transaction_id,
            'productDetails' => $item_name,
            'customerVaName' => \wp_get_current_user()->display_name,
            'email' => \wp_get_current_user()->user_email,
            'callbackUrl' => \rest_url( 'sofir/v1/payments/webhook/duitku' ),
            'returnUrl' => \home_url(),
        ];

        $signature = \hash( 'sha256', $merchant_code . $transaction_id . $amount . $api_key );
        $payload['signature'] = $signature;

        return \rest_ensure_response( [
            'status' => 'redirect',
            'payment_url' => 'https://sandbox.duitku.com/checkout/v1/' . $merchant_code,
            'payload' => $payload,
        ] );
    }

    private function create_xendit_payment( string $transaction_id, float $amount, string $item_name ): \WP_REST_Response {
        $api_key = $this->settings['xendit_api_key'] ?? '';

        if ( ! $api_key ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Xendit not configured', 'sofir' ) ], 400 );
        }

        $payload = [
            'external_id' => $transaction_id,
            'amount' => $amount,
            'description' => $item_name,
            'customer' => [
                'given_names' => \wp_get_current_user()->display_name,
                'email' => \wp_get_current_user()->user_email,
            ],
            'success_redirect_url' => \home_url(),
            'failure_redirect_url' => \home_url(),
        ];

        return \rest_ensure_response( [
            'status' => 'pending',
            'transaction_id' => $transaction_id,
            'message' => \__( 'Payment initiated with Xendit', 'sofir' ),
        ] );
    }

    private function create_midtrans_payment( string $transaction_id, float $amount, string $item_name ): \WP_REST_Response {
        $server_key = $this->settings['midtrans_server_key'] ?? '';
        $client_key = $this->settings['midtrans_client_key'] ?? '';
        $sandbox = $this->settings['midtrans_sandbox'] ?? false;

        if ( ! $server_key || ! $client_key ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Midtrans not configured', 'sofir' ) ], 400 );
        }

        $snap_url = $sandbox ? 'https://app.sandbox.midtrans.com/snap/v1/transactions' : 'https://app.midtrans.com/snap/v1/transactions';

        $payload = [
            'transaction_details' => [
                'order_id' => $transaction_id,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => \wp_get_current_user()->display_name,
                'email' => \wp_get_current_user()->user_email,
            ],
        ];

        return \rest_ensure_response( [
            'status' => 'pending',
            'transaction_id' => $transaction_id,
            'snap_url' => $snap_url,
            'client_key' => $client_key,
        ] );
    }

    public function webhook_duitku( \WP_REST_Request $request ): \WP_REST_Response {
        $transaction_id = $request->get_param( 'merchantOrderId' );
        $status = $request->get_param( 'resultCode' );

        if ( $status === '00' ) {
            $this->update_transaction_status( $transaction_id, 'completed' );
        } else {
            $this->update_transaction_status( $transaction_id, 'failed' );
        }

        \do_action( 'sofir/payment/duitku_webhook', $transaction_id, $status, $request->get_params() );

        return \rest_ensure_response( [ 'status' => 'ok' ] );
    }

    public function webhook_xendit( \WP_REST_Request $request ): \WP_REST_Response {
        $transaction_id = $request->get_param( 'external_id' );
        $status = $request->get_param( 'status' );

        if ( $status === 'PAID' ) {
            $this->update_transaction_status( $transaction_id, 'completed' );
        } elseif ( $status === 'EXPIRED' ) {
            $this->update_transaction_status( $transaction_id, 'failed' );
        }

        \do_action( 'sofir/payment/xendit_webhook', $transaction_id, $status, $request->get_params() );

        return \rest_ensure_response( [ 'status' => 'ok' ] );
    }

    public function webhook_midtrans( \WP_REST_Request $request ): \WP_REST_Response {
        $transaction_id = $request->get_param( 'order_id' );
        $status = $request->get_param( 'transaction_status' );

        if ( in_array( $status, [ 'capture', 'settlement' ], true ) ) {
            $this->update_transaction_status( $transaction_id, 'completed' );
        } elseif ( in_array( $status, [ 'deny', 'cancel', 'expire' ], true ) ) {
            $this->update_transaction_status( $transaction_id, 'failed' );
        }

        \do_action( 'sofir/payment/midtrans_webhook', $transaction_id, $status, $request->get_params() );

        return \rest_ensure_response( [ 'status' => 'ok' ] );
    }

    public function rest_get_transactions( \WP_REST_Request $request ): \WP_REST_Response {
        $transactions = \get_option( 'sofir_payment_transactions', [] );
        return \rest_ensure_response( array_values( $transactions ) );
    }

    private function update_transaction_status( string $transaction_id, string $status ): void {
        $transactions = \get_option( 'sofir_payment_transactions', [] );

        if ( isset( $transactions[ $transaction_id ] ) ) {
            $transactions[ $transaction_id ]['status'] = $status;
            $transactions[ $transaction_id ]['updated_at'] = \current_time( 'mysql' );
            \update_option( 'sofir_payment_transactions', $transactions );

            \do_action( 'sofir/payment/status_changed', $transaction_id, $status );
        }
    }

    private function format_price( float $amount ): string {
        $currency = $this->settings['currency'] ?? 'IDR';
        return $currency . ' ' . \number_format_i18n( $amount, 2 );
    }

    private function load_settings(): array {
        $defaults = [
            'currency' => 'IDR',
            'manual_enabled' => true,
            'duitku_merchant_code' => '',
            'duitku_api_key' => '',
            'duitku_enabled' => false,
            'xendit_api_key' => '',
            'xendit_enabled' => false,
            'midtrans_server_key' => '',
            'midtrans_client_key' => '',
            'midtrans_sandbox' => true,
            'midtrans_enabled' => false,
        ];

        $settings = \get_option( self::OPTION_SETTINGS, [] );

        if ( ! \is_array( $settings ) ) {
            $settings = [];
        }

        return \wp_parse_args( $settings, $defaults );
    }

    private function load_gateways(): array {
        $gateways = [
            'manual' => [
                'id' => 'manual',
                'name' => \__( 'Manual Payment', 'sofir' ),
                'enabled' => $this->settings['manual_enabled'] ?? true,
            ],
            'duitku' => [
                'id' => 'duitku',
                'name' => 'Duitku',
                'enabled' => $this->settings['duitku_enabled'] ?? false,
            ],
            'xendit' => [
                'id' => 'xendit',
                'name' => 'Xendit',
                'enabled' => $this->settings['xendit_enabled'] ?? false,
            ],
            'midtrans' => [
                'id' => 'midtrans',
                'name' => 'Midtrans',
                'enabled' => $this->settings['midtrans_enabled'] ?? false,
            ],
        ];

        return \apply_filters( 'sofir/payment/gateways', $gateways );
    }
}
