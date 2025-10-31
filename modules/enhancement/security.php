<?php
namespace Sofir\Enhancement;

class Security {
    private static ?Security $instance = null;

    private int $max_attempts;
    private int $lock_minutes;

    public static function instance(): Security {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->max_attempts = (int) \apply_filters( 'sofir/security/max_login_attempts', 5 );
        $this->lock_minutes = (int) \apply_filters( 'sofir/security/lock_minutes', 15 );
    }

    public function boot(): void {
        \add_action( 'wp_login_failed', [ $this, 'track_failed_login' ], 10, 1 );
        \add_filter( 'authenticate', [ $this, 'maybe_block_login' ], 100, 3 );
        \add_action( 'wp_login', [ $this, 'reset_login_attempts' ], 10, 2 );
        \add_action( 'comment_form_default_fields', [ $this, 'inject_honeypot_field' ] );
        \add_action( 'preprocess_comment', [ $this, 'validate_honeypot' ] );
        \add_filter( 'wp_handle_upload_prefilter', [ $this, 'guard_uploads' ] );
        \add_action( 'login_enqueue_scripts', [ $this, 'render_login_notice' ] );
        \add_action( 'wp_login', [ $this, 'capture_last_login' ], 10, 2 );
    }

    public function track_failed_login( string $username ): void {
        $ip    = $this->get_request_ip();
        $count = $this->get_attempt_count( $ip ) + 1;

        \set_transient( $this->get_transient_key( $ip ), $count, $this->lock_minutes * MINUTE_IN_SECONDS );

        \do_action( 'sofir/security/login_failed', $username, $ip, $count );
    }

    public function maybe_block_login( $user, string $username, string $password ) {
        $ip    = $this->get_request_ip();
        $count = $this->get_attempt_count( $ip );

        if ( $count < $this->max_attempts ) {
            return $user;
        }

        $message = \apply_filters(
            'sofir/security/lock_message',
            \sprintf( \__( 'Too many login attempts. Please try again in %d minutes.', 'sofir' ), $this->lock_minutes )
        );

        return new \WP_Error( 'sofir_locked', $message );
    }

    public function reset_login_attempts( string $user_login, \WP_User $user ): void {
        $ip = $this->get_request_ip();
        \delete_transient( $this->get_transient_key( $ip ) );
    }

    public function inject_honeypot_field( array $fields ): array {
        $fields['sofir_hp'] = '<p class="comment-form-sofir-hp" style="position:absolute;left:-999em;">
            <label for="sofir_hp">' . \esc_html__( 'Leave this field empty', 'sofir' ) . '</label>
            <input type="text" name="sofir_hp" id="sofir_hp" value="" autocomplete="off" />
        </p>';

        return $fields;
    }

    public function validate_honeypot( array $commentdata ): array {
        if ( ! empty( $_POST['sofir_hp'] ) ) {
            \wp_die( \esc_html__( 'Spam detected.', 'sofir' ) );
        }

        return $commentdata;
    }

    public function guard_uploads( array $file ): array {
        $extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );
        $blocked   = (array) \apply_filters( 'sofir/security/blocked_extensions', [ 'php', 'exe', 'sh', 'bat', 'js', 'ps1' ] );

        if ( in_array( $extension, $blocked, true ) ) {
            $file['error'] = \__( 'File type is not allowed for security reasons.', 'sofir' );
        }

        return $file;
    }

    public function render_login_notice(): void {
        $ip    = $this->get_request_ip();
        $count = $this->get_attempt_count( $ip );

        if ( $count < $this->max_attempts ) {
            return;
        }

        printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            \esc_html( \sprintf( \__( 'Login temporarily locked. Try again in %d minutes.', 'sofir' ), $this->lock_minutes ) )
        );
    }

    public function capture_last_login( string $user_login, \WP_User $user ): void {
        \update_user_meta( $user->ID, 'sofir_last_login', \current_time( 'mysql' ) );
    }

    private function get_attempt_count( string $ip ): int {
        return (int) \get_transient( $this->get_transient_key( $ip ) );
    }

    private function get_transient_key( string $ip ): string {
        return 'sofir_lock_' . md5( $ip );
    }

    private function get_request_ip(): string {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) && ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $parts = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
            $ip    = trim( reset( $parts ) );
        }

        return $ip ?: '0.0.0.0';
    }
}
