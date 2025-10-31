<?php
namespace Sofir\Admin;

use Sofir\Ai\Builder as AiBuilder;
use Sofir\Seo\Engine;

class SeoPanel {
    private static ?SeoPanel $instance = null;

    public static function instance(): SeoPanel {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function render(): void {
        $engine     = Engine::instance();
        $settings   = $engine->get_settings();
        $redirects  = $engine->get_redirects();
        $notice     = isset( $_GET['sofir_notice'] ) ? \sanitize_key( $_GET['sofir_notice'] ) : '';
        $top_posts  = $engine->get_top_posts( 5 );
        $event_logs = $engine->get_event_summary();

        echo '<div class="sofir-admin">';

        if ( $notice ) {
            $this->render_notice( $notice );
        }

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'SEO Global Settings', 'sofir' ) . '</h2>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '">';
        echo '<input type="hidden" name="action" value="sofir_save_seo_settings" />';
        \wp_nonce_field( 'sofir_seo_settings', '_sofir_nonce' );

        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Title Pattern', 'sofir' ) . '</span>';
        echo '<input type="text" class="regular-text" name="sofir_title_pattern" value="' . \esc_attr( $settings['title_pattern'] ?? '' ) . '" placeholder="%title% | %site%" />';
        echo '</label>';

        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Default Meta Description', 'sofir' ) . '</span>';
        echo '<textarea name="sofir_default_description" rows="3" class="large-text">' . \esc_textarea( $settings['default_description'] ?? '' ) . '</textarea>';
        echo '</label>';

        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Default OpenGraph Image URL', 'sofir' ) . '</span>';
        echo '<input type="url" class="regular-text" name="sofir_default_image" value="' . \esc_attr( $settings['default_image'] ?? '' ) . '" />';
        echo '</label>';

        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Twitter Handle', 'sofir' ) . '</span>';
        echo '<input type="text" class="regular-text" name="sofir_twitter_handle" value="' . \esc_attr( $settings['twitter_handle'] ?? '' ) . '" placeholder="@brand" />';
        echo '</label>';

        echo '<label class="sofir-toggle">';
        echo '<input type="checkbox" name="sofir_enable_schema" value="1" ' . \checked( ! empty( $settings['enable_schema'] ), true, false ) . ' />';
        echo '<span>' . \esc_html__( 'Enable automatic JSON-LD schema output', 'sofir' ) . '</span>';
        echo '</label>';

        echo '<label class="sofir-toggle">';
        echo '<input type="checkbox" name="sofir_enable_analytics" value="1" ' . \checked( ! empty( $settings['analytics_enabled'] ), true, false ) . ' />';
        echo '<span>' . \esc_html__( 'Enable on-site analytics tracking', 'sofir' ) . '</span>';
        echo '</label>';

        echo '<p class="submit"><button type="submit" class="button button-primary">' . \esc_html__( 'Save Settings', 'sofir' ) . '</button></p>';
        echo '</form>';
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Redirect Manager', 'sofir' ) . '</h2>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '" class="sofir-redirect-form">';
        echo '<input type="hidden" name="action" value="sofir_add_redirect" />';
        \wp_nonce_field( 'sofir_seo_redirect', '_sofir_nonce' );
        echo '<div class="sofir-redirect-grid">';
        echo '<label><span>' . \esc_html__( 'From path', 'sofir' ) . '</span><input type="text" name="sofir_redirect_from" placeholder="/old-url" required /></label>';
        echo '<label><span>' . \esc_html__( 'To URL', 'sofir' ) . '</span><input type="url" name="sofir_redirect_to" placeholder="https://example.com/new-url" required /></label>';
        echo '</div>';
        echo '<p class="submit"><button type="submit" class="button">' . \esc_html__( 'Add Redirect', 'sofir' ) . '</button></p>';
        echo '</form>';

        if ( ! empty( $redirects ) ) {
            echo '<table class="widefat">';
            echo '<thead><tr><th>' . \esc_html__( 'From', 'sofir' ) . '</th><th>' . \esc_html__( 'To', 'sofir' ) . '</th><th>' . \esc_html__( 'Actions', 'sofir' ) . '</th></tr></thead><tbody>';
            foreach ( $redirects as $index => $redirect ) {
                $delete_url = \wp_nonce_url(
                    \add_query_arg(
                        [
                            'action' => 'sofir_delete_redirect',
                            'index'  => $index,
                        ],
                        \admin_url( 'admin-post.php' )
                    ),
                    'sofir_delete_redirect',
                    '_sofir_nonce'
                );

                echo '<tr>';
                echo '<td>' . \esc_html( $redirect['from'] ) . '</td>';
                echo '<td><a href="' . \esc_url( $redirect['to'] ) . '" target="_blank" rel="noopener">' . \esc_html( $redirect['to'] ) . '</a></td>';
                echo '<td><a class="button-link-delete" href="' . \esc_url( $delete_url ) . '">' . \esc_html__( 'Delete', 'sofir' ) . '</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>' . \esc_html__( 'No redirects configured.', 'sofir' ) . '</p>';
        }

        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Analytics Overview', 'sofir' ) . '</h2>';

        if ( empty( $settings['analytics_enabled'] ) ) {
            echo '<p>' . \esc_html__( 'Enable analytics to start collecting page view statistics and interaction heatmaps.', 'sofir' ) . '</p>';
        } else {
            echo '<div class="sofir-metrics">';
            echo '<div>';
            echo '<h3>' . \esc_html__( 'Top Content', 'sofir' ) . '</h3>';
            if ( empty( $top_posts ) ) {
                echo '<p>' . \esc_html__( 'No data yet.', 'sofir' ) . '</p>';
            } else {
                echo '<ul class="sofir-metric-list">';
                foreach ( $top_posts as $post ) {
                    echo '<li><a href="' . \esc_url( $post['edit'] ) . '">' . \esc_html( $post['title'] ) . '</a> <span class="count">' . \esc_html( $post['views'] ) . '</span></li>';
                }
                echo '</ul>';
            }
            echo '</div>';

            echo '<div>';
            echo '<h3>' . \esc_html__( 'Tracked Interactions', 'sofir' ) . '</h3>';
            if ( empty( $event_logs ) ) {
                echo '<p>' . \esc_html__( 'No tracked events yet.', 'sofir' ) . '</p>';
            } else {
                echo '<ul class="sofir-metric-list">';
                foreach ( $event_logs as $key => $count ) {
                    $parts    = explode( '|', $key );
                    $selector = $parts[0] ?? '';
                    $path     = $parts[1] ?? '';
                    echo '<li><code>' . \esc_html( $selector ) . '</code> <span class="path">' . \esc_html( $path ) . '</span> <span class="count">' . \esc_html( $count ) . '</span></li>';
                }
                echo '</ul>';
            }
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        $latest = \get_posts(
            [
                'post_type'      => 'any',
                'post_status'    => 'publish',
                'posts_per_page' => 1,
                'orderby'        => 'modified',
                'order'          => 'DESC',
            ]
        );

        if ( $latest ) {
            $post     = $latest[0];
            $insights = AiBuilder::instance()->generate_insights( $post->post_title, $post->post_content, $post->post_type );

            echo '<div class="sofir-card">';
            echo '<h2>' . \esc_html__( 'AI Assistant Suggestions', 'sofir' ) . '</h2>';
            echo '<p>' . \esc_html__( 'Rekomendasi otomatis berdasarkan konten terbaru Anda.', 'sofir' ) . '</p>';
            echo '<h3>' . \esc_html( $insights['title'] ) . '</h3>';
            echo '<p>' . \esc_html( $insights['description'] ) . '</p>';
            if ( ! empty( $insights['keywords'] ) ) {
                echo '<p><strong>' . \esc_html__( 'Keywords:', 'sofir' ) . '</strong> ' . \esc_html( implode( ', ', $insights['keywords'] ) ) . '</p>';
            }
            echo '<p><strong>' . \esc_html__( 'Recommended Template:', 'sofir' ) . '</strong> ' . \esc_html( $insights['template'] ) . '</p>';
            echo '<p><strong>' . \esc_html__( 'SEO Score:', 'sofir' ) . '</strong> ' . \esc_html( $insights['seo_score'] ) . '/100</p>';

            if ( ! empty( $insights['recommendations'] ) ) {
                echo '<ul class="sofir-metric-list">';
                foreach ( $insights['recommendations'] as $tip ) {
                    echo '<li>' . \esc_html( $tip ) . '</li>';
                }
                echo '</ul>';
            }

            echo '<p class="description">' . \esc_html__( 'Gunakan endpoint REST /sofir/v1/ai/suggest untuk analisis konten lainnya.', 'sofir' ) . '</p>';
            echo '</div>';
        }

        echo '</div>';
    }

    private function render_notice( string $notice ): void {
        $messages = [
            'seo_saved'        => \__( 'SEO settings updated.', 'sofir' ),
            'redirect_saved'   => \__( 'Redirect added.', 'sofir' ),
            'redirect_deleted' => \__( 'Redirect removed.', 'sofir' ),
        ];

        if ( empty( $messages[ $notice ] ) ) {
            return;
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . \esc_html( $messages[ $notice ] ) . '</p></div>';
    }
}
