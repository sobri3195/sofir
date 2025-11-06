<?php
namespace Sofir\Admin;

use Sofir\Cpt\Manager as CptManager;

class LibraryPanel {
    private static ?LibraryPanel $instance = null;

    public static function instance(): LibraryPanel {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'admin_post_sofir_export_cpt', [ $this, 'handle_export_cpt' ] );
        \add_action( 'admin_post_sofir_import_cpt', [ $this, 'handle_import_cpt' ] );
        \add_action( 'wp_ajax_sofir_get_export_preview', [ $this, 'handle_export_preview_ajax' ] );
    }

    public function render(): void {
        $manager    = CptManager::instance();
        $post_types = $manager->get_post_types();
        $notice     = isset( $_GET['sofir_notice'] ) ? \sanitize_key( $_GET['sofir_notice'] ) : '';

        if ( $notice ) {
            $this->render_notice( $notice );
        }

        echo '<div class="sofir-library-panel">';
        echo '<p class="description" style="font-size: 14px; margin-bottom: 20px;">';
        echo \esc_html__( 'Library CPT berisi template-template Custom Post Type siap pakai yang dapat diekspor dan diimpor dengan mudah. Bagikan CPT Anda atau gunakan CPT dari komunitas.', 'sofir' );
        echo '</p>';

        echo '<div class="sofir-grid">';

        echo '<div class="sofir-card">';
        echo '<h2>📦 ' . \esc_html__( 'Export CPT Package', 'sofir' ) . '</h2>';
        echo '<p>' . \esc_html__( 'Ekspor Custom Post Type beserta konten, taxonomies, dan terms ke dalam file ZIP untuk dibagikan atau digunakan di website lain.', 'sofir' ) . '</p>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '" id="sofir-export-form">';
        echo '<input type="hidden" name="action" value="sofir_export_cpt" />';
        \wp_nonce_field( 'sofir_export_cpt', '_sofir_nonce' );

        echo '<div class="sofir-field-group">';
        echo '<label><strong>' . \esc_html__( 'Pilih Post Types untuk Diekspor:', 'sofir' ) . '</strong></label>';
        
        if ( empty( $post_types ) ) {
            echo '<div class="notice notice-warning inline" style="margin: 15px 0; padding: 12px;">';
            echo '<p>' . \esc_html__( 'Tidak ada post type tersedia untuk diekspor. Silakan buat Custom Post Type terlebih dahulu di tab Content.', 'sofir' ) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="sofir-checkbox-group" style="margin: 15px 0;">';
            foreach ( $post_types as $slug => $definition ) {
                $label = $definition['args']['labels']['name'] ?? ucfirst( $slug );
                $field_count = count( $definition['fields'] ?? [] );
                
                echo '<label style="display: block; margin-bottom: 10px; padding: 10px; background: #f9f9f9; border-radius: 4px;">';
                echo '<input type="checkbox" name="export_post_types[]" value="' . \esc_attr( $slug ) . '" class="sofir-export-checkbox" /> ';
                echo '<strong>' . \esc_html( $label ) . '</strong> ';
                echo '<small style="color: #666;">(' . \esc_html( $slug ) . ' • ' . \esc_html( $field_count ) . ' fields)</small>';
                echo '</label>';
            }
            echo '</div>';

            echo '<div id="sofir-export-preview" style="display: none; margin-top: 20px; padding: 15px; background: #f0f0f1; border-radius: 4px;"></div>';

            echo '<label class="sofir-field" style="margin-top: 20px; display: block;">';
            echo '<span>' . \esc_html__( 'Nama File (tanpa ekstensi)', 'sofir' ) . '</span>';
            echo '<input type="text" name="export_filename" value="' . \esc_attr( 'sofir-cpt-' . gmdate( 'Y-m-d' ) ) . '" placeholder="sofir-cpt-export" style="max-width: 400px;" />';
            echo '</label>';
            
            echo '<p class="submit">';
            echo '<button type="button" id="sofir-preview-export" class="button button-secondary" disabled>' . \esc_html__( '👁 Preview Data', 'sofir' ) . '</button> ';
            echo '<button type="submit" class="button button-primary" disabled id="sofir-download-export">' . \esc_html__( '⬇ Download Ekspor', 'sofir' ) . '</button>';
            echo '</p>';
        }
        echo '</div>';
        echo '</form>';
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>📥 ' . \esc_html__( 'Import CPT Package', 'sofir' ) . '</h2>';
        echo '<p>' . \esc_html__( 'Upload dan install paket Custom Post Type dari file ZIP atau JSON yang telah diekspor sebelumnya. Paket akan otomatis mendaftarkan CPT beserta field dan filter.', 'sofir' ) . '</p>';
        echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '" enctype="multipart/form-data">';
        echo '<input type="hidden" name="action" value="sofir_import_cpt" />';
        \wp_nonce_field( 'sofir_import_cpt', '_sofir_nonce' );

        echo '<div class="sofir-field-group">';
        echo '<label class="sofir-field">';
        echo '<span>' . \esc_html__( 'Pilih File ZIP atau JSON:', 'sofir' ) . '</span>';
        echo '<input type="file" name="import_file" accept=".zip,.json" required style="max-width: 400px;" />';
        echo '<small class="description" style="display: block; margin-top: 8px;">' . \esc_html__( 'Upload file ZIP atau JSON hasil ekspor dari SOFIR. File akan diproses dan CPT akan didaftarkan secara otomatis.', 'sofir' ) . '</small>';
        echo '</label>';

        echo '<div class="notice notice-info inline" style="margin: 20px 0; padding: 12px;">';
        echo '<h4 style="margin-top: 0;">' . \esc_html__( '💡 Tips Import:', 'sofir' ) . '</h4>';
        echo '<ul style="margin: 10px 0 0 20px;">';
        echo '<li>' . \esc_html__( 'Pastikan file ZIP atau JSON berasal dari ekspor SOFIR', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Jika CPT sudah ada, data akan di-merge otomatis', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Setelah import, refresh permalink di Settings → Permalinks', 'sofir' ) . '</li>';
        echo '</ul>';
        echo '</div>';

        echo '<p class="submit">';
        echo '<button type="submit" class="button button-primary">' . \esc_html__( '⬆ Import Paket CPT', 'sofir' ) . '</button>';
        echo '</p>';
        echo '</div>';
        echo '</form>';
        echo '</div>';

        echo '<div class="sofir-card">';
        echo '<h2>📚 ' . \esc_html__( 'CPT Library Guide', 'sofir' ) . '</h2>';
        echo '<div style="line-height: 1.8;">';
        echo '<h3>' . \esc_html__( 'Apa itu CPT Library?', 'sofir' ) . '</h3>';
        echo '<p>' . \esc_html__( 'CPT Library adalah sistem ekspor/impor yang memungkinkan Anda membagikan atau memindahkan Custom Post Type antar website WordPress dengan mudah.', 'sofir' ) . '</p>';
        
        echo '<h3>' . \esc_html__( 'Yang Diekspor:', 'sofir' ) . '</h3>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li>✓ ' . \esc_html__( 'Definisi Custom Post Type (CPT)', 'sofir' ) . '</li>';
        echo '<li>✓ ' . \esc_html__( 'Field & metadata configuration', 'sofir' ) . '</li>';
        echo '<li>✓ ' . \esc_html__( 'Taxonomies & terms', 'sofir' ) . '</li>';
        echo '<li>✓ ' . \esc_html__( 'Filter & query settings', 'sofir' ) . '</li>';
        echo '<li>✓ ' . \esc_html__( 'Konten posts (opsional)', 'sofir' ) . '</li>';
        echo '</ul>';

        echo '<h3>' . \esc_html__( 'Use Cases:', 'sofir' ) . '</h3>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li>🚀 ' . \esc_html__( 'Clone website dengan struktur CPT yang sama', 'sofir' ) . '</li>';
        echo '<li>💼 ' . \esc_html__( 'Bagikan template CPT ke client atau tim', 'sofir' ) . '</li>';
        echo '<li>🔄 ' . \esc_html__( 'Backup & restore CPT configuration', 'sofir' ) . '</li>';
        echo '<li>🌐 ' . \esc_html__( 'Migrasi dari development ke production', 'sofir' ) . '</li>';
        echo '</ul>';
        echo '</div>';
        echo '</div>';

        echo '</div>';
        echo '</div>';

        $this->render_export_import_scripts();
    }

    public function handle_export_cpt(): void {
        $this->verify_request( 'sofir_export_cpt' );

        $export_post_types = $_POST['export_post_types'] ?? [];
        $filename          = \sanitize_file_name( $_POST['export_filename'] ?? 'sofir-cpt-export' );

        if ( empty( $export_post_types ) ) {
            \wp_die( \esc_html__( 'Tidak ada post type dipilih untuk diekspor.', 'sofir' ) );
        }

        $exporter = new CptExporter();
        $exporter->export_package( $export_post_types, $filename );
    }

    public function handle_import_cpt(): void {
        $this->verify_request( 'sofir_import_cpt' );

        if ( ! isset( $_FILES['import_file'] ) || UPLOAD_ERR_OK !== $_FILES['import_file']['error'] ) {
            \wp_die( \esc_html__( 'File upload gagal. Silakan coba lagi.', 'sofir' ) );
        }

        $importer = new CptImporter();
        $result   = $importer->import_package( $_FILES['import_file'] );

        if ( is_wp_error( $result ) ) {
            \wp_die( $result->get_error_message() );
        }

        $message = sprintf(
            \__( 'Berhasil import %d CPT, %d taxonomies, dan %d posts.', 'sofir' ),
            $result['cpt_count'] ?? 0,
            $result['taxonomy_count'] ?? 0,
            $result['post_count'] ?? 0
        );

        $redirect = \add_query_arg(
            [
                'page'           => 'sofir-dashboard',
                'tab'            => 'library',
                'sofir_notice'   => 'cpt_imported',
                'sofir_message'  => urlencode( $message ),
            ],
            \admin_url( 'admin.php' )
        );

        \wp_safe_redirect( $redirect );
        exit;
    }

    public function handle_export_preview_ajax(): void {
        \check_ajax_referer( 'sofir_admin', 'nonce' );

        $post_types = isset( $_POST['post_types'] ) ? (array) $_POST['post_types'] : [];

        if ( empty( $post_types ) ) {
            \wp_send_json_error( [ 'message' => \__( 'Tidak ada post type dipilih.', 'sofir' ) ] );
        }

        $manager = CptManager::instance();
        $preview = [];

        foreach ( $post_types as $slug ) {
            $count = \wp_count_posts( $slug );
            $published = isset( $count->publish ) ? (int) $count->publish : 0;
            
            $definition = $manager->get_post_types()[ $slug ] ?? [];
            $fields     = $definition['fields'] ?? [];
            $taxonomies = $definition['taxonomies'] ?? [];

            $preview[] = [
                'slug'       => $slug,
                'posts'      => $published,
                'fields'     => count( $fields ),
                'taxonomies' => count( $taxonomies ),
            ];
        }

        \wp_send_json_success( [ 'preview' => $preview ] );
    }

    private function render_notice( string $notice ): void {
        $messages = [
            'cpt_imported' => isset( $_GET['sofir_message'] ) ? urldecode( $_GET['sofir_message'] ) : \__( 'CPT package imported successfully.', 'sofir' ),
        ];

        $message = $messages[ $notice ] ?? '';

        if ( $message ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . \esc_html( $message ) . '</p></div>';
        }
    }

    private function verify_request( string $action ): void {
        if ( ! isset( $_REQUEST['_sofir_nonce'] ) || ! \wp_verify_nonce( $_REQUEST['_sofir_nonce'], $action ) ) {
            \wp_die( \esc_html__( 'Nonce verification failed', 'sofir' ) );
        }

        if ( ! \current_user_can( 'manage_options' ) ) {
            \wp_die( \esc_html__( 'You do not have permission to perform this action.', 'sofir' ) );
        }
    }

    private function render_export_import_scripts(): void {
        ?>
        <script>
        (function() {
            var exportCheckboxes = document.querySelectorAll('.sofir-export-checkbox');
            var previewButton = document.getElementById('sofir-preview-export');
            var downloadButton = document.getElementById('sofir-download-export');
            var previewContainer = document.getElementById('sofir-export-preview');

            if (!exportCheckboxes.length) return;

            function updateButtons() {
                var checked = document.querySelectorAll('.sofir-export-checkbox:checked').length > 0;
                if (previewButton) previewButton.disabled = !checked;
                if (downloadButton) downloadButton.disabled = !checked;
            }

            exportCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', updateButtons);
            });

            if (previewButton) {
                previewButton.addEventListener('click', function() {
                    var selectedTypes = [];
                    document.querySelectorAll('.sofir-export-checkbox:checked').forEach(function(cb) {
                        selectedTypes.push(cb.value);
                    });

                    if (selectedTypes.length === 0) return;

                    previewButton.disabled = true;
                    previewButton.textContent = '⏳ Loading...';

                    fetch(ajaxurl, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: new URLSearchParams({
                            action: 'sofir_get_export_preview',
                            nonce: SOFIR_ADMIN_DATA.nonce,
                            post_types: selectedTypes
                        })
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.success && data.data.preview) {
                            var html = '<h3>📊 Preview Data Ekspor:</h3><ul style="margin: 10px 0 0 20px;">';
                            data.data.preview.forEach(function(item) {
                                html += '<li><strong>' + item.slug + '</strong>: ';
                                html += item.posts + ' posts, ' + item.fields + ' fields, ' + item.taxonomies + ' taxonomies';
                                html += '</li>';
                            });
                            html += '</ul>';
                            previewContainer.innerHTML = html;
                            previewContainer.style.display = 'block';
                        }
                    })
                    .catch(function(err) {
                        console.error('Preview error:', err);
                    })
                    .finally(function() {
                        previewButton.disabled = false;
                        previewButton.textContent = '👁 Preview Data';
                    });
                });
            }
        })();
        </script>
        <?php
    }
}

class CptExporter {
    public function export_package( array $post_types, string $filename ): void {
        $manager = CptManager::instance();
        $package = [
            'version'    => SOFIR_VERSION,
            'exported'   => gmdate( 'Y-m-d H:i:s' ),
            'post_types' => [],
            'taxonomies' => [],
            'posts'      => [],
        ];

        foreach ( $post_types as $slug ) {
            $definition = $manager->get_post_types()[ $slug ] ?? null;
            if ( ! $definition ) {
                continue;
            }

            $package['post_types'][ $slug ] = $definition;

            $query = new \WP_Query( [
                'post_type'      => $slug,
                'posts_per_page' => -1,
                'post_status'    => 'any',
            ] );

            foreach ( $query->posts as $post ) {
                $package['posts'][] = [
                    'post_type'    => $post->post_type,
                    'post_title'   => $post->post_title,
                    'post_content' => $post->post_content,
                    'post_excerpt' => $post->post_excerpt,
                    'post_status'  => $post->post_status,
                    'meta'         => \get_post_meta( $post->ID ),
                    'terms'        => $this->get_post_terms( $post->ID ),
                ];
            }

            if ( isset( $definition['taxonomies'] ) ) {
                foreach ( $definition['taxonomies'] as $tax_slug ) {
                    if ( ! isset( $package['taxonomies'][ $tax_slug ] ) ) {
                        $tax_definition = $manager->get_taxonomies()[ $tax_slug ] ?? null;
                        if ( $tax_definition ) {
                            $package['taxonomies'][ $tax_slug ] = $tax_definition;
                        }
                    }
                }
            }
        }

        $json_content = \wp_json_encode( $package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );

        header( 'Content-Type: application/json' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '.json"' );
        header( 'Content-Length: ' . strlen( $json_content ) );

        echo $json_content;
        exit;
    }

    private function get_post_terms( int $post_id ): array {
        $terms = [];
        $taxonomies = \get_post_taxonomies( $post_id );

        foreach ( $taxonomies as $taxonomy ) {
            $post_terms = \wp_get_post_terms( $post_id, $taxonomy, [ 'fields' => 'names' ] );
            if ( ! is_wp_error( $post_terms ) && ! empty( $post_terms ) ) {
                $terms[ $taxonomy ] = $post_terms;
            }
        }

        return $terms;
    }
}

class CptImporter {
    private function extract_filters( array $fields ): array {
        $filters = [];
        
        foreach ( $fields as $field_key => $field ) {
            if ( ! empty( $field['filterable'] ) && ! empty( $field['filter'] ) ) {
                $filters[] = $field['filter']['query_var'] ?? $field_key;
            }
        }
        
        return $filters;
    }

    public function import_package( array $file ): array|\WP_Error {
        $tmp_file = $file['tmp_name'];
        $file_ext = pathinfo( $file['name'], PATHINFO_EXTENSION );

        if ( 'json' === strtolower( $file_ext ) ) {
            $content = file_get_contents( $tmp_file );
            $package = json_decode( $content, true );
        } elseif ( 'zip' === strtolower( $file_ext ) ) {
            return new \WP_Error( 'not_implemented', \__( 'ZIP import belum diimplementasikan. Gunakan file JSON.', 'sofir' ) );
        } else {
            return new \WP_Error( 'invalid_file', \__( 'Format file tidak valid. Gunakan ZIP atau JSON.', 'sofir' ) );
        }

        if ( ! $package || ! is_array( $package ) ) {
            return new \WP_Error( 'invalid_json', \__( 'File JSON tidak valid.', 'sofir' ) );
        }

        $manager = CptManager::instance();
        $cpt_count = 0;
        $taxonomy_count = 0;
        $post_count = 0;

        if ( isset( $package['post_types'] ) && is_array( $package['post_types'] ) ) {
            foreach ( $package['post_types'] as $slug => $definition ) {
                $payload = [
                    'slug'         => $slug,
                    'singular'     => $definition['args']['labels']['singular_name'] ?? ucfirst( $slug ),
                    'plural'       => $definition['args']['labels']['name'] ?? ucfirst( $slug ) . 's',
                    'menu_icon'    => $definition['args']['menu_icon'] ?? 'dashicons-admin-post',
                    'supports'     => $definition['args']['supports'] ?? [ 'title', 'editor' ],
                    'has_archive'  => $definition['args']['has_archive'] ?? false,
                    'hierarchical' => $definition['args']['hierarchical'] ?? false,
                    'rest_base'    => $definition['args']['rest_base'] ?? $slug,
                    'rewrite'      => $slug,
                    'taxonomies'   => $definition['taxonomies'] ?? [],
                    'fields'       => isset( $definition['fields'] ) ? array_keys( $definition['fields'] ) : [],
                    'filters'      => $this->extract_filters( $definition['fields'] ?? [] ),
                ];
                
                $manager->save_post_type( $payload );
                $cpt_count++;
            }
        }

        if ( isset( $package['taxonomies'] ) && is_array( $package['taxonomies'] ) ) {
            foreach ( $package['taxonomies'] as $slug => $definition ) {
                $payload = [
                    'slug'         => $slug,
                    'singular'     => $definition['args']['labels']['singular_name'] ?? ucfirst( $slug ),
                    'plural'       => $definition['args']['labels']['name'] ?? ucfirst( $slug ) . 's',
                    'object_type'  => $definition['object_type'] ?? [],
                    'hierarchical' => $definition['args']['hierarchical'] ?? true,
                    'filterable'   => $definition['filterable'] ?? false,
                    'rewrite'      => $slug,
                ];
                
                $manager->save_taxonomy( $payload );
                $taxonomy_count++;
            }
        }

        if ( isset( $package['posts'] ) && is_array( $package['posts'] ) ) {
            foreach ( $package['posts'] as $post_data ) {
                $post_id = \wp_insert_post( [
                    'post_type'    => $post_data['post_type'] ?? 'post',
                    'post_title'   => $post_data['post_title'] ?? '',
                    'post_content' => $post_data['post_content'] ?? '',
                    'post_excerpt' => $post_data['post_excerpt'] ?? '',
                    'post_status'  => 'draft',
                ] );

                if ( $post_id && ! is_wp_error( $post_id ) ) {
                    if ( isset( $post_data['meta'] ) && is_array( $post_data['meta'] ) ) {
                        foreach ( $post_data['meta'] as $meta_key => $meta_values ) {
                            \delete_post_meta( $post_id, $meta_key );
                            foreach ( (array) $meta_values as $meta_value ) {
                                \add_post_meta( $post_id, $meta_key, $meta_value );
                            }
                        }
                    }

                    if ( isset( $post_data['terms'] ) && is_array( $post_data['terms'] ) ) {
                        foreach ( $post_data['terms'] as $taxonomy => $terms ) {
                            \wp_set_object_terms( $post_id, $terms, $taxonomy );
                        }
                    }

                    $post_count++;
                }
            }
        }

        \flush_rewrite_rules();

        return [
            'cpt_count'      => $cpt_count,
            'taxonomy_count' => $taxonomy_count,
            'post_count'     => $post_count,
        ];
    }
}
