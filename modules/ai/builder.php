<?php
namespace Sofir\Ai;

class Builder {
    private static ?Builder $instance = null;

    public static function instance(): Builder {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    public function register_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/ai/suggest',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'rest_suggest' ],
                'permission_callback' => '__return_true',
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/ai/analyze/(?P<id>\d+)',
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'rest_analyze' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function rest_suggest( \WP_REST_Request $request ): \WP_REST_Response {
        $title   = (string) $request->get_param( 'title' );
        $content = (string) $request->get_param( 'content' );
        $type    = (string) $request->get_param( 'post_type' );

        return \rest_ensure_response( $this->generate_insights( $title, $content, $type ) );
    }

    public function rest_analyze( \WP_REST_Request $request ): \WP_REST_Response {
        $post = \get_post( (int) $request['id'] );

        if ( ! $post ) {
            return new \WP_REST_Response( [ 'message' => \__( 'Post not found.', 'sofir' ) ], 404 );
        }

        return \rest_ensure_response( $this->generate_insights( $post->post_title, $post->post_content, $post->post_type ) );
    }

    public function generate_insights( string $title, string $content, string $post_type = '' ): array {
        $keywords   = $this->extract_keywords( $content );
        $description = $this->suggest_description( $content, $title );
        $template   = $this->suggest_template( $title, $content, $post_type );
        $score      = $this->score_seo( $title, $description, $content );

        return [
            'title'         => $this->suggest_title( $title ),
            'description'   => $description,
            'keywords'      => $keywords,
            'template'      => $template,
            'seo_score'     => $score,
            'recommendations' => $this->generate_recommendations( $score, $content, $keywords ),
        ];
    }

    private function suggest_title( string $title ): string {
        if ( strlen( $title ) >= 40 && strlen( $title ) <= 60 ) {
            return $title;
        }

        $trimmed = trim( $title );
        if ( strlen( $trimmed ) < 40 ) {
            return $trimmed . ' | ' . \get_bloginfo( 'name' );
        }

        return substr( $trimmed, 0, 57 ) . '…';
    }

    private function suggest_description( string $content, string $title ): string {
        $stripped = trim( strip_tags( $content ) );

        if ( '' === $stripped ) {
            return sprintf( \__( 'Explore %s on %s.', 'sofir' ), $title, \get_bloginfo( 'name' ) );
        }

        $excerpt = mb_substr( $stripped, 0, 155 );

        if ( strlen( $stripped ) > 155 ) {
            $last_space = strrpos( $excerpt, ' ' );
            if ( false !== $last_space ) {
                $excerpt = substr( $excerpt, 0, $last_space ) . '…';
            }
        }

        return $excerpt;
    }

    /**
     * @return array<int, string>
     */
    private function extract_keywords( string $content ): array {
        $stripped = strtolower( strip_tags( $content ) );
        $stripped = preg_replace( '/[^a-z0-9\s]/', ' ', $stripped );
        $words    = array_filter( explode( ' ', $stripped ), static function ( $word ) {
            return strlen( $word ) >= 4;
        } );

        $counts = [];

        foreach ( $words as $word ) {
            $counts[ $word ] = ( $counts[ $word ] ?? 0 ) + 1;
        }

        arsort( $counts );

        return array_slice( array_keys( $counts ), 0, 10 );
    }

    private function suggest_template( string $title, string $content, string $post_type ): string {
        $text = strtolower( $title . ' ' . $content . ' ' . $post_type );

        if ( false !== strpos( $text, 'directory' ) ) {
            return 'directory/city-directory';
        }

        if ( false !== strpos( $text, 'profile' ) || 'profile' === $post_type ) {
            return 'profile/business-profile';
        }

        if ( false !== strpos( $text, 'health' ) ) {
            return 'directory/healthcare-network';
        }

        if ( false !== strpos( $text, 'agency' ) ) {
            return 'landing/agency-spotlight';
        }

        if ( false !== strpos( $text, 'startup' ) ) {
            return 'landing/startup-launch';
        }

        if ( false !== strpos( $text, 'news' ) || false !== strpos( $text, 'blog' ) ) {
            return 'blog/modern-magazine';
        }

        return 'landing/startup-launch';
    }

    private function score_seo( string $title, string $description, string $content ): int {
        $score = 50;

        $title_length = strlen( $title );
        if ( $title_length >= 40 && $title_length <= 60 ) {
            $score += 15;
        }

        $desc_length = strlen( $description );
        if ( $desc_length >= 90 && $desc_length <= 160 ) {
            $score += 15;
        }

        if ( strlen( strip_tags( $content ) ) > 600 ) {
            $score += 10;
        }

        $headings = substr_count( strtolower( $content ), '<h2' );
        if ( $headings >= 2 ) {
            $score += 5;
        }

        $images = substr_count( strtolower( $content ), '<img' );
        if ( $images >= 1 ) {
            $score += 5;
        }

        return min( 100, $score );
    }

    private function generate_recommendations( int $score, string $content, array $keywords ): array {
        $recommendations = [];

        if ( $score < 80 ) {
            $recommendations[] = \__( 'Expand your content to at least 600 words and add descriptive headings.', 'sofir' );
        }

        if ( substr_count( strtolower( $content ), '<img' ) === 0 ) {
            $recommendations[] = \__( 'Tambahkan minimal satu gambar dengan alt text yang relevan.', 'sofir' );
        }

        if ( empty( $keywords ) ) {
            $recommendations[] = \__( 'Gunakan kata kunci turunan secara alami di paragraf pembuka.', 'sofir' );
        }

        $links = substr_count( strtolower( $content ), '<a ' );
        if ( $links < 2 ) {
            $recommendations[] = \__( 'Tambahkan internal link ke konten terkait untuk meningkatkan interlinking.', 'sofir' );
        }

        return $recommendations;
    }
}
