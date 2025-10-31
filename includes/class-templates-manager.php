<?php
namespace Sofir\Templates;

class Manager {
    private static ?Manager $instance = null;

    /** @var array<string, array<int, array<string, mixed>>> */
    private array $catalog = [];

    /** @var array<string, array<string, mixed>> */
    private array $index = [];

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->catalog = $this->load_catalog();
        $this->index   = $this->build_index( $this->catalog );
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_block_patterns' ], 12 );
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function get_catalog(): array {
        return $this->catalog;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function get_template( string $slug ): ?array {
        $slug = \sanitize_key( $slug );

        return $this->index[ $slug ] ?? null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get_templates_flat(): array {
        return array_values( $this->index );
    }

    public function register_block_patterns(): void {
        if ( ! function_exists( 'register_block_pattern_category' ) ) {
            return;
        }

        $categories = [
            'landing'   => \__( 'SOFIR Landing', 'sofir' ),
            'directory' => \__( 'SOFIR Directory', 'sofir' ),
            'blog'      => \__( 'SOFIR Blog', 'sofir' ),
            'profile'   => \__( 'SOFIR Profile', 'sofir' ),
        ];

        foreach ( $categories as $slug => $label ) {
            \register_block_pattern_category( 'sofir-' . $slug, [ 'label' => $label ] );
        }

        if ( ! function_exists( 'register_block_pattern' ) ) {
            return;
        }

        foreach ( $this->get_templates_flat() as $template ) {
            $content = $this->get_template_content( $template );

            if ( '' === $content ) {
                continue;
            }

            $pattern_slug = 'sofir/' . $template['slug'];

            \register_block_pattern(
                $pattern_slug,
                [
                    'title'       => $template['title'],
                    'description' => $template['description'] ?? '',
                    'categories'  => [ 'sofir-' . ( $template['category'] ?? 'landing' ) ],
                    'content'     => $content,
                    'keywords'    => [ 'sofir', $template['category'] ?? 'layout' ],
                ]
            );
        }
    }

    public function get_template_content( array $template ): string {
        $path = $template['path'] ?? '';

        if ( ! $path || ! file_exists( $path ) ) {
            return '';
        }

        $content = file_get_contents( $path );

        return is_string( $content ) ? $content : '';
    }

    /**
     * Create or update a WordPress page from template.
     */
    public function import_to_page( array $template, array $args = [] ): int {
        $content = $this->get_template_content( $template );

        if ( '' === $content ) {
            return 0;
        }

        $title = $template['title'] ?? ucfirst( $template['slug'] );

        $postarr = [
            'post_type'    => 'page',
            'post_title'   => $title,
            'post_status'  => 'draft',
            'post_content' => $content,
            'meta_input'   => [
                '_sofir_template_slug' => $template['slug'],
            ],
        ];

        $postarr = \wp_parse_args( $args, $postarr );

        $post_id = \wp_insert_post( $postarr, true );

        if ( \is_wp_error( $post_id ) ) {
            return 0;
        }

        return (int) $post_id;
    }

    /**
     * Create a block template (FSE) entry.
     */
    public function import_to_fse_template( array $template, string $theme = '' ): int {
        $content = $this->get_template_content( $template );

        if ( '' === $content ) {
            return 0;
        }

        $theme = $theme ?: \wp_get_theme()->get_stylesheet();

        $postarr = [
            'post_type'    => 'wp_template',
            'post_status'  => 'publish',
            'post_title'   => $template['title'] ?? $template['slug'],
            'post_name'    => $template['slug'],
            'post_content' => $content,
            'tax_input'    => [
                'wp_theme' => [ $theme ],
            ],
            'meta_input'   => [
                '_sofir_template_slug' => $template['slug'],
            ],
        ];

        $existing = $this->find_existing_template( $template['slug'], $theme );

        if ( $existing ) {
            $postarr['ID'] = $existing;
        }

        $result = \wp_insert_post( $postarr, true );

        if ( \is_wp_error( $result ) ) {
            return 0;
        }

        return (int) $result;
    }

    private function find_existing_template( string $slug, string $theme ): int {
        $query = new \WP_Query(
            [
                'post_type'      => 'wp_template',
                'name'           => $slug,
                'posts_per_page' => 1,
                'tax_query'      => [
                    [
                        'taxonomy' => 'wp_theme',
                        'field'    => 'slug',
                        'terms'    => $theme,
                    ],
                ],
                'fields'         => 'ids',
            ]
        );

        if ( empty( $query->posts ) ) {
            return 0;
        }

        return (int) $query->posts[0];
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function load_catalog(): array {
        $manifest = SOFIR_PLUGIN_DIR . 'templates/templates.php';

        if ( ! file_exists( $manifest ) ) {
            return [];
        }

        $data = include $manifest;

        return \is_array( $data ) ? $data : [];
    }

    /**
     * @param array<string, array<int, array<string, mixed>>> $catalog
     *
     * @return array<string, array<string, mixed>>
     */
    private function build_index( array $catalog ): array {
        $index = [];

        foreach ( $catalog as $category => $templates ) {
            foreach ( $templates as $template ) {
                if ( empty( $template['slug'] ) ) {
                    continue;
                }

                $template['category'] = $template['category'] ?? $category;
                $index[ $template['slug'] ] = $template;
            }
        }

        return $index;
    }
}
