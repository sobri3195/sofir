<?php
namespace Sofir\Directory;

class Mobile {
    private const OPTION_SETTINGS = 'sofir_directory_mobile_settings';

    private static ?Mobile $instance = null;

    private array $settings = [];

    public static function instance(): Mobile {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->settings = $this->load_settings();
    }

    public function boot(): void {
        if ( ! $this->settings['enabled'] ) {
            return;
        }

        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_mobile_assets' ] );
        \add_action( 'wp_footer', [ $this, 'render_mobile_menu' ] );
        \add_action( 'wp_footer', [ $this, 'render_bottom_navbar' ] );
        \add_shortcode( 'sofir_mobile_menu', [ $this, 'render_mobile_menu_shortcode' ] );
        \add_shortcode( 'sofir_bottom_navbar', [ $this, 'render_bottom_navbar_shortcode' ] );
        \add_action( 'admin_post_sofir_save_mobile_settings', [ $this, 'handle_save_settings' ] );
    }

    public function get_settings(): array {
        return $this->settings;
    }

    public function handle_save_settings(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_mobile_settings', '_sofir_nonce' );

        $enabled = isset( $_POST['mobile_enabled'] );
        $menu_id = (int) ( $_POST['mobile_menu_id'] ?? 0 );
        $show_bottom_nav = isset( $_POST['show_bottom_nav'] );
        $breakpoint = (int) ( $_POST['mobile_breakpoint'] ?? 768 );

        $this->settings = [
            'enabled' => $enabled,
            'menu_id' => $menu_id,
            'show_bottom_nav' => $show_bottom_nav,
            'breakpoint' => $breakpoint,
        ];

        \update_option( self::OPTION_SETTINGS, $this->settings );

        \wp_safe_redirect(
            \add_query_arg(
                [
                    'page' => 'sofir-dashboard',
                    'tab' => 'content',
                    'sofir_notice' => 'mobile_settings_saved',
                ],
                \admin_url( 'admin.php' )
            )
        );
        exit;
    }

    public function enqueue_mobile_assets(): void {
        if ( ! \wp_script_is( 'sofir-mobile', 'registered' ) ) {
            \wp_register_script(
                'sofir-mobile',
                SOFIR_ASSETS_URL . 'js/mobile.js',
                [ 'jquery' ],
                SOFIR_VERSION,
                true
            );

            \wp_localize_script(
                'sofir-mobile',
                'SOFIR_MOBILE_DATA',
                [
                    'breakpoint' => $this->settings['breakpoint'],
                    'isMobile' => \wp_is_mobile(),
                ]
            );
        }

        if ( ! \wp_style_is( 'sofir-mobile', 'registered' ) ) {
            \wp_register_style(
                'sofir-mobile',
                SOFIR_ASSETS_URL . 'css/mobile.css',
                [],
                SOFIR_VERSION
            );
        }

        if ( \wp_is_mobile() || $this->is_responsive_view() ) {
            \wp_enqueue_script( 'sofir-mobile' );
            \wp_enqueue_style( 'sofir-mobile' );
        }
    }

    public function render_mobile_menu(): void {
        if ( ! $this->should_render_mobile_ui() ) {
            return;
        }

        echo $this->render_mobile_menu_shortcode();
    }

    public function render_mobile_menu_shortcode( array $atts = [] ): string {
        $atts = \shortcode_atts(
            [
                'menu_id' => $this->settings['menu_id'] ?? 0,
            ],
            $atts,
            'sofir_mobile_menu'
        );

        $menu_id = $atts['menu_id'];

        ob_start();
        echo '<div class="sofir-mobile-menu" id="sofir-mobile-menu">';
        echo '<div class="sofir-mobile-menu-overlay"></div>';
        echo '<div class="sofir-mobile-menu-panel">';
        echo '<div class="sofir-mobile-menu-header">';
        echo '<button class="sofir-mobile-menu-close" aria-label="' . \esc_attr__( 'Close menu', 'sofir' ) . '">';
        echo '<span>&times;</span>';
        echo '</button>';
        echo '</div>';

        echo '<div class="sofir-mobile-menu-content">';

        if ( $menu_id ) {
            \wp_nav_menu( [
                'menu' => $menu_id,
                'container' => 'nav',
                'container_class' => 'sofir-mobile-nav',
                'fallback_cb' => false,
            ] );
        } else {
            \wp_nav_menu( [
                'theme_location' => 'primary',
                'container' => 'nav',
                'container_class' => 'sofir-mobile-nav',
                'fallback_cb' => false,
            ] );
        }

        if ( \is_user_logged_in() ) {
            $user = \wp_get_current_user();
            echo '<div class="sofir-mobile-user-info">';
            echo \get_avatar( $user->ID, 48 );
            echo '<div class="sofir-mobile-user-details">';
            echo '<strong>' . \esc_html( $user->display_name ) . '</strong>';
            echo '<a href="' . \esc_url( \wp_logout_url() ) . '">' . \esc_html__( 'Logout', 'sofir' ) . '</a>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="sofir-mobile-auth-buttons">';
            echo '<a href="' . \esc_url( \wp_login_url() ) . '" class="button">' . \esc_html__( 'Login', 'sofir' ) . '</a>';
            if ( \get_option( 'users_can_register' ) ) {
                echo '<a href="' . \esc_url( \wp_registration_url() ) . '" class="button button-primary">' . \esc_html__( 'Register', 'sofir' ) . '</a>';
            }
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<button class="sofir-mobile-menu-toggle" aria-label="' . \esc_attr__( 'Open menu', 'sofir' ) . '">';
        echo '<span></span>';
        echo '<span></span>';
        echo '<span></span>';
        echo '</button>';

        return (string) ob_get_clean();
    }

    public function render_bottom_navbar(): void {
        if ( ! $this->should_render_mobile_ui() || ! $this->settings['show_bottom_nav'] ) {
            return;
        }

        echo $this->render_bottom_navbar_shortcode();
    }

    public function render_bottom_navbar_shortcode( array $atts = [] ): string {
        $atts = \shortcode_atts(
            [
                'items' => 'home,search,add,messages,profile',
            ],
            $atts,
            'sofir_bottom_navbar'
        );

        $items = \array_filter( \array_map( 'trim', \explode( ',', $atts['items'] ) ) );

        ob_start();
        echo '<nav class="sofir-bottom-navbar">';

        foreach ( $items as $item ) {
            switch ( $item ) {
                case 'home':
                    echo '<a href="' . \esc_url( \home_url() ) . '" class="sofir-bottom-nav-item">';
                    echo '<span class="sofir-nav-icon">🏠</span>';
                    echo '<span class="sofir-nav-label">' . \esc_html__( 'Home', 'sofir' ) . '</span>';
                    echo '</a>';
                    break;

                case 'search':
                    echo '<a href="' . \esc_url( \home_url( '?s=' ) ) . '" class="sofir-bottom-nav-item">';
                    echo '<span class="sofir-nav-icon">🔍</span>';
                    echo '<span class="sofir-nav-label">' . \esc_html__( 'Search', 'sofir' ) . '</span>';
                    echo '</a>';
                    break;

                case 'add':
                    if ( \is_user_logged_in() ) {
                        echo '<a href="' . \esc_url( \admin_url( 'post-new.php' ) ) . '" class="sofir-bottom-nav-item sofir-nav-primary">';
                        echo '<span class="sofir-nav-icon">➕</span>';
                        echo '<span class="sofir-nav-label">' . \esc_html__( 'Add', 'sofir' ) . '</span>';
                        echo '</a>';
                    }
                    break;

                case 'messages':
                    if ( \is_user_logged_in() ) {
                        echo '<a href="#" class="sofir-bottom-nav-item">';
                        echo '<span class="sofir-nav-icon">💬</span>';
                        echo '<span class="sofir-nav-label">' . \esc_html__( 'Messages', 'sofir' ) . '</span>';
                        echo '</a>';
                    }
                    break;

                case 'profile':
                    if ( \is_user_logged_in() ) {
                        echo '<a href="' . \esc_url( \admin_url( 'profile.php' ) ) . '" class="sofir-bottom-nav-item">';
                        echo '<span class="sofir-nav-icon">👤</span>';
                        echo '<span class="sofir-nav-label">' . \esc_html__( 'Profile', 'sofir' ) . '</span>';
                        echo '</a>';
                    } else {
                        echo '<a href="' . \esc_url( \wp_login_url() ) . '" class="sofir-bottom-nav-item">';
                        echo '<span class="sofir-nav-icon">👤</span>';
                        echo '<span class="sofir-nav-label">' . \esc_html__( 'Login', 'sofir' ) . '</span>';
                        echo '</a>';
                    }
                    break;

                default:
                    \do_action( 'sofir/mobile/bottom_nav_item', $item );
                    break;
            }
        }

        echo '</nav>';

        return (string) ob_get_clean();
    }

    private function should_render_mobile_ui(): bool {
        return $this->settings['enabled'] && ( \wp_is_mobile() || $this->is_responsive_view() );
    }

    private function is_responsive_view(): bool {
        return isset( $_SERVER['HTTP_USER_AGENT'] ) && 
               \preg_match( '/(android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini)/i', $_SERVER['HTTP_USER_AGENT'] );
    }

    private function load_settings(): array {
        $defaults = [
            'enabled' => true,
            'menu_id' => 0,
            'show_bottom_nav' => true,
            'breakpoint' => 768,
        ];

        $settings = \get_option( self::OPTION_SETTINGS, [] );

        if ( ! \is_array( $settings ) ) {
            $settings = [];
        }

        return \wp_parse_args( $settings, $defaults );
    }
}
