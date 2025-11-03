<?php
namespace Sofir;

class Importer {
    private static ?Importer $instance = null;

    public static function instance(): Importer {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'admin_init', [ $this, 'register_import_actions' ] );
        \add_action( 'wp_ajax_sofir_import_template', [ $this, 'handle_ajax_import' ] );
    }

    public function register_import_actions(): void {
        \do_action( 'sofir/importer/register' );
    }

    public function handle_ajax_import(): void {
        \check_ajax_referer( 'sofir_admin', 'nonce' );

        if ( ! \current_user_can( 'edit_posts' ) ) {
            \wp_send_json_error( [ 'message' => \__( 'Insufficient permission.', 'sofir' ) ], 403 );
        }

        $slug    = isset( $_POST['template'] ) ? \sanitize_key( $_POST['template'] ) : '';
        $context = isset( $_POST['context'] ) ? \sanitize_key( $_POST['context'] ) : 'page';
        $status  = isset( $_POST['status'] ) ? \sanitize_key( $_POST['status'] ) : 'draft';
        $title   = isset( $_POST['title'] ) ? \sanitize_text_field( $_POST['title'] ) : '';

        if ( '' === $slug ) {
            \wp_send_json_error( [ 'message' => \__( 'Template slug required.', 'sofir' ) ], 400 );
        }

        $template = Templates\Manager::instance()->get_template( $slug );

        if ( null === $template ) {
            \wp_send_json_error( [ 'message' => \__( 'Template not found.', 'sofir' ) ], 404 );
        }

        $response = [ 
            'context' => $context,
            'steps'   => [],
        ];

        \do_action( 'sofir/importer/before_response', $template, $context );

        $this->ensure_theme_compatibility( $template );
        $response['steps'][] = \__( 'Theme compatibility checked', 'sofir' );

        if ( ! empty( $template['demo_cpt'] ) ) {
            $this->register_template_cpts( $template['demo_cpt'] );
            $response['steps'][] = sprintf( 
                \__( 'Created %d custom post type(s)', 'sofir' ),
                count( $template['demo_cpt'] )
            );
        }

        $this->import_demo_content( $template );
        $response['steps'][] = \__( 'Demo content imported', 'sofir' );

        if ( 'template' === $context ) {
            $result = Templates\Manager::instance()->import_to_fse_template( $template );

            if ( 0 === $result ) {
                \wp_send_json_error( [ 'message' => \__( 'Failed to import site template.', 'sofir' ) ] );
            }

            $this->setup_header_footer( $template, $result );
            $response['steps'][] = \__( 'Header/Footer configured', 'sofir' );

            $response['postId']  = $result;
            $response['editUrl'] = \get_edit_post_link( $result, 'raw' );
            $response['message'] = \__( 'Template imported successfully!', 'sofir' );
        } else {
            $args = [];

            if ( $title ) {
                $args['post_title'] = $title;
            }

            if ( in_array( $status, [ 'draft', 'publish', 'pending', 'future' ], true ) ) {
                $args['post_status'] = $status;
            }

            $result = Templates\Manager::instance()->import_to_page( $template, $args );

            if ( 0 === $result ) {
                \wp_send_json_error( [ 'message' => \__( 'Failed to import page.', 'sofir' ) ] );
            }

            $response['postId']   = $result;
            $response['editUrl']  = \get_edit_post_link( $result, 'raw' );
            $response['viewUrl']  = \get_permalink( $result );
            $response['message']  = \__( 'Page imported successfully!', 'sofir' );
            $response['steps'][]  = \__( 'Page created and ready to edit', 'sofir' );
        }

        \do_action( 'sofir/importer/after_response', $response, $template );

        \wp_send_json_success( $response );
    }

    private function ensure_theme_compatibility( array $template ): void {
        $required_theme = $template['theme'] ?? '';

        if ( empty( $required_theme ) ) {
            return;
        }

        $current_theme = \wp_get_theme();

        if ( 'block-theme' === $required_theme && ! $current_theme->is_block_theme() ) {
            \update_option( '_sofir_theme_notice', \__( 'This template works best with a block theme. Consider switching to Twenty Twenty-Four or similar.', 'sofir' ) );
        }

        \do_action( 'sofir/importer/theme_checked', $template, $current_theme );
    }

    private function register_template_cpts( array $cpt_slugs ): void {
        foreach ( $cpt_slugs as $cpt_slug ) {
            $existing = \get_option( 'sofir_custom_post_types', [] );

            if ( ! is_array( $existing ) ) {
                $existing = [];
            }

            if ( isset( $existing[ $cpt_slug ] ) ) {
                continue;
            }

            $labels = $this->generate_cpt_labels( $cpt_slug );

            $existing[ $cpt_slug ] = [
                'singular'    => $labels['singular'],
                'plural'      => $labels['plural'],
                'has_archive' => true,
                'public'      => true,
                'supports'    => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
                'taxonomies'  => [],
                'meta_fields' => [],
            ];

            \update_option( 'sofir_custom_post_types', $existing );
        }

        \do_action( 'sofir/importer/cpts_registered', $cpt_slugs );
    }

    private function generate_cpt_labels( string $slug ): array {
        $singular = ucwords( str_replace( '_', ' ', $slug ) );
        $plural   = $singular . 's';

        $label_map = [
            'testimonial'       => [ 'singular' => 'Testimonial', 'plural' => 'Testimonials' ],
            'pricing'           => [ 'singular' => 'Pricing Plan', 'plural' => 'Pricing Plans' ],
            'service'           => [ 'singular' => 'Service', 'plural' => 'Services' ],
            'menu_item'         => [ 'singular' => 'Menu Item', 'plural' => 'Menu Items' ],
            'property'          => [ 'singular' => 'Property', 'plural' => 'Properties' ],
            'directory_listing' => [ 'singular' => 'Listing', 'plural' => 'Listings' ],
            'doctor'            => [ 'singular' => 'Doctor', 'plural' => 'Doctors' ],
            'fitness_studio'    => [ 'singular' => 'Fitness Studio', 'plural' => 'Fitness Studios' ],
            'team_member'       => [ 'singular' => 'Team Member', 'plural' => 'Team Members' ],
            'project'           => [ 'singular' => 'Project', 'plural' => 'Projects' ],
        ];

        return $label_map[ $slug ] ?? [ 'singular' => $singular, 'plural' => $plural ];
    }

    private function import_demo_content( array $template ): void {
        \do_action( 'sofir/importer/demo_content', $template );
    }

    private function setup_header_footer( array $template, int $template_id ): void {
        \do_action( 'sofir/importer/setup_header_footer', $template, $template_id );
    }
}
