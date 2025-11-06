<?php
namespace Sofir\Admin;

use Sofir\Cpt\Manager as CptManager;
use Sofir\Directory\Manager as DirectoryManager;

class ContentPanel {
    private static ?ContentPanel $instance = null;

    public static function instance(): ContentPanel {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'admin_post_sofir_save_cpt', [ $this, 'handle_save_cpt' ] );
        \add_action( 'admin_post_sofir_delete_cpt', [ $this, 'handle_delete_cpt' ] );
        \add_action( 'admin_post_sofir_save_taxonomy', [ $this, 'handle_save_taxonomy' ] );
        \add_action( 'admin_post_sofir_delete_taxonomy', [ $this, 'handle_delete_taxonomy' ] );
    }

    public function render(): void {
        $manager            = CptManager::instance();
        $post_types         = $manager->get_post_types();
        $taxonomies         = $manager->get_taxonomies();
        $field_catalog      = $manager->get_field_catalog();
        $directory_settings = DirectoryManager::instance()->get_settings();
        $notice             = isset( $_GET['sofir_notice'] ) ? \sanitize_key( $_GET['sofir_notice'] ) : '';

        if ( $notice ) {
            $this->render_notice( $notice );
        }

        $this->render_statistics_dashboard( $post_types );

        echo '<div class="sofir-grid">';
        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Custom Post Type Builder', 'sofir' ) . '</h2>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '">';
        echo '<input type="hidden" name="action" value="sofir_save_cpt" />';
        \wp_nonce_field( 'sofir_save_cpt', '_sofir_nonce' );

        echo '<div class="sofir-field-group">';
        $this->render_input_field( 'post_type', \__( 'Slug', 'sofir' ), true, 'text', [ 'placeholder' => 'places' ] );
        $this->render_input_field( 'singular', \__( 'Singular Label', 'sofir' ), true, 'text', [ 'placeholder' => 'Place' ] );
        $this->render_input_field( 'plural', \__( 'Plural Label', 'sofir' ), true, 'text', [ 'placeholder' => 'Places' ] );
        $this->render_input_field( 'menu_icon', \__( 'Dashicon', 'sofir' ), false, 'text', [ 'placeholder' => 'dashicons-location-alt' ] );
        $this->render_input_field( 'supports', \__( 'Supports', 'sofir' ), false, 'checkbox-group', [
            'options' => [
                'title'           => \__( 'Title', 'sofir' ),
                'editor'          => \__( 'Editor', 'sofir' ),
                'thumbnail'       => \__( 'Featured Image', 'sofir' ),
                'excerpt'         => \__( 'Excerpt', 'sofir' ),
                'revisions'       => \__( 'Revisions', 'sofir' ),
                'author'          => \__( 'Author', 'sofir' ),
                'comments'        => \__( 'Comments', 'sofir' ),
                'custom-fields'   => \__( 'Custom Fields', 'sofir' ),
                'page-attributes' => \__( 'Page Attributes', 'sofir' ),
            ],
        ] );
        $this->render_input_field( 'taxonomies', \__( 'Attach Taxonomies', 'sofir' ), false, 'text', [ 'placeholder' => 'listing_category,listing_location' ] );
        $this->render_input_field( 'rest_base', \__( 'REST Base', 'sofir' ), false, 'text', [ 'placeholder' => 'places' ] );
        $this->render_toggle_field( 'has_archive', \__( 'Enable Archive', 'sofir' ) );
        $this->render_toggle_field( 'hierarchical', \__( 'Hierarchical', 'sofir' ) );
        echo '</div>';

        echo '<hr />';
        echo '<h3>' . \esc_html__( 'Automatic Fields & Filters', 'sofir' ) . '</h3>';
        echo '<p>' . \esc_html__( 'Pilih field bawaan yang akan ditambahkan otomatis beserta filter dynamic-nya.', 'sofir' ) . '</p>';
        echo '<div class="sofir-field-catalog">';

        foreach ( $field_catalog as $field_key => $field ) {
            echo '<div class="sofir-field-catalog__item">';
            echo '<label class="sofir-field-catalog__heading">';
            echo '<input type="checkbox" name="fields[]" value="' . \esc_attr( $field_key ) . '" />';
            echo '<span>' . \esc_html( $field['label'] ?? $field_key ) . '</span>';
            echo '</label>';
            if ( ! empty( $field['description'] ) ) {
                echo '<p class="description">' . \esc_html( $field['description'] ) . '</p>';
            }
            if ( ! empty( $field['filter'] ) ) {
                $filter_key = \esc_attr( $field['filter']['query_var'] ?? $field_key );
                echo '<label class="sofir-field-catalog__filter">';
                echo '<input type="checkbox" name="filters[]" value="' . $filter_key . '" />';
                echo '<span>' . \esc_html__( 'Aktifkan sebagai filter dinamis', 'sofir' ) . '</span>';
                if ( 'schedule' === ( $field['filter']['mode'] ?? '' ) ) {
                    echo '<small>' . \esc_html__( 'Mengaktifkan filter buka sekarang (open now).', 'sofir' ) . '</small>';
                }
                echo '</label>';
            }
            echo '</div>';
        }

        echo '</div>';

        echo '<p class="submit">';
        echo '<button type="submit" class="button button-primary">' . \esc_html__( 'Save Post Type', 'sofir' ) . '</button>';
        echo '</p>';
        echo '</form>';
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Registered Post Types', 'sofir' ) . '</h2>';
        if ( empty( $post_types ) ) {
            echo '<p>' . \esc_html__( 'Belum ada post type terdaftar.', 'sofir' ) . '</p>';
        } else {
            echo '<table class="widefat sofir-table">';
            echo '<thead><tr><th>' . \esc_html__( 'Slug', 'sofir' ) . '</th><th>' . \esc_html__( 'Fields', 'sofir' ) . '</th><th>' . \esc_html__( 'Filters', 'sofir' ) . '</th><th class="column-actions">' . \esc_html__( 'Actions', 'sofir' ) . '</th></tr></thead>';
            echo '<tbody>';
            foreach ( $post_types as $slug => $definition ) {
                $fields  = isset( $definition['fields'] ) ? array_keys( $definition['fields'] ) : [];
                $filters = [];
                foreach ( $definition['fields'] ?? [] as $field_key => $field ) {
                    if ( ! empty( $field['filterable'] ) && ! empty( $field['filter'] ) ) {
                        $filters[] = $field['filter']['query_var'] ?? $field_key;
                    }
                }
                echo '<tr>';
                echo '<td><strong>' . \esc_html( $slug ) . '</strong></td>';
                echo '<td>' . \esc_html( implode( ', ', $fields ) ?: '—' ) . '</td>';
                echo '<td>' . \esc_html( implode( ', ', $filters ) ?: '—' ) . '</td>';
                echo '<td class="column-actions">';
                $delete_url = \wp_nonce_url(
                    \add_query_arg(
                        [
                            'action'    => 'sofir_delete_cpt',
                            'post_type' => $slug,
                        ],
                        \admin_url( 'admin-post.php' )
                    ),
                    'sofir_delete_cpt_' . $slug,
                    '_sofir_nonce'
                );
                echo '<a class="delete" href="' . \esc_url( $delete_url ) . '">' . \esc_html__( 'Delete', 'sofir' ) . '</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Taxonomy Builder', 'sofir' ) . '</h2>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '">';
        echo '<input type="hidden" name="action" value="sofir_save_taxonomy" />';
        \wp_nonce_field( 'sofir_save_taxonomy', '_sofir_nonce' );

        echo '<div class="sofir-field-group">';
        $this->render_input_field( 'taxonomy', \__( 'Slug', 'sofir' ), true, 'text', [ 'placeholder' => 'category' ] );
        $this->render_input_field( 'taxonomy_singular', \__( 'Singular Label', 'sofir' ), true, 'text', [ 'placeholder' => 'Category' ] );
        $this->render_input_field( 'taxonomy_plural', \__( 'Plural Label', 'sofir' ), true, 'text', [ 'placeholder' => 'Categories' ] );
        $this->render_input_field( 'taxonomy_objects', \__( 'Attached Post Types', 'sofir' ), true, 'text', [ 'placeholder' => 'listing,profile' ] );
        $this->render_toggle_field( 'taxonomy_hierarchical', \__( 'Hierarchical', 'sofir' ) );
        $this->render_toggle_field( 'taxonomy_filterable', \__( 'Enable Filter', 'sofir' ), true );
        echo '</div>';

        echo '<p class="submit">';
        echo '<button type="submit" class="button button-secondary">' . \esc_html__( 'Save Taxonomy', 'sofir' ) . '</button>';
        echo '</p>';
        echo '</form>';
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Registered Taxonomies', 'sofir' ) . '</h2>';
        if ( empty( $taxonomies ) ) {
            echo '<p>' . \esc_html__( 'Belum ada taxonomy terdaftar.', 'sofir' ) . '</p>';
        } else {
            echo '<table class="widefat sofir-table">';
            echo '<thead><tr><th>' . \esc_html__( 'Slug', 'sofir' ) . '</th><th>' . \esc_html__( 'Object Types', 'sofir' ) . '</th><th>' . \esc_html__( 'Filterable', 'sofir' ) . '</th><th class="column-actions">' . \esc_html__( 'Actions', 'sofir' ) . '</th></tr></thead>';
            echo '<tbody>';
            foreach ( $taxonomies as $slug => $definition ) {
                $objects    = isset( $definition['object_type'] ) ? implode( ', ', (array) $definition['object_type'] ) : '—';
                $filterable = ! empty( $definition['filterable'] ) ? \esc_html__( 'Yes', 'sofir' ) : \esc_html__( 'No', 'sofir' );
                $delete_url = \wp_nonce_url(
                    \add_query_arg(
                        [
                            'action'   => 'sofir_delete_taxonomy',
                            'taxonomy' => $slug,
                        ],
                        \admin_url( 'admin-post.php' )
                    ),
                    'sofir_delete_taxonomy_' . $slug,
                    '_sofir_nonce'
                );
                echo '<tr>';
                echo '<td><strong>' . \esc_html( $slug ) . '</strong></td>';
                echo '<td>' . \esc_html( $objects ) . '</td>';
                echo '<td>' . \esc_html( $filterable ) . '</td>';
                echo '<td class="column-actions"><a class="delete" href="' . \esc_url( $delete_url ) . '">' . \esc_html__( 'Delete', 'sofir' ) . '</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>' . \esc_html__( 'Directory & Map Settings', 'sofir' ) . '</h2>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '">';
        echo '<input type="hidden" name="action" value="sofir_save_directory_settings" />';
        \wp_nonce_field( 'sofir_directory_settings', '_sofir_nonce' );

        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Map Provider', 'sofir' ) . '</span>';
        echo '<select name="sofir_provider">';
        $provider = $directory_settings['provider'] ?? 'mapbox';
        echo '<option value="mapbox"' . \selected( $provider, 'mapbox', false ) . '>' . \esc_html__( 'Mapbox', 'sofir' ) . '</option>';
        echo '<option value="google"' . \selected( $provider, 'google', false ) . '>' . \esc_html__( 'Google Maps', 'sofir' ) . '</option>';
        echo '</select>';
        echo '</label>';

        $this->render_input_field( 'sofir_mapbox_token', \__( 'Mapbox Public Token', 'sofir' ), false, 'text', [ 'placeholder' => 'pk.xxx', 'value' => $directory_settings['mapbox_token'] ?? '' ] );
        $this->render_input_field( 'sofir_google_api', \__( 'Google Maps API Key', 'sofir' ), false, 'text', [ 'placeholder' => 'AIza...', 'value' => $directory_settings['google_api'] ?? '' ] );
        echo '<label class="sofir-toggle">';
        echo '<input type="checkbox" name="sofir_cluster" value="1" ' . \checked( ! empty( $directory_settings['cluster'] ), true, false ) . ' />';
        echo '<span>' . \esc_html__( 'Enable marker clustering', 'sofir' ) . '</span>';
        echo '</label>';

        echo '<p class="submit"><button type="submit" class="button button-secondary">' . \esc_html__( 'Save Directory Settings', 'sofir' ) . '</button></p>';
        echo '</form>';
        echo '</div>';

        echo '</div>';
    }

    private function render_statistics_dashboard( array $post_types ): void {
        $manager = CptManager::instance();
        $cpt_stats = $manager->get_cpt_statistics();
        $tax_stats = $manager->get_taxonomy_statistics();

        echo '<div class="sofir-statistics-dashboard" style="margin-bottom: 20px;">';
        echo '<h2>' . \esc_html__( 'Content Statistics', 'sofir' ) . '</h2>';

        echo '<div class="sofir-stats-summary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">';
        
        $stats = $this->get_content_statistics( $post_types );
        foreach ( $stats as $stat ) {
            echo '<div class="sofir-stat-card" style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; box-shadow: 0 1px 1px rgba(0,0,0,.04);">';
            echo '<div class="sofir-stat-icon" style="font-size: 24px; margin-bottom: 10px;">' . $stat['icon'] . '</div>';
            echo '<div class="sofir-stat-value" style="font-size: 32px; font-weight: bold; color: #2271b1; margin-bottom: 5px;">' . \esc_html( $stat['value'] ) . '</div>';
            echo '<div class="sofir-stat-label" style="color: #646970; font-size: 14px;">' . \esc_html( $stat['label'] ) . '</div>';
            if ( ! empty( $stat['link'] ) ) {
                echo '<a href="' . \esc_url( $stat['link'] ) . '" style="display: inline-block; margin-top: 10px; text-decoration: none;">' . \esc_html__( 'View All', 'sofir' ) . ' →</a>';
            }
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="sofir-stats-detailed" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">';
        
        echo '<div class="sofir-card">';
        echo '<h3>' . \esc_html__( 'Custom Post Types Details', 'sofir' ) . '</h3>';
        if ( empty( $cpt_stats ) ) {
            echo '<p>' . \esc_html__( 'No custom post types registered yet.', 'sofir' ) . '</p>';
        } else {
            echo '<table class="widefat" style="margin-top: 10px;">';
            echo '<thead><tr>';
            echo '<th>' . \esc_html__( 'Post Type', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Published', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Draft', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Fields', 'sofir' ) . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            
            foreach ( $cpt_stats as $slug => $cpt ) {
                echo '<tr>';
                echo '<td><strong>' . \esc_html( $cpt['label'] ) . '</strong><br><small>' . \esc_html( $slug ) . '</small></td>';
                echo '<td>' . \esc_html( $cpt['published'] ) . '</td>';
                echo '<td>' . \esc_html( $cpt['draft'] ) . '</td>';
                echo '<td>' . \esc_html( count( $cpt['fields'] ) ) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        }
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h3>' . \esc_html__( 'Taxonomies Details', 'sofir' ) . '</h3>';
        if ( empty( $tax_stats ) ) {
            echo '<p>' . \esc_html__( 'No taxonomies registered yet.', 'sofir' ) . '</p>';
        } else {
            echo '<table class="widefat" style="margin-top: 10px;">';
            echo '<thead><tr>';
            echo '<th>' . \esc_html__( 'Taxonomy', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Terms', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Type', 'sofir' ) . '</th>';
            echo '<th>' . \esc_html__( 'Filterable', 'sofir' ) . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            
            foreach ( $tax_stats as $slug => $tax ) {
                echo '<tr>';
                echo '<td><strong>' . \esc_html( $tax['label'] ) . '</strong><br><small>' . \esc_html( $slug ) . '</small></td>';
                echo '<td>' . \esc_html( $tax['term_count'] ) . '</td>';
                echo '<td>' . ( $tax['hierarchical'] ? \esc_html__( 'Hierarchical', 'sofir' ) : \esc_html__( 'Flat', 'sofir' ) ) . '</td>';
                echo '<td>' . ( $tax['filterable'] ? '✓' : '—' ) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        }
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }

    private function get_content_statistics( array $post_types ): array {
        $stats = [];

        $total_posts = 0;
        foreach ( $post_types as $slug => $definition ) {
            $count = \wp_count_posts( $slug );
            $published = isset( $count->publish ) ? (int) $count->publish : 0;
            $total_posts += $published;

            $object = \get_post_type_object( $slug );
            $label = $object ? $object->labels->name : ucfirst( $slug );

            $stats[] = [
                'icon'  => '📄',
                'value' => $published,
                'label' => $label,
                'link'  => \admin_url( 'edit.php?post_type=' . $slug ),
            ];
        }

        array_unshift( $stats, [
            'icon'  => '📊',
            'value' => $total_posts,
            'label' => \__( 'Total Content Items', 'sofir' ),
            'link'  => '',
        ] );

        $user_count = \count_users();
        $stats[] = [
            'icon'  => '👥',
            'value' => $user_count['total_users'] ?? 0,
            'label' => \__( 'Total Users', 'sofir' ),
            'link'  => \admin_url( 'users.php' ),
        ];

        $comment_count = \wp_count_comments();
        $stats[] = [
            'icon'  => '💬',
            'value' => $comment_count->approved ?? 0,
            'label' => \__( 'Comments', 'sofir' ),
            'link'  => \admin_url( 'edit-comments.php' ),
        ];

        return $stats;
    }

    public function handle_save_cpt(): void {
        $this->verify_request( 'sofir_save_cpt' );

        $payload = [
            'slug'        => $_POST['post_type'] ?? '',
            'singular'    => $_POST['singular'] ?? '',
            'plural'      => $_POST['plural'] ?? '',
            'menu_icon'   => $_POST['menu_icon'] ?? '',
            'supports'    => $_POST['supports'] ?? [],
            'taxonomies'  => $this->explode_list( $_POST['taxonomies'] ?? '' ),
            'has_archive' => isset( $_POST['has_archive'] ),
            'hierarchical'=> isset( $_POST['hierarchical'] ),
            'rest_base'   => $_POST['rest_base'] ?? '',
            'rewrite'     => $_POST['post_type'] ?? '',
            'fields'      => $_POST['fields'] ?? [],
            'filters'     => $_POST['filters'] ?? [],
        ];

        CptManager::instance()->save_post_type( $payload );

        $this->redirect_with_notice( 'content', 'saved' );
    }

    public function handle_delete_cpt(): void {
        $slug = isset( $_GET['post_type'] ) ? \sanitize_key( $_GET['post_type'] ) : '';
        $this->verify_request( 'sofir_delete_cpt_' . $slug );

        if ( $slug ) {
            CptManager::instance()->delete_post_type( $slug );
        }

        $this->redirect_with_notice( 'content', 'deleted' );
    }

    public function handle_save_taxonomy(): void {
        $this->verify_request( 'sofir_save_taxonomy' );

        $payload = [
            'slug'          => $_POST['taxonomy'] ?? '',
            'singular'      => $_POST['taxonomy_singular'] ?? '',
            'plural'        => $_POST['taxonomy_plural'] ?? '',
            'object_type'   => $this->explode_list( $_POST['taxonomy_objects'] ?? '' ),
            'hierarchical'  => isset( $_POST['taxonomy_hierarchical'] ),
            'filterable'    => isset( $_POST['taxonomy_filterable'] ),
            'rewrite'       => $_POST['taxonomy'] ?? '',
        ];

        CptManager::instance()->save_taxonomy( $payload );

        $this->redirect_with_notice( 'content', 'taxonomy_saved' );
    }

    public function handle_delete_taxonomy(): void {
        $slug = isset( $_GET['taxonomy'] ) ? \sanitize_key( $_GET['taxonomy'] ) : '';
        $this->verify_request( 'sofir_delete_taxonomy_' . $slug );

        if ( $slug ) {
            CptManager::instance()->delete_taxonomy( $slug );
        }

        $this->redirect_with_notice( 'content', 'taxonomy_deleted' );
    }

    private function render_notice( string $notice ): void {
        $messages = [
            'saved'            => \__( 'Post type saved successfully.', 'sofir' ),
            'deleted'          => \__( 'Post type deleted.', 'sofir' ),
            'taxonomy_saved'   => \__( 'Taxonomy saved successfully.', 'sofir' ),
            'taxonomy_deleted' => \__( 'Taxonomy deleted.', 'sofir' ),
            'directory_settings_saved' => \__( 'Directory settings updated.', 'sofir' ),
            'cpt_imported'     => isset( $_GET['sofir_message'] ) ? urldecode( $_GET['sofir_message'] ) : \__( 'CPT package imported successfully.', 'sofir' ),
        ];

        $message = $messages[ $notice ] ?? '';

        if ( $message ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . \esc_html( $message ) . '</p></div>';
        }
    }

    private function render_input_field( string $name, string $label, bool $required = false, string $type = 'text', array $args = [] ): void {
        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html( $label );
        if ( $required ) {
            echo ' <span class="required">*</span>';
        }
        echo '</span>';

        switch ( $type ) {
            case 'checkbox-group':
                echo '<div class="sofir-checkbox-group">';
                foreach ( $args['options'] ?? [] as $value => $label_text ) {
                    echo '<label><input type="checkbox" name="' . \esc_attr( $name ) . '[]" value="' . \esc_attr( $value ) . '" /> ' . \esc_html( $label_text ) . '</label>';
                }
                echo '</div>';
                break;
            default:
                $attributes = [
                    'type'  => $type,
                    'name'  => $name,
                    'id'    => $name,
                    'class' => 'regular-text',
                ];

                if ( ! empty( $args['placeholder'] ) ) {
                    $attributes['placeholder'] = $args['placeholder'];
                }

                if ( isset( $args['value'] ) ) {
                    $attributes['value'] = $args['value'];
                }

                if ( $required ) {
                    $attributes['required'] = 'required';
                }

                echo '<input ' . $this->attributes_to_string( $attributes ) . ' />';
                break;
        }

        echo '</label>';
    }

    private function render_toggle_field( string $name, string $label, bool $default = false ): void {
        echo '<label class="sofir-toggle">';
        echo '<input type="checkbox" name="' . \esc_attr( $name ) . '" value="1" ' . \checked( $default, true, false ) . ' />';
        echo '<span>' . \esc_html( $label ) . '</span>';
        echo '</label>';
    }

    private function verify_request( string $nonce_action ): void {
        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'Anda tidak memiliki izin.', 'sofir' ) );
        }

        $nonce = $_REQUEST['_sofir_nonce'] ?? '';

        if ( ! \wp_verify_nonce( $nonce, $nonce_action ) ) {
            \wp_die( \esc_html__( 'Nonce tidak valid.', 'sofir' ) );
        }
    }

    private function redirect_with_notice( string $tab, string $notice ): void {
        $url = \add_query_arg(
            [
                'page'         => 'sofir-dashboard',
                'tab'          => $tab,
                'sofir_notice' => $notice,
            ],
            \admin_url( 'admin.php' )
        );

        \wp_safe_redirect( $url );
        exit;
    }

    private function attributes_to_string( array $attributes ): string {
        $compiled = [];

        foreach ( $attributes as $key => $value ) {
            if ( is_bool( $value ) ) {
                $compiled[] = $value ? \esc_attr( $key ) : '';
                continue;
            }

            if ( '' === $value || null === $value ) {
                continue;
            }

            $compiled[] = \sprintf( '%s="%s"', \esc_attr( $key ), \esc_attr( (string) $value ) );
        }

        return implode( ' ', array_filter( $compiled ) );
    }

    /**
     * @param string|array $value
     *
     * @return array<int, string>
     */
    private function explode_list( $value ): array {
        if ( \is_array( $value ) ) {
            return array_filter( array_map( 'sanitize_key', $value ) );
        }

        $value = (string) $value;

        if ( '' === $value ) {
            return [];
        }

        $items = array_map( 'trim', explode( ',', $value ) );

        return array_filter( array_map( 'sanitize_key', $items ) );
    }
}
