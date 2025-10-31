<?php
namespace Sofir\Enhancement;

class Web {
    private static ?Web $instance = null;

    public static function instance(): Web {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_rewrite_rules' ] );
        \add_action( 'template_redirect', [ $this, 'maybe_render_sitemap' ] );
        \add_action( 'template_redirect', [ $this, 'maybe_render_amp' ], 5 );
        \add_filter( 'big_image_size_threshold', [ $this, 'limit_image_size' ], 10, 4 );
        \add_filter( 'upload_mimes', [ $this, 'allow_webp_uploads' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'wp_head', [ $this, 'output_amp_link' ] );
        \add_action( 'sofir/activate/before_flush', [ $this, 'register_rewrite_rules' ] );
    }

    public function register_rewrite_rules(): void {
        \add_rewrite_rule( '^sofir-sitemap\.xml$', 'index.php?sofir_sitemap=xml', 'top' );
        \add_rewrite_rule( '^sofir-sitemap\.json$', 'index.php?sofir_sitemap=json', 'top' );
        \add_rewrite_tag( '%sofir_sitemap%', '([^&]+)' );
    }

    public function maybe_render_sitemap(): void {
        $format = \get_query_var( 'sofir_sitemap' );

        if ( ! $format ) {
            return;
        }

        $entries = $this->build_sitemap_entries();

        if ( 'json' === $format ) {
            \wp_send_json( [ 'items' => $entries ] );
        }

        header( 'Content-Type: application/xml; charset=utf-8' );
        echo $this->render_xml( $entries );
        exit;
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/sitemap',
            [
                'methods'             => 'GET',
                'callback'            => function () {
                    return \rest_ensure_response( [ 'items' => $this->build_sitemap_entries() ] );
                },
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function limit_image_size( $threshold, $imagesize, $file, $attachment_id ) {
        return 1920;
    }

    public function allow_webp_uploads( array $mimes ): array {
        $mimes['webp'] = 'image/webp';

        return $mimes;
    }

    public function output_amp_link(): void {
        if ( \is_singular() ) {
            echo '<link rel="amphtml" href="' . \esc_url( \add_query_arg( 'amp', '1', \get_permalink() ) ) . '" />' . "\n";
        }
    }

    public function maybe_render_amp(): void {
        if ( ! \is_singular() || empty( $_GET['amp'] ) ) {
            return;
        }

        $post = \get_queried_object();

        if ( ! $post ) {
            return;
        }

        $content = \apply_filters( 'the_content', $post->post_content );
        $content = \wp_kses_post( $content );

        header( 'Content-Type: text/html; charset=utf-8' );
        echo '<!doctype html><html amp><head><meta charset="utf-8"><title>' . \esc_html( \get_the_title( $post ) ) . '</title>';
        echo '<meta name="viewport" content="width=device-width,minimum-scale=1"><style amp-custom>body{font-family:Helvetica,Arial,sans-serif;padding:20px;}img{max-width:100%;height:auto;}figure{margin:0 0 1.5rem;}h1,h2,h3{color:#111827;}</style>';
        echo '<script async src="https://cdn.ampproject.org/v0.js"></script></head><body>';
        echo '<main><article>' . $content . '</article></main>';
        echo '</body></html>';
        exit;
    }

    private function build_sitemap_entries(): array {
        $entries = [];

        $post_types = \get_post_types( [ 'public' => true ], 'names' );

        foreach ( $post_types as $post_type ) {
            $posts = \get_posts(
                [
                    'post_type'      => $post_type,
                    'post_status'    => 'publish',
                    'posts_per_page' => 200,
                    'orderby'        => 'modified',
                    'order'          => 'DESC',
                ]
            );

            foreach ( $posts as $post ) {
                $entries[] = [
                    'loc'     => \get_permalink( $post ),
                    'lastmod' => \get_post_modified_time( DATE_W3C, true, $post ),
                    'type'    => $post_type,
                ];
            }
        }

        return $entries;
    }

    private function render_xml( array $entries ): string {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ( $entries as $entry ) {
            $xml .= '<url>';
            $xml .= '<loc>' . \esc_url( $entry['loc'] ) . '</loc>';
            $xml .= '<lastmod>' . \esc_html( $entry['lastmod'] ) . '</lastmod>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';

        return $xml;
    }
}
