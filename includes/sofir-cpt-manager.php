<?php
namespace Sofir\Cpt;

class Manager {
    private const OPTION_POST_TYPES   = 'sofir_cpt_definitions';
    private const OPTION_TAXONOMIES   = 'sofir_taxonomy_definitions';
    private const FILTER_PREFIX       = 'sofir_';
    private const TAX_FILTER_PREFIX   = 'sofir_tax_';

    private static ?Manager $instance = null;

    /** @var array<string, array> */
    private array $post_types = [];

    /** @var array<string, array> */
    private array $taxonomies = [];

    /** @var array<string, array<string, array>> */
    private array $filter_map = [];

    /** @var array<string, array{query_var: string, field: string, meta_key: string}> */
    private array $schedule_filters = [];

    /** @var array<string, array{query_var: string, object_type: array, args: array}> */
    private array $taxonomy_filter_map = [];

    /** @var array<string, string> */
    private array $seed_labels_cache = [];

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->post_types = $this->load_or_seed_option( self::OPTION_POST_TYPES, $this->get_seed_post_types() );
        $this->taxonomies = $this->load_or_seed_option( self::OPTION_TAXONOMIES, $this->get_seed_taxonomies() );
    }

    public function boot(): void {
        \add_action( 'init', [ $this, 'register_dynamic_post_types' ], 1 );
        \add_action( 'init', [ $this, 'register_dynamic_taxonomies' ], 2 );
        \add_filter( 'query_vars', [ $this, 'register_query_vars' ] );
        \add_action( 'pre_get_posts', [ $this, 'apply_meta_filters' ] );
        \add_filter( 'the_posts', [ $this, 'filter_open_now' ], 10, 2 );
    }

    /**
     * @return array<string, array>
     */
    public function get_post_types(): array {
        return $this->post_types;
    }

    /**
     * @return array<string, array>
     */
    public function get_taxonomies(): array {
        return $this->taxonomies;
    }

    /**
     * @return array<string, array>
     */
    public function get_field_catalog(): array {
        return \apply_filters(
            'sofir/cpt/field_catalog',
            [
                'location' => [
                    'label'       => \__( 'Location', 'sofir' ),
                    'description' => \__( 'Capture address, city, country, and geo coordinates.', 'sofir' ),
                    'meta'        => [
                        'type'              => 'object',
                        'single'            => true,
                        'show_in_rest'      => [
                            'schema' => [
                                'type'       => 'object',
                                'properties' => [
                                    'address' => [ 'type' => 'string' ],
                                    'city'    => [ 'type' => 'string' ],
                                    'state'   => [ 'type' => 'string' ],
                                    'country' => [ 'type' => 'string' ],
                                    'postal'  => [ 'type' => 'string' ],
                                    'lat'     => [ 'type' => 'number' ],
                                    'lng'     => [ 'type' => 'number' ],
                                ],
                            ],
                        ],
                        'sanitize_callback' => [ __CLASS__, 'sanitize_location' ],
                        'auth_callback'     => [ __CLASS__, 'authorize_meta' ],
                    ],
                    'filter'      => [
                        'mode'      => 'meta_like',
                        'query_var' => 'location',
                        'compare'   => 'LIKE',
                    ],
                ],
                'hours'    => [
                    'label'       => \__( 'Operating Hours', 'sofir' ),
                    'description' => \__( 'Weekly schedule indicating open and close time ranges.', 'sofir' ),
                    'meta'        => [
                        'type'              => 'object',
                        'single'            => true,
                        'show_in_rest'      => [
                            'schema' => [
                                'type'       => 'object',
                                'properties' => $this->get_weekday_schema(),
                            ],
                        ],
                        'sanitize_callback' => [ __CLASS__, 'sanitize_hours' ],
                        'auth_callback'     => [ __CLASS__, 'authorize_meta' ],
                    ],
                    'filter'      => [
                        'mode'      => 'schedule',
                        'query_var' => 'open_now',
                    ],
                ],
                'rating'  => [
                    'label'       => \__( 'Rating', 'sofir' ),
                    'description' => \__( 'Average rating value between 0 and 5.', 'sofir' ),
                    'meta'        => [
                        'type'              => 'number',
                        'single'            => true,
                        'show_in_rest'      => true,
                        'default'           => 0,
                        'sanitize_callback' => [ __CLASS__, 'sanitize_rating' ],
                        'auth_callback'     => [ __CLASS__, 'authorize_meta' ],
                    ],
                    'filter'      => [
                        'mode'      => 'meta_numeric',
                        'query_var' => 'rating_min',
                        'compare'   => '>=',
                        'type'      => 'NUMERIC',
                    ],
                ],
                'status'  => [
                    'label'       => \__( 'Status', 'sofir' ),
                    'description' => \__( 'Operational status such as open, closed, featured, or pending.', 'sofir' ),
                    'meta'        => [
                        'type'              => 'string',
                        'single'            => true,
                        'show_in_rest'      => true,
                        'default'           => 'active',
                        'sanitize_callback' => [ __CLASS__, 'sanitize_status' ],
                        'auth_callback'     => [ __CLASS__, 'authorize_meta' ],
                    ],
                    'filter'      => [
                        'mode'      => 'meta_exact',
                        'query_var' => 'status',
                        'compare'   => '=',
                    ],
                ],
                'price'   => [
                    'label'       => \__( 'Price Range', 'sofir' ),
                    'description' => \__( 'Tag listings with a human readable price range (e.g. $$, Premium).', 'sofir' ),
                    'meta'        => [
                        'type'              => 'string',
                        'single'            => true,
                        'show_in_rest'      => true,
                        'sanitize_callback' => [ __CLASS__, 'sanitize_text_meta' ],
                        'auth_callback'     => [ __CLASS__, 'authorize_meta' ],
                    ],
                    'filter'      => [
                        'mode'      => 'meta_exact',
                        'query_var' => 'price',
                        'compare'   => '=',
                    ],
                ],
                'contact' => [
                    'label'       => \__( 'Contact Info', 'sofir' ),
                    'description' => \__( 'Email, phone, and website references for the entry.', 'sofir' ),
                    'meta'        => [
                        'type'              => 'object',
                        'single'            => true,
                        'show_in_rest'      => [
                            'schema' => [
                                'type'       => 'object',
                                'properties' => [
                                    'email'   => [ 'type' => 'string', 'format' => 'email' ],
                                    'phone'   => [ 'type' => 'string' ],
                                    'website' => [ 'type' => 'string', 'format' => 'uri' ],
                                ],
                            ],
                        ],
                        'sanitize_callback' => [ __CLASS__, 'sanitize_contact' ],
                        'auth_callback'     => [ __CLASS__, 'authorize_meta' ],
                    ],
                ],
                'gallery' => [
                    'label'       => \__( 'Media Gallery', 'sofir' ),
                    'description' => \__( 'Collection of attachment IDs to showcase media.', 'sofir' ),
                    'meta'        => [
                        'type'              => 'array',
                        'single'            => true,
                        'show_in_rest'      => [
                            'schema' => [
                                'type'  => 'array',
                                'items' => [ 'type' => 'integer' ],
                            ],
                        ],
                        'sanitize_callback' => [ __CLASS__, 'sanitize_gallery' ],
                        'auth_callback'     => [ __CLASS__, 'authorize_meta' ],
                    ],
                ],
                'attributes' => [
                    'label'       => \__( 'Attributes', 'sofir' ),
                    'description' => \__( 'Key-value attributes for dynamic filters and badges.', 'sofir' ),
                    'meta'        => [
                        'type'              => 'object',
                        'single'            => true,
                        'show_in_rest'      => true,
                        'sanitize_callback' => [ __CLASS__, 'sanitize_attributes' ],
                        'auth_callback'     => [ __CLASS__, 'authorize_meta' ],
                    ],
                    'filter'      => [
                        'mode'      => 'meta_like',
                        'query_var' => 'attribute',
                        'compare'   => 'LIKE',
                    ],
                ],
            ]
        );
    }

    public function register_dynamic_post_types(): void {
        $definitions = \apply_filters( 'sofir/cpt/definitions', $this->post_types );

        foreach ( $definitions as $post_type => $definition ) {
            $args        = isset( $definition['args'] ) ? (array) $definition['args'] : [];
            $fields      = isset( $definition['fields'] ) ? (array) $definition['fields'] : [];
            $taxonomies  = isset( $definition['taxonomies'] ) ? (array) $definition['taxonomies'] : [];
            $supports    = isset( $args['supports'] ) ? (array) $args['supports'] : [];
            $singular    = $args['labels']['singular_name'] ?? $this->guess_label( $post_type );
            $plural      = $args['labels']['name'] ?? $singular . 's';

            $defaults = [
                'public'            => true,
                'show_in_rest'      => true,
                'supports'          => ! empty( $supports ) ? $supports : [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ],
                'has_archive'       => true,
                'menu_position'     => 20,
                'rewrite'           => [ 'slug' => $post_type ],
                'labels'            => $this->build_labels( $singular, $plural ),
            ];

            $normalized_args = \wp_parse_args( $args, $defaults );

            if ( ! empty( $taxonomies ) ) {
                $normalized_args['taxonomies'] = array_unique( array_map( 'sanitize_key', $taxonomies ) );
            }

            \register_post_type( $post_type, $normalized_args );

            $this->register_meta_fields( $post_type, $fields );
            $this->register_rest_filters( $post_type, $fields );
        }
    }

    public function register_dynamic_taxonomies(): void {
        $definitions = \apply_filters( 'sofir/taxonomy/definitions', $this->taxonomies );

        foreach ( $definitions as $taxonomy => $definition ) {
            $object_types = isset( $definition['object_type'] ) ? (array) $definition['object_type'] : [];
            $args         = isset( $definition['args'] ) ? (array) $definition['args'] : [];

            $singular = $args['labels']['singular_name'] ?? $this->guess_label( $taxonomy );
            $plural   = $args['labels']['name'] ?? $singular . 's';

            $defaults = [
                'public'            => true,
                'hierarchical'      => false,
                'show_in_rest'      => true,
                'show_admin_column' => true,
                'labels'            => $this->build_taxonomy_labels( $singular, $plural ),
            ];

            $normalized_args = \wp_parse_args( $args, $defaults );

            \register_taxonomy( $taxonomy, array_map( 'sanitize_key', $object_types ), $normalized_args );

            if ( ! empty( $definition['filterable'] ) ) {
                $this->taxonomy_filter_map[ $taxonomy ] = [
                    'query_var'   => $this->build_taxonomy_query_var( $taxonomy ),
                    'object_type' => $object_types,
                    'args'        => $normalized_args,
                ];
            }
        }
    }

    /**
     * @param array<string, string> $vars
     *
     * @return array<string, string>
     */
    public function register_query_vars( array $vars ): array {
        foreach ( $this->filter_map as $post_type => $filters ) {
            foreach ( $filters as $query_var => $config ) {
                $vars[] = $query_var;
            }
        }

        foreach ( $this->schedule_filters as $post_type => $config ) {
            $vars[] = $config['query_var'];
        }

        foreach ( $this->taxonomy_filter_map as $taxonomy => $config ) {
            $vars[] = $config['query_var'];
        }

        return array_values( array_unique( $vars ) );
    }

    public function apply_meta_filters( \WP_Query $query ): void {
        if ( \is_admin() && ! $query->is_main_query() && ! $query->get( 'sofir_rest' ) ) {
            return;
        }

        $post_type = $query->get( 'post_type' );
        if ( \is_array( $post_type ) ) {
            $post_type = reset( $post_type ) ?: '';
        }

        if ( ! \is_string( $post_type ) || '' === $post_type ) {
            return;
        }

        $meta_filters = $this->filter_map[ $post_type ] ?? [];

        if ( ! empty( $meta_filters ) ) {
            $meta_query = $query->get( 'meta_query' );
            if ( ! \is_array( $meta_query ) ) {
                $meta_query = [];
            }

            foreach ( $meta_filters as $query_var => $config ) {
                $value = $this->get_query_var_value( $query, $query_var );

                if ( null === $value || '' === $value ) {
                    continue;
                }

                $clause = $this->build_meta_query_clause( $config, $value );

                if ( ! empty( $clause ) ) {
                    $meta_query[] = $clause;
                }
            }

            if ( ! empty( $meta_query ) ) {
                $query->set( 'meta_query', $meta_query );
            }
        }

        $tax_query = $query->get( 'tax_query' );
        if ( ! \is_array( $tax_query ) ) {
            $tax_query = [];
        }

        foreach ( $this->taxonomy_filter_map as $taxonomy => $config ) {
            if ( ! $this->is_taxonomy_applicable( $post_type, $config['object_type'] ) ) {
                continue;
            }

            $value = $this->get_query_var_value( $query, $config['query_var'] );

            if ( null === $value || '' === $value ) {
                continue;
            }

            $terms = array_map( '\sanitize_title', array_filter( array_map( 'trim', (array) explode( ',', (string) $value ) ) ) );

            if ( empty( $terms ) ) {
                continue;
            }

            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $terms,
            ];
        }

        if ( ! empty( $tax_query ) ) {
            $query->set( 'tax_query', $tax_query );
        }
    }

    /**
     * @param array<int, \WP_Post> $posts
     * @param \WP_Query            $query
     *
     * @return array<int, \WP_Post>
     */
    public function filter_open_now( array $posts, \WP_Query $query ): array {
        if ( empty( $posts ) ) {
            return $posts;
        }

        $post_type = $query->get( 'post_type' );
        if ( \is_array( $post_type ) ) {
            $post_type = reset( $post_type ) ?: '';
        }

        if ( ! \is_string( $post_type ) || '' === $post_type ) {
            return $posts;
        }

        $schedule = $this->schedule_filters[ $post_type ] ?? null;

        if ( null === $schedule ) {
            return $posts;
        }

        $value = $this->get_query_var_value( $query, $schedule['query_var'] );

        if ( null === $value || '' === $value ) {
            return $posts;
        }

        $flag = \filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

        if ( false === $flag || null === $flag ) {
            return $posts;
        }

        $timestamp = \current_time( 'timestamp' );
        $weekday   = strtolower( gmdate( 'l', $timestamp ) );

        $filtered = [];

        foreach ( $posts as $post ) {
            $schedule_meta = \get_post_meta( $post->ID, $schedule['meta_key'], true );

            if ( self::is_schedule_open( $schedule_meta, $timestamp, $weekday ) ) {
                $filtered[] = $post;
            }
        }

        return $filtered;
    }

    public function save_post_type( array $payload ): void {
        $slug = isset( $payload['slug'] ) ? \sanitize_key( $payload['slug'] ) : '';

        if ( '' === $slug ) {
            return;
        }

        $singular = isset( $payload['singular'] ) ? $this->sanitize_label( $payload['singular'] ) : $this->guess_label( $slug );
        $plural   = isset( $payload['plural'] ) ? $this->sanitize_label( $payload['plural'] ) : $singular . 's';
        $icon     = isset( $payload['menu_icon'] ) ? $this->sanitize_icon( $payload['menu_icon'] ) : 'dashicons-admin-post';

        $supports = isset( $payload['supports'] ) ? array_map( 'sanitize_key', (array) $payload['supports'] ) : [];
        $taxes    = isset( $payload['taxonomies'] ) ? array_map( 'sanitize_key', (array) $payload['taxonomies'] ) : [];

        $rest_base = isset( $payload['rest_base'] ) ? \sanitize_title( $payload['rest_base'] ) : $slug;

        $args = [
            'labels'            => $this->build_labels( $singular, $plural ),
            'menu_icon'         => $icon,
            'supports'          => ! empty( $supports ) ? $supports : [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
            'has_archive'       => ! empty( $payload['has_archive'] ),
            'hierarchical'      => ! empty( $payload['hierarchical'] ),
            'public'            => true,
            'show_in_rest'      => true,
            'rest_base'         => $rest_base,
            'rewrite'           => [ 'slug' => $payload['rewrite'] ?? $slug ],
            'taxonomies'        => $taxes,
        ];

        $fields_selected = isset( $payload['fields'] ) ? array_map( 'sanitize_key', (array) $payload['fields'] ) : [];
        $filters         = isset( $payload['filters'] ) ? array_map( 'sanitize_key', (array) $payload['filters'] ) : [];

        $fields = $this->prepare_field_selection( $fields_selected, $filters );

        $this->post_types[ $slug ] = [
            'args'       => $args,
            'fields'     => $fields,
            'taxonomies' => $taxes,
        ];

        \update_option( self::OPTION_POST_TYPES, $this->post_types );
    }

    public function delete_post_type( string $slug ): void {
        $slug = \sanitize_key( $slug );

        unset( $this->post_types[ $slug ] );

        \update_option( self::OPTION_POST_TYPES, $this->post_types );
    }

    public function save_taxonomy( array $payload ): void {
        $slug = isset( $payload['slug'] ) ? \sanitize_key( $payload['slug'] ) : '';

        if ( '' === $slug ) {
            return;
        }

        $singular     = isset( $payload['singular'] ) ? $this->sanitize_label( $payload['singular'] ) : $this->guess_label( $slug );
        $plural       = isset( $payload['plural'] ) ? $this->sanitize_label( $payload['plural'] ) : $singular . 's';
        $hierarchical = ! empty( $payload['hierarchical'] );

        $object_types = isset( $payload['object_type'] ) ? array_map( 'sanitize_key', (array) $payload['object_type'] ) : [];

        $args = [
            'labels'       => $this->build_taxonomy_labels( $singular, $plural ),
            'hierarchical' => $hierarchical,
            'public'       => true,
            'show_in_rest' => true,
            'rewrite'      => [ 'slug' => $payload['rewrite'] ?? $slug ],
        ];

        $filterable = ! empty( $payload['filterable'] );

        $this->taxonomies[ $slug ] = [
            'args'        => $args,
            'object_type' => $object_types,
            'filterable'  => $filterable,
        ];

        \update_option( self::OPTION_TAXONOMIES, $this->taxonomies );
    }

    public function delete_taxonomy( string $slug ): void {
        $slug = \sanitize_key( $slug );

        unset( $this->taxonomies[ $slug ] );

        \update_option( self::OPTION_TAXONOMIES, $this->taxonomies );
    }

    /**
     * @param array<string, array> $fields
     */
    private function register_meta_fields( string $post_type, array $fields ): void {
        foreach ( $fields as $field_key => $field_config ) {
            if ( empty( $field_config['meta'] ) || ! \is_array( $field_config['meta'] ) ) {
                continue;
            }

            $meta_key = $this->build_meta_key( $post_type, $field_key );
            $meta     = $field_config['meta'];

            if ( empty( $meta['auth_callback'] ) ) {
                $meta['auth_callback'] = [ __CLASS__, 'authorize_meta' ];
            }

            if ( ! isset( $meta['show_in_rest'] ) ) {
                $meta['show_in_rest'] = true;
            }

            if ( ! isset( $meta['single'] ) ) {
                $meta['single'] = true;
            }

            \register_post_meta( $post_type, $meta_key, $meta );

            $filterable = ! empty( $field_config['filterable'] ) && ! empty( $field_config['filter'] );

            if ( $filterable ) {
                $query_var_key = isset( $field_config['filter']['query_var'] ) ? \sanitize_key( $field_config['filter']['query_var'] ) : $field_key;
                $mode          = $field_config['filter']['mode'] ?? 'meta_like';
                $query_var     = $this->build_query_var( $post_type, $query_var_key );

                if ( 'schedule' === $mode ) {
                    $this->schedule_filters[ $post_type ] = [
                        'query_var' => $query_var,
                        'field'     => $field_key,
                        'meta_key'  => $meta_key,
                    ];
                } else {
                    $this->filter_map[ $post_type ][ $query_var ] = [
                        'field'    => $field_key,
                        'meta_key' => $meta_key,
                        'config'   => $field_config,
                    ];
                }
            }
        }
    }

    /**
     * @param array<string, array> $fields
     */
    private function register_rest_filters( string $post_type, array $fields ): void {
        \add_filter(
            "rest_{$post_type}_collection_params",
            function ( array $params ) use ( $post_type, $fields ): array {
                foreach ( $fields as $field_key => $config ) {
                    if ( empty( $config['filterable'] ) || empty( $config['filter'] ) ) {
                        continue;
                    }

                    $query_var_key = isset( $config['filter']['query_var'] ) ? \sanitize_key( $config['filter']['query_var'] ) : $field_key;
                    $query_var     = $this->build_query_var( $post_type, $query_var_key );

                    $params[ $query_var ] = [
                        'description' => \sprintf( \__( 'Filter %1$s by %2$s.', 'sofir' ), $post_type, $config['label'] ?? $field_key ),
                        'type'        => 'string',
                    ];
                }

                foreach ( $this->taxonomy_filter_map as $taxonomy => $tax_config ) {
                    if ( ! $this->is_taxonomy_applicable( $post_type, $tax_config['object_type'] ) ) {
                        continue;
                    }

                    $params[ $tax_config['query_var'] ] = [
                        'description' => \sprintf( \__( 'Filter %1$s by taxonomy %2$s.', 'sofir' ), $post_type, $taxonomy ),
                        'type'        => 'string',
                    ];
                }

                if ( isset( $this->schedule_filters[ $post_type ] ) ) {
                    $params[ $this->schedule_filters[ $post_type ]['query_var'] ] = [
                        'description' => \__( 'Filter entries that are open right now.', 'sofir' ),
                        'type'        => 'boolean',
                    ];
                }

                return $params;
            }
        );

        \add_filter(
            "rest_{$post_type}_query",
            function ( array $args, \WP_REST_Request $request ) use ( $post_type ): array {
                $args['sofir_rest'] = true;

                foreach ( $this->filter_map[ $post_type ] ?? [] as $query_var => $config ) {
                    $raw = $request->get_param( $query_var );

                    if ( null === $raw || '' === $raw ) {
                        continue;
                    }

                    $clause = $this->build_meta_query_clause( $config, $raw );

                    if ( empty( $clause ) ) {
                        continue;
                    }

                    if ( empty( $args['meta_query'] ) || ! \is_array( $args['meta_query'] ) ) {
                        $args['meta_query'] = [];
                    }

                    $args['meta_query'][] = $clause;
                }

                foreach ( $this->taxonomy_filter_map as $taxonomy => $config ) {
                    if ( ! $this->is_taxonomy_applicable( $post_type, $config['object_type'] ) ) {
                        continue;
                    }

                    $raw = $request->get_param( $config['query_var'] );

                    if ( null === $raw || '' === $raw ) {
                        continue;
                    }

                    $terms = array_map( '\sanitize_title', array_filter( array_map( 'trim', (array) explode( ',', (string) $raw ) ) ) );

                    if ( empty( $terms ) ) {
                        continue;
                    }

                    if ( empty( $args['tax_query'] ) || ! \is_array( $args['tax_query'] ) ) {
                        $args['tax_query'] = [];
                    }

                    $args['tax_query'][] = [
                        'taxonomy' => $taxonomy,
                        'field'    => 'slug',
                        'terms'    => $terms,
                    ];
                }

                if ( isset( $this->schedule_filters[ $post_type ] ) ) {
                    $param = $this->schedule_filters[ $post_type ]['query_var'];
                    $raw   = $request->get_param( $param );

                    if ( ! empty( $raw ) ) {
                        $args[ $param ] = $raw;
                    }
                }

                return $args;
            },
            10,
            2
        );
    }

    /**
     * @param array<int|string, mixed> $meta_filter
     * @param mixed                    $value
     *
     * @return array<string, mixed>
     */
    private function build_meta_query_clause( array $meta_filter, $value ): array {
        $field_config = $meta_filter['config'] ?? [];
        $mode         = $field_config['filter']['mode'] ?? 'meta_like';
        $meta_key     = $meta_filter['meta_key'] ?? '';

        if ( '' === $meta_key ) {
            return [];
        }

        switch ( $mode ) {
            case 'meta_numeric':
                $number = is_array( $value ) ? reset( $value ) : $value;
                $number = (float) $number;

                return [
                    'key'     => $meta_key,
                    'value'   => $number,
                    'compare' => $field_config['filter']['compare'] ?? '>=',
                    'type'    => $field_config['filter']['type'] ?? 'NUMERIC',
                ];
            case 'meta_exact':
                $text = is_array( $value ) ? reset( $value ) : $value;

                return [
                    'key'     => $meta_key,
                    'value'   => \sanitize_text_field( (string) $text ),
                    'compare' => $field_config['filter']['compare'] ?? '=',
                ];
            default:
                $text = is_array( $value ) ? reset( $value ) : $value;

                return [
                    'key'     => $meta_key,
                    'value'   => \sanitize_text_field( (string) $text ),
                    'compare' => $field_config['filter']['compare'] ?? 'LIKE',
                ];
        }
    }

    private function load_or_seed_option( string $option, array $defaults ): array {
        $stored = \get_option( $option, null );

        if ( ! \is_array( $stored ) || empty( $stored ) ) {
            $stored = $defaults;
            \update_option( $option, $stored );
        }

        return $stored;
    }

    /**
     * @return array<string, array>
     */
    private function get_seed_post_types(): array {
        return [
            'listing' => [
                'args'       => [
                    'labels'    => $this->build_labels( \__( 'Listing', 'sofir' ), \__( 'Listings', 'sofir' ) ),
                    'menu_icon' => 'dashicons-location-alt',
                    'rewrite'   => [ 'slug' => 'listings' ],
                    'supports'  => [ 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ],
                ],
                'fields'     => $this->prepare_field_selection( [ 'location', 'hours', 'rating', 'status', 'price', 'contact', 'gallery', 'attributes' ], [ 'location', 'rating', 'status', 'price', 'attribute', 'open_now' ] ),
                'taxonomies' => [ 'listing_category', 'listing_location' ],
            ],
            'profile' => [
                'args'       => [
                    'labels'    => $this->build_labels( \__( 'Profile', 'sofir' ), \__( 'Profiles', 'sofir' ) ),
                    'menu_icon' => 'dashicons-id',
                    'rewrite'   => [ 'slug' => 'profiles' ],
                    'supports'  => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
                ],
                'fields'     => $this->prepare_field_selection( [ 'location', 'contact', 'status', 'attributes' ], [ 'location', 'status' ] ),
                'taxonomies' => [ 'profile_category' ],
            ],
            'article' => [
                'args'   => [
                    'labels'    => $this->build_labels( \__( 'Article', 'sofir' ), \__( 'Articles', 'sofir' ) ),
                    'menu_icon' => 'dashicons-media-document',
                    'rewrite'   => [ 'slug' => 'articles' ],
                    'supports'  => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions', 'comments' ],
                ],
                'fields' => $this->prepare_field_selection( [ 'attributes' ], [ 'attribute' ] ),
            ],
        ];
    }

    /**
     * @return array<string, array>
     */
    private function get_seed_taxonomies(): array {
        return [
            'listing_category' => [
                'args'        => [
                    'labels'       => $this->build_taxonomy_labels( \__( 'Listing Category', 'sofir' ), \__( 'Listing Categories', 'sofir' ) ),
                    'hierarchical' => true,
                ],
                'object_type' => [ 'listing' ],
                'filterable'  => true,
            ],
            'listing_location' => [
                'args'        => [
                    'labels'       => $this->build_taxonomy_labels( \__( 'Listing Location', 'sofir' ), \__( 'Listing Locations', 'sofir' ) ),
                    'hierarchical' => false,
                ],
                'object_type' => [ 'listing' ],
                'filterable'  => true,
            ],
            'profile_category' => [
                'args'        => [
                    'labels'       => $this->build_taxonomy_labels( \__( 'Profile Category', 'sofir' ), \__( 'Profile Categories', 'sofir' ) ),
                    'hierarchical' => false,
                ],
                'object_type' => [ 'profile' ],
                'filterable'  => true,
            ],
        ];
    }

    /**
     * @param array<int, string> $fields
     * @param array<int, string> $filters
     *
     * @return array<string, array>
     */
    private function prepare_field_selection( array $fields, array $filters ): array {
        $catalog = $this->get_field_catalog();
        $output  = [];

        foreach ( $fields as $field_key ) {
            if ( empty( $catalog[ $field_key ] ) ) {
                continue;
            }

            $blueprint           = $catalog[ $field_key ];
            $query_var_key       = isset( $blueprint['filter']['query_var'] ) ? $blueprint['filter']['query_var'] : $field_key;
            $is_filter_requested = in_array( $field_key, $filters, true ) || in_array( $query_var_key, $filters, true );

            $blueprint['filterable'] = $is_filter_requested;

            $output[ $field_key ] = $blueprint;
        }

        return $output;
    }

    private function build_meta_key( string $post_type, string $field ): string {
        return self::FILTER_PREFIX . $post_type . '_' . $field;
    }

    private function build_query_var( string $post_type, string $key ): string {
        return self::FILTER_PREFIX . $post_type . '_' . \sanitize_key( $key );
    }

    private function build_taxonomy_query_var( string $taxonomy ): string {
        return self::TAX_FILTER_PREFIX . \sanitize_key( $taxonomy );
    }

    private function get_query_var_value( \WP_Query $query, string $query_var ) {
        $value = $query->get( $query_var );

        if ( null === $value && isset( $_GET[ $query_var ] ) ) {
            $value = $_GET[ $query_var ];
        }

        if ( \is_string( $value ) ) {
            return \wp_unslash( $value );
        }

        if ( \is_array( $value ) ) {
            return \wp_unslash( $value );
        }

        return $value;
    }

    public function get_filter_query_vars( string $post_type ): array {
        $post_type = \sanitize_key( $post_type );

        return array_keys( $this->filter_map[ $post_type ] ?? [] );
    }

    public function get_schedule_query_var( string $post_type ): ?string {
        $post_type = \sanitize_key( $post_type );

        if ( empty( $this->schedule_filters[ $post_type ]['query_var'] ) ) {
            return null;
        }

        return $this->schedule_filters[ $post_type ]['query_var'];
    }

    public function get_taxonomy_query_vars( string $post_type ): array {
        $post_type  = \sanitize_key( $post_type );
        $query_vars = [];

        foreach ( $this->taxonomy_filter_map as $taxonomy => $config ) {
            if ( ! $this->is_taxonomy_applicable( $post_type, $config['object_type'] ) ) {
                continue;
            }

            $query_vars[] = $config['query_var'];
        }

        return $query_vars;
    }

    public function get_post_type_fields( string $post_type ): array {
        $post_type = \sanitize_key( $post_type );

        return $this->post_types[ $post_type ]['fields'] ?? [];
    }

    public function get_rest_base( string $post_type ): string {
        $post_type = \sanitize_key( $post_type );
        $object    = \get_post_type_object( $post_type );

        if ( $object && ! empty( $object->rest_base ) ) {
            return $object->rest_base;
        }

        return $post_type;
    }

    private function is_taxonomy_applicable( string $post_type, array $object_types ): bool {
        if ( empty( $object_types ) ) {
            return true;
        }

        return in_array( $post_type, array_map( 'sanitize_key', $object_types ), true );
    }

    private function sanitize_label( string $label ): string {
        return trim( \wp_strip_all_tags( $label ) );
    }

    private function sanitize_icon( string $icon ): string {
        $icon = trim( (string) $icon );

        if ( '' === $icon || 0 !== strpos( $icon, 'dashicons-' ) ) {
            return 'dashicons-admin-post';
        }

        return $icon;
    }

    private function build_labels( string $singular, string $plural ): array {
        $key = md5( $singular . '|' . $plural );

        if ( isset( $this->seed_labels_cache[ $key ] ) ) {
            return $this->seed_labels_cache[ $key ];
        }

        $labels = [
            'name'                     => $plural,
            'singular_name'            => $singular,
            'add_new'                  => \__( 'Add New', 'sofir' ),
            'add_new_item'             => \sprintf( \__( 'Add New %s', 'sofir' ), $singular ),
            'edit_item'                => \sprintf( \__( 'Edit %s', 'sofir' ), $singular ),
            'new_item'                 => \sprintf( \__( 'New %s', 'sofir' ), $singular ),
            'view_item'                => \sprintf( \__( 'View %s', 'sofir' ), $singular ),
            'view_items'               => \sprintf( \__( 'View %s', 'sofir' ), $plural ),
            'search_items'             => \sprintf( \__( 'Search %s', 'sofir' ), $plural ),
            'not_found'                => \sprintf( \__( 'No %s found', 'sofir' ), strtolower( $plural ) ),
            'not_found_in_trash'       => \sprintf( \__( 'No %s found in Trash', 'sofir' ), strtolower( $plural ) ),
            'all_items'                => \sprintf( \__( 'All %s', 'sofir' ), $plural ),
            'archives'                 => \sprintf( \__( '%s Archives', 'sofir' ), $singular ),
            'attributes'               => \sprintf( \__( '%s Attributes', 'sofir' ), $singular ),
            'insert_into_item'         => \sprintf( \__( 'Insert into %s', 'sofir' ), strtolower( $singular ) ),
            'uploaded_to_this_item'    => \sprintf( \__( 'Uploaded to this %s', 'sofir' ), strtolower( $singular ) ),
            'featured_image'           => \__( 'Featured Image', 'sofir' ),
            'set_featured_image'       => \__( 'Set featured image', 'sofir' ),
            'remove_featured_image'    => \__( 'Remove featured image', 'sofir' ),
            'use_featured_image'       => \__( 'Use as featured image', 'sofir' ),
            'menu_name'                => $plural,
            'filter_items_list'        => \sprintf( \__( 'Filter %s list', 'sofir' ), strtolower( $plural ) ),
            'items_list'               => \sprintf( \__( '%s list', 'sofir' ), $plural ),
            'items_list_navigation'    => \sprintf( \__( '%s list navigation', 'sofir' ), $plural ),
        ];

        $this->seed_labels_cache[ $key ] = $labels;

        return $labels;
    }

    private function build_taxonomy_labels( string $singular, string $plural ): array {
        return [
            'name'                       => $plural,
            'singular_name'              => $singular,
            'search_items'               => \sprintf( \__( 'Search %s', 'sofir' ), $plural ),
            'popular_items'              => \__( 'Popular Items', 'sofir' ),
            'all_items'                  => \sprintf( \__( 'All %s', 'sofir' ), $plural ),
            'parent_item'                => \sprintf( \__( 'Parent %s', 'sofir' ), $singular ),
            'parent_item_colon'          => \sprintf( \__( 'Parent %s:', 'sofir' ), $singular ),
            'edit_item'                  => \sprintf( \__( 'Edit %s', 'sofir' ), $singular ),
            'view_item'                  => \sprintf( \__( 'View %s', 'sofir' ), $singular ),
            'update_item'                => \sprintf( \__( 'Update %s', 'sofir' ), $singular ),
            'add_new_item'               => \sprintf( \__( 'Add New %s', 'sofir' ), $singular ),
            'new_item_name'              => \sprintf( \__( 'New %s Name', 'sofir' ), $singular ),
            'separate_items_with_commas' => \__( 'Separate items with commas', 'sofir' ),
            'add_or_remove_items'        => \__( 'Add or remove items', 'sofir' ),
            'choose_from_most_used'      => \__( 'Choose from the most used items', 'sofir' ),
            'not_found'                  => \__( 'No items found', 'sofir' ),
            'menu_name'                  => $plural,
        ];
    }

    private function guess_label( string $slug ): string {
        $name = str_replace( [ '-', '_' ], ' ', $slug );

        return ucwords( $name );
    }

    private function get_weekday_schema(): array {
        $schema = [];

        foreach ( self::get_weekdays() as $day ) {
            $schema[ $day ] = [
                'type'  => 'array',
                'items' => [
                    'type'       => 'object',
                    'properties' => [
                        'open'  => [ 'type' => 'string' ],
                        'close' => [ 'type' => 'string' ],
                    ],
                ],
            ];
        }

        return $schema;
    }

    public static function authorize_meta( bool $allowed, string $meta_key, int $post_id, int $user_id, string $cap, array $caps ): bool {
        return \current_user_can( 'edit_post', $post_id );
    }

    public static function sanitize_location( $value ) {
        if ( ! \is_array( $value ) ) {
            return [];
        }

        $fields = [ 'address', 'city', 'state', 'country', 'postal' ];

        $output = [];

        foreach ( $fields as $field ) {
            if ( isset( $value[ $field ] ) ) {
                $output[ $field ] = \sanitize_text_field( (string) $value[ $field ] );
            }
        }

        if ( isset( $value['lat'] ) ) {
            $output['lat'] = (float) $value['lat'];
        }

        if ( isset( $value['lng'] ) ) {
            $output['lng'] = (float) $value['lng'];
        }

        return $output;
    }

    public static function sanitize_hours( $value ) {
        if ( ! \is_array( $value ) ) {
            return [];
        }

        $result = [];

        foreach ( self::get_weekdays() as $day ) {
            if ( empty( $value[ $day ] ) || ! \is_array( $value[ $day ] ) ) {
                continue;
            }

            $slots = [];

            foreach ( $value[ $day ] as $slot ) {
                if ( empty( $slot['open'] ) || empty( $slot['close'] ) ) {
                    continue;
                }

                $open  = self::sanitize_time( (string) $slot['open'] );
                $close = self::sanitize_time( (string) $slot['close'] );

                if ( $open && $close ) {
                    $slots[] = [ 'open' => $open, 'close' => $close ];
                }
            }

            if ( ! empty( $slots ) ) {
                $result[ $day ] = $slots;
            }
        }

        return $result;
    }

    public static function sanitize_rating( $value ) {
        $value = is_array( $value ) ? reset( $value ) : $value;
        $value = (float) $value;

        if ( $value < 0 ) {
            $value = 0;
        }

        if ( $value > 5 ) {
            $value = 5;
        }

        return round( $value, 2 );
    }

    public static function sanitize_status( $value ) {
        $value = \sanitize_key( (string) $value );

        return $value ?: 'active';
    }

    public static function sanitize_text_meta( $value ) {
        $value = is_array( $value ) ? reset( $value ) : $value;

        return \sanitize_text_field( (string) $value );
    }

    public static function sanitize_contact( $value ) {
        if ( ! \is_array( $value ) ) {
            return [];
        }

        $output = [];

        if ( isset( $value['email'] ) && \is_email( $value['email'] ) ) {
            $output['email'] = $value['email'];
        }

        if ( isset( $value['phone'] ) ) {
            $output['phone'] = \sanitize_text_field( (string) $value['phone'] );
        }

        if ( isset( $value['website'] ) ) {
            $output['website'] = \esc_url_raw( $value['website'] );
        }

        return $output;
    }

    public static function sanitize_gallery( $value ) {
        if ( \is_array( $value ) ) {
            return array_map( 'absint', $value );
        }

        if ( '' === $value || null === $value ) {
            return [];
        }

        $parts = array_map( 'trim', explode( ',', (string) $value ) );

        return array_map( 'absint', array_filter( $parts ) );
    }

    public static function sanitize_attributes( $value ) {
        if ( ! \is_array( $value ) ) {
            return [];
        }

        $output = [];

        foreach ( $value as $key => $val ) {
            $key = \sanitize_key( (string) $key );

            if ( '' === $key ) {
                continue;
            }

            $output[ $key ] = \sanitize_text_field( (string) $val );
        }

        return $output;
    }

    private static function sanitize_time( string $time ): string {
        if ( preg_match( '/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time ) ) {
            return $time;
        }

        return '';
    }

    private static function get_weekdays(): array {
        return [ 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ];
    }

    private static function is_schedule_open( $schedule, int $timestamp, string $weekday ): bool {
        if ( ! \is_array( $schedule ) || empty( $schedule[ $weekday ] ) ) {
            return false;
        }

        $current_time = gmdate( 'H:i', $timestamp + (int) \get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS );

        foreach ( $schedule[ $weekday ] as $slot ) {
            if ( empty( $slot['open'] ) || empty( $slot['close'] ) ) {
                continue;
            }

            if ( $slot['open'] <= $current_time && $current_time <= $slot['close'] ) {
                return true;
            }
        }

        return false;
    }
}
