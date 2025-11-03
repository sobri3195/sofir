<?php
namespace Sofir\Seo;

class Engine {
    private const OPTION_SETTINGS  = 'sofir_seo_settings';
    private const OPTION_REDIRECTS = 'sofir_seo_redirects';
    private const OPTION_EVENTS    = 'sofir_analytics_events';

    private static ?Engine $instance = null;

    /** @var array<string, mixed> */
    private array $settings = [];

    /** @var array<int, array{from:string,to:string}> */
    private array $redirects = [];

    public static function instance(): Engine {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->settings  = $this->load_settings();
        $this->redirects = $this->load_redirects();
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_post_meta' ], 15 );
        \add_action( 'add_meta_boxes', [ $this, 'register_meta_box' ] );
        \add_action( 'save_post', [ $this, 'save_post_meta' ], 10, 2 );
        \add_action( 'wp_head', [ $this, 'render_meta_tags' ], 1 );
        \add_action( 'template_redirect', [ $this, 'handle_redirects' ], 1 );
        \add_action( 'admin_post_sofir_save_seo_settings', [ $this, 'handle_save_settings' ] );
        \add_action( 'admin_post_sofir_add_redirect', [ $this, 'handle_add_redirect' ] );
        \add_action( 'admin_post_sofir_delete_redirect', [ $this, 'handle_delete_redirect' ] );
        \add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
        \add_action( 'wp', [ $this, 'track_page_view' ] );

        \do_action( 'sofir/seo/booted', $this );
    }

    public function get_settings(): array {
        return $this->settings;
    }

    /**
     * @return array<int, array{from:string,to:string}>
     */
    public function get_redirects(): array {
        return $this->redirects;
    }

    public function get_top_posts( int $limit = 5 ): array {
        $query = new \WP_Query(
            [
                'post_type'      => 'any',
                'posts_per_page' => $limit,
                'meta_key'       => 'sofir_views_total',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'fields'         => 'ids',
            ]
        );

        $results = [];

        foreach ( $query->posts as $post_id ) {
            $results[] = [
                'id'     => $post_id,
                'title'  => \get_the_title( $post_id ),
                'views'  => (int) \get_post_meta( $post_id, 'sofir_views_total', true ),
                'edit'   => \get_edit_post_link( $post_id, 'raw' ),
                'link'   => \get_permalink( $post_id ),
            ];
        }

        return $results;
    }

    public function get_event_summary(): array {
        $events = \get_option( self::OPTION_EVENTS, [] );

        if ( ! \is_array( $events ) ) {
            return [];
        }

        arsort( $events );

        return array_slice( $events, 0, 20, true );
    }

    public function register_post_meta(): void {
        $post_types = \get_post_types( [ 'show_in_rest' => true ], 'names' );

        $meta_specs = [
            'sofir_seo_title'       => [ 'type' => 'string' ],
            'sofir_seo_description' => [ 'type' => 'string' ],
            'sofir_seo_keywords'    => [ 'type' => 'string' ],
            'sofir_seo_image'       => [ 'type' => 'integer' ],
            'sofir_noindex'         => [ 'type' => 'boolean', 'default' => false ],
            'sofir_redirect_url'    => [ 'type' => 'string' ],
        ];

        foreach ( $post_types as $post_type ) {
            foreach ( $meta_specs as $key => $schema ) {
                \register_post_meta(
                    $post_type,
                    $key,
                    [
                        'single'            => true,
                        'type'              => $schema['type'],
                        'show_in_rest'      => true,
                        'default'           => $schema['default'] ?? null,
                        'auth_callback'     => [ $this, 'can_manage_post_meta' ],
                        'sanitize_callback' => [ $this, 'sanitize_meta_value' ],
                    ]
                );
            }
        }
    }

    public function can_manage_post_meta( bool $allowed, string $meta_key, int $post_id ): bool {
        return \current_user_can( 'edit_post', $post_id );
    }

    public function sanitize_meta_value( $value ) {
        if ( \is_array( $value ) ) {
            return array_map( 'sanitize_text_field', $value );
        }

        return \sanitize_text_field( (string) $value );
    }

    public function register_meta_box(): void {
        $post_types = \get_post_types( [ 'show_in_rest' => true ], 'names' );

        foreach ( $post_types as $post_type ) {
            \add_meta_box(
                'sofir-seo-panel',
                \__( 'SOFIR SEO', 'sofir' ),
                [ $this, 'render_meta_box' ],
                $post_type,
                'normal',
                'default'
            );
        }
    }

    public function render_meta_box( \WP_Post $post ): void {
        $title       = \get_post_meta( $post->ID, 'sofir_seo_title', true );
        $description = \get_post_meta( $post->ID, 'sofir_seo_description', true );
        $keywords    = \get_post_meta( $post->ID, 'sofir_seo_keywords', true );
        $image       = (int) \get_post_meta( $post->ID, 'sofir_seo_image', true );
        $noindex     = (bool) \get_post_meta( $post->ID, 'sofir_noindex', true );
        $redirect    = \get_post_meta( $post->ID, 'sofir_redirect_url', true );

        \wp_nonce_field( 'sofir_save_seo_meta', 'sofir_seo_meta_nonce' );

        echo '<div class="sofir-meta">';
        echo '<p><label>' . \esc_html__( 'SEO Title', 'sofir' ) . '<br/><input type="text" class="widefat" name="sofir_seo_title" value="' . \esc_attr( $title ) . '" /></label></p>';
        echo '<p><label>' . \esc_html__( 'Meta Description', 'sofir' ) . '<br/><textarea class="widefat" rows="3" name="sofir_seo_description">' . \esc_textarea( $description ) . '</textarea></label></p>';
        echo '<p><label>' . \esc_html__( 'Focus Keywords (comma separated)', 'sofir' ) . '<br/><input type="text" class="widefat" name="sofir_seo_keywords" value="' . \esc_attr( $keywords ) . '" /></label></p>';
        echo '<p><label><input type="checkbox" name="sofir_noindex" value="1" ' . \checked( $noindex, true, false ) . ' /> ' . \esc_html__( 'noindex this content', 'sofir' ) . '</label></p>';
        echo '<p><label>' . \esc_html__( 'Redirect to URL', 'sofir' ) . '<br/><input type="url" class="widefat" name="sofir_redirect_url" value="' . \esc_attr( $redirect ) . '" placeholder="https://example.com/new-page" /></label></p>';

        echo '<p><label>' . \esc_html__( 'Custom Social Image (Attachment ID)', 'sofir' ) . '<br/><input type="number" class="small-text" name="sofir_seo_image" value="' . \esc_attr( $image ) . '" /></label></p>';
        echo '</div>';
    }

    public function save_post_meta( int $post_id, \WP_Post $post ): void {
        if ( ! isset( $_POST['sofir_seo_meta_nonce'] ) || ! \wp_verify_nonce( $_POST['sofir_seo_meta_nonce'], 'sofir_save_seo_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! \current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        $fields = [ 'sofir_seo_title', 'sofir_seo_description', 'sofir_seo_keywords', 'sofir_redirect_url' ];

        foreach ( $fields as $field ) {
            $value = isset( $_POST[ $field ] ) ? \sanitize_text_field( \wp_unslash( $_POST[ $field ] ) ) : '';
            if ( '' === $value ) {
                \delete_post_meta( $post_id, $field );
            } else {
                \update_post_meta( $post_id, $field, $value );
            }
        }

        $noindex = isset( $_POST['sofir_noindex'] );
        \update_post_meta( $post_id, 'sofir_noindex', $noindex );

        $image = isset( $_POST['sofir_seo_image'] ) ? (int) $_POST['sofir_seo_image'] : 0;
        if ( $image > 0 ) {
            \update_post_meta( $post_id, 'sofir_seo_image', $image );
        } else {
            \delete_post_meta( $post_id, 'sofir_seo_image' );
        }
    }

    public function render_meta_tags(): void {
        if ( \is_admin() ) {
            return;
        }

        $title       = $this->generate_title();
        $description = $this->generate_description();
        $canonical   = $this->generate_canonical();
        $image       = $this->generate_image_url();
        $robots      = $this->generate_robots();

        if ( $title ) {
            echo '<meta name="title" content="' . \esc_attr( $title ) . '" />' . "\n";
            echo '<meta property="og:title" content="' . \esc_attr( $title ) . '" />' . "\n";
            echo '<meta name="twitter:title" content="' . \esc_attr( $title ) . '" />' . "\n";
        }

        if ( $description ) {
            echo '<meta name="description" content="' . \esc_attr( $description ) . '" />' . "\n";
            echo '<meta property="og:description" content="' . \esc_attr( $description ) . '" />' . "\n";
            echo '<meta name="twitter:description" content="' . \esc_attr( $description ) . '" />' . "\n";
        }

        if ( $canonical ) {
            echo '<link rel="canonical" href="' . \esc_url( $canonical ) . '" />' . "\n";
        }

        echo '<meta property="og:type" content="' . ( \is_singular() ? 'article' : 'website' ) . '" />' . "\n";
        echo '<meta property="og:url" content="' . \esc_url( $canonical ?: \home_url( add_query_arg( [] ) ) ) . '" />' . "\n";

        if ( $image ) {
            echo '<meta property="og:image" content="' . \esc_url( $image ) . '" />' . "\n";
            echo '<meta name="twitter:image" content="' . \esc_url( $image ) . '" />' . "\n";
        }

        $twitter = $this->settings['twitter_handle'] ?? '';
        if ( $twitter ) {
            echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
            echo '<meta name="twitter:site" content="' . \esc_attr( $twitter ) . '" />' . "\n";
        }

        if ( $robots ) {
            echo '<meta name="robots" content="' . \esc_attr( $robots ) . '" />' . "\n";
        }

        if ( ! empty( $this->settings['enable_schema'] ) ) {
            $schema = $this->build_schema_payload();

            if ( $schema ) {
                echo '<script type="application/ld+json">' . \wp_json_encode( $schema ) . '</script>' . "\n";
            }
        }
    }

    public function handle_redirects(): void {
        if ( \is_user_logged_in() && \current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( \is_singular() ) {
            $target = \get_post_meta( \get_queried_object_id(), 'sofir_redirect_url', true );
            if ( $target ) {
                \wp_safe_redirect( $this->normalize_redirect_url( $target ), 301 );
                exit;
            }
        }

        $request_path = parse_url( add_query_arg( [], \home_url( $_SERVER['REQUEST_URI'] ?? '/' ) ), PHP_URL_PATH );
        $request_path = rtrim( (string) $request_path, '/' );

        foreach ( $this->redirects as $redirect ) {
            $from = rtrim( $redirect['from'], '/' );

            if ( $from === $request_path || $from === rtrim( '/' . ltrim( $request_path, '/' ), '/' ) ) {
                \wp_safe_redirect( $this->normalize_redirect_url( $redirect['to'] ), 301 );
                exit;
            }
        }
    }

    public function handle_save_settings(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_seo_settings', '_sofir_nonce' );

        $pattern      = isset( $_POST['sofir_title_pattern'] ) ? \sanitize_text_field( \wp_unslash( $_POST['sofir_title_pattern'] ) ) : '%title% | %site%';
        $description  = isset( $_POST['sofir_default_description'] ) ? \sanitize_text_field( \wp_unslash( $_POST['sofir_default_description'] ) ) : '';
        $image        = isset( $_POST['sofir_default_image'] ) ? \esc_url_raw( \wp_unslash( $_POST['sofir_default_image'] ) ) : '';
        $twitter      = isset( $_POST['sofir_twitter_handle'] ) ? \sanitize_text_field( \wp_unslash( $_POST['sofir_twitter_handle'] ) ) : '';
        $enableSchema = isset( $_POST['sofir_enable_schema'] );
        $analytics    = isset( $_POST['sofir_enable_analytics'] );

        $this->settings = [
            'title_pattern'       => $pattern,
            'default_description' => $description,
            'default_image'       => $image,
            'twitter_handle'      => $twitter,
            'enable_schema'       => $enableSchema,
            'analytics_enabled'   => $analytics,
        ];

        \update_option( self::OPTION_SETTINGS, $this->settings );

        \wp_safe_redirect( \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'seo', 'sofir_notice' => 'seo_saved' ], \admin_url( 'admin.php' ) ) );
        exit;
    }

    public function handle_add_redirect(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_seo_redirect', '_sofir_nonce' );

        $from = isset( $_POST['sofir_redirect_from'] ) ? \sanitize_text_field( \wp_unslash( $_POST['sofir_redirect_from'] ) ) : '';
        $to   = isset( $_POST['sofir_redirect_to'] ) ? \sanitize_text_field( \wp_unslash( $_POST['sofir_redirect_to'] ) ) : '';

        if ( $from && $to ) {
            $this->redirects[] = [
                'from' => '/' . ltrim( $from, '/' ),
                'to'   => $to,
            ];

            \update_option( self::OPTION_REDIRECTS, $this->redirects );
        }

        \wp_safe_redirect( \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'seo', 'sofir_notice' => 'redirect_saved' ], \admin_url( 'admin.php' ) ) );
        exit;
    }

    public function handle_delete_redirect(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_delete_redirect', '_sofir_nonce' );

        $index = isset( $_GET['index'] ) ? (int) $_GET['index'] : -1;
        if ( isset( $this->redirects[ $index ] ) ) {
            unset( $this->redirects[ $index ] );
            $this->redirects = array_values( $this->redirects );
            \update_option( self::OPTION_REDIRECTS, $this->redirects );
        }

        \wp_safe_redirect( \add_query_arg( [ 'page' => 'sofir-dashboard', 'tab' => 'seo', 'sofir_notice' => 'redirect_deleted' ], \admin_url( 'admin.php' ) ) );
        exit;
    }

    public function register_rest_routes(): void {
        \register_rest_route(
            'sofir/v1',
            '/analytics/overview',
            [
                'methods'             => 'GET',
                'callback'            => [ $this, 'rest_analytics_overview' ],
                'permission_callback' => function () {
                    return \current_user_can( 'manage_options' );
                },
            ]
        );

        \register_rest_route(
            'sofir/v1',
            '/analytics/event',
            [
                'methods'             => 'POST',
                'callback'            => [ $this, 'rest_track_event' ],
                'permission_callback' => '__return_true',
            ]
        );
    }

    public function rest_analytics_overview(): \WP_REST_Response {
        return \rest_ensure_response(
            [
                'views'  => $this->get_top_posts( 10 ),
                'events' => $this->get_event_summary(),
            ]
        );
    }

    public function rest_track_event( \WP_REST_Request $request ): \WP_REST_Response {
        if ( empty( $this->settings['analytics_enabled'] ) ) {
            return \rest_ensure_response( [ 'status' => 'disabled' ] );
        }

        $selector = substr( \sanitize_text_field( (string) $request->get_param( 'selector' ) ), 0, 120 );
        $path     = substr( \sanitize_text_field( (string) $request->get_param( 'path' ) ), 0, 180 );

        if ( ! $selector ) {
            return \rest_ensure_response( [ 'status' => 'ignored' ] );
        }

        $key    = $selector . '|' . $path;
        $events = \get_option( self::OPTION_EVENTS, [] );

        if ( ! \is_array( $events ) ) {
            $events = [];
        }

        $events[ $key ] = ( $events[ $key ] ?? 0 ) + 1;

        if ( count( $events ) > 200 ) {
            $events = array_slice( $events, -200, 200, true );
        }

        \update_option( self::OPTION_EVENTS, $events, false );

        return \rest_ensure_response( [ 'status' => 'ok' ] );
    }

    public function enqueue_frontend_assets(): void {
        if ( empty( $this->settings['analytics_enabled'] ) ) {
            return;
        }

        $handle = 'sofir-analytics';

        if ( ! \wp_script_is( $handle, 'registered' ) ) {
            \wp_register_script(
                $handle,
                SOFIR_ASSETS_URL . 'js/analytics.js',
                [],
                SOFIR_VERSION,
                true
            );
        }

        $post_id = 0;
        if ( \is_singular() ) {
            $post_id = \get_queried_object_id();
        }

        \wp_localize_script(
            $handle,
            'SOFIR_ANALYTICS_DATA',
            [
                'enabled' => true,
                'root'    => \esc_url_raw( \rest_url() ),
                'postId'  => $post_id,
            ]
        );

        \wp_enqueue_script( $handle );
    }

    public function track_page_view(): void {
        if ( empty( $this->settings['analytics_enabled'] ) || ! \is_singular() ) {
            return;
        }

        $post_id = \get_queried_object_id();
        if ( ! $post_id ) {
            return;
        }

        $count = (int) \get_post_meta( $post_id, 'sofir_views_total', true );
        \update_post_meta( $post_id, 'sofir_views_total', $count + 1 );
    }

    private function generate_title(): string {
        if ( \is_singular() ) {
            $post_id = \get_queried_object_id();
            $custom  = \get_post_meta( $post_id, 'sofir_seo_title', true );
            $base    = $custom ?: \get_the_title( $post_id );

            return $this->apply_title_pattern( $base );
        }

        if ( \is_home() || \is_front_page() ) {
            return $this->apply_title_pattern( \get_bloginfo( 'name' ) );
        }

        if ( \is_archive() ) {
            return $this->apply_title_pattern( \get_the_archive_title() );
        }

        return $this->apply_title_pattern( \get_bloginfo( 'name' ) );
    }

    private function apply_title_pattern( string $title ): string {
        $pattern = $this->settings['title_pattern'] ?? '%title% | %site%';

        $replacements = [
            '%title%' => $title,
            '%site%'  => \get_bloginfo( 'name' ),
        ];

        return strtr( $pattern, $replacements );
    }

    private function generate_description(): string {
        if ( \is_singular() ) {
            $post_id = \get_queried_object_id();
            $custom  = \get_post_meta( $post_id, 'sofir_seo_description', true );
            if ( $custom ) {
                return $custom;
            }

            $excerpt = \wp_trim_words( strip_tags( \get_the_excerpt( $post_id ) ), 30 );
            if ( $excerpt ) {
                return $excerpt;
            }
        }

        return $this->settings['default_description'] ?? '';
    }

    private function generate_canonical(): string {
        if ( \is_singular() ) {
            return \get_permalink();
        }

        if ( \is_home() ) {
            return \home_url( '/' );
        }

        if ( \is_archive() ) {
            return \get_pagenum_link();
        }

        return '';
    }

    private function generate_image_url(): string {
        if ( \is_singular() ) {
            $post_id = \get_queried_object_id();
            $image   = (int) \get_post_meta( $post_id, 'sofir_seo_image', true );

            if ( $image ) {
                $src = \wp_get_attachment_image_url( $image, 'full' );
                if ( $src ) {
                    return $src;
                }
            }

            if ( \has_post_thumbnail( $post_id ) ) {
                $src = \get_the_post_thumbnail_url( $post_id, 'full' );
                if ( $src ) {
                    return $src;
                }
            }
        }

        return $this->settings['default_image'] ?? '';
    }

    private function generate_robots(): string {
        if ( \is_singular() && \get_post_meta( \get_queried_object_id(), 'sofir_noindex', true ) ) {
            return 'noindex, follow';
        }

        return 'index, follow';
    }

    private function build_schema_payload(): array {
        if ( \is_singular() ) {
            $post_id = \get_queried_object_id();
            $post    = \get_post( $post_id );

            if ( ! $post ) {
                return [];
            }

            $payload = [
                '@context'      => 'https://schema.org',
                '@type'         => 'Article',
                'headline'      => $this->generate_title(),
                'datePublished' => \get_the_date( DATE_ATOM, $post_id ),
                'dateModified'  => \get_the_modified_date( DATE_ATOM, $post_id ),
                'author'        => [
                    '@type' => 'Person',
                    'name'  => \get_the_author_meta( 'display_name', $post->post_author ),
                ],
                'publisher'     => [
                    '@type' => 'Organization',
                    'name'  => \get_bloginfo( 'name' ),
                ],
                'mainEntityOfPage' => \get_permalink( $post_id ),
                'description'      => $this->generate_description(),
            ];

            $location = \get_post_meta( $post_id, 'sofir_' . $post->post_type . '_location', true );
            if ( \is_array( $location ) && ! empty( $location['lat'] ) && ! empty( $location['lng'] ) ) {
                $payload['about'] = [
                    '@type'    => 'Place',
                    'name'     => \get_the_title( $post_id ),
                    'address'  => $location['address'] ?? '',
                    'geo'      => [
                        '@type'    => 'GeoCoordinates',
                        'latitude' => (float) $location['lat'],
                        'longitude'=> (float) $location['lng'],
                    ],
                ];
            }

            return $payload;
        }

        return [
            '@context' => 'https://schema.org',
            '@type'    => 'WebSite',
            'name'     => \get_bloginfo( 'name' ),
            'url'      => \home_url( '/' ),
        ];
    }

    private function normalize_redirect_url( string $url ): string {
        if ( 0 === strpos( $url, 'http' ) ) {
            return $url;
        }

        return \home_url( '/' . ltrim( $url, '/' ) );
    }

    private function load_settings(): array {
        $defaults = [
            'title_pattern'       => '%title% | %site%',
            'default_description' => '',
            'default_image'       => '',
            'twitter_handle'      => '',
            'enable_schema'       => true,
            'analytics_enabled'   => true,
        ];

        $stored = \get_option( self::OPTION_SETTINGS, [] );

        if ( ! \is_array( $stored ) ) {
            $stored = [];
        }

        return \wp_parse_args( $stored, $defaults );
    }

    private function load_redirects(): array {
        $redirects = \get_option( self::OPTION_REDIRECTS, [] );

        if ( ! \is_array( $redirects ) ) {
            return [];
        }

        return $redirects;
    }
}
