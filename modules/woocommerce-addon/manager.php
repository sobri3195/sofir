<?php
namespace Sofir\WooCommerceAddon;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        require_once SOFIR_PLUGIN_DIR . '/modules/woocommerce-addon/integration.php';
        require_once SOFIR_PLUGIN_DIR . '/modules/woocommerce-addon/admin.php';
        require_once SOFIR_PLUGIN_DIR . '/modules/woocommerce-addon/snippets.php';
        require_once SOFIR_PLUGIN_DIR . '/modules/woocommerce-addon/addons-manager.php';

        \add_action( 'init', [ $this, 'initialize' ] );
        \add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        \add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    public function initialize(): void {
        Integration::instance()->init();
        Addons_Manager::instance();
    }

    public function add_admin_menu(): void {
        $icon = 'dashicons-shopping-cart';
        
        \add_menu_page(
            \__( 'WooCommerce Addon', 'sofir' ),
            \__( 'WC Addon', 'sofir' ),
            'manage_options',
            'sofir-woocommerce-addon',
            [ $this, 'render_dashboard' ],
            $icon,
            25.5
        );

        \add_submenu_page(
            'sofir-woocommerce-addon',
            \__( 'Dashboard', 'sofir' ),
            \__( 'Dashboard', 'sofir' ),
            'manage_options',
            'sofir-woocommerce-addon',
            [ $this, 'render_dashboard' ]
        );

        \add_submenu_page(
            'sofir-woocommerce-addon',
            \__( 'Addons', 'sofir' ),
            \__( 'Addons', 'sofir' ),
            'manage_options',
            'sofir-woocommerce-addon-addons',
            [ $this, 'render_addons_page' ]
        );

        \add_submenu_page(
            'sofir-woocommerce-addon',
            \__( 'Code Snippets', 'sofir' ),
            \__( 'Code Snippets', 'sofir' ),
            'manage_options',
            'sofir-woocommerce-addon-snippets',
            [ $this, 'render_snippets_page' ]
        );

        \add_submenu_page(
            'sofir-woocommerce-addon',
            \__( 'Extensions', 'sofir' ),
            \__( 'Extensions', 'sofir' ),
            'manage_options',
            'sofir-woocommerce-addon-extensions',
            [ $this, 'render_extensions_page' ]
        );

        \add_submenu_page(
            'sofir-woocommerce-addon',
            \__( 'Settings', 'sofir' ),
            \__( 'Settings', 'sofir' ),
            'manage_options',
            'sofir-woocommerce-addon-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    public function render_dashboard(): void {
        Admin::instance()->render_dashboard();
    }

    public function render_addons_page(): void {
        Admin::instance()->render_addons_page();
    }

    public function render_snippets_page(): void {
        Admin::instance()->render_snippets_page();
    }

    public function render_extensions_page(): void {
        Admin::instance()->render_extensions_page();
    }

    public function render_settings_page(): void {
        Admin::instance()->render_settings_page();
    }

    public function enqueue_admin_assets( string $hook ): void {
        if ( strpos( $hook, 'sofir-woocommerce-addon' ) === false ) {
            return;
        }

        \wp_enqueue_style(
            'sofir-woocommerce-addon',
            SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/admin.css',
            [],
            '1.0.0'
        );

        \wp_enqueue_script(
            'sofir-woocommerce-addon',
            SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/admin.js',
            [ 'jquery' ],
            '1.0.0',
            true
        );

        \wp_localize_script( 'sofir-woocommerce-addon', 'sofirWCAddon', [
            'nonce' => \wp_create_nonce( 'sofir_wc_addon_nonce' ),
            'ajaxurl' => \admin_url( 'admin-ajax.php' ),
            'i18n' => [
                'loading' => \__( 'Loading...', 'sofir' ),
                'copied' => \__( 'Copied to clipboard!', 'sofir' ),
                'error' => \__( 'Error', 'sofir' ),
                'success' => \__( 'Success', 'sofir' ),
            ],
        ] );
    }
}
