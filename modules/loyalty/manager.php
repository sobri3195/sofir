<?php
namespace Sofir\Loyalty;

class Manager {
    private const OPTION_SETTINGS = 'sofir_loyalty_settings';
    private const OPTION_REWARDS = 'sofir_loyalty_rewards';

    private static ?Manager $instance = null;

    private array $settings = [];
    private array $rewards = [];

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->settings = $this->load_settings();
        $this->rewards = $this->load_rewards();
    }

    public function boot(): void {
        \add_action( 'admin_post_sofir_save_loyalty_settings', [ $this, 'handle_save_settings' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_shortcode( 'sofir_loyalty_points', [ $this, 'render_points_shortcode' ] );
        \add_shortcode( 'sofir_loyalty_rewards', [ $this, 'render_rewards_shortcode' ] );
        
        \add_action( 'user_register', [ $this, 'award_signup_points' ] );
        \add_action( 'wp_login', [ $this, 'award_login_points' ], 10, 2 );
        \add_action( 'comment_post', [ $this, 'award_comment_points' ], 10, 3 );
        \add_action( 'publish_post', [ $this, 'award_post_points' ], 10, 2 );
        \add_action( 'sofir/payment/status_changed', [ $this, 'award_purchase_points' ], 10, 2 );
    }

    public function get_settings(): array {
        return $this->settings;
    }

    public function get_rewards(): array {
        return $this->rewards;
    }

    public function handle_save_settings(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_loyalty_settings', '_sofir_nonce' );

        $enabled = isset( $_POST['loyalty_enabled'] );
        $points_signup = (int) ( $_POST['points_signup'] ?? 0 );
        $points_login = (int) ( $_POST['points_login'] ?? 0 );
        $points_comment = (int) ( $_POST['points_comment'] ?? 0 );
        $points_post = (int) ( $_POST['points_post'] ?? 0 );
        $points_per_currency = (float) ( $_POST['points_per_currency'] ?? 0 );

        $this->settings = [
            'enabled' => $enabled,
            'points_signup' => $points_signup,
            'points_login' => $points_login,
            'points_comment' => $points_comment,
            'points_post' => $points_post,
            'points_per_currency' => $points_per_currency,
        ];

        \update_option( self::OPTION_SETTINGS, $this->settings );

        \wp_safe_redirect(
            \add_query_arg(
                [
                    'page' => 'sofir-dashboard',
                    'tab' => 'users',
                    'sofir_notice' => 'loyalty_settings_saved',
                ],
                \admin_url( 'admin.php' )
            )
        );
        exit;
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/loyalty/points/(?P<user_id>\d+)',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_user_points' ],
                'permission_callback' => function ( $request ) {
                    $user_id = $request->get_param( 'user_id' );
                    return \current_user_can( 'edit_user', $user_id ) || \get_current_user_id() === (int) $user_id;
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/loyalty/history/(?P<user_id>\d+)',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_user_history' ],
                'permission_callback' => function ( $request ) {
                    $user_id = $request->get_param( 'user_id' );
                    return \current_user_can( 'edit_user', $user_id ) || \get_current_user_id() === (int) $user_id;
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/loyalty/redeem',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_redeem_reward' ],
                'permission_callback' => function () {
                    return \is_user_logged_in();
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/loyalty/rewards',
            [
                'methods' => 'GET',
                'callback' => [ $this, 'rest_get_rewards' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function rest_get_user_points( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id = (int) $request->get_param( 'user_id' );
        $points = $this->get_user_points( $user_id );

        return \rest_ensure_response( [
            'user_id' => $user_id,
            'points' => $points,
        ] );
    }

    public function rest_get_user_history( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id = (int) $request->get_param( 'user_id' );
        $history = $this->get_user_history( $user_id );

        return \rest_ensure_response( $history );
    }

    public function rest_redeem_reward( \WP_REST_Request $request ): \WP_REST_Response {
        $reward_id = \sanitize_key( (string) $request->get_param( 'reward_id' ) );
        $user_id = \get_current_user_id();

        if ( ! isset( $this->rewards[ $reward_id ] ) ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Invalid reward', 'sofir' ) ], 400 );
        }

        $reward = $this->rewards[ $reward_id ];
        $user_points = $this->get_user_points( $user_id );

        if ( $user_points < $reward['points_cost'] ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Insufficient points', 'sofir' ) ], 400 );
        }

        $this->deduct_points( $user_id, $reward['points_cost'], 'Redeemed: ' . $reward['name'] );

        \do_action( 'sofir/loyalty/reward_redeemed', $user_id, $reward_id, $reward );

        return \rest_ensure_response( [
            'status' => 'success',
            'message' => \__( 'Reward redeemed successfully', 'sofir' ),
            'remaining_points' => $this->get_user_points( $user_id ),
        ] );
    }

    public function rest_get_rewards(): \WP_REST_Response {
        return \rest_ensure_response( array_values( $this->rewards ) );
    }

    public function render_points_shortcode( array $atts ): string {
        if ( ! \is_user_logged_in() ) {
            return '<p>' . \esc_html__( 'Please log in to view your points.', 'sofir' ) . '</p>';
        }

        $user_id = \get_current_user_id();
        $points = $this->get_user_points( $user_id );

        ob_start();
        echo '<div class="sofir-loyalty-points">';
        echo '<h3>' . \esc_html__( 'Your Loyalty Points', 'sofir' ) . '</h3>';
        echo '<div class="sofir-points-balance">' . \esc_html( \number_format_i18n( $points ) ) . '</div>';
        echo '<p class="sofir-points-label">' . \esc_html__( 'Points', 'sofir' ) . '</p>';
        echo '</div>';

        return (string) ob_get_clean();
    }

    public function render_rewards_shortcode( array $atts ): string {
        if ( empty( $this->rewards ) ) {
            return '';
        }

        $user_points = \is_user_logged_in() ? $this->get_user_points( \get_current_user_id() ) : 0;

        ob_start();
        echo '<div class="sofir-loyalty-rewards">';
        echo '<h3>' . \esc_html__( 'Available Rewards', 'sofir' ) . '</h3>';

        foreach ( $this->rewards as $reward ) {
            $can_redeem = $user_points >= $reward['points_cost'];
            $class = 'sofir-reward-item' . ( $can_redeem ? ' can-redeem' : ' insufficient-points' );

            echo '<div class="' . \esc_attr( $class ) . '">';
            echo '<h4>' . \esc_html( $reward['name'] ) . '</h4>';
            echo '<p class="sofir-reward-description">' . \esc_html( $reward['description'] ?? '' ) . '</p>';
            echo '<div class="sofir-reward-cost">' . \esc_html( \number_format_i18n( $reward['points_cost'] ) ) . ' ' . \esc_html__( 'points', 'sofir' ) . '</div>';

            if ( \is_user_logged_in() ) {
                if ( $can_redeem ) {
                    echo '<button class="button button-primary sofir-redeem-btn" data-reward-id="' . \esc_attr( $reward['id'] ) . '">' . \esc_html__( 'Redeem', 'sofir' ) . '</button>';
                } else {
                    echo '<button class="button" disabled>' . \esc_html__( 'Insufficient Points', 'sofir' ) . '</button>';
                }
            }

            echo '</div>';
        }

        echo '</div>';

        return (string) ob_get_clean();
    }

    public function award_signup_points( int $user_id ): void {
        if ( ! $this->settings['enabled'] || ! $this->settings['points_signup'] ) {
            return;
        }

        $this->add_points( $user_id, $this->settings['points_signup'], 'Sign up bonus' );
    }

    public function award_login_points( string $user_login, \WP_User $user ): void {
        if ( ! $this->settings['enabled'] || ! $this->settings['points_login'] ) {
            return;
        }

        $last_login = \get_user_meta( $user->ID, 'sofir_last_login_points', true );
        $today = \gmdate( 'Y-m-d' );

        if ( $last_login === $today ) {
            return;
        }

        $this->add_points( $user->ID, $this->settings['points_login'], 'Daily login bonus' );
        \update_user_meta( $user->ID, 'sofir_last_login_points', $today );
    }

    public function award_comment_points( int $comment_id, int $comment_approved, array $commentdata ): void {
        if ( ! $this->settings['enabled'] || ! $this->settings['points_comment'] || ! $comment_approved ) {
            return;
        }

        $user_id = $commentdata['user_id'] ?? 0;

        if ( ! $user_id ) {
            return;
        }

        $this->add_points( $user_id, $this->settings['points_comment'], 'Posted a comment' );
    }

    public function award_post_points( int $post_id, \WP_Post $post ): void {
        if ( ! $this->settings['enabled'] || ! $this->settings['points_post'] ) {
            return;
        }

        $this->add_points( (int) $post->post_author, $this->settings['points_post'], 'Published a post: ' . $post->post_title );
    }

    public function award_purchase_points( string $transaction_id, string $status ): void {
        if ( ! $this->settings['enabled'] || ! $this->settings['points_per_currency'] || $status !== 'completed' ) {
            return;
        }

        $transactions = \get_option( 'sofir_payment_transactions', [] );
        $transaction = $transactions[ $transaction_id ] ?? null;

        if ( ! $transaction || ! isset( $transaction['amount'] ) ) {
            return;
        }

        $points = (int) ( $transaction['amount'] * $this->settings['points_per_currency'] );

        if ( $points > 0 ) {
            $this->add_points( (int) $transaction['user_id'], $points, 'Purchase: ' . ( $transaction['item_name'] ?? 'Item' ) );
        }
    }

    public function add_points( int $user_id, int $points, string $reason ): void {
        $current = $this->get_user_points( $user_id );
        $new_total = $current + $points;

        \update_user_meta( $user_id, 'sofir_loyalty_points', $new_total );

        $this->add_history_entry( $user_id, $points, $reason );

        \do_action( 'sofir/loyalty/points_added', $user_id, $points, $new_total, $reason );
    }

    public function deduct_points( int $user_id, int $points, string $reason ): void {
        $current = $this->get_user_points( $user_id );
        $new_total = \max( 0, $current - $points );

        \update_user_meta( $user_id, 'sofir_loyalty_points', $new_total );

        $this->add_history_entry( $user_id, -$points, $reason );

        \do_action( 'sofir/loyalty/points_deducted', $user_id, $points, $new_total, $reason );
    }

    public function get_user_points( int $user_id ): int {
        return (int) \get_user_meta( $user_id, 'sofir_loyalty_points', true );
    }

    private function add_history_entry( int $user_id, int $points, string $reason ): void {
        $history = $this->get_user_history( $user_id );

        $history[] = [
            'points' => $points,
            'reason' => $reason,
            'date' => \current_time( 'mysql' ),
        ];

        $history = \array_slice( $history, -50 );

        \update_user_meta( $user_id, 'sofir_loyalty_history', $history );
    }

    private function get_user_history( int $user_id ): array {
        $history = \get_user_meta( $user_id, 'sofir_loyalty_history', true );

        if ( ! \is_array( $history ) ) {
            $history = [];
        }

        return $history;
    }

    private function load_settings(): array {
        $defaults = [
            'enabled' => true,
            'points_signup' => 100,
            'points_login' => 10,
            'points_comment' => 5,
            'points_post' => 20,
            'points_per_currency' => 1,
        ];

        $settings = \get_option( self::OPTION_SETTINGS, [] );

        if ( ! \is_array( $settings ) ) {
            $settings = [];
        }

        return \wp_parse_args( $settings, $defaults );
    }

    private function load_rewards(): array {
        $rewards = \get_option( self::OPTION_REWARDS, [] );

        if ( ! \is_array( $rewards ) || empty( $rewards ) ) {
            $rewards = [
                'discount_10' => [
                    'id' => 'discount_10',
                    'name' => \__( '10% Discount Coupon', 'sofir' ),
                    'description' => \__( 'Get 10% off your next purchase', 'sofir' ),
                    'points_cost' => 500,
                ],
                'discount_20' => [
                    'id' => 'discount_20',
                    'name' => \__( '20% Discount Coupon', 'sofir' ),
                    'description' => \__( 'Get 20% off your next purchase', 'sofir' ),
                    'points_cost' => 1000,
                ],
                'free_shipping' => [
                    'id' => 'free_shipping',
                    'name' => \__( 'Free Shipping', 'sofir' ),
                    'description' => \__( 'Free shipping on your next order', 'sofir' ),
                    'points_cost' => 750,
                ],
            ];

            \update_option( self::OPTION_REWARDS, $rewards );
        }

        return $rewards;
    }
}
