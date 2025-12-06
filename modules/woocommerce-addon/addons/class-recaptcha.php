<?php
namespace Sofir\WooCommerceAddon\Addons;

class reCAPTCHA extends Addon_Base {
    public function get_id(): string {
        return 'recaptcha';
    }

    public function get_name(): string {
        return __( 'reCAPTCHA', 'sofir' );
    }

    public function get_description(): string {
        return __( 'Protect your website from suspicious login attempts with Google reCAPTCHA v3.', 'sofir' );
    }

    public function get_category(): string {
        return 'security';
    }

    public function get_icon(): string {
        return 'dashicons-shield';
    }

    public function get_settings(): array {
        return [
            'enable_recaptcha' => [
                'type' => 'checkbox',
                'label' => __( 'Enable reCAPTCHA', 'sofir' ),
                'default' => false,
            ],
            'site_key' => [
                'type' => 'text',
                'label' => __( 'Site Key', 'sofir' ),
                'default' => '',
                'description' => __( 'Get from Google reCAPTCHA admin console', 'sofir' ),
            ],
            'secret_key' => [
                'type' => 'text',
                'label' => __( 'Secret Key', 'sofir' ),
                'default' => '',
                'description' => __( 'Get from Google reCAPTCHA admin console', 'sofir' ),
            ],
            'version' => [
                'type' => 'select',
                'label' => __( 'reCAPTCHA Version', 'sofir' ),
                'options' => [
                    'v3' => __( 'reCAPTCHA v3 (Recommended)', 'sofir' ),
                    'v2_checkbox' => __( 'reCAPTCHA v2 (Checkbox)', 'sofir' ),
                    'v2_invisible' => __( 'reCAPTCHA v2 (Invisible)', 'sofir' ),
                ],
                'default' => 'v3',
            ],
            'threshold_score' => [
                'type' => 'number',
                'label' => __( 'Threshold Score (v3 only)', 'sofir' ),
                'default' => 0.5,
                'min' => 0.0,
                'max' => 1.0,
                'step' => 0.1,
                'description' => __( 'Lower values are more restrictive', 'sofir' ),
            ],
            'protect_login' => [
                'type' => 'checkbox',
                'label' => __( 'Protect Login Form', 'sofir' ),
                'default' => true,
            ],
            'protect_register' => [
                'type' => 'checkbox',
                'label' => __( 'Protect Registration Form', 'sofir' ),
                'default' => true,
            ],
            'protect_checkout' => [
                'type' => 'checkbox',
                'label' => __( 'Protect Checkout Form', 'sofir' ),
                'default' => false,
            ],
            'protect_lost_password' => [
                'type' => 'checkbox',
                'label' => __( 'Protect Lost Password Form', 'sofir' ),
                'default' => true,
            ],
            'protect_comment' => [
                'type' => 'checkbox',
                'label' => __( 'Protect Comment Form', 'sofir' ),
                'default' => false,
            ],
            'protect_contact_form' => [
                'type' => 'checkbox',
                'label' => __( 'Protect Contact Forms', 'sofir' ),
                'default' => true,
            ],
            'error_message' => [
                'type' => 'text',
                'label' => __( 'Error Message', 'sofir' ),
                'default' => __( 'reCAPTCHA verification failed. Please try again.', 'sofir' ),
            ],
            'bypass_roles' => [
                'type' => 'select',
                'label' => __( 'Bypass for Roles', 'sofir' ),
                'options' => $this->get_user_roles(),
                'default' => ['administrator'],
                'multiple' => true,
                'description' => __( 'Selected roles will not see reCAPTCHA', 'sofir' ),
            ],
        ];
    }

    public function enable(): void {
        parent::enable();
        
        \add_action( 'login_form', [ $this, 'add_recaptcha_to_login' ] );
        \add_action( 'register_form', [ $this, 'add_recaptcha_to_register' ] );
        \add_action( 'woocommerce_before_checkout_form', [ $this, 'add_recaptcha_to_checkout' ] );
        \add_action( 'lostpassword_form', [ $this, 'add_recaptcha_to_lost_password' ] );
        \add_action( 'comment_form', [ $this, 'add_recaptcha_to_comment' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \add_action( 'login_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \add_filter( 'wp_authenticate_user', [ $this, 'verify_login_recaptcha' ], 10, 2 );
        \add_filter( 'registration_errors', [ $this, 'verify_register_recaptcha' ], 10, 3 );
        \add_action( 'woocommerce_checkout_process', [ $this, 'verify_checkout_recaptcha' ] );
        \add_filter( 'allow_password_reset', [ $this, 'verify_lost_password_recaptcha' ], 10, 2 );
        \add_filter( 'preprocess_comment', [ $this, 'verify_comment_recaptcha' ] );
        \add_action( 'wp_ajax_nopriv_sofir_verify_recaptcha', [ $this, 'ajax_verify_recaptcha' ] );
        \add_action( 'wp_footer', [ $this, 'add_recaptcha_script' ] );
        \add_action( 'login_footer', [ $this, 'add_recaptcha_script' ] );
    }

    public function disable(): void {
        parent::disable();
        
        \remove_action( 'login_form', [ $this, 'add_recaptcha_to_login' ] );
        \remove_action( 'register_form', [ $this, 'add_recaptcha_to_register' ] );
        \remove_action( 'woocommerce_before_checkout_form', [ $this, 'add_recaptcha_to_checkout' ] );
        \remove_action( 'lostpassword_form', [ $this, 'add_recaptcha_to_lost_password' ] );
        \remove_action( 'comment_form', [ $this, 'add_recaptcha_to_comment' ] );
        \remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \remove_action( 'login_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \remove_filter( 'wp_authenticate_user', [ $this, 'verify_login_recaptcha' ], 10 );
        \remove_filter( 'registration_errors', [ $this, 'verify_register_recaptcha' ], 10 );
        \remove_action( 'woocommerce_checkout_process', [ $this, 'verify_checkout_recaptcha' ] );
        \remove_filter( 'allow_password_reset', [ $this, 'verify_lost_password_recaptcha' ], 10 );
        \remove_filter( 'preprocess_comment', [ $this, 'verify_comment_recaptcha' ] );
        \remove_action( 'wp_ajax_nopriv_sofir_verify_recaptcha', [ $this, 'ajax_verify_recaptcha' ] );
        \remove_action( 'wp_footer', [ $this, 'add_recaptcha_script' ] );
        \remove_action( 'login_footer', [ $this, 'add_recaptcha_script' ] );
    }

    private function get_user_roles(): array {
        global $wp_roles;
        $roles = [];
        
        foreach ( $wp_roles->roles as $role_slug => $role_data ) {
            $roles[$role_slug] = $role_data['name'];
        }
        
        return $roles;
    }

    public function enqueue_scripts(): void {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_enable_recaptcha', false ) ) {
            return;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return;
        }

        $version = \get_option( 'sofir_wc_addon_recaptcha_version', 'v3' );
        $site_key = \get_option( 'sofir_wc_addon_recaptcha_site_key', '' );

        if ( empty( $site_key ) ) {
            return;
        }

        if ( $version === 'v3' ) {
            \wp_enqueue_script(
                'google-recaptcha-v3',
                'https://www.google.com/recaptcha/api.js?render=' . $site_key,
                [],
                '3.0',
                true
            );
        } else {
            \wp_enqueue_script(
                'google-recaptcha-v2',
                'https://www.google.com/recaptcha/api.js',
                [],
                '2.0',
                true
            );
        }

        \wp_enqueue_script(
            'sofir-recaptcha',
            SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/recaptcha.js',
            [ 'jquery' ],
            '1.0.0',
            true
        );

        \wp_localize_script( 'sofir-recaptcha', 'sofirRecaptcha', [
            'siteKey' => $site_key,
            'version' => $version,
            'ajaxurl' => \admin_url( 'admin-ajax.php' ),
            'nonce' => \wp_create_nonce( 'sofir_recaptcha_nonce' ),
            'i18n' => [
                'error' => \get_option( 'sofir_wc_addon_recaptcha_error_message', __( 'reCAPTCHA verification failed. Please try again.', 'sofir' ) ),
                'verifying' => __( 'Verifying...', 'sofir' ),
            ],
        ] );
    }

    private function should_bypass_recaptcha(): bool {
        if ( ! \is_user_logged_in() ) {
            return false;
        }

        $user = \wp_get_current_user();
        $bypass_roles = \get_option( 'sofir_wc_addon_recaptcha_bypass_roles', ['administrator'] );
        
        return ! empty( array_intersect( $user->roles, $bypass_roles ) );
    }

    public function add_recaptcha_to_login(): void {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_login', true ) ) {
            return;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return;
        }

        echo '<div class="sofir-recaptcha-field">';
        $this->render_recaptcha_field( 'login' );
        echo '</div>';
    }

    public function add_recaptcha_to_register(): void {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_register', true ) ) {
            return;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return;
        }

        echo '<div class="sofir-recaptcha-field">';
        $this->render_recaptcha_field( 'register' );
        echo '</div>';
    }

    public function add_recaptcha_to_checkout(): void {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_checkout', false ) ) {
            return;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return;
        }

        echo '<div class="sofir-recaptcha-field">';
        $this->render_recaptcha_field( 'checkout' );
        echo '</div>';
    }

    public function add_recaptcha_to_lost_password(): void {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_lost_password', true ) ) {
            return;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return;
        }

        echo '<div class="sofir-recaptcha-field">';
        $this->render_recaptcha_field( 'lost_password' );
        echo '</div>';
    }

    public function add_recaptcha_to_comment(): void {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_comment', false ) ) {
            return;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return;
        }

        echo '<div class="sofir-recaptcha-field">';
        $this->render_recaptcha_field( 'comment' );
        echo '</div>';
    }

    private function render_recaptcha_field( $form_type ): void {
        $version = \get_option( 'sofir_wc_addon_recaptcha_version', 'v3' );
        $site_key = \get_option( 'sofir_wc_addon_recaptcha_site_key', '' );

        if ( empty( $site_key ) ) {
            return;
        }

        if ( $version === 'v3' ) {
            echo '<input type="hidden" name="sofir-recaptcha-response" id="sofir-recaptcha-response-' . esc_attr( $form_type ) . '">';
            echo '<input type="hidden" name="sofir-recaptcha-form" value="' . esc_attr( $form_type ) . '">';
        } else {
            $theme = is_admin() ? 'light' : 'light';
            $size = $version === 'v2_invisible' ? 'invisible' : 'normal';
            
            echo '<div class="g-recaptcha" data-sitekey="' . esc_attr( $site_key ) . '" data-theme="' . esc_attr( $theme ) . '" data-size="' . esc_attr( $size ) . '"></div>';
            
            if ( $version === 'v2_invisible' ) {
                echo '<input type="hidden" name="sofir-recaptcha-form" value="' . esc_attr( $form_type ) . '">';
            }
        }
    }

    public function add_recaptcha_script(): void {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_enable_recaptcha', false ) ) {
            return;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return;
        }

        $version = \get_option( 'sofir_wc_addon_recaptcha_version', 'v3' );
        $site_key = \get_option( 'sofir_wc_addon_recaptcha_site_key', '' );

        if ( empty( $site_key ) ) {
            return;
        }

        if ( $version === 'v3' ) {
            echo '<script>
                function sofirRecaptchaCallback() {
                    grecaptcha.ready(function() {
                        grecaptcha.execute("' . esc_js( $site_key ) . '", {action: "homepage"}).then(function(token) {
                            var forms = document.querySelectorAll("form");
                            forms.forEach(function(form) {
                                var responseInput = form.querySelector("input[name=\"sofir-recaptcha-response\"]");
                                if (responseInput) {
                                    responseInput.value = token;
                                }
                            });
                        });
                    });
                }
                
                if (typeof grecaptcha !== "undefined") {
                    sofirRecaptchaCallback();
                }
            </script>';
        }
    }

    public function verify_login_recaptcha( $user, $password ): \WP_User|\WP_Error {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_login', true ) ) {
            return $user;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return $user;
        }

        $verification = $this->verify_recaptcha_response();
        if ( ! $verification['success'] ) {
            return new \WP_Error( 'recaptcha_failed', \get_option( 'sofir_wc_addon_recaptcha_error_message', __( 'reCAPTCHA verification failed. Please try again.', 'sofir' ) ) );
        }

        return $user;
    }

    public function verify_register_recaptcha( $errors, $sanitized_user_login, $user_email ): \WP_Error {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_register', true ) ) {
            return $errors;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return $errors;
        }

        $verification = $this->verify_recaptcha_response();
        if ( ! $verification['success'] ) {
            $errors->add( 'recaptcha_failed', \get_option( 'sofir_wc_addon_recaptcha_error_message', __( 'reCAPTCHA verification failed. Please try again.', 'sofir' ) ) );
        }

        return $errors;
    }

    public function verify_checkout_recaptcha(): void {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_checkout', false ) ) {
            return;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return;
        }

        $verification = $this->verify_recaptcha_response();
        if ( ! $verification['success'] ) {
            \wc_add_notice( \get_option( 'sofir_wc_addon_recaptcha_error_message', __( 'reCAPTCHA verification failed. Please try again.', 'sofir' ) ), 'error' );
        }
    }

    public function verify_lost_password_recaptcha( $allow, $user_id ): bool {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_lost_password', true ) ) {
            return $allow;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return $allow;
        }

        $verification = $this->verify_recaptcha_response();
        if ( ! $verification['success'] ) {
            \wp_die( \get_option( 'sofir_wc_addon_recaptcha_error_message', __( 'reCAPTCHA verification failed. Please try again.', 'sofir' ) ) );
        }

        return $allow;
    }

    public function verify_comment_recaptcha( $commentdata ): array {
        if ( ! \get_option( 'sofir_wc_addon_recaptcha_protect_comment', false ) ) {
            return $commentdata;
        }

        if ( $this->should_bypass_recaptcha() ) {
            return $commentdata;
        }

        $verification = $this->verify_recaptcha_response();
        if ( ! $verification['success'] ) {
            \wp_die( \get_option( 'sofir_wc_addon_recaptcha_error_message', __( 'reCAPTCHA verification failed. Please try again.', 'sofir' ) ) );
        }

        return $commentdata;
    }

    public function ajax_verify_recaptcha(): void {
        \check_ajax_referer( 'sofir_recaptcha_nonce', 'nonce' );

        $verification = $this->verify_recaptcha_response();
        
        if ( $verification['success'] ) {
            \wp_send_json_success( [ 'message' => __( 'Verification successful', 'sofir' ) ] );
        } else {
            \wp_send_json_error( [ 'message' => \get_option( 'sofir_wc_addon_recaptcha_error_message', __( 'reCAPTCHA verification failed. Please try again.', 'sofir' ) ) ] );
        }
    }

    private function verify_recaptcha_response(): array {
        $secret_key = \get_option( 'sofir_wc_addon_recaptcha_secret_key', '' );
        $version = \get_option( 'sofir_wc_addon_recaptcha_version', 'v3' );
        $threshold = \get_option( 'sofir_wc_addon_recaptcha_threshold_score', 0.5 );

        if ( empty( $secret_key ) ) {
            return [ 'success' => false, 'error' => 'Secret key not configured' ];
        }

        $response = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
        if ( $version === 'v3' ) {
            $response = isset( $_POST['sofir-recaptcha-response'] ) ? $_POST['sofir-recaptcha-response'] : '';
        }

        if ( empty( $response ) ) {
            return [ 'success' => false, 'error' => 'No response received' ];
        }

        $remote_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        
        $verify_response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $secret_key,
                'response' => $response,
                'remoteip' => $remote_ip,
            ],
        ] );

        if ( is_wp_error( $verify_response ) ) {
            return [ 'success' => false, 'error' => 'Verification service error' ];
        }

        $body = json_decode( wp_remote_retrieve_body( $verify_response ), true );
        
        if ( ! $body['success'] ) {
            return [ 'success' => false, 'error' => $body['error-codes'][0] ?? 'Unknown error' ];
        }

        if ( $version === 'v3' && isset( $body['score'] ) ) {
            if ( $body['score'] < $threshold ) {
                return [ 'success' => false, 'error' => 'Score below threshold' ];
            }
        }

        return [ 'success' => true ];
    }

    public function is_recaptcha_configured(): bool {
        $site_key = \get_option( 'sofir_wc_addon_recaptcha_site_key', '' );
        $secret_key = \get_option( 'sofir_wc_addon_recaptcha_secret_key', '' );
        
        return ! empty( $site_key ) && ! empty( $secret_key );
    }

    public function get_recaptcha_version(): string {
        return \get_option( 'sofir_wc_addon_recaptcha_version', 'v3' );
    }

    public function get_threshold_score(): float {
        return (float) \get_option( 'sofir_wc_addon_recaptcha_threshold_score', 0.5 );
    }

    public function is_form_protected( $form_type ): bool {
        $setting_map = [
            'login' => 'protect_login',
            'register' => 'protect_register',
            'checkout' => 'protect_checkout',
            'lost_password' => 'protect_lost_password',
            'comment' => 'protect_comment',
            'contact' => 'protect_contact_form',
        ];

        $setting = $setting_map[$form_type] ?? '';
        if ( empty( $setting ) ) {
            return false;
        }

        return \get_option( 'sofir_wc_addon_recaptcha_' . $setting, false );
    }
}
