<?php
namespace Sofir\Enhancement;

class Auth {
    private static ?Auth $instance = null;

    public static function instance(): Auth {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_shortcode( 'sofir_login_form', [ $this, 'render_login_form' ] );
        \add_shortcode( 'sofir_logout_link', [ $this, 'render_logout_link' ] );
        \add_shortcode( 'sofir_register_form', [ $this, 'render_register_form' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'show_user_profile', [ $this, 'render_phone_field' ] );
        \add_action( 'edit_user_profile', [ $this, 'render_phone_field' ] );
        \add_action( 'personal_options_update', [ $this, 'save_phone_field' ] );
        \add_action( 'edit_user_profile_update', [ $this, 'save_phone_field' ] );
    }

    public function render_login_form( array $atts = [] ): string {
        if ( \is_user_logged_in() ) {
            $user    = \wp_get_current_user();
            $display = \esc_html( $user->display_name ?: $user->user_login );

            $html  = '<div class="sofir-login sofir-login--authenticated">';
            $html .= '<p>' . \sprintf( \esc_html__( 'You are logged in as %s.', 'sofir' ), '<strong>' . $display . '</strong>' ) . '</p>';
            $html .= '<p>' . \do_shortcode( '[sofir_logout_link]' ) . '</p>';
            $html .= '</div>';

            return $html;
        }

        $atts = \shortcode_atts(
            [
                'redirect' => \sanitize_text_field( $_SERVER['REQUEST_URI'] ?? '' ),
                'label'    => \__( 'Login', 'sofir' ),
            ],
            $atts,
            'sofir_login_form'
        );

        $args = [
            'echo'           => false,
            'remember'       => true,
            'redirect'       => $atts['redirect'],
            'label_username' => \__( 'Email or Username', 'sofir' ),
            'label_password' => \__( 'Password', 'sofir' ),
            'label_log_in'   => $atts['label'],
            'remember_label' => \__( 'Remember me', 'sofir' ),
        ];

        $html  = '<div class="sofir-login">';
        $html .= \wp_login_form( $args );
        $html .= '<p class="sofir-login__links">';
        $html .= '<a href="' . \esc_url( \wp_lostpassword_url() ) . '">' . \esc_html__( 'Forgot password?', 'sofir' ) . '</a>';

        if ( \get_option( 'users_can_register' ) ) {
            $html .= ' · <a href="' . \esc_url( \wp_registration_url() ) . '">' . \esc_html__( 'Register', 'sofir' ) . '</a>';
        }

        $html .= '</p>';
        $html .= '</div>';

        return $html;
    }

    public function render_logout_link( array $atts = [] ): string {
        if ( ! \is_user_logged_in() ) {
            return '';
        }

        $atts = \shortcode_atts(
            [
                'redirect' => home_url(),
                'label'    => \__( 'Log out', 'sofir' ),
            ],
            $atts,
            'sofir_logout_link'
        );

        $url = \wp_logout_url( $atts['redirect'] );

        return '<a class="sofir-logout-link" href="' . \esc_url( $url ) . '">' . \esc_html( $atts['label'] ) . '</a>';
    }

    public function render_register_form( array $atts = [] ): string {
        if ( \is_user_logged_in() ) {
            return '<p>' . \esc_html__( 'You are already registered and logged in.', 'sofir' ) . '</p>';
        }

        if ( ! \get_option( 'users_can_register' ) ) {
            return '<p>' . \esc_html__( 'User registration is currently not available.', 'sofir' ) . '</p>';
        }

        $atts = \shortcode_atts(
            [
                'redirect' => \home_url(),
                'phone_only' => false,
            ],
            $atts,
            'sofir_register_form'
        );

        \wp_enqueue_script( 'sofir-auth' );

        ob_start();
        echo '<div class="sofir-register">';
        echo '<form class="sofir-register-form" method="post">';
        echo \wp_nonce_field( 'sofir_register', 'sofir_register_nonce', true, false );
        
        if ( $atts['phone_only'] ) {
            echo '<p class="sofir-form-field">';
            echo '<label for="sofir_phone">' . \esc_html__( 'Phone Number', 'sofir' ) . '</label>';
            echo '<input type="tel" name="sofir_phone" id="sofir_phone" required />';
            echo '</p>';
        } else {
            echo '<p class="sofir-form-field">';
            echo '<label for="sofir_username">' . \esc_html__( 'Username', 'sofir' ) . '</label>';
            echo '<input type="text" name="sofir_username" id="sofir_username" required />';
            echo '</p>';

            echo '<p class="sofir-form-field">';
            echo '<label for="sofir_email">' . \esc_html__( 'Email', 'sofir' ) . '</label>';
            echo '<input type="email" name="sofir_email" id="sofir_email" required />';
            echo '</p>';

            echo '<p class="sofir-form-field">';
            echo '<label for="sofir_phone">' . \esc_html__( 'Phone Number', 'sofir' ) . '</label>';
            echo '<input type="tel" name="sofir_phone" id="sofir_phone" />';
            echo '</p>';

            echo '<p class="sofir-form-field">';
            echo '<label for="sofir_password">' . \esc_html__( 'Password', 'sofir' ) . '</label>';
            echo '<input type="password" name="sofir_password" id="sofir_password" required />';
            echo '</p>';
        }

        echo '<input type="hidden" name="sofir_redirect" value="' . \esc_url( $atts['redirect'] ) . '" />';
        echo '<input type="hidden" name="sofir_phone_only" value="' . ( $atts['phone_only'] ? '1' : '0' ) . '" />';

        echo '<p class="sofir-form-submit">';
        echo '<button type="submit" class="button button-primary">' . \esc_html__( 'Register', 'sofir' ) . '</button>';
        echo '</p>';

        echo '</form>';
        echo '</div>';

        return (string) ob_get_clean();
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/auth/register',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_register_user' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/auth/phone-login',
            [
                'methods' => 'POST',
                'callback' => [ $this, 'rest_phone_login' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function rest_register_user( \WP_REST_Request $request ): \WP_REST_Response {
        $phone_only = (bool) $request->get_param( 'phone_only' );
        $phone = \sanitize_text_field( (string) $request->get_param( 'phone' ) );

        if ( $phone_only ) {
            if ( ! $phone ) {
                return new \WP_REST_Response( [ 'message' => \__( 'Phone number is required', 'sofir' ) ], 400 );
            }

            $is_valid = \apply_filters( 'sofir/auth/validate_phone', true, $phone );
            if ( ! $is_valid ) {
                return new \WP_REST_Response( [ 'message' => \__( 'Invalid phone number format', 'sofir' ) ], 400 );
            }

            $existing = $this->get_user_by_phone( $phone );
            if ( $existing ) {
                return new \WP_REST_Response( [ 'message' => \__( 'Phone number already registered', 'sofir' ) ], 400 );
            }

            $username = \apply_filters( 'sofir/auth/generate_username', 'user_' . \sanitize_title( $phone ), $phone );
            $email = $username . '@phone.local';
            $password = \wp_generate_password( 12, true, true );

            $user_id = \wp_create_user( $username, $password, $email );

            if ( \is_wp_error( $user_id ) ) {
                return new \WP_REST_Response( [ 'message' => $user_id->get_error_message() ], 400 );
            }

            \update_user_meta( $user_id, 'sofir_phone', $phone );
            \update_user_meta( $user_id, 'sofir_phone_only_registration', true );

            \do_action( 'sofir/auth/user_registered', $user_id, true );

            \wp_set_current_user( $user_id );
            \wp_set_auth_cookie( $user_id );

            $redirect = \apply_filters( 'sofir/auth/register_redirect', '', $user_id );

            return \rest_ensure_response( [
                'status' => 'success',
                'user_id' => $user_id,
                'redirect' => $redirect,
                'message' => \__( 'Registration successful', 'sofir' ),
            ] );
        }

        $username = \sanitize_user( (string) $request->get_param( 'username' ) );
        $email = \sanitize_email( (string) $request->get_param( 'email' ) );
        $password = (string) $request->get_param( 'password' );

        if ( ! $username || ! $email || ! $password ) {
            return new \WP_REST_Response( [ 'message' => \__( 'All fields are required', 'sofir' ) ], 400 );
        }

        $user_id = \wp_create_user( $username, $password, $email );

        if ( \is_wp_error( $user_id ) ) {
            return new \WP_REST_Response( [ 'message' => $user_id->get_error_message() ], 400 );
        }

        if ( $phone ) {
            \update_user_meta( $user_id, 'sofir_phone', $phone );
        }

        \do_action( 'sofir/auth/user_registered', $user_id, false );

        \wp_set_current_user( $user_id );
        \wp_set_auth_cookie( $user_id );

        $redirect = \apply_filters( 'sofir/auth/register_redirect', '', $user_id );

        return \rest_ensure_response( [
            'status' => 'success',
            'user_id' => $user_id,
            'redirect' => $redirect,
            'message' => \__( 'Registration successful', 'sofir' ),
        ] );
    }

    public function rest_phone_login( \WP_REST_Request $request ): \WP_REST_Response {
        $phone = \sanitize_text_field( (string) $request->get_param( 'phone' ) );

        if ( ! $phone ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Phone number is required', 'sofir' ) ], 400 );
        }

        $user = $this->get_user_by_phone( $phone );

        if ( ! $user ) {
            return new \WP_REST_Response( [ 'message' => \__( 'User not found', 'sofir' ) ], 404 );
        }

        \do_action( 'sofir/auth/phone_login', $user->ID, $phone );

        \wp_set_current_user( $user->ID );
        \wp_set_auth_cookie( $user->ID );

        return \rest_ensure_response( [
            'status' => 'success',
            'user_id' => $user->ID,
            'message' => \__( 'Login successful', 'sofir' ),
        ] );
    }

    public function render_phone_field( \WP_User $user ): void {
        $phone = \get_user_meta( $user->ID, 'sofir_phone', true );

        echo '<h2>' . \esc_html__( 'Phone Number', 'sofir' ) . '</h2>';
        echo '<table class="form-table">';
        echo '<tr>';
        echo '<th><label for="sofir_phone">' . \esc_html__( 'Phone Number', 'sofir' ) . '</label></th>';
        echo '<td>';
        echo '<input type="tel" name="sofir_phone" id="sofir_phone" value="' . \esc_attr( $phone ) . '" class="regular-text" />';
        echo '</td>';
        echo '</tr>';
        echo '</table>';
    }

    public function save_phone_field( int $user_id ): void {
        if ( ! \current_user_can( 'edit_user', $user_id ) ) {
            return;
        }

        if ( isset( $_POST['sofir_phone'] ) ) {
            $phone = \sanitize_text_field( \wp_unslash( $_POST['sofir_phone'] ) );
            \update_user_meta( $user_id, 'sofir_phone', $phone );
        }
    }

    private function get_user_by_phone( string $phone ): ?\WP_User {
        $users = \get_users( [
            'meta_key' => 'sofir_phone',
            'meta_value' => $phone,
            'number' => 1,
        ] );

        return ! empty( $users ) ? $users[0] : null;
    }
}
