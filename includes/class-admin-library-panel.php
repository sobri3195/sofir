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
        \add_action( 'admin_post_sofir_install_ready_cpt', [ $this, 'handle_install_ready_cpt' ] );
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
        echo '<h2>🎁 ' . \esc_html__( 'Ready-to-Use CPT Library', 'sofir' ) . '</h2>';
        echo '<p style="font-size: 14px; line-height: 1.6; margin-bottom: 12px;">' . \esc_html__( 'Template CPT siap pakai untuk berbagai jenis website. One-click install meliputi:', 'sofir' ) . '</p>';
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 20px;">';
        echo '<div style="padding: 12px; background: #f0f6fc; border-left: 3px solid #0073aa; border-radius: 4px;">';
        echo '<strong style="color: #0073aa;">📦 Post Types & Fields</strong><br>';
        echo '<small style="color: #666;">' . \esc_html__( 'CPT, metadata, dan custom fields', 'sofir' ) . '</small>';
        echo '</div>';
        echo '<div style="padding: 12px; background: #f0f6fc; border-left: 3px solid #0073aa; border-radius: 4px;">';
        echo '<strong style="color: #0073aa;">🏷 Taxonomies & Filters</strong><br>';
        echo '<small style="color: #666;">' . \esc_html__( 'Kategori dan filter pencarian', 'sofir' ) . '</small>';
        echo '</div>';
        echo '<div style="padding: 12px; background: #f0f6fc; border-left: 3px solid #0073aa; border-radius: 4px;">';
        echo '<strong style="color: #0073aa;">📄 Sample Pages</strong><br>';
        echo '<small style="color: #666;">' . \esc_html__( 'Single & archive templates', 'sofir' ) . '</small>';
        echo '</div>';
        echo '<div style="padding: 12px; background: #f0f6fc; border-left: 3px solid #0073aa; border-radius: 4px;">';
        echo '<strong style="color: #0073aa;">🔗 Navigation Menu</strong><br>';
        echo '<small style="color: #666;">' . \esc_html__( 'Menu otomatis untuk pages', 'sofir' ) . '</small>';
        echo '</div>';
        echo '</div>';
        
        $this->render_ready_templates();
        
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
        echo '<p>' . \esc_html__( 'CPT Library adalah sistem one-click install yang memungkinkan Anda membuat website lengkap dengan Custom Post Type, template pages, dan navigation menu hanya dalam sekali klik.', 'sofir' ) . '</p>';
        
        echo '<h3>' . \esc_html__( 'Ready-to-Use Templates:', 'sofir' ) . '</h3>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li>🏢 ' . \esc_html__( '<strong>Business Directory</strong> - Direktori bisnis dengan lokasi & rating', 'sofir' ) . '</li>';
        echo '<li>🏨 ' . \esc_html__( '<strong>Hotel & Accommodation</strong> - Website penginapan dengan harga & galeri', 'sofir' ) . '</li>';
        echo '<li>📰 ' . \esc_html__( '<strong>News & Blog</strong> - Website berita dengan artikel & kategori', 'sofir' ) . '</li>';
        echo '<li>📅 ' . \esc_html__( '<strong>Events & Calendar</strong> - Website event dengan tanggal & kapasitas', 'sofir' ) . '</li>';
        echo '<li>⏰ ' . \esc_html__( '<strong>Appointments</strong> - Sistem booking dengan status & provider', 'sofir' ) . '</li>';
        echo '<li>🛒 ' . \esc_html__( '<strong>Toko Online</strong> - E-commerce dengan produk & multi-vendor', 'sofir' ) . '</li>';
        echo '</ul>';

        echo '<h3>' . \esc_html__( 'Yang Diinstall Otomatis:', 'sofir' ) . '</h3>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li>✓ ' . \esc_html__( '<strong>Custom Post Types</strong> - Post type dengan field & metadata', 'sofir' ) . '</li>';
        echo '<li>✓ ' . \esc_html__( '<strong>Taxonomies</strong> - Kategori dan tags untuk filter', 'sofir' ) . '</li>';
        echo '<li>✓ ' . \esc_html__( '<strong>Fields & Filters</strong> - Custom fields dan query filters', 'sofir' ) . '</li>';
        echo '<li>✓ ' . \esc_html__( '<strong>Sample Pages</strong> - Archive dan single page templates', 'sofir' ) . '</li>';
        echo '<li>✓ ' . \esc_html__( '<strong>Navigation Menu</strong> - Menu dengan link ke pages & archive', 'sofir' ) . '</li>';
        echo '</ul>';

        echo '<h3>' . \esc_html__( 'Use Cases:', 'sofir' ) . '</h3>';
        echo '<ul style="margin-left: 20px;">';
        echo '<li>🚀 ' . \esc_html__( 'Launch website baru dalam hitungan menit', 'sofir' ) . '</li>';
        echo '<li>💼 ' . \esc_html__( 'Setup website client dengan cepat', 'sofir' ) . '</li>';
        echo '<li>🔄 ' . \esc_html__( 'Clone struktur website ke multi-site', 'sofir' ) . '</li>';
        echo '<li>🌐 ' . \esc_html__( 'Ekspor/impor CPT untuk backup & restore', 'sofir' ) . '</li>';
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

    public function handle_install_ready_cpt(): void {
        $this->verify_request( 'sofir_install_ready_cpt' );

        $template_key = isset( $_POST['template'] ) ? \sanitize_key( $_POST['template'] ) : '';
        
        if ( empty( $template_key ) ) {
            \wp_die( \esc_html__( 'Template tidak valid.', 'sofir' ) );
        }

        $templates = $this->get_ready_templates();
        
        if ( ! isset( $templates[ $template_key ] ) ) {
            \wp_die( \esc_html__( 'Template tidak ditemukan.', 'sofir' ) );
        }

        $template = $templates[ $template_key ];
        $manager = CptManager::instance();
        $installer = new ReadyCptInstaller();
        
        foreach ( $template['cpts'] as $cpt_slug => $cpt_config ) {
            $manager->save_post_type( $cpt_config );
        }
        
        foreach ( $template['taxonomies'] as $tax_slug => $tax_config ) {
            $manager->save_taxonomy( $tax_config );
        }

        $installer->install_template_pages( $template_key, $template );
        $installer->create_menu_items( $template_key, $template );

        \flush_rewrite_rules();

        $redirect = \add_query_arg(
            [
                'page'           => 'sofir-dashboard',
                'tab'            => 'library',
                'sofir_notice'   => 'ready_template_installed',
                'template_name'  => urlencode( $template['name'] ),
            ],
            \admin_url( 'admin.php' )
        );

        \wp_safe_redirect( $redirect );
        exit;
    }

    private function render_ready_templates(): void {
        $templates = $this->get_ready_templates();
        $manager = CptManager::instance();
        $existing_cpts = array_keys( $manager->get_post_types() );
        
        echo '<div class="sofir-ready-templates" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin: 20px 0;">';
        
        foreach ( $templates as $key => $template ) {
            $is_installed = false;
            foreach ( $template['cpts'] as $cpt_slug => $cpt_config ) {
                if ( in_array( $cpt_slug, $existing_cpts, true ) ) {
                    $is_installed = true;
                    break;
                }
            }
            
            $badge_style = $is_installed ? 'background: #00a32a; color: #fff;' : 'background: #0073aa; color: #fff;';
            $button_text = $is_installed ? '✓ Sudah Terinstall' : '🚀 Install Sekarang';
            $button_disabled = $is_installed ? ' disabled' : '';
            
            echo '<div class="sofir-template-card" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 20px; position: relative; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform=\'translateY(-4px)\'; this.style.boxShadow=\'0 4px 12px rgba(0,0,0,0.15)\';" onmouseout="this.style.transform=\'translateY(0)\'; this.style.boxShadow=\'0 2px 4px rgba(0,0,0,0.1)\';">';
            echo '<div style="position: absolute; top: 10px; right: 10px; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; ' . $badge_style . '">';
            echo \esc_html( $template['badge'] );
            echo '</div>';
            
            echo '<div style="font-size: 42px; margin-bottom: 15px;">' . $template['icon'] . '</div>';
            echo '<h3 style="margin: 10px 0; font-size: 17px; font-weight: 600;">' . \esc_html( $template['name'] ) . '</h3>';
            echo '<p style="font-size: 13px; color: #666; margin: 10px 0; line-height: 1.5;">' . \esc_html( $template['description'] ) . '</p>';
            
            echo '<div style="background: #f5f5f5; border-radius: 6px; padding: 12px; margin: 15px 0;">';
            echo '<p style="margin: 0 0 8px 0; font-size: 11px; font-weight: bold; color: #666; text-transform: uppercase;">' . \esc_html__( 'Fitur Utama:', 'sofir' ) . '</p>';
            echo '<ul style="margin: 0; padding-left: 18px; font-size: 12px; color: #555; line-height: 1.8;">';
            foreach ( $template['features'] as $feature ) {
                echo '<li>' . \esc_html( $feature ) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            
            $cpt_count = count( $template['cpts'] );
            $tax_count = count( $template['taxonomies'] );
            $template_count = isset( $template['templates'] ) ? count( $template['templates'] ) : 0;
            
            echo '<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin: 15px 0; padding: 12px; background: #f9f9f9; border-radius: 6px;">';
            echo '<div style="text-align: center;">';
            echo '<div style="font-size: 20px; font-weight: bold; color: #0073aa;">' . $cpt_count . '</div>';
            echo '<div style="font-size: 10px; color: #666; text-transform: uppercase;">' . \esc_html__( 'CPT', 'sofir' ) . '</div>';
            echo '</div>';
            echo '<div style="text-align: center; border-left: 1px solid #ddd; border-right: 1px solid #ddd;">';
            echo '<div style="font-size: 20px; font-weight: bold; color: #0073aa;">' . $tax_count . '</div>';
            echo '<div style="font-size: 10px; color: #666; text-transform: uppercase;">' . \esc_html__( 'Taxonomies', 'sofir' ) . '</div>';
            echo '</div>';
            echo '<div style="text-align: center;">';
            echo '<div style="font-size: 20px; font-weight: bold; color: #0073aa;">' . $template_count . '</div>';
            echo '<div style="font-size: 10px; color: #666; text-transform: uppercase;">' . \esc_html__( 'Pages', 'sofir' ) . '</div>';
            echo '</div>';
            echo '</div>';
            
            if ( ! $is_installed ) {
                echo '<div style="margin: 15px 0; padding: 10px; background: #e8f5f9; border-left: 3px solid #0073aa; border-radius: 4px;">';
                echo '<p style="margin: 0; font-size: 11px; line-height: 1.6; color: #555;">';
                echo '<strong>' . \esc_html__( 'One-click install meliputi:', 'sofir' ) . '</strong><br>';
                echo '✓ Post Types & Fields<br>';
                echo '✓ Taxonomies & Filters<br>';
                echo '✓ Sample Pages & Menu';
                echo '</p>';
                echo '</div>';
            }
            
            echo '<form method="post" action="' . \esc_url( \admin_url( 'admin-post.php' ) ) . '" style="margin-top: 15px;">';
            echo '<input type="hidden" name="action" value="sofir_install_ready_cpt" />';
            echo '<input type="hidden" name="template" value="' . \esc_attr( $key ) . '" />';
            \wp_nonce_field( 'sofir_install_ready_cpt', '_sofir_nonce' );
            echo '<button type="submit" class="button button-primary" style="width: 100%; height: 40px; font-weight: 600;"' . $button_disabled . '>' . \esc_html( $button_text ) . '</button>';
            echo '</form>';
            
            echo '</div>';
        }
        
        echo '</div>';
    }

    private function get_ready_templates(): array {
        $manager = CptManager::instance();
        $field_catalog = $manager->get_field_catalog();
        
        return [
            'business_directory' => [
                'name' => \__( 'Business Directory', 'sofir' ),
                'icon' => '🏢',
                'badge' => \__( 'Popular', 'sofir' ),
                'description' => \__( 'Direktori bisnis lengkap dengan lokasi, rating, jam buka, dan kontak.', 'sofir' ),
                'features' => [
                    \__( 'Lokasi & peta', 'sofir' ),
                    \__( 'Rating & review', 'sofir' ),
                    \__( 'Jam operasional', 'sofir' ),
                    \__( 'Filter pencarian', 'sofir' ),
                ],
                'cpts' => [
                    'listing' => [
                        'slug' => 'listing',
                        'singular' => \__( 'Listing', 'sofir' ),
                        'plural' => \__( 'Listings', 'sofir' ),
                        'menu_icon' => 'dashicons-location-alt',
                        'rewrite' => 'listings',
                        'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields', 'revisions' ],
                        'has_archive' => true,
                        'fields' => [ 'location', 'hours', 'rating', 'status', 'price', 'contact', 'gallery', 'attributes' ],
                        'filters' => [ 'location', 'rating', 'status', 'price', 'attribute', 'open_now' ],
                        'taxonomies' => [ 'listing_category', 'listing_location' ],
                    ],
                ],
                'templates' => [ 'city-directory', 'web-directory-dashboard' ],
                'taxonomies' => [
                    'listing_category' => [
                        'slug' => 'listing_category',
                        'singular' => \__( 'Category', 'sofir' ),
                        'plural' => \__( 'Categories', 'sofir' ),
                        'object_types' => [ 'listing' ],
                        'hierarchical' => true,
                        'filterable' => true,
                    ],
                    'listing_location' => [
                        'slug' => 'listing_location',
                        'singular' => \__( 'Location', 'sofir' ),
                        'plural' => \__( 'Locations', 'sofir' ),
                        'object_types' => [ 'listing' ],
                        'hierarchical' => false,
                        'filterable' => true,
                    ],
                ],
            ],
            'accommodation' => [
                'name' => \__( 'Hotel & Accommodation', 'sofir' ),
                'icon' => '🏨',
                'badge' => \__( 'New', 'sofir' ),
                'description' => \__( 'Website hotel atau penginapan dengan harga, rating, lokasi, dan galeri foto.', 'sofir' ),
                'features' => [
                    \__( 'Harga per malam', 'sofir' ),
                    \__( 'Galeri foto', 'sofir' ),
                    \__( 'Rating & review', 'sofir' ),
                    \__( 'Filter lokasi', 'sofir' ),
                ],
                'cpts' => [
                    'listing' => [
                        'slug' => 'listing',
                        'singular' => \__( 'Property', 'sofir' ),
                        'plural' => \__( 'Properties', 'sofir' ),
                        'menu_icon' => 'dashicons-admin-home',
                        'rewrite' => 'properties',
                        'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions' ],
                        'has_archive' => true,
                        'fields' => [ 'location', 'rating', 'price', 'contact', 'gallery', 'attributes' ],
                        'filters' => [ 'location', 'rating', 'price', 'attribute' ],
                        'taxonomies' => [ 'listing_category', 'listing_location' ],
                    ],
                ],
                'templates' => [ 'hotel-booking' ],
                'taxonomies' => [
                    'listing_category' => [
                        'slug' => 'listing_category',
                        'singular' => \__( 'Property Type', 'sofir' ),
                        'plural' => \__( 'Property Types', 'sofir' ),
                        'object_types' => [ 'listing' ],
                        'hierarchical' => true,
                        'filterable' => true,
                    ],
                    'listing_location' => [
                        'slug' => 'listing_location',
                        'singular' => \__( 'Location', 'sofir' ),
                        'plural' => \__( 'Locations', 'sofir' ),
                        'object_types' => [ 'listing' ],
                        'hierarchical' => false,
                        'filterable' => true,
                    ],
                ],
            ],
            'news_blog' => [
                'name' => \__( 'News & Blog', 'sofir' ),
                'icon' => '📰',
                'badge' => \__( 'Simple', 'sofir' ),
                'description' => \__( 'Website berita atau blog dengan artikel, kategori, dan komentar.', 'sofir' ),
                'features' => [
                    \__( 'Artikel lengkap', 'sofir' ),
                    \__( 'Featured image', 'sofir' ),
                    \__( 'Komentar', 'sofir' ),
                    \__( 'Kategori', 'sofir' ),
                ],
                'cpts' => [
                    'article' => [
                        'slug' => 'article',
                        'singular' => \__( 'Article', 'sofir' ),
                        'plural' => \__( 'Articles', 'sofir' ),
                        'menu_icon' => 'dashicons-media-document',
                        'rewrite' => 'articles',
                        'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions', 'comments' ],
                        'has_archive' => true,
                        'fields' => [ 'attributes' ],
                        'filters' => [ 'attribute' ],
                        'taxonomies' => [],
                    ],
                ],
                'templates' => [ 'modern-magazine', 'tech-news-portal', 'personal-blog' ],
                'taxonomies' => [],
            ],
            'events' => [
                'name' => \__( 'Events & Calendar', 'sofir' ),
                'icon' => '📅',
                'badge' => \__( 'Popular', 'sofir' ),
                'description' => \__( 'Website event dengan tanggal, lokasi, kapasitas, dan pendaftaran.', 'sofir' ),
                'features' => [
                    \__( 'Tanggal & waktu', 'sofir' ),
                    \__( 'Kapasitas peserta', 'sofir' ),
                    \__( 'Lokasi event', 'sofir' ),
                    \__( 'Filter tanggal', 'sofir' ),
                ],
                'cpts' => [
                    'event' => [
                        'slug' => 'event',
                        'singular' => \__( 'Event', 'sofir' ),
                        'plural' => \__( 'Events', 'sofir' ),
                        'menu_icon' => 'dashicons-calendar',
                        'rewrite' => 'events',
                        'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions', 'comments' ],
                        'has_archive' => true,
                        'fields' => [ 'event_date', 'event_capacity', 'location', 'contact', 'gallery', 'status', 'attributes' ],
                        'filters' => [ 'event_after', 'location', 'capacity_min', 'status' ],
                        'taxonomies' => [ 'event_category', 'event_tag' ],
                    ],
                ],
                'templates' => [ 'event-registration' ],
                'taxonomies' => [
                    'event_category' => [
                        'slug' => 'event_category',
                        'singular' => \__( 'Event Category', 'sofir' ),
                        'plural' => \__( 'Event Categories', 'sofir' ),
                        'object_types' => [ 'event' ],
                        'hierarchical' => true,
                        'filterable' => true,
                    ],
                    'event_tag' => [
                        'slug' => 'event_tag',
                        'singular' => \__( 'Event Tag', 'sofir' ),
                        'plural' => \__( 'Event Tags', 'sofir' ),
                        'object_types' => [ 'event' ],
                        'hierarchical' => false,
                        'filterable' => true,
                    ],
                ],
            ],
            'appointments' => [
                'name' => \__( 'Appointments & Booking', 'sofir' ),
                'icon' => '⏰',
                'badge' => \__( 'Pro', 'sofir' ),
                'description' => \__( 'Sistem booking appointment dengan status, provider, dan client.', 'sofir' ),
                'features' => [
                    \__( 'Tanggal & waktu', 'sofir' ),
                    \__( 'Status booking', 'sofir' ),
                    \__( 'Provider & client', 'sofir' ),
                    \__( 'Filter status', 'sofir' ),
                ],
                'cpts' => [
                    'appointment' => [
                        'slug' => 'appointment',
                        'singular' => \__( 'Appointment', 'sofir' ),
                        'plural' => \__( 'Appointments', 'sofir' ),
                        'menu_icon' => 'dashicons-clock',
                        'rewrite' => 'appointments',
                        'supports' => [ 'title', 'editor', 'thumbnail', 'author', 'revisions' ],
                        'has_archive' => true,
                        'fields' => [ 'appointment_datetime', 'appointment_duration', 'appointment_status', 'appointment_provider', 'appointment_client', 'contact', 'attributes' ],
                        'filters' => [ 'appointment_after', 'appointment_status', 'provider_id', 'client_id' ],
                        'taxonomies' => [ 'appointment_service' ],
                    ],
                ],
                'templates' => [ 'healthcare-network' ],
                'taxonomies' => [
                    'appointment_service' => [
                        'slug' => 'appointment_service',
                        'singular' => \__( 'Service', 'sofir' ),
                        'plural' => \__( 'Services', 'sofir' ),
                        'object_types' => [ 'appointment' ],
                        'hierarchical' => true,
                        'filterable' => true,
                    ],
                ],
            ],
            'online_store' => [
                'name' => \__( 'Toko Online / E-Commerce', 'sofir' ),
                'icon' => '🛒',
                'badge' => \__( 'Popular', 'sofir' ),
                'description' => \__( 'Website toko online lengkap dengan produk, kategori, harga, stok, dan multi-vendor.', 'sofir' ),
                'features' => [
                    \__( 'Produk & SKU', 'sofir' ),
                    \__( 'Harga & stok', 'sofir' ),
                    \__( 'Vendor stores', 'sofir' ),
                    \__( 'Filter produk', 'sofir' ),
                ],
                'cpts' => [
                    'vendor_store' => [
                        'slug' => 'vendor_store',
                        'singular' => \__( 'Store', 'sofir' ),
                        'plural' => \__( 'Stores', 'sofir' ),
                        'menu_icon' => 'dashicons-store',
                        'rewrite' => 'stores',
                        'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ],
                        'has_archive' => true,
                        'fields' => [ 'location', 'contact', 'rating', 'hours', 'attributes' ],
                        'filters' => [ 'location', 'rating' ],
                        'taxonomies' => [ 'store_category' ],
                    ],
                    'vendor_product' => [
                        'slug' => 'vendor_product',
                        'singular' => \__( 'Product', 'sofir' ),
                        'plural' => \__( 'Products', 'sofir' ),
                        'menu_icon' => 'dashicons-products',
                        'rewrite' => 'products',
                        'supports' => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ],
                        'has_archive' => true,
                        'fields' => [ 'price', 'gallery', 'attributes' ],
                        'filters' => [ 'price', 'attribute', 'vendor_id' ],
                        'taxonomies' => [ 'product_category', 'product_tag' ],
                    ],
                ],
                'templates' => [ 'product-catalog' ],
                'taxonomies' => [
                    'store_category' => [
                        'slug' => 'store_category',
                        'singular' => \__( 'Store Category', 'sofir' ),
                        'plural' => \__( 'Store Categories', 'sofir' ),
                        'object_types' => [ 'vendor_store' ],
                        'hierarchical' => true,
                        'filterable' => true,
                    ],
                    'product_category' => [
                        'slug' => 'product_category',
                        'singular' => \__( 'Product Category', 'sofir' ),
                        'plural' => \__( 'Product Categories', 'sofir' ),
                        'object_types' => [ 'vendor_product' ],
                        'hierarchical' => true,
                        'filterable' => true,
                    ],
                    'product_tag' => [
                        'slug' => 'product_tag',
                        'singular' => \__( 'Product Tag', 'sofir' ),
                        'plural' => \__( 'Product Tags', 'sofir' ),
                        'object_types' => [ 'vendor_product' ],
                        'hierarchical' => false,
                        'filterable' => true,
                    ],
                ],
            ],
        ];
    }

    private function render_notice( string $notice ): void {
        if ( 'ready_template_installed' === $notice ) {
            $template_name = isset( $_GET['template_name'] ) ? urldecode( $_GET['template_name'] ) : 'CPT';
            
            echo '<div class="notice notice-success is-dismissible">';
            echo '<h3 style="margin: 0.5em 0;">✅ ' . \esc_html( sprintf( \__( 'Template "%s" Berhasil Diinstall!', 'sofir' ), $template_name ) ) . '</h3>';
            echo '<p style="margin: 0.5em 0;"><strong>' . \esc_html__( 'Yang telah diinstall:', 'sofir' ) . '</strong></p>';
            echo '<ul style="margin: 0.5em 0 1em 20px;">';
            echo '<li>✓ ' . \esc_html__( 'Custom Post Types (CPT)', 'sofir' ) . '</li>';
            echo '<li>✓ ' . \esc_html__( 'Taxonomies & Terms', 'sofir' ) . '</li>';
            echo '<li>✓ ' . \esc_html__( 'Custom Fields & Metadata', 'sofir' ) . '</li>';
            echo '<li>✓ ' . \esc_html__( 'Filters & Query Settings', 'sofir' ) . '</li>';
            echo '<li>✓ ' . \esc_html__( 'Sample Pages dengan Template', 'sofir' ) . '</li>';
            echo '<li>✓ ' . \esc_html__( 'Navigation Menu', 'sofir' ) . '</li>';
            echo '</ul>';
            echo '<p style="margin: 0.5em 0;"><strong>' . \esc_html__( 'Langkah selanjutnya:', 'sofir' ) . '</strong></p>';
            echo '<ol style="margin: 0.5em 0 1em 20px;">';
            echo '<li>' . \esc_html__( 'Refresh permalink di', 'sofir' ) . ' <a href="' . \esc_url( \admin_url( 'options-permalink.php' ) ) . '"><strong>Settings → Permalinks</strong></a></li>';
            echo '<li>' . \esc_html__( 'Buat konten pertama Anda di menu CPT yang baru ditambahkan', 'sofir' ) . '</li>';
            echo '<li>' . \esc_html__( 'Atur menu navigasi di', 'sofir' ) . ' <a href="' . \esc_url( \admin_url( 'nav-menus.php' ) ) . '"><strong>Appearance → Menus</strong></a></li>';
            echo '<li>' . \esc_html__( 'Lihat preview pages yang telah dibuat', 'sofir' ) . '</li>';
            echo '</ol>';
            echo '</div>';
        } elseif ( 'cpt_imported' === $notice ) {
            $message = isset( $_GET['sofir_message'] ) ? urldecode( $_GET['sofir_message'] ) : \__( 'CPT package imported successfully.', 'sofir' );
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

class ReadyCptInstaller {
    public function install_template_pages( string $template_key, array $template ): void {
        $templates_manager = \Sofir\Templates\Manager::instance();
        $installed_pages = [];
        
        if ( empty( $template['templates'] ) ) {
            return;
        }
        
        foreach ( $template['templates'] as $template_slug ) {
            $page_template = $templates_manager->get_template( $template_slug );
            
            if ( ! $page_template ) {
                continue;
            }
            
            $existing_page = \get_page_by_path( $template_slug, OBJECT, 'page' );
            
            if ( $existing_page ) {
                continue;
            }
            
            $content = $templates_manager->get_template_content( $page_template );
            
            if ( empty( $content ) ) {
                continue;
            }
            
            $page_id = \wp_insert_post( [
                'post_type'    => 'page',
                'post_title'   => $page_template['title'],
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_name'    => $template_slug,
            ] );
            
            if ( $page_id && ! is_wp_error( $page_id ) ) {
                \update_post_meta( $page_id, '_sofir_template', $template_slug );
                \update_post_meta( $page_id, '_sofir_library_template', $template_key );
                $installed_pages[] = $page_id;
            }
        }
        
        if ( ! empty( $installed_pages ) ) {
            \update_option( 'sofir_library_' . $template_key . '_pages', $installed_pages );
        }
    }
    
    public function create_menu_items( string $template_key, array $template ): void {
        $menu_name = $template['name'];
        $menu_slug = 'sofir-' . $template_key;
        
        $menu_exists = \wp_get_nav_menu_object( $menu_slug );
        
        if ( $menu_exists ) {
            return;
        }
        
        $menu_id = \wp_create_nav_menu( $menu_name );
        
        if ( is_wp_error( $menu_id ) ) {
            return;
        }
        
        $installed_pages = \get_option( 'sofir_library_' . $template_key . '_pages', [] );
        
        foreach ( $installed_pages as $page_id ) {
            \wp_update_nav_menu_item( $menu_id, 0, [
                'menu-item-object-id'   => $page_id,
                'menu-item-object'      => 'page',
                'menu-item-type'        => 'post_type',
                'menu-item-status'      => 'publish',
            ] );
        }
        
        foreach ( $template['cpts'] as $cpt_slug => $cpt_config ) {
            if ( empty( $cpt_config['has_archive'] ) ) {
                continue;
            }
            
            \wp_update_nav_menu_item( $menu_id, 0, [
                'menu-item-title'       => $cpt_config['plural'],
                'menu-item-url'         => \home_url( '/' . $cpt_config['rewrite'] ),
                'menu-item-status'      => 'publish',
                'menu-item-type'        => 'custom',
            ] );
        }
        
        \update_option( 'sofir_library_' . $template_key . '_menu', $menu_id );
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
