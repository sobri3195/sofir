<?php
namespace Sofir\Blocks;

use Sofir\Directory\Manager as DirectoryManager;
use Sofir\Enhancement\Auth as AuthEnhancer;
use Sofir\Membership\Manager as MembershipManager;

class Registrar {
    private static ?Registrar $instance = null;

    public static function instance(): Registrar {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_blocks' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'register_block_assets' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'setup_api_fetch' ], 20 );
        \add_filter( 'block_categories_all', [ $this, 'register_category' ], 10, 2 );
    }

    public function register_block_assets(): void {
        \wp_register_script( 'sofir-countdown', SOFIR_ASSETS_URL . 'js/countdown.js', [], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-create-post', SOFIR_ASSETS_URL . 'js/create-post.js', [ 'wp-api-fetch' ], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-messages', SOFIR_ASSETS_URL . 'js/messages.js', [ 'wp-api-fetch' ], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-navbar', SOFIR_ASSETS_URL . 'js/navbar.js', [], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-popup', SOFIR_ASSETS_URL . 'js/popup.js', [], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-quick-search', SOFIR_ASSETS_URL . 'js/quick-search.js', [ 'wp-api-fetch' ], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-slider', SOFIR_ASSETS_URL . 'js/slider.js', [], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-charts', SOFIR_ASSETS_URL . 'js/charts.js', [], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-auth', SOFIR_ASSETS_URL . 'js/auth.js', [ 'wp-api-fetch' ], SOFIR_VERSION, true );
        \wp_register_script( 'sofir-appointment', SOFIR_ASSETS_URL . 'js/appointment.js', [], SOFIR_VERSION, true );
        
        \wp_localize_script( 'sofir-appointment', 'sofirData', [
            'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
            'nonce'   => \wp_create_nonce( 'sofir_appointment' ),
        ] );
        
        \wp_localize_script( 'sofir-create-post', 'wpApiSettings', [
            'root'  => \esc_url_raw( \rest_url() ),
            'nonce' => \wp_create_nonce( 'wp_rest' ),
        ] );
    }

    public function setup_api_fetch(): void {
        if ( ! \is_admin() && \wp_script_is( 'wp-api-fetch', 'enqueued' ) ) {
            \wp_add_inline_script(
                'wp-api-fetch',
                sprintf(
                    'wp.apiFetch.use( wp.apiFetch.createNonceMiddleware( "%s" ) );',
                    \wp_create_nonce( 'wp_rest' )
                ),
                'after'
            );
        }
    }

    public function register_blocks(): void {
        if ( ! \function_exists( 'register_block_type' ) ) {
            return;
        }

        \register_block_type(
            'sofir/directory-map',
            [
                'attributes'      => [
                    'postType' => [ 'type' => 'string', 'default' => 'listing' ],
                    'zoom'     => [ 'type' => 'number', 'default' => 12 ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $atts = [
                        'post_type' => $attributes['postType'] ?? 'listing',
                        'zoom'      => isset( $attributes['zoom'] ) ? (int) $attributes['zoom'] : 12,
                    ];

                    return DirectoryManager::instance()->render_map_shortcode( $atts );
                },
                'supports'        => [ 'align' => [ 'wide', 'full' ] ],
            ]
        );

        \register_block_type(
            'sofir/membership-pricing',
            [
                'render_callback' => function (): string {
                    return MembershipManager::instance()->render_pricing_shortcode();
                },
            ]
        );

        \register_block_type(
            'sofir/login-form',
            [
                'attributes'      => [
                    'label' => [ 'type' => 'string', 'default' => \__( 'Login', 'sofir' ) ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $atts = [ 'label' => $attributes['label'] ?? \__( 'Login', 'sofir' ) ];

                    return AuthEnhancer::instance()->render_login_form( $atts );
                },
            ]
        );

        \register_block_type(
            'sofir/register-form',
            [
                'attributes'      => [
                    'phoneOnly' => [ 'type' => 'boolean', 'default' => false ],
                    'redirect'  => [ 'type' => 'string', 'default' => '' ],
                ],
                'render_callback' => function ( array $attributes ): string {
                    $atts = [
                        'phone_only' => $attributes['phoneOnly'] ?? false,
                        'redirect'   => $attributes['redirect'] ?? \home_url(),
                    ];

                    return AuthEnhancer::instance()->render_register_form( $atts );
                },
            ]
        );
    }

    public function register_category( array $categories, $context ): array {
        $categories[] = [
            'slug'  => 'sofir',
            'title' => \__( 'SOFIR', 'sofir' ),
        ];

        return $categories;
    }
}
