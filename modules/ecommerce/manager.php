<?php
namespace Sofir\Ecommerce;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        $this->load_integrations();
        
        \add_action( 'init', [ $this, 'register_integrations' ] );
        \add_filter( 'sofir/blocks/post_types', [ $this, 'add_ecommerce_post_types' ] );
    }

    private function load_integrations(): void {
        if ( class_exists( 'WooCommerce' ) ) {
            require_once SOFIR_PLUGIN_DIR . '/modules/ecommerce/woocommerce.php';
        }

        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            require_once SOFIR_PLUGIN_DIR . '/modules/ecommerce/edd.php';
        }

        if ( function_exists( 'north_commerce_init' ) ) {
            require_once SOFIR_PLUGIN_DIR . '/modules/ecommerce/north-commerce.php';
        }
    }

    public function register_integrations(): void {
        if ( class_exists( 'WooCommerce' ) ) {
            WooCommerce::instance()->init();
        }

        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            EDD::instance()->init();
        }

        if ( function_exists( 'north_commerce_init' ) ) {
            NorthCommerce::instance()->init();
        }
    }

    public function add_ecommerce_post_types( array $post_types ): array {
        if ( class_exists( 'WooCommerce' ) ) {
            $post_types[] = 'product';
        }

        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            $post_types[] = 'download';
        }

        if ( function_exists( 'north_commerce_init' ) ) {
            $post_types[] = 'nc_product';
        }

        return $post_types;
    }
}
