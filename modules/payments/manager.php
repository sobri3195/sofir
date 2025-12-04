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
        \add_action( 'init', [ $this, 'register_payment_cpt' ] );
        \add_action( 'admin_menu', [ $this, 'add_payments_menu' ] );
        \add_action( 'admin_post_sofir_save_payment_settings', [ $this, 'handle_save_settings' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        \add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        \add_action( 'sofir_process_mock_payment', [ $this, 'process_mock_payment' ], 10, 2 );
        \add_shortcode( 'sofir_payment_form', [ $this, 'render_payment_form' ] );
        \add_shortcode( 'sofir_donation_form', [ $this, 'render_donation_form' ] );
        \add_shortcode( 'sofir_subscription_form', [ $this, 'render_subscription_form' ] );
        \add_shortcode( 'sofir_product_catalog', [ $this, 'render_product_catalog' ] );
        
        $this->register_payment_cron();
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
        $duitku_test_mode = isset( $_POST['duitku_test_mode'] );
        
        $xendit_api = isset( $_POST['xendit_api_key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['xendit_api_key'] ) ) : '';
        $xendit_enabled = isset( $_POST['enable_xendit'] );
        $xendit_test_mode = isset( $_POST['xendit_test_mode'] );
        
        $midtrans_server = isset( $_POST['midtrans_server_key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['midtrans_server_key'] ) ) : '';
        $midtrans_client = isset( $_POST['midtrans_client_key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['midtrans_client_key'] ) ) : '';
        $midtrans_sandbox = isset( $_POST['midtrans_sandbox'] );
        $midtrans_enabled = isset( $_POST['enable_midtrans'] );
        $midtrans_test_mode = isset( $_POST['midtrans_test_mode'] );

        $this->settings = [
            'currency' => $currency,
            'manual_enabled' => $manual_enabled,
            'duitku_merchant_code' => $duitku_merchant,
            'duitku_api_key' => $duitku_api,
            'duitku_enabled' => $duitku_enabled,
            'duitku_test_mode' => $duitku_test_mode,
            'xendit_api_key' => $xendit_api,
            'xendit_enabled' => $xendit_enabled,
            'xendit_test_mode' => $xendit_test_mode,
            'midtrans_server_key' => $midtrans_server,
            'midtrans_client_key' => $midtrans_client,
            'midtrans_sandbox' => $midtrans_sandbox,
            'midtrans_enabled' => $midtrans_enabled,
            'midtrans_test_mode' => $midtrans_test_mode,
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

        \register_rest_route(
            'sofir/v1',
            '/payments/test/trigger',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_trigger_test_payment' ],
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
        
        $is_test_mode = false;
        switch ( $gateway ) {
            case 'duitku':
                $is_test_mode = $this->settings['duitku_test_mode'] ?? false;
                break;
            case 'xendit':
                $is_test_mode = $this->settings['xendit_test_mode'] ?? false;
                break;
            case 'midtrans':
                $is_test_mode = $this->settings['midtrans_test_mode'] ?? false;
                break;
        }
        
        $transaction = [
            'id' => $transaction_id,
            'gateway' => $gateway,
            'amount' => $amount,
            'item_name' => $item_name,
            'status' => 'pending',
            'user_id' => \get_current_user_id(),
            'test_mode' => $is_test_mode,
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
        $test_mode = $this->settings['duitku_test_mode'] ?? false;
        
        if ( $test_mode ) {
            return $this->create_mock_duitku_payment( $transaction_id, $amount, $item_name );
        }
        
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
        $test_mode = $this->settings['xendit_test_mode'] ?? false;
        
        if ( $test_mode ) {
            return $this->create_mock_xendit_payment( $transaction_id, $amount, $item_name );
        }
        
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
        $test_mode = $this->settings['midtrans_test_mode'] ?? false;
        
        if ( $test_mode ) {
            return $this->create_mock_midtrans_payment( $transaction_id, $amount, $item_name );
        }
        
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

    public function rest_trigger_test_payment( \WP_REST_Request $request ): \WP_REST_Response {
        $transaction_id = \sanitize_text_field( (string) $request->get_param( 'transaction_id' ) );
        
        if ( ! $transaction_id ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Transaction ID required', 'sofir' ) ], 400 );
        }
        
        $transactions = \get_option( 'sofir_payment_transactions', [] );
        
        if ( ! isset( $transactions[ $transaction_id ] ) ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Transaction not found', 'sofir' ) ], 404 );
        }
        
        if ( ! ( $transactions[ $transaction_id ]['test_mode'] ?? false ) ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Not a test transaction', 'sofir' ) ], 400 );
        }
        
        $gateway = $transactions[ $transaction_id ]['gateway'];
        $this->process_mock_payment( $transaction_id, $gateway );
        
        return \rest_ensure_response( [
            'status' => 'success',
            'message' => \__( 'Test payment processed', 'sofir' ),
            'transaction_id' => $transaction_id,
        ] );
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

    private function create_mock_duitku_payment( string $transaction_id, float $amount, string $item_name ): \WP_REST_Response {
        $mock_reference = 'MOCK-DUITKU-' . \wp_rand( 10000, 99999 );
        $mock_va_number = '8808' . \wp_rand( 1000000000, 9999999999 );
        
        $this->schedule_mock_webhook( $transaction_id, 'duitku', 10 );
        
        return \rest_ensure_response( [
            'status' => 'success',
            'payment_method' => 'duitku',
            'transaction_id' => $transaction_id,
            'test_mode' => true,
            'reference' => $mock_reference,
            'va_number' => $mock_va_number,
            'payment_url' => \admin_url( 'admin.php?page=sofir-dashboard&tab=payments&test_payment=' . $transaction_id ),
            'instructions' => \__( '🧪 TEST MODE - This is a simulated Duitku payment. Payment will auto-complete in 10 seconds.', 'sofir' ),
            'bank_info' => [
                'bank' => 'BCA Virtual Account (Test)',
                'account_number' => $mock_va_number,
                'account_name' => 'SOFIR Payment Test',
            ],
        ] );
    }

    private function create_mock_xendit_payment( string $transaction_id, float $amount, string $item_name ): \WP_REST_Response {
        $mock_invoice_id = 'MOCK-XEN-' . \wp_rand( 10000, 99999 );
        $mock_invoice_url = \admin_url( 'admin.php?page=sofir-dashboard&tab=payments&test_payment=' . $transaction_id );
        
        $this->schedule_mock_webhook( $transaction_id, 'xendit', 10 );
        
        return \rest_ensure_response( [
            'status' => 'success',
            'payment_method' => 'xendit',
            'transaction_id' => $transaction_id,
            'test_mode' => true,
            'invoice_id' => $mock_invoice_id,
            'invoice_url' => $mock_invoice_url,
            'payment_methods' => [ 'Bank Transfer', 'E-Wallet', 'Credit Card', 'Retail Outlets' ],
            'instructions' => \__( '🧪 TEST MODE - This is a simulated Xendit payment. Payment will auto-complete in 10 seconds.', 'sofir' ),
            'qr_code' => 'https://placehold.co/300x300/667eea/ffffff?text=Xendit+QR+Test',
        ] );
    }

    private function create_mock_midtrans_payment( string $transaction_id, float $amount, string $item_name ): \WP_REST_Response {
        $mock_snap_token = 'MOCK-SNAP-' . \wp_rand( 10000, 99999 );
        $mock_redirect_url = \admin_url( 'admin.php?page=sofir-dashboard&tab=payments&test_payment=' . $transaction_id );
        
        $this->schedule_mock_webhook( $transaction_id, 'midtrans', 10 );
        
        return \rest_ensure_response( [
            'status' => 'success',
            'payment_method' => 'midtrans',
            'transaction_id' => $transaction_id,
            'test_mode' => true,
            'snap_token' => $mock_snap_token,
            'redirect_url' => $mock_redirect_url,
            'payment_methods' => [ 'Credit Card', 'Bank Transfer', 'GoPay', 'ShopeePay', 'Alfamart', 'Indomaret' ],
            'instructions' => \__( '🧪 TEST MODE - This is a simulated Midtrans payment. Payment will auto-complete in 10 seconds.', 'sofir' ),
            'snap_url' => $mock_redirect_url,
        ] );
    }

    private function schedule_mock_webhook( string $transaction_id, string $gateway, int $delay_seconds ): void {
        \wp_schedule_single_event(
            \time() + $delay_seconds,
            'sofir_process_mock_payment',
            [ $transaction_id, $gateway ]
        );
    }

    public function process_mock_payment( string $transaction_id, string $gateway ): void {
        $success_rate = 90;
        $is_success = \wp_rand( 1, 100 ) <= $success_rate;
        
        if ( $is_success ) {
            $this->update_transaction_status( $transaction_id, 'completed' );
            
            switch ( $gateway ) {
                case 'duitku':
                    \do_action( 'sofir/payment/duitku_webhook', $transaction_id, '00', [
                        'merchantOrderId' => $transaction_id,
                        'resultCode' => '00',
                        'reference' => 'MOCK-REF-' . \wp_rand( 100000, 999999 ),
                        'amount' => 0,
                        'test_mode' => true,
                    ] );
                    break;
                    
                case 'xendit':
                    \do_action( 'sofir/payment/xendit_webhook', $transaction_id, 'PAID', [
                        'external_id' => $transaction_id,
                        'status' => 'PAID',
                        'id' => 'MOCK-INV-' . \wp_rand( 100000, 999999 ),
                        'amount' => 0,
                        'test_mode' => true,
                    ] );
                    break;
                    
                case 'midtrans':
                    \do_action( 'sofir/payment/midtrans_webhook', $transaction_id, 'settlement', [
                        'order_id' => $transaction_id,
                        'transaction_status' => 'settlement',
                        'transaction_id' => 'MOCK-TRX-' . \wp_rand( 100000, 999999 ),
                        'gross_amount' => 0,
                        'test_mode' => true,
                    ] );
                    break;
            }
        } else {
            $this->update_transaction_status( $transaction_id, 'failed' );
        }
        
        \do_action( 'sofir/payment/mock_processed', $transaction_id, $gateway, $is_success );
    }

    private function load_settings(): array {
        $defaults = [
            'currency' => 'IDR',
            'manual_enabled' => true,
            'duitku_merchant_code' => '',
            'duitku_api_key' => '',
            'duitku_enabled' => false,
            'duitku_test_mode' => false,
            'xendit_api_key' => '',
            'xendit_enabled' => false,
            'xendit_test_mode' => false,
            'midtrans_server_key' => '',
            'midtrans_client_key' => '',
            'midtrans_sandbox' => true,
            'midtrans_enabled' => false,
            'midtrans_test_mode' => false,
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

    public function register_payment_cpt(): void {
        \register_post_type(
            'sofir_product',
            [
                'label' => \__( 'Products', 'sofir' ),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => [ 'title', 'editor', 'thumbnail' ],
                'capability_type' => 'post',
                'has_archive' => true,
            ]
        );

        \register_post_type(
            'sofir_coupon',
            [
                'label' => \__( 'Coupons', 'sofir' ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => [ 'title' ],
                'capability_type' => 'post',
            ]
        );

        \register_post_type(
            'sofir_subscription',
            [
                'label' => \__( 'Subscriptions', 'sofir' ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => [ 'title' ],
                'capability_type' => 'post',
            ]
        );

        \register_post_type(
            'sofir_invoice',
            [
                'label' => \__( 'Invoices', 'sofir' ),
                'public' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'supports' => [ 'title' ],
                'capability_type' => 'post',
            ]
        );
    }

    public function add_payments_menu(): void {
        \add_menu_page(
            \__( 'Payments', 'sofir' ),
            \__( 'Payments', 'sofir' ),
            'manage_options',
            'sofir-payments',
            [ $this, 'render_payments_dashboard' ],
            'dashicons-money-alt',
            32
        );

        \add_submenu_page(
            'sofir-payments',
            \__( 'Dashboard', 'sofir' ),
            \__( 'Dashboard', 'sofir' ),
            'manage_options',
            'sofir-payments',
            [ $this, 'render_payments_dashboard' ]
        );

        \add_submenu_page(
            'sofir-payments',
            \__( 'Transactions', 'sofir' ),
            \__( 'Transactions', 'sofir' ),
            'manage_options',
            'sofir-transactions',
            [ $this, 'render_transactions_page' ]
        );

        \add_submenu_page(
            'sofir-payments',
            \__( 'Products', 'sofir' ),
            \__( 'Products', 'sofir' ),
            'manage_options',
            'edit.php?post_type=sofir_product'
        );

        \add_submenu_page(
            'sofir-payments',
            \__( 'Coupons', 'sofir' ),
            \__( 'Coupons', 'sofir' ),
            'manage_options',
            'edit.php?post_type=sofir_coupon'
        );

        \add_submenu_page(
            'sofir-payments',
            \__( 'Subscriptions', 'sofir' ),
            \__( 'Subscriptions', 'sofir' ),
            'manage_options',
            'edit.php?post_type=sofir_subscription'
        );

        \add_submenu_page(
            'sofir-payments',
            \__( 'Invoices', 'sofir' ),
            \__( 'Invoices', 'sofir' ),
            'manage_options',
            'edit.php?post_type=sofir_invoice'
        );

        \add_submenu_page(
            'sofir-payments',
            \__( 'Settings', 'sofir' ),
            \__( 'Settings', 'sofir' ),
            'manage_options',
            'sofir-payment-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    public function render_payments_dashboard(): void {
        $transactions = \get_option( 'sofir_payment_transactions', [] );
        $total_revenue = 0;
        $completed_count = 0;
        $pending_count = 0;

        foreach ( $transactions as $transaction ) {
            if ( $transaction['status'] === 'completed' ) {
                $total_revenue += $transaction['amount'];
                $completed_count++;
            } elseif ( $transaction['status'] === 'pending' ) {
                $pending_count++;
            }
        }

        ?>
        <div class="wrap">
            <h1><?php \esc_html_e( 'Payments Dashboard', 'sofir' ); ?></h1>
            
            <div class="sofir-dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <div class="sofir-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; opacity: 0.9;"><?php \esc_html_e( 'Total Revenue', 'sofir' ); ?></h3>
                    <p style="font-size: 36px; font-weight: bold; margin: 0;"><?php echo \esc_html( $this->format_price( $total_revenue ) ); ?></p>
                </div>

                <div class="sofir-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; opacity: 0.9;"><?php \esc_html_e( 'Completed Payments', 'sofir' ); ?></h3>
                    <p style="font-size: 36px; font-weight: bold; margin: 0;"><?php echo \esc_html( $completed_count ); ?></p>
                </div>

                <div class="sofir-stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; opacity: 0.9;"><?php \esc_html_e( 'Pending Payments', 'sofir' ); ?></h3>
                    <p style="font-size: 36px; font-weight: bold; margin: 0;"><?php echo \esc_html( $pending_count ); ?></p>
                </div>

                <div class="sofir-stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <h3 style="margin: 0 0 10px 0; opacity: 0.9;"><?php \esc_html_e( 'Total Transactions', 'sofir' ); ?></h3>
                    <p style="font-size: 36px; font-weight: bold; margin: 0;"><?php echo \esc_html( \count( $transactions ) ); ?></p>
                </div>
            </div>

            <h2 style="margin-top: 40px;"><?php \esc_html_e( 'Recent Transactions', 'sofir' ); ?></h2>
            <?php $this->render_recent_transactions( 10 ); ?>
        </div>
        <?php
    }

    public function render_transactions_page(): void {
        ?>
        <div class="wrap">
            <h1><?php \esc_html_e( 'Transactions', 'sofir' ); ?></h1>
            <?php $this->render_recent_transactions( -1 ); ?>
        </div>
        <?php
    }

    private function render_recent_transactions( int $limit ): void {
        $transactions = \get_option( 'sofir_payment_transactions', [] );
        $transactions = \array_reverse( $transactions );

        if ( $limit > 0 ) {
            $transactions = \array_slice( $transactions, 0, $limit );
        }

        if ( empty( $transactions ) ) {
            echo '<p>' . \esc_html__( 'No transactions found.', 'sofir' ) . '</p>';
            return;
        }

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . \esc_html__( 'Transaction ID', 'sofir' ) . '</th>';
        echo '<th>' . \esc_html__( 'Gateway', 'sofir' ) . '</th>';
        echo '<th>' . \esc_html__( 'Amount', 'sofir' ) . '</th>';
        echo '<th>' . \esc_html__( 'Item', 'sofir' ) . '</th>';
        echo '<th>' . \esc_html__( 'Status', 'sofir' ) . '</th>';
        echo '<th>' . \esc_html__( 'Date', 'sofir' ) . '</th>';
        echo '<th>' . \esc_html__( 'Actions', 'sofir' ) . '</th>';
        echo '</tr></thead><tbody>';

        foreach ( $transactions as $transaction ) {
            $status_color = match ( $transaction['status'] ) {
                'completed' => '#00a32a',
                'pending' => '#f0b849',
                'failed' => '#d63638',
                default => '#757575',
            };

            echo '<tr>';
            echo '<td><strong>' . \esc_html( $transaction['id'] ) . '</strong></td>';
            echo '<td>' . \esc_html( \ucfirst( $transaction['gateway'] ) ) . '</td>';
            echo '<td>' . \esc_html( $this->format_price( $transaction['amount'] ) ) . '</td>';
            echo '<td>' . \esc_html( $transaction['item_name'] ) . '</td>';
            echo '<td><span style="padding: 4px 8px; border-radius: 4px; color: white; background: ' . \esc_attr( $status_color ) . ';">' . \esc_html( \ucfirst( $transaction['status'] ) ) . '</span></td>';
            echo '<td>' . \esc_html( $transaction['created_at'] ) . '</td>';
            echo '<td><button class="button button-small">' . \esc_html__( 'View', 'sofir' ) . '</button></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }

    public function render_settings_page(): void {
        ?>
        <div class="wrap">
            <h1><?php \esc_html_e( 'Payment Settings', 'sofir' ); ?></h1>
            <p><?php \esc_html_e( 'Configure your payment gateways and settings.', 'sofir' ); ?></p>
        </div>
        <?php
    }

    public function render_donation_form( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'title' => \__( 'Support Us', 'sofir' ),
                'description' => '',
                'suggested_amounts' => '10,25,50,100',
                'currency' => 'USD',
            ],
            $atts,
            'sofir_donation_form'
        );

        \wp_enqueue_script( 'sofir-payments' );
        \wp_enqueue_style( 'sofir-payments' );

        $amounts = \explode( ',', $atts['suggested_amounts'] );

        ob_start();
        ?>
        <div class="sofir-donation-form">
            <h2><?php echo \esc_html( $atts['title'] ); ?></h2>
            <?php if ( $atts['description'] ) : ?>
                <p><?php echo \esc_html( $atts['description'] ); ?></p>
            <?php endif; ?>

            <div class="sofir-donation-amounts" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px; margin: 20px 0;">
                <?php foreach ( $amounts as $amount ) : ?>
                    <button type="button" class="button sofir-amount-btn" data-amount="<?php echo \esc_attr( \trim( $amount ) ); ?>">
                        <?php echo \esc_html( $atts['currency'] . ' ' . \trim( $amount ) ); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="sofir-custom-amount" style="margin: 20px 0;">
                <label><?php \esc_html_e( 'Or enter custom amount:', 'sofir' ); ?></label>
                <input type="number" class="sofir-donation-amount" placeholder="0.00" min="1" step="0.01" style="width: 100%; padding: 10px; margin-top: 5px;" />
            </div>

            <div class="sofir-donor-info" style="margin: 20px 0;">
                <input type="text" class="sofir-donor-name" placeholder="<?php \esc_attr_e( 'Your Name', 'sofir' ); ?>" style="width: 100%; padding: 10px; margin-bottom: 10px;" />
                <input type="email" class="sofir-donor-email" placeholder="<?php \esc_attr_e( 'Your Email', 'sofir' ); ?>" style="width: 100%; padding: 10px;" />
            </div>

            <button type="button" class="button button-primary sofir-donate-btn" style="width: 100%; padding: 15px; font-size: 16px;">
                <?php \esc_html_e( 'Donate Now', 'sofir' ); ?>
            </button>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    public function render_subscription_form( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'plans' => '',
                'currency' => 'USD',
            ],
            $atts,
            'sofir_subscription_form'
        );

        \wp_enqueue_script( 'sofir-payments' );

        ob_start();
        ?>
        <div class="sofir-subscription-form">
            <h2><?php \esc_html_e( 'Choose Your Plan', 'sofir' ); ?></h2>
            <div class="sofir-subscription-plans" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                <div class="sofir-plan-card" style="border: 2px solid #e0e0e0; border-radius: 8px; padding: 30px; text-align: center;">
                    <h3><?php \esc_html_e( 'Basic', 'sofir' ); ?></h3>
                    <p style="font-size: 36px; font-weight: bold; margin: 20px 0;">$9<span style="font-size: 18px; font-weight: normal;">/mo</span></p>
                    <ul style="list-style: none; padding: 0; margin: 20px 0; text-align: left;">
                        <li>✓ Feature 1</li>
                        <li>✓ Feature 2</li>
                        <li>✓ Feature 3</li>
                    </ul>
                    <button class="button button-primary sofir-subscribe-btn" data-plan="basic" data-amount="9">
                        <?php \esc_html_e( 'Subscribe', 'sofir' ); ?>
                    </button>
                </div>

                <div class="sofir-plan-card" style="border: 2px solid #0073aa; border-radius: 8px; padding: 30px; text-align: center; position: relative;">
                    <span style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #0073aa; color: white; padding: 5px 15px; border-radius: 15px; font-size: 12px;">
                        <?php \esc_html_e( 'Popular', 'sofir' ); ?>
                    </span>
                    <h3><?php \esc_html_e( 'Pro', 'sofir' ); ?></h3>
                    <p style="font-size: 36px; font-weight: bold; margin: 20px 0;">$29<span style="font-size: 18px; font-weight: normal;">/mo</span></p>
                    <ul style="list-style: none; padding: 0; margin: 20px 0; text-align: left;">
                        <li>✓ All Basic features</li>
                        <li>✓ Feature 4</li>
                        <li>✓ Feature 5</li>
                        <li>✓ Feature 6</li>
                    </ul>
                    <button class="button button-primary sofir-subscribe-btn" data-plan="pro" data-amount="29">
                        <?php \esc_html_e( 'Subscribe', 'sofir' ); ?>
                    </button>
                </div>

                <div class="sofir-plan-card" style="border: 2px solid #e0e0e0; border-radius: 8px; padding: 30px; text-align: center;">
                    <h3><?php \esc_html_e( 'Enterprise', 'sofir' ); ?></h3>
                    <p style="font-size: 36px; font-weight: bold; margin: 20px 0;">$99<span style="font-size: 18px; font-weight: normal;">/mo</span></p>
                    <ul style="list-style: none; padding: 0; margin: 20px 0; text-align: left;">
                        <li>✓ All Pro features</li>
                        <li>✓ Feature 7</li>
                        <li>✓ Feature 8</li>
                        <li>✓ Priority support</li>
                    </ul>
                    <button class="button button-primary sofir-subscribe-btn" data-plan="enterprise" data-amount="99">
                        <?php \esc_html_e( 'Subscribe', 'sofir' ); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    public function render_product_catalog( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'columns' => 3,
                'limit' => 12,
            ],
            $atts,
            'sofir_product_catalog'
        );

        $products = \get_posts( [
            'post_type' => 'sofir_product',
            'posts_per_page' => (int) $atts['limit'],
            'orderby' => 'date',
            'order' => 'DESC',
        ] );

        if ( empty( $products ) ) {
            return '<p>' . \esc_html__( 'No products found.', 'sofir' ) . '</p>';
        }

        \wp_enqueue_script( 'sofir-payments' );

        ob_start();
        ?>
        <div class="sofir-product-catalog">
            <div class="sofir-products-grid" style="display: grid; grid-template-columns: repeat(<?php echo \esc_attr( $atts['columns'] ); ?>, 1fr); gap: 30px;">
                <?php foreach ( $products as $product ) : ?>
                    <?php
                    $price = \get_post_meta( $product->ID, 'sofir_product_price', true );
                    $sale_price = \get_post_meta( $product->ID, 'sofir_product_sale_price', true );
                    $thumbnail = \get_the_post_thumbnail_url( $product->ID, 'medium' );
                    ?>
                    <div class="sofir-product-card" style="border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                        <?php if ( $thumbnail ) : ?>
                            <img src="<?php echo \esc_url( $thumbnail ); ?>" alt="<?php echo \esc_attr( $product->post_title ); ?>" style="width: 100%; height: 200px; object-fit: cover;" />
                        <?php endif; ?>
                        
                        <div style="padding: 20px;">
                            <h3 style="margin: 0 0 10px 0;"><?php echo \esc_html( $product->post_title ); ?></h3>
                            <div class="sofir-product-excerpt" style="color: #666; margin-bottom: 15px;">
                                <?php echo \esc_html( \wp_trim_words( $product->post_excerpt, 15 ) ); ?>
                            </div>
                            
                            <div class="sofir-product-price" style="margin-bottom: 15px;">
                                <?php if ( $sale_price ) : ?>
                                    <span style="text-decoration: line-through; color: #999; margin-right: 10px;">
                                        <?php echo \esc_html( $this->format_price( (float) $price ) ); ?>
                                    </span>
                                    <span style="font-size: 24px; font-weight: bold; color: #d63638;">
                                        <?php echo \esc_html( $this->format_price( (float) $sale_price ) ); ?>
                                    </span>
                                <?php else : ?>
                                    <span style="font-size: 24px; font-weight: bold;">
                                        <?php echo \esc_html( $this->format_price( (float) $price ) ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <button class="button button-primary sofir-add-to-cart" data-product-id="<?php echo \esc_attr( $product->ID ); ?>" style="width: 100%;">
                                <?php \esc_html_e( 'Add to Cart', 'sofir' ); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    public function enqueue_admin_assets( string $hook ): void {
        if ( ! \str_contains( $hook, 'sofir-payment' ) ) {
            return;
        }

        \wp_enqueue_style(
            'sofir-payments-admin',
            SOFIR_ASSETS_URL . 'css/payments-admin.css',
            [],
            SOFIR_VERSION
        );

        \wp_enqueue_script(
            'sofir-payments-admin',
            SOFIR_ASSETS_URL . 'js/payments-admin.js',
            [ 'jquery', 'jquery-ui-datepicker' ],
            SOFIR_VERSION,
            true
        );
    }

    public function register_payment_cron(): void {
        if ( ! \wp_next_scheduled( 'sofir_payments_daily_check' ) ) {
            \wp_schedule_event( \time(), 'daily', 'sofir_payments_daily_check' );
        }

        \add_action( 'sofir_payments_daily_check', [ $this, 'check_subscription_renewals' ] );
    }

    public function check_subscription_renewals(): void {
        $subscriptions = \get_posts( [
            'post_type' => 'sofir_subscription',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'subscription_status',
                    'value' => 'active',
                ],
            ],
        ] );

        foreach ( $subscriptions as $subscription ) {
            $next_billing = \get_post_meta( $subscription->ID, 'next_billing_date', true );
            if ( $next_billing && \strtotime( $next_billing ) <= \time() ) {
                $this->process_subscription_renewal( $subscription->ID );
            }
        }
    }

    private function process_subscription_renewal( int $subscription_id ): void {
        $amount = \get_post_meta( $subscription_id, 'subscription_amount', true );
        $gateway = \get_post_meta( $subscription_id, 'subscription_gateway', true );
        $user_id = \get_post_meta( $subscription_id, 'subscription_user_id', true );

        \do_action( 'sofir/payment/subscription_renewal', $subscription_id, $amount, $gateway, $user_id );
    }

    public function apply_coupon( string $coupon_code, float $amount ): array {
        $coupons = \get_posts( [
            'post_type' => 'sofir_coupon',
            'title' => $coupon_code,
            'posts_per_page' => 1,
        ] );

        if ( empty( $coupons ) ) {
            return [
                'valid' => false,
                'message' => \__( 'Invalid coupon code.', 'sofir' ),
            ];
        }

        $coupon = $coupons[0];
        $discount_type = \get_post_meta( $coupon->ID, 'discount_type', true );
        $discount_value = (float) \get_post_meta( $coupon->ID, 'discount_value', true );
        $expiry_date = \get_post_meta( $coupon->ID, 'expiry_date', true );
        $usage_limit = (int) \get_post_meta( $coupon->ID, 'usage_limit', true );
        $usage_count = (int) \get_post_meta( $coupon->ID, 'usage_count', true );

        if ( $expiry_date && \strtotime( $expiry_date ) < \time() ) {
            return [
                'valid' => false,
                'message' => \__( 'This coupon has expired.', 'sofir' ),
            ];
        }

        if ( $usage_limit > 0 && $usage_count >= $usage_limit ) {
            return [
                'valid' => false,
                'message' => \__( 'This coupon has reached its usage limit.', 'sofir' ),
            ];
        }

        $discount = 0;
        if ( $discount_type === 'percentage' ) {
            $discount = ( $amount * $discount_value ) / 100;
        } else {
            $discount = $discount_value;
        }

        $new_amount = \max( 0, $amount - $discount );

        \update_post_meta( $coupon->ID, 'usage_count', $usage_count + 1 );

        return [
            'valid' => true,
            'discount' => $discount,
            'new_amount' => $new_amount,
            'message' => \sprintf( \__( 'Coupon applied! You saved %s', 'sofir' ), $this->format_price( $discount ) ),
        ];
    }

    public function generate_invoice( string $transaction_id ): int {
        $transactions = \get_option( 'sofir_payment_transactions', [] );

        if ( ! isset( $transactions[ $transaction_id ] ) ) {
            return 0;
        }

        $transaction = $transactions[ $transaction_id ];

        $invoice_id = \wp_insert_post( [
            'post_title' => 'Invoice #' . $transaction_id,
            'post_type' => 'sofir_invoice',
            'post_status' => 'publish',
        ] );

        \update_post_meta( $invoice_id, 'transaction_id', $transaction_id );
        \update_post_meta( $invoice_id, 'invoice_amount', $transaction['amount'] );
        \update_post_meta( $invoice_id, 'invoice_date', \current_time( 'mysql' ) );
        \update_post_meta( $invoice_id, 'invoice_status', $transaction['status'] );

        return $invoice_id;
    }

    public function get_payment_analytics(): array {
        $transactions = \get_option( 'sofir_payment_transactions', [] );
        
        $total_revenue = 0;
        $completed = 0;
        $pending = 0;
        $failed = 0;
        $refunded = 0;
        
        $gateway_stats = [];
        $monthly_revenue = [];

        foreach ( $transactions as $transaction ) {
            switch ( $transaction['status'] ) {
                case 'completed':
                    $total_revenue += $transaction['amount'];
                    $completed++;
                    break;
                case 'pending':
                    $pending++;
                    break;
                case 'failed':
                    $failed++;
                    break;
                case 'refunded':
                    $refunded++;
                    break;
            }

            if ( ! isset( $gateway_stats[ $transaction['gateway'] ] ) ) {
                $gateway_stats[ $transaction['gateway'] ] = [
                    'count' => 0,
                    'revenue' => 0,
                ];
            }
            $gateway_stats[ $transaction['gateway'] ]['count']++;
            if ( $transaction['status'] === 'completed' ) {
                $gateway_stats[ $transaction['gateway'] ]['revenue'] += $transaction['amount'];
            }

            $month = \date( 'Y-m', \strtotime( $transaction['created_at'] ) );
            if ( ! isset( $monthly_revenue[ $month ] ) ) {
                $monthly_revenue[ $month ] = 0;
            }
            if ( $transaction['status'] === 'completed' ) {
                $monthly_revenue[ $month ] += $transaction['amount'];
            }
        }

        return [
            'total_revenue' => $total_revenue,
            'total_transactions' => \count( $transactions ),
            'completed' => $completed,
            'pending' => $pending,
            'failed' => $failed,
            'refunded' => $refunded,
            'gateway_stats' => $gateway_stats,
            'monthly_revenue' => $monthly_revenue,
            'conversion_rate' => \count( $transactions ) > 0 ? ( $completed / \count( $transactions ) ) * 100 : 0,
        ];
    }
}
