<?php
namespace Sofir\Membership;

class Manager {
    private const OPTION_PLANS    = 'sofir_membership_plans';
    private const OPTION_SETTINGS = 'sofir_membership_settings';

    private static ?Manager $instance = null;

    /** @var array<string, array<string, mixed>> */
    private array $plans = [];

    /** @var array<string, mixed> */
    private array $settings = [];

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->plans    = $this->load_plans();
        $this->settings = $this->load_settings();
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_roles' ] );
        \add_action( 'admin_post_sofir_save_membership_plan', [ $this, 'handle_save_plan' ] );
        \add_action( 'admin_post_sofir_delete_membership_plan', [ $this, 'handle_delete_plan' ] );
        \add_action( 'admin_post_sofir_save_membership_settings', [ $this, 'handle_save_settings' ] );
        \add_shortcode( 'sofir_membership_pricing', [ $this, 'render_pricing_shortcode' ] );
        \add_shortcode( 'sofir_protected', [ $this, 'render_protected_content' ] );
        \add_action( 'show_user_profile', [ $this, 'render_user_membership_field' ] );
        \add_action( 'edit_user_profile', [ $this, 'render_user_membership_field' ] );
        \add_action( 'personal_options_update', [ $this, 'save_user_membership' ] );
        \add_action( 'edit_user_profile_update', [ $this, 'save_user_membership' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
    }

    public function register_roles(): void {
        if ( ! \get_role( 'sofir_member' ) ) {
            \add_role( 'sofir_member', \__( 'SOFIR Member', 'sofir' ), [ 'read' => true, 'upload_files' => true ] );
        }

        if ( ! \get_role( 'sofir_premium' ) ) {
            \add_role( 'sofir_premium', \__( 'SOFIR Premium', 'sofir' ), [ 'read' => true, 'upload_files' => true, 'edit_posts' => true ] );
        }
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function get_plans(): array {
        return $this->plans;
    }

    public function get_settings(): array {
        return $this->settings;
    }

    public function handle_save_plan(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_membership_plan', '_sofir_nonce' );

        $plan_id = isset( $_POST['plan_id'] ) ? \sanitize_key( \wp_unslash( $_POST['plan_id'] ) ) : '';
        $name    = isset( $_POST['plan_name'] ) ? \sanitize_text_field( \wp_unslash( $_POST['plan_name'] ) ) : '';
        $price   = isset( $_POST['plan_price'] ) ? (float) $_POST['plan_price'] : 0.0;
        $billing = isset( $_POST['plan_billing'] ) ? \sanitize_key( \wp_unslash( $_POST['plan_billing'] ) ) : 'monthly';
        $role    = isset( $_POST['plan_role'] ) ? \sanitize_key( \wp_unslash( $_POST['plan_role'] ) ) : 'subscriber';
        $features_raw = isset( $_POST['plan_features'] ) ? (string) \wp_unslash( $_POST['plan_features'] ) : '';
        $highlight    = isset( $_POST['plan_highlight'] );

        if ( '' === $plan_id ) {
            $plan_id = \sanitize_title( $name );
        }

        $features = array_filter( array_map( 'trim', explode( PHP_EOL, $features_raw ) ) );

        $this->plans[ $plan_id ] = [
            'id'        => $plan_id,
            'name'      => $name,
            'price'     => $price,
            'billing'   => in_array( $billing, [ 'monthly', 'yearly', 'lifetime' ], true ) ? $billing : 'monthly',
            'role'      => $role,
            'features'  => $features,
            'highlight' => $highlight,
        ];

        \update_option( self::OPTION_PLANS, $this->plans );

        \wp_safe_redirect( \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'users', 'sofir_notice' => 'plan_saved' ], \admin_url( 'admin.php' ) ) );
        exit;
    }

    public function handle_delete_plan(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_delete_plan', '_sofir_nonce' );

        $plan_id = isset( $_GET['plan_id'] ) ? \sanitize_key( \wp_unslash( $_GET['plan_id'] ) ) : '';

        if ( isset( $this->plans[ $plan_id ] ) ) {
            unset( $this->plans[ $plan_id ] );
            \update_option( self::OPTION_PLANS, $this->plans );
        }

        \wp_safe_redirect( \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'users', 'sofir_notice' => 'plan_deleted' ], \admin_url( 'admin.php' ) ) );
        exit;
    }

    public function handle_save_settings(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_membership_settings', '_sofir_nonce' );

        $currency   = isset( $_POST['membership_currency'] ) ? \sanitize_text_field( \wp_unslash( $_POST['membership_currency'] ) ) : 'USD';
        $stripe_pub = isset( $_POST['stripe_publishable'] ) ? \sanitize_text_field( \wp_unslash( $_POST['stripe_publishable'] ) ) : '';
        $stripe_sec = isset( $_POST['stripe_secret'] ) ? \sanitize_text_field( \wp_unslash( $_POST['stripe_secret'] ) ) : '';

        $this->settings = [
            'currency'          => $currency,
            'stripe_publishable'=> $stripe_pub,
            'stripe_secret'     => $stripe_sec,
        ];

        \update_option( self::OPTION_SETTINGS, $this->settings );

        \wp_safe_redirect( \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'users', 'sofir_notice' => 'membership_settings_saved' ], \admin_url( 'admin.php' ) ) );
        exit;
    }

    public function render_pricing_shortcode( array $atts = [] ): string {
        if ( empty( $this->plans ) ) {
            return '';
        }

        $currency = $this->settings['currency'] ?? 'USD';

        \wp_enqueue_style( 'sofir-membership' );

        ob_start();
        echo '<div class="sofir-membership-pricing">';

        foreach ( $this->plans as $plan ) {
            $classes = 'sofir-membership-plan';
            if ( ! empty( $plan['highlight'] ) ) {
                $classes .= ' is-highlighted';
            }

            $price_display = $plan['price'] > 0 ? $currency . ' ' . \number_format_i18n( $plan['price'], 2 ) : \__( 'Free', 'sofir' );
            $billing_label = $this->get_billing_label( $plan['billing'] ?? 'monthly' );

            echo '<article class="' . \esc_attr( $classes ) . '">';
            echo '<h3>' . \esc_html( $plan['name'] ?? '' ) . '</h3>';
            echo '<p class="price">' . \esc_html( $price_display ) . ' <span>' . \esc_html( $billing_label ) . '</span></p>';

            if ( ! empty( $plan['features'] ) ) {
                echo '<ul>';
                foreach ( $plan['features'] as $feature ) {
                    echo '<li>' . \esc_html( $feature ) . '</li>';
                }
                echo '</ul>';
            }

            $url = \apply_filters( 'sofir/membership/plan_url', \wp_login_url(), $plan );
            echo '<p><a class="button button-primary" href="' . \esc_url( $url ) . '">' . \esc_html__( 'Subscribe', 'sofir' ) . '</a></p>';
            echo '</article>';
        }

        echo '</div>';

        return (string) ob_get_clean();
    }

    public function render_protected_content( array $atts, string $content = '' ): string {
        $atts = \shortcode_atts(
            [
                'plan' => '',
            ],
            $atts,
            'sofir_protected'
        );

        if ( ! \is_user_logged_in() ) {
            return '<div class="sofir-protected"><p>' . \esc_html__( 'Please log in to access this content.', 'sofir' ) . '</p>' . \wp_login_form( [ 'echo' => false ] ) . '</div>';
        }

        $user_plan = $this->get_user_plan( \get_current_user_id() );

        if ( '' === $atts['plan'] || $atts['plan'] === $user_plan ) {
            return \do_shortcode( $content );
        }

        return '<div class="sofir-protected"><p>' . \esc_html__( 'Your membership level does not include this content.', 'sofir' ) . '</p></div>';
    }

    public function render_user_membership_field( \WP_User $user ): void {
        $plans    = $this->plans;
        $current  = $this->get_user_plan( $user->ID );

        echo '<h2>' . \esc_html__( 'SOFIR Membership', 'sofir' ) . '</h2>';
        echo '<table class="form-table"><tr><th>' . \esc_html__( 'Membership Plan', 'sofir' ) . '</th><td>';
        echo '<select name="sofir_membership_plan">';
        echo '<option value="">' . \esc_html__( 'None', 'sofir' ) . '</option>';
        foreach ( $plans as $plan ) {
            echo '<option value="' . \esc_attr( $plan['id'] ) . '" ' . \selected( $current, $plan['id'], false ) . '>' . \esc_html( $plan['name'] ) . '</option>';
        }
        echo '</select>';
        echo '</td></tr></table>';
    }

    public function save_user_membership( int $user_id ): void {
        if ( ! \current_user_can( 'edit_user', $user_id ) ) {
            return;
        }

        $plan = isset( $_POST['sofir_membership_plan'] ) ? \sanitize_key( \wp_unslash( $_POST['sofir_membership_plan'] ) ) : '';

        if ( '' === $plan ) {
            \delete_user_meta( $user_id, 'sofir_membership_plan' );
            return;
        }

        if ( ! isset( $this->plans[ $plan ] ) ) {
            return;
        }

        \update_user_meta( $user_id, 'sofir_membership_plan', $plan );

        if ( ! empty( $this->plans[ $plan ]['role'] ) ) {
            $user = \get_userdata( $user_id );
            if ( $user ) {
                $user->set_role( $this->plans[ $plan ]['role'] );
            }
        }
    }

    public function get_user_plan( int $user_id ): string {
        return (string) \get_user_meta( $user_id, 'sofir_membership_plan', true );
    }

    private function load_plans(): array {
        $plans = \get_option( self::OPTION_PLANS, [] );

        if ( ! \is_array( $plans ) || empty( $plans ) ) {
            $plans = [
                'starter' => [
                    'id'        => 'starter',
                    'name'      => \__( 'Starter', 'sofir' ),
                    'price'     => 0,
                    'billing'   => 'monthly',
                    'role'      => 'sofir_member',
                    'features'  => [ \__( 'Access to directory listings', 'sofir' ) ],
                    'highlight' => false,
                ],
                'pro'     => [
                    'id'        => 'pro',
                    'name'      => \__( 'Professional', 'sofir' ),
                    'price'     => 29,
                    'billing'   => 'monthly',
                    'role'      => 'sofir_premium',
                    'features'  => [ \__( 'Advanced analytics', 'sofir' ), \__( 'Priority support', 'sofir' ) ],
                    'highlight' => true,
                ],
            ];

            \update_option( self::OPTION_PLANS, $plans );
        }

        return $plans;
    }

    private function load_settings(): array {
        $defaults = [
            'currency'          => 'USD',
            'stripe_publishable'=> '',
            'stripe_secret'     => '',
        ];

        $settings = \get_option( self::OPTION_SETTINGS, [] );

        if ( ! \is_array( $settings ) ) {
            $settings = [];
        }

        return \wp_parse_args( $settings, $defaults );
    }

    public function register_assets(): void {
        if ( ! \wp_style_is( 'sofir-membership', 'registered' ) ) {
            \wp_register_style(
                'sofir-membership',
                SOFIR_ASSETS_URL . 'css/membership.css',
                [],
                SOFIR_VERSION
            );
        }
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/membership/plans',
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return \rest_ensure_response( array_values( $this->plans ) );
                },
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/membership/assign',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'rest_assign_membership' ],
                'permission_callback' => function () {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );
    }

    public function rest_assign_membership( \WP_REST_Request $request ): \WP_REST_Response {
        $user_id = (int) $request->get_param( 'user_id' );
        $plan    = \sanitize_key( (string) $request->get_param( 'plan_id' ) );

        if ( ! $user_id || ! isset( $this->plans[ $plan ] ) ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Invalid request.', 'sofir' ) ], 400 );
        }

        \update_user_meta( $user_id, 'sofir_membership_plan', $plan );

        if ( ! empty( $this->plans[ $plan ]['role'] ) ) {
            $user = \get_userdata( $user_id );
            if ( $user ) {
                $user->set_role( $this->plans[ $plan ]['role'] );
            }
        }

        return \rest_ensure_response( [ 'status' => 'ok' ] );
    }

    private function get_billing_label( string $billing ): string {
        switch ( $billing ) {
            case 'yearly':
                return \__( 'per year', 'sofir' );
            case 'lifetime':
                return \__( 'lifetime access', 'sofir' );
            default:
                return \__( 'per month', 'sofir' );
        }
    }
}
