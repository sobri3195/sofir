<?php
namespace Sofir\Blocks;

class Compatibility {
    private static ?Compatibility $instance = null;

    public static function instance(): Compatibility {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_compatibility_hooks' ] );
        \add_filter( 'render_block', [ $this, 'add_block_wrapper_classes' ], 10, 2 );
        \add_action( 'wp_head', [ $this, 'add_compatibility_styles' ], 999 );
        \add_action( 'admin_head', [ $this, 'add_editor_compatibility_styles' ], 999 );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_compatibility_fixes' ], 999 );
    }

    public function register_compatibility_hooks(): void {
        if ( $this->is_templately_active() ) {
            \add_filter( 'templately/import/before', [ $this, 'prepare_for_templately_import' ] );
            \add_filter( 'templately/import/after', [ $this, 'cleanup_after_templately_import' ] );
        }

        if ( $this->is_full_site_editing_theme() ) {
            \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_fse_compatibility' ], 20 );
        }
    }

    private function is_templately_active(): bool {
        return \class_exists( 'Templately' ) || \defined( 'TEMPLATELY_VERSION' );
    }

    private function is_full_site_editing_theme(): bool {
        return \function_exists( 'wp_is_block_theme' ) && \wp_is_block_theme();
    }

    public function prepare_for_templately_import( $data ) {
        \update_option( 'sofir_templately_import_in_progress', true );
        return $data;
    }

    public function cleanup_after_templately_import( $data ) {
        \delete_option( 'sofir_templately_import_in_progress' );
        \do_action( 'sofir/templately/import_completed', $data );
        return $data;
    }

    public function add_block_wrapper_classes( string $block_content, array $block ): string {
        if ( empty( $block['blockName'] ) || 0 !== strpos( $block['blockName'], 'sofir/' ) ) {
            return $block_content;
        }

        $block_name = str_replace( 'sofir/', '', $block['blockName'] );
        $wrapper_class = 'sofir-block sofir-block--' . $block_name;

        if ( false !== strpos( $block_content, '<div' ) ) {
            $block_content = preg_replace(
                '/(<div[^>]*class=["\'])/',
                '$1' . esc_attr( $wrapper_class ) . ' ',
                $block_content,
                1
            );
        } elseif ( ! empty( $block_content ) ) {
            $block_content = '<div class="' . esc_attr( $wrapper_class ) . '">' . $block_content . '</div>';
        }

        return $block_content;
    }

    public function add_compatibility_styles(): void {
        ?>
        <style id="sofir-compatibility-styles">
            /* Reset conflicts from themes */
            .sofir-block * {
                box-sizing: border-box;
            }
            
            /* Templately compatibility */
            .templately-content .sofir-block {
                margin-bottom: 1.5em;
            }
            
            /* Full Site Editing compatibility */
            .wp-site-blocks .sofir-block {
                max-width: none;
            }
            
            /* Common theme conflicts fixes */
            .sofir-block img {
                max-width: 100%;
                height: auto;
            }
            
            .sofir-block a {
                text-decoration: none;
            }
            
            /* Prevent layout shifts */
            .sofir-block {
                contain: layout style;
            }
            
            /* Responsive containers */
            @media (max-width: 768px) {
                .sofir-block {
                    padding-left: 15px;
                    padding-right: 15px;
                }
            }
        </style>
        <?php
    }

    public function add_editor_compatibility_styles(): void {
        $screen = \get_current_screen();
        if ( ! $screen || 'post' !== $screen->base ) {
            return;
        }
        ?>
        <style id="sofir-editor-compatibility-styles">
            /* Editor compatibility */
            .block-editor .sofir-block {
                margin: 1em 0;
            }
            
            .block-editor .sofir-block * {
                box-sizing: border-box;
            }
            
            /* Better visual hierarchy in editor */
            .block-editor .sofir-block {
                border: 1px solid #e0e0e0;
                padding: 1em;
                border-radius: 4px;
            }
            
            .block-editor .sofir-block:hover {
                border-color: #007cba;
            }
        </style>
        <?php
    }

    public function enqueue_fse_compatibility(): void {
        \wp_add_inline_style(
            'sofir-blocks',
            '
            .wp-site-blocks .sofir-block {
                width: 100%;
            }
            .wp-site-blocks .alignfull {
                max-width: none;
            }
            '
        );
    }

    public function enqueue_compatibility_fixes(): void {
        \wp_enqueue_style(
            'sofir-compatibility-fixes',
            SOFIR_ASSETS_URL . 'css/compatibility-fixes.css',
            [ 'sofir-blocks' ],
            SOFIR_VERSION
        );
    }
}
