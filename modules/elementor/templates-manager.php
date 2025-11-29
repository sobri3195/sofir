<?php
namespace Sofir\Elementor;

class Templates_Manager {
    private static ?Templates_Manager $instance = null;

    public static function instance(): Templates_Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'elementor/editor/after_register_scripts', [ $this, 'register_templates' ] );
        \add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        \add_action( 'wp_ajax_sofir_import_elementor_template', [ $this, 'import_template' ] );
        \add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
        \add_filter( 'elementor/editor/localize_settings', [ $this, 'add_template_library' ] );
    }

    public function enqueue_assets(): void {
        \wp_enqueue_style(
            'sofir-elementor-templates',
            SOFIR_PLUGIN_URL . 'assets/css/elementor-templates.css',
            [],
            SOFIR_VERSION
        );

        \wp_enqueue_script(
            'sofir-elementor-templates',
            SOFIR_PLUGIN_URL . 'assets/js/elementor-templates.js',
            [ 'jquery', 'elementor-editor' ],
            SOFIR_VERSION,
            true
        );
    }

    public function register_templates(): void {
        \wp_localize_script(
            'elementor-editor',
            'sofirElementorTemplates',
            [
                'templates' => $this->get_all_templates(),
                'ajaxurl'   => \admin_url( 'admin-ajax.php' ),
                'nonce'     => \wp_create_nonce( 'sofir_elementor_templates' ),
            ]
        );
    }

    public function add_template_library( array $config ): array {
        $config['sofir_templates'] = [
            'enabled'   => true,
            'templates' => $this->get_all_templates(),
            'logo'      => SOFIR_PLUGIN_URL . 'assets/images/sofir-logo.svg',
        ];

        return $config;
    }

    public function register_ajax_actions( \Elementor\Core\Common\Modules\Ajax\Module $ajax_module ): void {
        $ajax_module->register_ajax_action(
            'sofir_get_templates',
            [ $this, 'ajax_get_templates' ]
        );

        $ajax_module->register_ajax_action(
            'sofir_import_template',
            [ $this, 'ajax_import_template' ]
        );
    }

    public function ajax_get_templates( array $data ): array {
        $type = $data['template_type'] ?? 'all';

        return [
            'templates' => $this->get_templates_by_type( $type ),
        ];
    }

    public function ajax_import_template( array $data ): array {
        $template_id = $data['template_id'] ?? '';

        if ( empty( $template_id ) ) {
            throw new \Exception( \__( 'Template ID is required', 'sofir' ) );
        }

        $template_data = $this->get_template_data( $template_id );

        if ( ! $template_data ) {
            throw new \Exception( \__( 'Template not found', 'sofir' ) );
        }

        return [
            'content' => $template_data,
        ];
    }

    public function import_template(): void {
        \check_ajax_referer( 'sofir_elementor_templates', 'nonce' );

        if ( ! \current_user_can( 'edit_posts' ) ) {
            \wp_send_json_error( [ 'message' => \__( 'Permission denied', 'sofir' ) ] );
        }

        $template_id = \sanitize_text_field( $_POST['template_id'] ?? '' );

        if ( empty( $template_id ) ) {
            \wp_send_json_error( [ 'message' => \__( 'Template ID is required', 'sofir' ) ] );
        }

        $template_data = $this->get_template_data( $template_id );

        if ( ! $template_data ) {
            \wp_send_json_error( [ 'message' => \__( 'Template not found', 'sofir' ) ] );
        }

        \wp_send_json_success( [
            'content' => $template_data,
            'title'   => $this->get_template_title( $template_id ),
        ] );
    }

    private function get_all_templates(): array {
        $templates_file = SOFIR_PLUGIN_DIR . '/modules/elementor/templates/library.php';

        if ( ! \file_exists( $templates_file ) ) {
            return [];
        }

        return include $templates_file;
    }

    private function get_templates_by_type( string $type ): array {
        $all_templates = $this->get_all_templates();

        if ( 'all' === $type ) {
            return $all_templates;
        }

        $filtered = [];
        foreach ( $all_templates as $category => $templates ) {
            if ( $category === $type ) {
                $filtered[ $category ] = $templates;
            }
        }

        return $filtered;
    }

    private function get_template_data( string $template_id ): ?array {
        $template_file = SOFIR_PLUGIN_DIR . '/modules/elementor/templates/data/' . $template_id . '.json';

        if ( ! \file_exists( $template_file ) ) {
            return null;
        }

        $json_data = \file_get_contents( $template_file );
        return \json_decode( $json_data, true );
    }

    private function get_template_title( string $template_id ): string {
        $all_templates = $this->get_all_templates();

        foreach ( $all_templates as $category => $templates ) {
            foreach ( $templates as $template ) {
                if ( $template['id'] === $template_id ) {
                    return $template['title'];
                }
            }
        }

        return '';
    }
}
