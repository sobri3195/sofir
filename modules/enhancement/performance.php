<?php
namespace Sofir\Enhancement;

class Performance {
    private static ?Performance $instance = null;

    public static function instance(): Performance {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'disable_emojis' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'clean_frontend_scripts' ], 100 );
        \add_filter( 'script_loader_src', [ $this, 'remove_asset_version' ], 15, 1 );
        \add_filter( 'style_loader_src', [ $this, 'remove_asset_version' ], 15, 1 );
        \add_filter( 'the_content', [ $this, 'lazyload_iframes' ], 20 );
        \add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_image_attributes' ], 20, 3 );
        \add_action( 'wp_head', [ $this, 'output_resource_hints' ], 2 );
    }

    public function disable_emojis(): void {
        \remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        \remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        \remove_action( 'wp_print_styles', 'print_emoji_styles' );
        \remove_action( 'admin_print_styles', 'print_emoji_styles' );
        \remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        \remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        \remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    }

    public function clean_frontend_scripts(): void {
        if ( ! \is_admin() ) {
            \wp_deregister_script( 'wp-embed' );

            if ( ! \is_user_logged_in() ) {
                \wp_dequeue_style( 'dashicons' );
            }
        }
    }

    public function remove_asset_version( $src ) {
        $src = (string) $src;

        if ( strpos( $src, 'ver=' ) !== false ) {
            $src = \remove_query_arg( 'ver', $src );
        }

        return $src;
    }

    public function lazyload_iframes( string $content ): string {
        if ( false === strpos( $content, '<iframe' ) ) {
            return $content;
        }

        return (string) preg_replace( '/<iframe(.*?)>/', '<iframe loading="lazy"$1>', $content );
    }

    public function optimize_image_attributes( array $attr, \WP_Post $attachment, $size ): array {
        $attr['decoding'] = 'async';
        $attr['loading']  = $attr['loading'] ?? 'lazy';

        return $attr;
    }

    public function output_resource_hints(): void {
        $hints = (array) \apply_filters( 'sofir/performance/resource_hints', [] );

        foreach ( $hints as $relation => $urls ) {
            foreach ( (array) $urls as $url ) {
                printf( '<link rel="%1$s" href="%2$s" />' . "\n", \esc_attr( $relation ), \esc_url( $url ) );
            }
        }
    }
}
