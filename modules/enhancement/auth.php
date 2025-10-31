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
}
