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
        \add_filter( 'block_categories_all', [ $this, 'register_category' ], 10, 2 );
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
    }

    public function register_category( array $categories, $context ): array {
        $categories[] = [
            'slug'  => 'sofir',
            'title' => \__( 'SOFIR', 'sofir' ),
        ];

        return $categories;
    }
}
