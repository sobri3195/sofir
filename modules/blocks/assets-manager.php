<?php
namespace Sofir\Blocks;

class AssetsManager {
    private static ?AssetsManager $instance = null;

    public static function instance(): AssetsManager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets' ] );
        \add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ], 5 );
        \add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
    }

    public function register_block_category( array $categories, $context ): array {
        return array_merge(
            [
                [
                    'slug'  => 'sofir',
                    'title' => __( 'SOFIR Blocks', 'sofir' ),
                    'icon'  => 'star-filled',
                ],
            ],
            $categories
        );
    }

    public function enqueue_block_assets(): void {
        \wp_enqueue_style(
            'sofir-blocks',
            SOFIR_ASSETS_URL . 'css/blocks.css',
            [],
            SOFIR_VERSION
        );

        \wp_enqueue_script(
            'sofir-blocks-frontend',
            SOFIR_ASSETS_URL . 'js/blocks-frontend.js',
            [ 'jquery' ],
            SOFIR_VERSION,
            true
        );

        \wp_localize_script(
            'sofir-blocks-frontend',
            'sofirBlocks',
            [
                'ajaxUrl' => \admin_url( 'admin-ajax.php' ),
                'nonce'   => \wp_create_nonce( 'sofir_blocks' ),
                'restUrl' => \rest_url( 'sofir/v1/' ),
                'restNonce' => \wp_create_nonce( 'wp_rest' ),
            ]
        );
    }

    public function enqueue_editor_assets(): void {
        \wp_enqueue_style(
            'sofir-blocks-editor',
            SOFIR_ASSETS_URL . 'css/blocks-editor.css',
            [ 'wp-edit-blocks' ],
            SOFIR_VERSION
        );

        \wp_enqueue_script(
            'sofir-blocks-register',
            SOFIR_ASSETS_URL . 'js/blocks-register.js',
            [ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ],
            SOFIR_VERSION,
            true
        );

        \wp_enqueue_script(
            'sofir-blocks-editor',
            SOFIR_ASSETS_URL . 'js/blocks-editor.js',
            [ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'sofir-blocks-register' ],
            SOFIR_VERSION,
            true
        );
    }

    public function enqueue_frontend_assets(): void {
        if ( ! \is_admin() ) {
            \wp_enqueue_style(
                'sofir-blocks-frontend',
                SOFIR_ASSETS_URL . 'css/blocks-frontend.css',
                [],
                SOFIR_VERSION
            );
        }
    }
}
