<?php
namespace Sofir\Webhooks;

class Manager {
    private const OPTION_WEBHOOKS = 'sofir_webhooks';

    private static ?Manager $instance = null;

    private array $webhooks = [];

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->webhooks = $this->load_webhooks();
    }

    public function boot(): void {
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'sofir/payment/status_changed', [ $this, 'trigger_payment_webhook' ], 10, 2 );
        \add_action( 'user_register', [ $this, 'trigger_user_register_webhook' ] );
        \add_action( 'profile_update', [ $this, 'trigger_user_update_webhook' ] );
        \add_action( 'wp_login', [ $this, 'trigger_login_webhook' ], 10, 2 );
        \add_action( 'publish_post', [ $this, 'trigger_post_publish_webhook' ], 10, 2 );
        \add_action( 'comment_post', [ $this, 'trigger_comment_webhook' ], 10, 3 );
        \add_action( 'sofir/form/submission', [ $this, 'trigger_form_webhook' ], 10, 2 );
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/webhooks',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_webhooks' ],
                'permission_callback' => function () {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/webhooks',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_create_webhook' ],
                'permission_callback' => function () {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/webhooks/(?P<id>[a-zA-Z0-9_-]+)',
            [
                'methods' => 'DELETE',
                'callback' => [ $this, 'rest_delete_webhook' ],
                'permission_callback' => function () {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/webhooks/test',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_test_webhook' ],
                'permission_callback' => function () {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/webhooks/triggers',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_triggers' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function rest_get_webhooks(): \WP_REST_Response {
        return \rest_ensure_response( array_values( $this->webhooks ) );
    }

    public function rest_create_webhook( \WP_REST_Request $request ): \WP_REST_Response {
        $name = \sanitize_text_field( (string) $request->get_param( 'name' ) );
        $url = \esc_url_raw( (string) $request->get_param( 'url' ) );
        $trigger = \sanitize_key( (string) $request->get_param( 'trigger' ) );
        $active = (bool) $request->get_param( 'active' );

        if ( ! $name || ! $url || ! $trigger ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Missing required fields', 'sofir' ) ], 400 );
        }

        $webhook_id = 'webhook_' . \wp_rand( 10000, 99999 );

        $this->webhooks[ $webhook_id ] = [
            'id' => $webhook_id,
            'name' => $name,
            'url' => $url,
            'trigger' => $trigger,
            'active' => $active,
            'created_at' => \current_time( 'mysql' ),
        ];

        \update_option( self::OPTION_WEBHOOKS, $this->webhooks );

        return \rest_ensure_response( $this->webhooks[ $webhook_id ] );
    }

    public function rest_delete_webhook( \WP_REST_Request $request ): \WP_REST_Response {
        $id = $request->get_param( 'id' );

        if ( ! isset( $this->webhooks[ $id ] ) ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Webhook not found', 'sofir' ) ], 404 );
        }

        unset( $this->webhooks[ $id ] );
        \update_option( self::OPTION_WEBHOOKS, $this->webhooks );

        return \rest_ensure_response( [ 'status' => 'ok' ] );
    }

    public function rest_test_webhook( \WP_REST_Request $request ): \WP_REST_Response {
        $url = \esc_url_raw( (string) $request->get_param( 'url' ) );

        if ( ! $url ) {
            return new \WP_REST_Response( [ 'message' => \__( 'URL is required', 'sofir' ) ], 400 );
        }

        $test_data = [
            'event' => 'test',
            'timestamp' => \current_time( 'timestamp' ),
            'data' => [ 'message' => 'This is a test webhook from SOFIR' ],
        ];

        $response = $this->send_webhook( $url, $test_data );

        if ( \is_wp_error( $response ) ) {
            return new \WP_REST_Response(
                [
                    'status' => 'error',
                    'message' => $response->get_error_message(),
                ],
                500
            );
        }

        return \rest_ensure_response( [
            'status' => 'success',
            'response_code' => \wp_remote_retrieve_response_code( $response ),
        ] );
    }

    public function rest_get_triggers(): \WP_REST_Response {
        $triggers = [
            [
                'key' => 'user_register',
                'label' => \__( 'User Registration', 'sofir' ),
                'description' => \__( 'Triggered when a new user registers', 'sofir' ),
            ],
            [
                'key' => 'user_update',
                'label' => \__( 'User Profile Update', 'sofir' ),
                'description' => \__( 'Triggered when a user updates their profile', 'sofir' ),
            ],
            [
                'key' => 'user_login',
                'label' => \__( 'User Login', 'sofir' ),
                'description' => \__( 'Triggered when a user logs in', 'sofir' ),
            ],
            [
                'key' => 'post_publish',
                'label' => \__( 'Post Published', 'sofir' ),
                'description' => \__( 'Triggered when a post is published', 'sofir' ),
            ],
            [
                'key' => 'comment_post',
                'label' => \__( 'New Comment', 'sofir' ),
                'description' => \__( 'Triggered when a new comment is posted', 'sofir' ),
            ],
            [
                'key' => 'form_submission',
                'label' => \__( 'Form Submission', 'sofir' ),
                'description' => \__( 'Triggered when a form is submitted', 'sofir' ),
            ],
            [
                'key' => 'payment_completed',
                'label' => \__( 'Payment Completed', 'sofir' ),
                'description' => \__( 'Triggered when a payment is completed', 'sofir' ),
            ],
            [
                'key' => 'membership_changed',
                'label' => \__( 'Membership Changed', 'sofir' ),
                'description' => \__( 'Triggered when user membership changes', 'sofir' ),
            ],
        ];

        return \rest_ensure_response( \apply_filters( 'sofir/webhook/triggers', $triggers ) );
    }

    public function trigger_payment_webhook( string $transaction_id, string $status ): void {
        if ( $status !== 'completed' ) {
            return;
        }

        $transactions = \get_option( 'sofir_payment_transactions', [] );
        $transaction = $transactions[ $transaction_id ] ?? null;

        if ( ! $transaction ) {
            return;
        }

        $data = [
            'event' => 'payment_completed',
            'timestamp' => \current_time( 'timestamp' ),
            'data' => $transaction,
        ];

        $this->trigger_webhooks( 'payment_completed', $data );
    }

    public function trigger_user_register_webhook( int $user_id ): void {
        $user = \get_userdata( $user_id );

        if ( ! $user ) {
            return;
        }

        $data = [
            'event' => 'user_register',
            'timestamp' => \current_time( 'timestamp' ),
            'data' => [
                'user_id' => $user_id,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'display_name' => $user->display_name,
                'roles' => $user->roles,
            ],
        ];

        $this->trigger_webhooks( 'user_register', $data );
    }

    public function trigger_user_update_webhook( int $user_id ): void {
        $user = \get_userdata( $user_id );

        if ( ! $user ) {
            return;
        }

        $data = [
            'event' => 'user_update',
            'timestamp' => \current_time( 'timestamp' ),
            'data' => [
                'user_id' => $user_id,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'display_name' => $user->display_name,
                'roles' => $user->roles,
            ],
        ];

        $this->trigger_webhooks( 'user_update', $data );
    }

    public function trigger_login_webhook( string $user_login, \WP_User $user ): void {
        $data = [
            'event' => 'user_login',
            'timestamp' => \current_time( 'timestamp' ),
            'data' => [
                'user_id' => $user->ID,
                'username' => $user->user_login,
                'email' => $user->user_email,
                'display_name' => $user->display_name,
                'login_time' => \current_time( 'mysql' ),
            ],
        ];

        $this->trigger_webhooks( 'user_login', $data );
    }

    public function trigger_post_publish_webhook( int $post_id, \WP_Post $post ): void {
        $data = [
            'event' => 'post_publish',
            'timestamp' => \current_time( 'timestamp' ),
            'data' => [
                'post_id' => $post_id,
                'post_type' => $post->post_type,
                'post_title' => $post->post_title,
                'post_author' => $post->post_author,
                'post_date' => $post->post_date,
                'permalink' => \get_permalink( $post_id ),
            ],
        ];

        $this->trigger_webhooks( 'post_publish', $data );
    }

    public function trigger_comment_webhook( int $comment_id, int $comment_approved, array $commentdata ): void {
        $data = [
            'event' => 'comment_post',
            'timestamp' => \current_time( 'timestamp' ),
            'data' => [
                'comment_id' => $comment_id,
                'post_id' => $commentdata['comment_post_ID'],
                'author' => $commentdata['comment_author'],
                'author_email' => $commentdata['comment_author_email'],
                'content' => $commentdata['comment_content'],
                'approved' => $comment_approved,
            ],
        ];

        $this->trigger_webhooks( 'comment_post', $data );
    }

    public function trigger_form_webhook( string $form_id, array $form_data ): void {
        $data = [
            'event' => 'form_submission',
            'timestamp' => \current_time( 'timestamp' ),
            'data' => [
                'form_id' => $form_id,
                'form_data' => $form_data,
            ],
        ];

        $this->trigger_webhooks( 'form_submission', $data );
    }

    private function trigger_webhooks( string $trigger, array $data ): void {
        foreach ( $this->webhooks as $webhook ) {
            if ( $webhook['trigger'] !== $trigger || ! $webhook['active'] ) {
                continue;
            }

            $this->send_webhook( $webhook['url'], $data );
        }

        \do_action( 'sofir/webhook/triggered', $trigger, $data );
    }

    private function send_webhook( string $url, array $data ) {
        $args = [
            'method' => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
                'X-SOFIR-Webhook' => 'true',
            ],
            'body' => \wp_json_encode( $data ),
            'timeout' => 15,
        ];

        return \wp_remote_post( $url, $args );
    }

    private function load_webhooks(): array {
        $webhooks = \get_option( self::OPTION_WEBHOOKS, [] );

        if ( ! \is_array( $webhooks ) ) {
            $webhooks = [];
        }

        return $webhooks;
    }
}
