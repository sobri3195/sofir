<?php
namespace Sofir\Directory;

use Sofir\Cpt\Manager as CptManager;

class Manager {
    private const OPTION = 'sofir_directory_settings';

    private static ?Manager $instance = null;

    /** @var array<string, mixed> */
    private array $settings = [];

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $defaults       = [
            'provider'      => 'mapbox',
            'mapbox_token'  => '',
            'google_api'    => '',
            'cluster'       => true,
        ];
        $stored         = \get_option( self::OPTION, [] );
        $this->settings = \wp_parse_args( is_array( $stored ) ? $stored : [], $defaults );
    }

    public function boot(): void {
        \add_action( 'admin_post_sofir_save_directory_settings', [ $this, 'handle_settings_save' ] );
        \add_shortcode( 'sofir_directory_map', [ $this, 'render_map_shortcode' ] );
        \add_shortcode( 'sofir_directory_filters', [ $this, 'render_filter_shortcode' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
        \add_action( 'comment_form_after_fields', [ $this, 'render_review_field' ] );
        \add_action( 'comment_form_logged_in_after', [ $this, 'render_review_field' ] );
        \add_action( 'comment_post', [ $this, 'store_review_meta' ], 10, 3 );
        \add_action( 'edit_comment', [ $this, 'store_review_meta_on_edit' ], 10, 2 );
        \add_action( 'trashed_comment', [ $this, 'recalculate_rating_on_comment_change' ] );
        \add_action( 'deleted_comment', [ $this, 'recalculate_rating_on_comment_change' ] );
        \add_action( 'transition_comment_status', [ $this, 'handle_comment_transition' ], 10, 3 );
    }

    public function get_settings(): array {
        return $this->settings;
    }

    public function handle_settings_save(): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Unauthorized', 'sofir' ) );
        }

        \check_admin_referer( 'sofir_directory_settings', '_sofir_nonce' );

        $provider = isset( $_POST['sofir_provider'] ) ? \sanitize_key( $_POST['sofir_provider'] ) : 'mapbox';
        $mapbox   = isset( $_POST['sofir_mapbox_token'] ) ? \sanitize_text_field( $_POST['sofir_mapbox_token'] ) : '';
        $google   = isset( $_POST['sofir_google_api'] ) ? \sanitize_text_field( $_POST['sofir_google_api'] ) : '';
        $cluster  = isset( $_POST['sofir_cluster'] );

        $this->settings = [
            'provider'     => in_array( $provider, [ 'mapbox', 'google' ], true ) ? $provider : 'mapbox',
            'mapbox_token' => $mapbox,
            'google_api'   => $google,
            'cluster'      => $cluster,
        ];

        \update_option( self::OPTION, $this->settings );

        $redirect = \add_query_arg(
            [
                'page'         => 'sofir-dashboard',
                'tab'          => 'content',
                'sofir_notice' => 'directory_settings_saved',
            ],
            \admin_url( 'admin.php' )
        );

        \wp_safe_redirect( $redirect );
        exit;
    }

    public function register_assets(): void {
        $handle = 'sofir-directory';

        if ( ! \wp_style_is( $handle, 'registered' ) ) {
            \wp_register_style(
                $handle,
                SOFIR_ASSETS_URL . 'css/directory.css',
                [],
                SOFIR_VERSION
            );
        }

        if ( ! \wp_script_is( $handle, 'registered' ) ) {
            \wp_register_script(
                $handle,
                SOFIR_ASSETS_URL . 'js/directory.js',
                [ 'wp-api-fetch' ],
                SOFIR_VERSION,
                true
            );

            \wp_localize_script(
                $handle,
                'SOFIR_DIRECTORY_DATA',
                [
                    'provider'    => $this->settings['provider'],
                    'mapboxToken' => $this->settings['mapbox_token'],
                    'googleKey'   => $this->settings['google_api'],
                    'cluster'     => (bool) $this->settings['cluster'],
                    'restRoot'    => \esc_url_raw( \rest_url() ),
                ]
            );
        }
    }

    public function render_map_shortcode( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'post_type' => 'listing',
                'zoom'      => 12,
            ],
            $atts,
            'sofir_directory_map'
        );

        \wp_enqueue_style( 'sofir-directory' );
        \wp_enqueue_script( 'sofir-directory' );

        $post_type = \sanitize_key( $atts['post_type'] );
        $filters   = $this->get_filters_for_post_type( $post_type );
        $rest_base = CptManager::instance()->get_rest_base( $post_type );

        return sprintf(
            '<div class="sofir-directory-map" data-post-type="%1$s" data-rest-base="%2$s" data-zoom="%3$d" data-filters="%4$s"></div>',
            \esc_attr( $post_type ),
            \esc_attr( $rest_base ),
            (int) $atts['zoom'],
            \esc_attr( \wp_json_encode( $filters ) )
        );
    }

    public function render_filter_shortcode( array $atts ): string {
        $atts = \shortcode_atts(
            [
                'post_type' => 'listing',
            ],
            $atts,
            'sofir_directory_filters'
        );

        $post_type = \sanitize_key( $atts['post_type'] );
        $fields    = CptManager::instance()->get_post_type_fields( $post_type );
        $filters   = $this->get_filters_for_post_type( $post_type );

        if ( empty( $filters ) ) {
            return '';
        }

        \wp_enqueue_style( 'sofir-directory' );
        \wp_enqueue_script( 'sofir-directory' );

        ob_start();
        echo '<form class="sofir-directory-filters" data-post-type="' . \esc_attr( $post_type ) . '">';

        foreach ( $filters as $filter ) {
            $label = $filter;
            $label = preg_replace( '/^sofir_' . preg_quote( $post_type, '/' ) . '_/i', '', $label );
            $label = preg_replace( '/^sofir_tax_/i', '', (string) $label );
            $label = ucwords( str_replace( '_', ' ', (string) $label ) );
            echo '<label class="sofir-directory-filter">';
            echo '<span>' . \esc_html( $label ) . '</span>';

            if ( false !== strpos( $filter, 'rating' ) ) {
                echo '<select name="' . \esc_attr( $filter ) . '">';
                echo '<option value="">' . \esc_html__( 'Any rating', 'sofir' ) . '</option>';
                for ( $i = 5; $i >= 1; $i-- ) {
                    echo '<option value="' . \esc_attr( $i ) . '">' . \esc_html( $i . '+' ) . '</option>';
                }
                echo '</select>';
            } else {
                echo '<input type="text" name="' . \esc_attr( $filter ) . '" />';
            }

            echo '</label>';
        }

        echo '<button type="submit" class="button button-primary">' . \esc_html__( 'Filter', 'sofir' ) . '</button>';
        echo '</form>';

        return (string) ob_get_clean();
    }

    public function render_review_field(): void {
        echo '<p class="comment-form-rating"><label for="sofir_review_rating">' . \esc_html__( 'Rating', 'sofir' ) . '</label>';
        echo '<select name="sofir_review_rating" id="sofir_review_rating">';
        echo '<option value="">' . \esc_html__( 'Choose…', 'sofir' ) . '</option>';
        for ( $i = 5; $i >= 1; $i-- ) {
            echo '<option value="' . \esc_attr( $i ) . '">' . \esc_html( $i ) . '</option>';
        }
        echo '</select></p>';
    }

    public function store_review_meta( int $comment_id, int $comment_approved, array $commentdata ): void {
        if ( empty( $_POST['sofir_review_rating'] ) ) {
            return;
        }

        $rating = (float) $_POST['sofir_review_rating'];

        if ( $rating < 1 || $rating > 5 ) {
            return;
        }

        \update_comment_meta( $comment_id, 'sofir_rating', $rating );
        $this->update_post_rating( $commentdata['comment_post_ID'] );
    }

    public function store_review_meta_on_edit( int $comment_id, array $commentdata ): void {
        if ( isset( $_POST['sofir_review_rating'] ) ) {
            $rating = (float) $_POST['sofir_review_rating'];
            \update_comment_meta( $comment_id, 'sofir_rating', min( 5, max( 1, $rating ) ) );
            $this->update_post_rating( $commentdata['comment_post_ID'] );
        }
    }

    public function recalculate_rating_on_comment_change( int $comment_id ): void {
        $comment = \get_comment( $comment_id );

        if ( ! $comment ) {
            return;
        }

        $this->update_post_rating( (int) $comment->comment_post_ID );
    }

    public function handle_comment_transition( string $new_status, string $old_status, \WP_Comment $comment ): void {
        if ( 'approve' === $new_status || 'approve' === $old_status ) {
            $this->update_post_rating( (int) $comment->comment_post_ID );
        }
    }

    private function update_post_rating( int $post_id ): void {
        $comments = \get_comments(
            [
                'post_id' => $post_id,
                'status'  => 'approve',
                'meta_key' => 'sofir_rating',
            ]
        );

        if ( empty( $comments ) ) {
            \delete_post_meta( $post_id, 'sofir_review_average' );
            return;
        }

        $sum = 0;
        $cnt = 0;

        foreach ( $comments as $comment ) {
            $rating = (float) \get_comment_meta( $comment->comment_ID, 'sofir_rating', true );
            if ( $rating > 0 ) {
                $sum += $rating;
                $cnt++;
            }
        }

        if ( $cnt > 0 ) {
            \update_post_meta( $post_id, 'sofir_review_average', round( $sum / $cnt, 2 ) );
        }
    }

    /**
     * @return array<int, string>
     */
    private function get_filters_for_post_type( string $post_type ): array {
        $query_vars = CptManager::instance()->get_filter_query_vars( $post_type );
        $schedule   = CptManager::instance()->get_schedule_query_var( $post_type );
        $taxes      = CptManager::instance()->get_taxonomy_query_vars( $post_type );

        if ( $schedule ) {
            $query_vars[] = $schedule;
        }

        return array_values( array_unique( array_merge( $query_vars, $taxes ) ) );
    }
}
