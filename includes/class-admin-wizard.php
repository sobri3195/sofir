<?php
namespace Sofir\Admin;

class Wizard {
    private static ?Wizard $instance = null;

    private string $slug = 'sofir-setup';

    public static function instance(): Wizard {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'admin_menu', [ $this, 'register_page' ] );
        \add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function register_page(): void {
        \add_submenu_page(
            'sofir-dashboard',
            \__( 'SOFIR Setup Wizard', 'sofir' ),
            \__( 'Setup Wizard', 'sofir' ),
            'manage_options',
            $this->slug,
            [ $this, 'render' ]
        );
    }

    public function enqueue_assets( string $hook ): void {
        if ( false === strpos( $hook, $this->slug ) ) {
            return;
        }

        \wp_enqueue_style( 'sofir-admin' );
        \wp_enqueue_script( 'sofir-admin' );
    }

    public function render(): void {
        $steps = $this->get_steps();

        echo '<div class="wrap sofir-admin sofir-wizard">';
        echo '<h1>' . \esc_html__( 'SOFIR Quick Setup', 'sofir' ) . '</h1>';
        echo '<ol class="sofir-wizard-steps">';

        foreach ( $steps as $step => $description ) {
            printf(
                '<li><strong>%1$s</strong><span>%2$s</span></li>',
                \esc_html( $step ),
                \esc_html( $description )
            );
        }

        echo '</ol>';
        echo '</div>';
    }

    /**
     * @return array<string, string>
     */
    private function get_steps(): array {
        $steps = [
            \__( 'Pilih Mode Situs', 'sofir' )          => \__( 'Landing page, direktori, portal berita, atau profil bisnis.', 'sofir' ),
            \__( 'Konfigurasi Konten', 'sofir' )        => \__( 'Pilih CPT, field, dan taxonomy yang dibutuhkan.', 'sofir' ),
            \__( 'Aktifkan Modul', 'sofir' )            => \__( 'Tentukan modul keamanan, performa, dan membership.', 'sofir' ),
            \__( 'Optimasi SEO & Analitik', 'sofir' )   => \__( 'Sesuaikan meta, schema, redirect, dan tracking.', 'sofir' ),
            \__( 'Publikasikan Template', 'sofir' )     => \__( 'Import layout Gutenberg/FSE dan lakukan penyesuaian akhir.', 'sofir' ),
        ];

        /** @var array<string, string> $steps */
        $steps = \apply_filters( 'sofir/admin/wizard/steps', $steps );

        return $steps;
    }
}
