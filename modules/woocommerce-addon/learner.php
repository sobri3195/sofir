<?php
namespace Sofir\WooCommerceAddon;

class Learner {
    private static ?Learner $instance = null;
    private array $external_sources = [];

    public static function instance(): Learner {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        $this->register_external_sources();
    }

    private function register_external_sources(): void {
        $this->external_sources = [
            'wpbeaches' => [
                'name' => 'WP Beaches',
                'url' => 'https://wpbeaches.com',
                'tag' => 'woocommerce',
                'description' => 'WordPress tutorials dan snippets',
            ],
            'siteground' => [
                'name' => 'SiteGround Tutorials',
                'url' => 'https://www.siteground.com/tutorials',
                'tag' => 'woocommerce',
                'description' => 'SiteGround WooCommerce guides',
            ],
            'wpkube' => [
                'name' => 'WPKube',
                'url' => 'https://www.wpkube.com',
                'tag' => 'woocommerce',
                'description' => 'WordPress dan WooCommerce tips',
            ],
        ];
    }

    public function get_external_sources(): array {
        return $this->external_sources;
    }

    public function fetch_snippets_from_source( string $source, string $category = '' ): array {
        $snippets = [];

        switch ( $source ) {
            case 'wpbeaches':
                $snippets = $this->fetch_from_wpbeaches( $category );
                break;
        }

        return $snippets;
    }

    private function fetch_from_wpbeaches( string $category = '' ): array {
        $cache_key = 'sofir_wc_wpbeaches_snippets_' . ( $category ?: 'all' );
        $cached = \get_transient( $cache_key );

        if ( is_array( $cached ) ) {
            return $cached;
        }

        $snippets = [];

        $url = 'https://wpbeaches.com/tag/woocommerce/';
        if ( $category ) {
            $url .= $category . '/';
        }

        $response = wp_remote_get( $url, [
            'timeout' => 10,
            'user-agent' => 'SOFIR WooCommerce Addon',
        ] );

        if ( is_wp_error( $response ) ) {
            \error_log( sprintf( '[SOFIR WC Addon] Failed to fetch from WP Beaches: %s', $response->get_error_message() ) );
            return $snippets;
        }

        $body = wp_remote_retrieve_body( $response );
        if ( ! $body ) {
            return $snippets;
        }

        $snippets = $this->parse_wpbeaches_content( $body );

        \set_transient( $cache_key, $snippets, 7 * DAY_IN_SECONDS );

        return $snippets;
    }

    private function parse_wpbeaches_content( string $html ): array {
        $snippets = [];

        if ( ! function_exists( 'str_get_html' ) ) {
            return $this->parse_with_regex( $html );
        }

        return $snippets;
    }

    private function parse_with_regex( string $html ): array {
        $snippets = [];

        preg_match_all( '/<article[^>]*>(.*?)<\/article>/is', $html, $articles );

        if ( empty( $articles[1] ) ) {
            return $snippets;
        }

        foreach ( $articles[1] as $article ) {
            preg_match( '/<h[1-6][^>]*>.*?<a[^>]*href="([^"]*)"[^>]*>([^<]*)<\/a>/is', $article, $title_match );

            if ( empty( $title_match[1] ) ) {
                continue;
            }

            $title = strip_tags( $title_match[2] );
            $url = $title_match[1];

            preg_match( '/<div class="[^"]*entry-content[^"]*"[^>]*>(.*?)<\/div>/is', $article, $content_match );
            $excerpt = $content_match[1] ?? '';
            $excerpt = wp_strip_all_tags( $excerpt );
            $excerpt = substr( $excerpt, 0, 200 ) . '...';

            preg_match( '/class="[^"]*tag[^"]*"[^>]*>([^<]*)<\/a>/is', $article, $tag_match );
            $category = isset( $tag_match[1] ) ? sanitize_text_field( $tag_match[1] ) : 'general';

            $snippet_id = 'external-' . sanitize_key( $title ) . '-' . time();

            $snippets[ $snippet_id ] = [
                'id' => $snippet_id,
                'name' => $title,
                'url' => $url,
                'excerpt' => $excerpt,
                'category' => $category,
                'source' => 'wpbeaches',
                'source_name' => 'WP Beaches',
                'date' => current_time( 'mysql' ),
                'is_external' => true,
            ];
        }

        return $snippets;
    }

    public function cache_snippet( string $id, array $snippet_data ): bool {
        $key = 'sofir_wc_snippet_' . $id;
        return set_transient( $key, $snippet_data, 30 * DAY_IN_SECONDS );
    }

    public function get_cached_snippet( string $id ): ?array {
        $key = 'sofir_wc_snippet_' . $id;
        $cached = get_transient( $key );
        return is_array( $cached ) ? $cached : null;
    }

    public function save_snippet_locally( string $id, array $snippet_data ): bool {
        $custom = \get_option( 'sofir_wc_saved_snippets', [] );
        $custom[ $id ] = array_merge( $snippet_data, [
            'saved_date' => current_time( 'mysql' ),
            'is_external' => isset( $snippet_data['is_external'] ) ? $snippet_data['is_external'] : false,
        ] );

        return \update_option( 'sofir_wc_saved_snippets', $custom );
    }

    public function get_saved_snippets(): array {
        return \get_option( 'sofir_wc_saved_snippets', [] );
    }

    public function export_snippet( string $id, string $format = 'json' ): string {
        $custom = $this->get_saved_snippets();
        if ( ! isset( $custom[ $id ] ) ) {
            return '';
        }

        $snippet = $custom[ $id ];

        switch ( $format ) {
            case 'json':
                return wp_json_encode( $snippet );
            case 'php':
                return $this->export_as_php_file( $snippet );
            case 'txt':
                return $this->export_as_text( $snippet );
            default:
                return wp_json_encode( $snippet );
        }
    }

    private function export_as_php_file( array $snippet ): string {
        $code = $snippet['code'] ?? '';
        $name = $snippet['name'] ?? 'snippet';
        $comment = "/**\n * " . $name . "\n * Generated by SOFIR WooCommerce Addon\n * " . current_time( 'mysql' ) . "\n */\n\n";

        return "<?php\n" . $comment . $code . "\n";
    }

    private function export_as_text( array $snippet ): string {
        $text = "=====================================\n";
        $text .= "SNIPPET: " . ( $snippet['name'] ?? 'Untitled' ) . "\n";
        $text .= "=====================================\n\n";
        $text .= "Category: " . ( $snippet['category'] ?? 'general' ) . "\n";
        $text .= "Date: " . current_time( 'mysql' ) . "\n";
        $text .= "Source: " . ( $snippet['source_name'] ?? 'Custom' ) . "\n";
        $text .= "\n--- CODE ---\n\n";
        $text .= $snippet['code'] ?? '';
        $text .= "\n\n=====================================\n";

        return $text;
    }

    public function rate_snippet( string $id, int $rating ): bool {
        if ( $rating < 1 || $rating > 5 ) {
            return false;
        }

        $ratings = \get_option( 'sofir_wc_snippet_ratings', [] );
        $user_id = get_current_user_id();
        $rating_key = $id . '_' . $user_id;

        $ratings[ $rating_key ] = [
            'snippet_id' => $id,
            'user_id' => $user_id,
            'rating' => $rating,
            'date' => current_time( 'mysql' ),
        ];

        return \update_option( 'sofir_wc_snippet_ratings', $ratings );
    }

    public function get_snippet_average_rating( string $id ): float {
        $ratings = \get_option( 'sofir_wc_snippet_ratings', [] );
        $snippet_ratings = array_filter( $ratings, function ( $r ) use ( $id ) {
            return $r['snippet_id'] === $id;
        } );

        if ( empty( $snippet_ratings ) ) {
            return 0;
        }

        $sum = array_sum( array_column( $snippet_ratings, 'rating' ) );
        return round( $sum / count( $snippet_ratings ), 1 );
    }

    public function get_snippet_comments( string $id ): array {
        $comments = \get_option( 'sofir_wc_snippet_comments', [] );
        return array_filter( $comments, function ( $c ) use ( $id ) {
            return $c['snippet_id'] === $id;
        } );
    }

    public function add_snippet_comment( string $id, string $comment ): bool {
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        $comments = \get_option( 'sofir_wc_snippet_comments', [] );
        $comment_id = $id . '_' . time();

        $comments[ $comment_id ] = [
            'snippet_id' => $id,
            'user_id' => get_current_user_id(),
            'user_name' => wp_get_current_user()->display_name,
            'comment' => sanitize_textarea_field( $comment ),
            'date' => current_time( 'mysql' ),
        ];

        return \update_option( 'sofir_wc_snippet_comments', $comments );
    }

    public function get_snippet_stats( string $id ): array {
        $saved = $this->get_saved_snippets();
        $snippet = $saved[ $id ] ?? null;

        if ( ! $snippet ) {
            return [];
        }

        return [
            'id' => $id,
            'name' => $snippet['name'] ?? '',
            'category' => $snippet['category'] ?? '',
            'source' => $snippet['source_name'] ?? 'Custom',
            'rating' => $this->get_snippet_average_rating( $id ),
            'comments_count' => count( $this->get_snippet_comments( $id ) ),
            'saved_date' => $snippet['saved_date'] ?? '',
        ];
    }
}
