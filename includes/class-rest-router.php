<?php
namespace Sofir\Rest;

class Router {
    private static ?Router $instance = null;

    private string $namespace = 'sofir/v1';

    public static function instance(): Router {
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
            $this->namespace,
            '/status',
            [
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_status' ],
                'permission_callback' => '__return_true',
            ]
        );

        \do_action( 'sofir/rest/register_routes', $this->namespace );
    }

    public function get_status( \WP_REST_Request $request ) {
        $data = [
            'version' => SOFIR_VERSION,
            'modules' => \apply_filters( 'sofir/rest/status/modules', [] ),
        ];

        \do_action( 'sofir/rest/status', $data, $request );

        return \rest_ensure_response( $data );
    }
}
