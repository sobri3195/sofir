<?php
namespace Sofir\Enhancement;

use Sofir\Cpt\Manager as CptManager;

class Dashboard {
    private static ?Dashboard $instance = null;

    public static function instance(): Dashboard {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_shortcode( 'sofir_user_dashboard', [ $this, 'render_dashboard' ] );
    }

    public function render_dashboard(): string {
        if ( ! \is_user_logged_in() ) {
            return \do_shortcode( '[sofir_login_form]' );
        }

        $user      = \wp_get_current_user();
        $post_map  = $this->collect_user_content( $user->ID );
        $metrics   = $this->collect_quick_metrics( $user->ID, $post_map );
        $dashboard = '<div class="sofir-dashboard">';

        $dashboard .= '<section class="sofir-dashboard__hero">';
        $dashboard .= '<h2>' . \esc_html__( 'Hello,', 'sofir' ) . ' ' . \esc_html( $user->display_name ?: $user->user_login ) . '</h2>';
        $dashboard .= '<p>' . \esc_html__( 'Kelola konten, membership, dan statistik situs Anda melalui panel terpadu.', 'sofir' ) . '</p>';
        $dashboard .= '</section>';

        $dashboard .= '<section class="sofir-dashboard__metrics">';
        foreach ( $metrics as $metric ) {
            $dashboard .= '<div class="sofir-dashboard__metric">';
            $dashboard .= '<span class="sofir-dashboard__metric-label">' . \esc_html( $metric['label'] ) . '</span>';
            $dashboard .= '<strong class="sofir-dashboard__metric-value">' . \esc_html( $metric['value'] ) . '</strong>';
            if ( ! empty( $metric['description'] ) ) {
                $dashboard .= '<small>' . \esc_html( $metric['description'] ) . '</small>';
            }
            $dashboard .= '</div>';
        }
        $dashboard .= '</section>';

        if ( ! empty( $post_map ) ) {
            $dashboard .= '<section class="sofir-dashboard__content">';
            $dashboard .= '<h3>' . \esc_html__( 'Your latest content', 'sofir' ) . '</h3>';
            foreach ( $post_map as $post_type => $posts ) {
                $dashboard .= '<div class="sofir-dashboard__content-group">';
                $dashboard .= '<h4>' . \esc_html( $post_type ) . '</h4>';

                if ( empty( $posts ) ) {
                    $dashboard .= '<p>' . \esc_html__( 'No entries yet.', 'sofir' ) . '</p>';
                } else {
                    $dashboard .= '<ul class="sofir-dashboard__list">';
                    foreach ( $posts as $post ) {
                        $dashboard .= '<li><a href="' . \esc_url( \get_edit_post_link( $post->ID, 'raw' ) ) . '">' . \esc_html( $post->post_title ) . '</a> <span class="status">' . \esc_html( ucfirst( $post->post_status ) ) . '</span></li>';
                    }
                    $dashboard .= '</ul>';
                }

                $dashboard .= '</div>';
            }
            $dashboard .= '</section>';
        }

        $dashboard .= '<section class="sofir-dashboard__cta">';
        $dashboard .= '<h3>' . \esc_html__( 'Need analytics or membership insights?', 'sofir' ) . '</h3>';
        $dashboard .= '<p>' . \esc_html__( 'Aktifkan modul SEO, Analytics, dan Membership untuk melihat statistik kunjungan, heatmap, serta paket langganan.', 'sofir' ) . '</p>';
        $dashboard .= '</section>';

        $dashboard .= '</div>';

        return $dashboard;
    }

    /**
     * @return array<string, array<int, \WP_Post>>
     */
    private function collect_user_content( int $user_id ): array {
        $post_types = array_keys( CptManager::instance()->get_post_types() );

        if ( empty( $post_types ) ) {
            $post_types = [ 'post', 'page' ];
        }

        $map = [];

        foreach ( $post_types as $post_type ) {
            $posts = \get_posts(
                [
                    'author'         => $user_id,
                    'post_type'      => $post_type,
                    'post_status'    => [ 'publish', 'draft', 'pending' ],
                    'posts_per_page' => 5,
                    'orderby'        => 'modified',
                    'order'          => 'DESC',
                ]
            );

            $map[ $post_type ] = $posts;
        }

        return $map;
    }

    /**
     * @param array<string, array<int, \WP_Post>> $post_map
     *
     * @return array<int, array<string, string>>
     */
    private function collect_quick_metrics( int $user_id, array $post_map ): array {
        $total_posts = 0;

        foreach ( $post_map as $posts ) {
            $total_posts += count( $posts );
        }

        $last_login = \get_user_meta( $user_id, 'sofir_last_login', true );

        if ( ! $last_login ) {
            $last_login = \current_time( 'mysql' );
        }

        return [
            [
                'label'       => \__( 'Total Entries', 'sofir' ),
                'value'       => (string) $total_posts,
                'description' => \__( 'Konten yang Anda kelola di seluruh CPT.', 'sofir' ),
            ],
            [
                'label'       => \__( 'Membership Level', 'sofir' ),
                'value'       => \esc_html( implode( ', ', $this->get_user_roles( $user_id ) ) ),
                'description' => \__( 'Kelola paket melalui modul Membership.', 'sofir' ),
            ],
            [
                'label'       => \__( 'Last Activity', 'sofir' ),
                'value'       => \esc_html( \date_i18n( \get_option( 'date_format' ) . ' H:i', strtotime( $last_login ) ) ),
                'description' => \__( 'Terekam otomatis untuk insight keamanan.', 'sofir' ),
            ],
        ];
    }

    private function get_user_roles( int $user_id ): array {
        $user = \get_userdata( $user_id );

        if ( ! $user || empty( $user->roles ) ) {
            return [ \__( 'Member', 'sofir' ) ];
        }

        return array_map( 'ucfirst', $user->roles );
    }
}
