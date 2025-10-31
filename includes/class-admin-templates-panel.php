<?php
namespace Sofir\Admin;

use Sofir\Templates\Manager as TemplateManager;

class TemplatesPanel {
    private static ?TemplatesPanel $instance = null;

    public static function instance(): TemplatesPanel {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function render(): void {
        $catalog = TemplateManager::instance()->get_catalog();

        if ( empty( $catalog ) ) {
            echo '<p>' . \esc_html__( 'Template catalog not available.', 'sofir' ) . '</p>';

            return;
        }

        echo '<div class="sofir-templates">';
        echo '<p class="description">' . \esc_html__( 'Import template hanya dengan sekali klik. Anda dapat memilih import sebagai halaman Gutenberg atau template Full Site Editor.', 'sofir' ) . '</p>';

        foreach ( $catalog as $group => $templates ) {
            echo '<section class="sofir-template-group" data-group="' . \esc_attr( $group ) . '">';
            echo '<header class="sofir-template-group__header">';
            echo '<h2>' . \esc_html( $this->group_label( $group ) ) . '</h2>';
            echo '<p>' . \esc_html( $this->group_description( $group ) ) . '</p>';
            echo '</header>';

            echo '<div class="sofir-template-group__grid">';
            foreach ( $templates as $template ) {
                $contexts = isset( $template['context'] ) ? (array) $template['context'] : [ 'page' ];
                $context_labels = $this->context_labels( $contexts );

                echo '<article class="sofir-template-card" data-template="' . \esc_attr( $template['slug'] ) . '">';
                echo '<h3>' . \esc_html( $template['title'] ) . '</h3>';
                if ( ! empty( $template['description'] ) ) {
                    echo '<p class="description">' . \esc_html( $template['description'] ) . '</p>';
                }

                echo '<div class="sofir-template-card__meta">';
                echo '<span class="sofir-badge">' . \esc_html( implode( ' · ', $context_labels ) ) . '</span>';
                echo '</div>';

                echo '<div class="sofir-template-card__actions">';
                if ( in_array( 'page', $contexts, true ) ) {
                    echo '<button type="button" class="button button-primary sofir-template-import" data-template="' . \esc_attr( $template['slug'] ) . '" data-context="page">' . \esc_html__( 'Import as Page', 'sofir' ) . '</button>';
                }

                if ( in_array( 'template', $contexts, true ) ) {
                    echo '<button type="button" class="button sofir-template-import" data-template="' . \esc_attr( $template['slug'] ) . '" data-context="template">' . \esc_html__( 'Import to FSE', 'sofir' ) . '</button>';
                }

                echo '</div>';
                echo '</article>';
            }
            echo '</div>';
            echo '</section>';
        }

        echo '</div>';
    }

    private function group_label( string $group ): string {
        $map = [
            'landing'   => \__( 'Landing Templates', 'sofir' ),
            'directory' => \__( 'Directory Templates', 'sofir' ),
            'blog'      => \__( 'Blog & Portal', 'sofir' ),
            'profile'   => \__( 'Profile & Company', 'sofir' ),
        ];

        return $map[ $group ] ?? ucfirst( $group );
    }

    private function group_description( string $group ): string {
        $map = [
            'landing'   => \__( 'Optimized landing pages dengan CTA dan konversi tinggi.', 'sofir' ),
            'directory' => \__( 'Listing &amp; direktori lengkap dengan filter dinamis.', 'sofir' ),
            'blog'      => \__( 'Portal berita, magazine, dan konten editorial.', 'sofir' ),
            'profile'   => \__( 'Profil bisnis, portofolio, dan company page.', 'sofir' ),
        ];

        return $map[ $group ] ?? '';
    }

    /**
     * @param array<int, string> $contexts
     *
     * @return array<int, string>
     */
    private function context_labels( array $contexts ): array {
        $labels = [];

        foreach ( $contexts as $context ) {
            switch ( $context ) {
                case 'template':
                    $labels[] = \__( 'Full Site Editing', 'sofir' );
                    break;
                case 'page':
                default:
                    $labels[] = \__( 'Page', 'sofir' );
            }
        }

        return $labels;
    }
}
