<?php
namespace Sofir\Elementor;

class Manager {
    private static ?Manager $instance = null;

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        \add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
        \add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );
        \add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'enqueue_frontend_styles' ] );
        \add_action( 'elementor/frontend/after_register_scripts', [ $this, 'enqueue_frontend_scripts' ] );
    }

    public function register_category( $elements_manager ): void {
        $elements_manager->add_category(
            'sofir',
            [
                'title' => \esc_html__( 'SOFIR Elements', 'sofir' ),
                'icon' => 'fa fa-plug',
            ]
        );

        $elements_manager->add_category(
            'sofir-booking',
            [
                'title' => \esc_html__( 'SOFIR Booking & Events', 'sofir' ),
                'icon' => 'fa fa-calendar',
            ]
        );

        $elements_manager->add_category(
            'sofir-ecommerce',
            [
                'title' => \esc_html__( 'SOFIR E-Commerce', 'sofir' ),
                'icon' => 'fa fa-shopping-cart',
            ]
        );

        $elements_manager->add_category(
            'sofir-learning',
            [
                'title' => \esc_html__( 'SOFIR E-Learning', 'sofir' ),
                'icon' => 'fa fa-graduation-cap',
            ]
        );
    }

    public function register_widgets( $widgets_manager ): void {
        $widget_files = [
            'post-feed',
            'term-feed',
            'search-form',
            'map',
            'contact-info',
            'review-stats',
            'visit-chart',
            'ring-chart',
            'countdown',
            'create-post',
            'dynamic-data',
            'appointment-form',
            'event-list',
            'event-calendar',
            'event-registration',
            'booking-form',
            'restaurant-menu',
            'restaurant-order-form',
            'restaurant-delivery-form',
            'vendor-products',
            'vendor-store-list',
            'course-list',
            'course-progress',
            'my-courses',
            'voxel-listings',
            'voxel-search-form',
            'gallery',
            'slideshow',
            'filmstrip-gallery',
            'album',
        ];

        foreach ( $widget_files as $widget_file ) {
            $file_path = SOFIR_PLUGIN_DIR . '/modules/elementor/widgets/' . $widget_file . '.php';
            if ( file_exists( $file_path ) ) {
                require_once $file_path;
                
                $class_name = $this->get_widget_class_name( $widget_file );
                if ( class_exists( $class_name ) ) {
                    $widgets_manager->register( new $class_name() );
                }
            }
        }

        if ( class_exists( 'WooCommerce' ) ) {
            $this->register_woocommerce_widgets( $widgets_manager );
        }

        if ( class_exists( 'Easy_Digital_Downloads' ) ) {
            $this->register_edd_widgets( $widgets_manager );
        }

        if ( function_exists( 'north_commerce_init' ) ) {
            $this->register_north_widgets( $widgets_manager );
        }
    }

    private function register_woocommerce_widgets( $widgets_manager ): void {
        $wc_widgets = [
            'woocommerce-products',
            'woocommerce-cart',
            'woocommerce-checkout',
            'woocommerce-categories',
            'woocommerce-account',
        ];

        foreach ( $wc_widgets as $widget_file ) {
            $file_path = SOFIR_PLUGIN_DIR . '/modules/elementor/widgets/' . $widget_file . '.php';
            if ( file_exists( $file_path ) ) {
                require_once $file_path;
                
                $class_name = $this->get_widget_class_name( $widget_file );
                if ( class_exists( $class_name ) ) {
                    $widgets_manager->register( new $class_name() );
                }
            }
        }
    }

    private function register_edd_widgets( $widgets_manager ): void {
        $edd_widgets = [
            'edd-products',
            'edd-cart',
            'edd-checkout',
            'edd-download-button',
            'edd-categories',
        ];

        foreach ( $edd_widgets as $widget_file ) {
            $file_path = SOFIR_PLUGIN_DIR . '/modules/elementor/widgets/' . $widget_file . '.php';
            if ( file_exists( $file_path ) ) {
                require_once $file_path;
                
                $class_name = $this->get_widget_class_name( $widget_file );
                if ( class_exists( $class_name ) ) {
                    $widgets_manager->register( new $class_name() );
                }
            }
        }
    }

    private function register_north_widgets( $widgets_manager ): void {
        $north_widgets = [
            'north-products',
            'north-cart',
            'north-checkout',
            'north-categories',
        ];

        foreach ( $north_widgets as $widget_file ) {
            $file_path = SOFIR_PLUGIN_DIR . '/modules/elementor/widgets/' . $widget_file . '.php';
            if ( file_exists( $file_path ) ) {
                require_once $file_path;
                
                $class_name = $this->get_widget_class_name( $widget_file );
                if ( class_exists( $class_name ) ) {
                    $widgets_manager->register( new $class_name() );
                }
            }
        }
    }

    private function get_widget_class_name( string $widget_file ): string {
        $parts = explode( '-', $widget_file );
        $parts = array_map( 'ucfirst', $parts );
        return '\\Sofir\\Elementor\\Widgets\\' . implode( '_', $parts );
    }

    public function enqueue_editor_styles(): void {
        \wp_enqueue_style(
            'sofir-elementor-editor',
            SOFIR_PLUGIN_URL . 'assets/css/elementor-editor.css',
            [],
            SOFIR_VERSION
        );
    }

    public function enqueue_frontend_styles(): void {
        \wp_enqueue_style(
            'sofir-gallery',
            SOFIR_PLUGIN_URL . 'assets/css/gallery.css',
            [],
            SOFIR_VERSION
        );
    }

    public function enqueue_frontend_scripts(): void {
        \wp_register_script(
            'sofir-gallery',
            SOFIR_PLUGIN_URL . 'assets/js/gallery.js',
            [],
            SOFIR_VERSION,
            true
        );
        \wp_enqueue_script( 'sofir-gallery' );
    }
}
