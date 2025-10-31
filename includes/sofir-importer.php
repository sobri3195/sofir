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

        $response = [ 'context' => $context ];

        \do_action( 'sofir/importer/before_response', $template, $context );

        if ( 'template' === $context ) {
            $result = Templates\Manager::instance()->import_to_fse_template( $template );

            if ( 0 === $result ) {
                \wp_send_json_error( [ 'message' => \__( 'Failed to import site template.', 'sofir' ) ] );
            }

            $response['postId']  = $result;
            $response['editUrl'] = \get_edit_post_link( $result, 'raw' );
            $response['message'] = \__( 'Template imported into Full Site Editor.', 'sofir' );
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
            $response['message']  = \__( 'Template imported as draft page.', 'sofir' );
        }

        \do_action( 'sofir/importer/after_response', $response, $template );

        \wp_send_json_success( $response );
    }
}
