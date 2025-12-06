<?php
namespace Sofir\Admin;

use Sofir\Admin\ContentPanel;
use Sofir\Admin\SeoPanel;
use Sofir\Admin\TemplatesPanel;
use Sofir\Admin\LibraryPanel;
use Sofir\Admin\UserPanel;
use Sofir\Admin\PaymentPanel;
use Sofir\Admin\VoxelPanel;
use Sofir\Admin\Wizard;
use Sofir\Templates\Manager as TemplateManager;

class Manager {
    private static ?Manager $instance = null;

    private string $menu_slug = 'sofir-dashboard';

    public static function instance(): Manager {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function boot(): void {
        \add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ], 5 );
        \add_action( 'admin_menu', [ $this, 'register_menu' ] );
        \add_action( 'admin_init', [ $this, 'register_settings' ] );
        \add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );

        \add_action( 'sofir/admin/tab/content', [ $this, 'render_content_tab' ] );
        \add_action( 'sofir/admin/tab/templates', [ $this, 'render_templates_tab' ] );
        \add_action( 'sofir/admin/tab/library', [ $this, 'render_library_tab' ] );
        \add_action( 'sofir/admin/tab/enhancement', [ $this, 'render_enhancement_tab' ] );
        \add_action( 'sofir/admin/tab/payments', [ $this, 'render_payments_tab' ] );
        \add_action( 'sofir/admin/tab/seo', [ $this, 'render_seo_tab' ] );
        \add_action( 'sofir/admin/tab/users', [ $this, 'render_users_tab' ] );
        \add_action( 'sofir/admin/tab/voxel', [ $this, 'render_voxel_tab' ] );
        \add_action( 'sofir/admin/tab/tools', [ $this, 'render_tools_tab' ] );

        ContentPanel::instance()->boot();
        LibraryPanel::instance()->boot();
        PaymentPanel::instance()->boot();
        VoxelPanel::instance()->boot();
        Wizard::instance()->boot();
    }

    public function register_menu(): void {
        \add_menu_page(
            \__( 'SOFIR', 'sofir' ),
            \__( 'SOFIR', 'sofir' ),
            'manage_options',
            $this->menu_slug,
            [ $this, 'render_main_page' ],
            'dashicons-layout',
            58
        );
    }

    public function register_settings(): void {
        \do_action( 'sofir/admin/register_settings' );
    }

    public function enqueue_assets( string $hook ): void {
        if ( false === strpos( $hook, $this->menu_slug ) ) {
            return;
        }

        $handle = 'sofir-admin';

        \wp_enqueue_style( $handle );
        \wp_enqueue_script( $handle );

        $active_tab = $this->get_active_tab();
        if ( 'seo' === $active_tab ) {
            \wp_enqueue_style( 'sofir-seo-ai-generator' );
            \wp_enqueue_script( 'sofir-seo-ai-generator' );
        }
    }

    public function register_assets(): void {
        $handle = 'sofir-admin';

        if ( ! \wp_style_is( $handle, 'registered' ) ) {
            \wp_register_style(
                $handle,
                SOFIR_ASSETS_URL . 'css/admin.css',
                [],
                SOFIR_VERSION
            );
        }

        if ( ! \wp_script_is( $handle, 'registered' ) ) {
            \wp_register_script(
                $handle,
                SOFIR_ASSETS_URL . 'js/admin.js',
                [ 'wp-element', 'wp-components', 'wp-i18n' ],
                SOFIR_VERSION,
                true
            );

            \wp_localize_script(
                $handle,
                'SOFIR_ADMIN_DATA',
                [
                    'tabs'          => $this->get_tabs(),
                    'nonce'         => \wp_create_nonce( 'sofir_admin' ),
                    'restRoot'      => \esc_url_raw( \rest_url( 'sofir/v1' ) ),
                    'assetsUrl'     => SOFIR_ASSETS_URL,
                    'version'       => SOFIR_VERSION,
                    'templates'     => $this->get_templates_payload(),
                    'themeStyleUrl' => \get_stylesheet_uri(),
                ]
            );
        }

        $seo_ai_handle = 'sofir-seo-ai-generator';

        if ( ! \wp_style_is( $seo_ai_handle, 'registered' ) ) {
            \wp_register_style(
                $seo_ai_handle,
                SOFIR_ASSETS_URL . 'css/seo-ai-generator.css',
                [],
                SOFIR_VERSION
            );
        }

        if ( ! \wp_script_is( $seo_ai_handle, 'registered' ) ) {
            \wp_register_script(
                $seo_ai_handle,
                SOFIR_ASSETS_URL . 'js/seo-ai-generator.js',
                [ 'jquery' ],
                SOFIR_VERSION,
                true
            );
        }
    }

    public function render_main_page(): void {
        $active = $this->get_active_tab();
        $tabs   = $this->get_tabs();

        echo '<div class="wrap sofir-admin">';
        echo '<h1>' . \esc_html__( 'SOFIR Control Center', 'sofir' ) . '</h1>';
        echo '<nav class="sofir-tabs">';

        foreach ( $tabs as $tab => $label ) {
            $class = $tab === $active ? ' nav-tab nav-tab-active' : ' nav-tab';
            $url   = \add_query_arg( [ 'page' => $this->menu_slug, 'tab' => $tab ], \admin_url( 'admin.php' ) );
            printf(
                '<a href="%1$s" class="%2$s">%3$s</a>',
                \esc_url( $url ),
                \esc_attr( trim( $class ) ),
                \esc_html( $label )
            );
        }

        echo '</nav>';
        echo '<div class="sofir-tab-content">';

        \do_action( 'sofir/admin/tab/' . $active );

        echo '</div>';
        echo '</div>';
    }

    public function render_content_tab(): void {
        ContentPanel::instance()->render();
    }

    public function render_templates_tab(): void {
        TemplatesPanel::instance()->render();
    }

    public function render_library_tab(): void {
        LibraryPanel::instance()->render();
    }

    public function render_enhancement_tab(): void {
        $modules     = $this->get_enhancement_modules();
        $hero_points = [
            \__( 'Login & registrasi siap pakai untuk semua funnel.', 'sofir' ),
            \__( 'Keamanan anti brute force + honeypot komentar otomatis.', 'sofir' ),
            \__( 'Optimasi performa front-end tanpa konfigurasi tambahan.', 'sofir' ),
            \__( 'Dashboard anggota dengan metrik real-time dari seluruh CPT.', 'sofir' ),
        ];
        $hero_tags   = [
            \__( 'Shortcode siap pakai', 'sofir' ),
            \__( 'REST API', 'sofir' ),
            \__( 'Hooks untuk developer', 'sofir' ),
            \__( 'Tanpa add-on tambahan', 'sofir' ),
        ];

        echo '<section class="sofir-enhancement">';
        echo '<div class="sofir-enhancement__hero">';
        echo '<p class="sofir-enhancement__eyebrow">' . \esc_html__( 'SOFIR Control Center · Enhancement', 'sofir' ) . '</p>';
        echo '<h2>' . \esc_html__( 'Aktifkan Login, Keamanan, Performa, dan Dashboard Pengguna', 'sofir' ) . '</h2>';
        echo '<p>' . \esc_html__( 'Semua modul di bawah ini sudah aktif otomatis. Tinggal sisipkan shortcode atau panggil REST API-nya — tidak perlu instal plugin tambahan.', 'sofir' ) . '</p>';

        if ( ! empty( $hero_points ) ) {
            echo '<ul class="sofir-enhancement__pillars">';
            foreach ( $hero_points as $point ) {
                echo '<li>' . \esc_html( $point ) . '</li>';
            }
            echo '</ul>';
        }

        if ( ! empty( $hero_tags ) ) {
            echo '<div class="sofir-enhancement__chips">';
            foreach ( $hero_tags as $tag ) {
                echo '<span class="sofir-chip">' . \esc_html( $tag ) . '</span>';
            }
            echo '</div>';
        }

        echo '</div>';

        if ( ! empty( $modules ) ) {
            echo '<div class="sofir-enhancement__grid">';
            foreach ( $modules as $module ) {
                $status_class = 'sofir-module-card__status';

                if ( ! empty( $module['status'] ) ) {
                    $status_class .= ' is-' . \sanitize_html_class( (string) $module['status'] );
                }

                echo '<article class="sofir-module-card">';
                echo '<header class="sofir-module-card__header">';
                echo '<div class="sofir-module-card__title">';

                if ( ! empty( $module['icon'] ) ) {
                    echo '<span class="sofir-module-card__icon" aria-hidden="true">' . \esc_html( (string) $module['icon'] ) . '</span>';
                }

                echo '<div>';
                if ( ! empty( $module['category'] ) ) {
                    echo '<p class="sofir-module-card__category">' . \esc_html( (string) $module['category'] ) . '</p>';
                }
                echo '<h3>' . \esc_html( (string) $module['title'] ) . '</h3>';
                echo '</div>';
                echo '</div>';

                if ( ! empty( $module['status_label'] ) ) {
                    echo '<span class="' . \esc_attr( $status_class ) . '">' . \esc_html( (string) $module['status_label'] ) . '</span>';
                }

                echo '</header>';

                if ( ! empty( $module['description'] ) ) {
                    echo '<p class="sofir-module-card__description">' . \esc_html( (string) $module['description'] ) . '</p>';
                }

                if ( ! empty( $module['features'] ) && is_array( $module['features'] ) ) {
                    echo '<ul class="sofir-module-card__features">';
                    foreach ( $module['features'] as $feature ) {
                        echo '<li>' . \esc_html( (string) $feature ) . '</li>';
                    }
                    echo '</ul>';
                }

                if ( ! empty( $module['shortcodes'] ) && is_array( $module['shortcodes'] ) ) {
                    echo '<div class="sofir-module-card__section">';
                    echo '<span class="sofir-module-card__label">' . \esc_html__( 'Shortcode', 'sofir' ) . '</span>';
                    foreach ( $module['shortcodes'] as $shortcode => $label ) {
                        echo '<div class="sofir-module-snippet">';
                        echo '<code>' . \esc_html( (string) $shortcode ) . '</code>';
                        echo '<p>' . \esc_html( (string) $label ) . '</p>';
                        echo '</div>';
                    }
                    echo '</div>';
                }

                if ( ! empty( $module['rest'] ) && is_array( $module['rest'] ) ) {
                    echo '<div class="sofir-module-card__section">';
                    echo '<span class="sofir-module-card__label">' . \esc_html__( 'REST API', 'sofir' ) . '</span>';
                    echo '<ul class="sofir-module-card__list">';
                    foreach ( $module['rest'] as $endpoint => $label ) {
                        echo '<li><code>' . \esc_html( (string) $endpoint ) . '</code><p>' . \esc_html( (string) $label ) . '</p></li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }

                if ( ! empty( $module['filters'] ) && is_array( $module['filters'] ) ) {
                    echo '<div class="sofir-module-card__section">';
                    echo '<span class="sofir-module-card__label">' . \esc_html__( 'Hooks & Filters', 'sofir' ) . '</span>';
                    echo '<ul class="sofir-module-card__list">';
                    foreach ( $module['filters'] as $hook => $label ) {
                        echo '<li><code>' . \esc_html( (string) $hook ) . '</code><p>' . \esc_html( (string) $label ) . '</p></li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }

                if ( ! empty( $module['notes'] ) ) {
                    echo '<p class="sofir-module-card__note">' . \esc_html( (string) $module['notes'] ) . '</p>';
                }

                echo '</article>';
            }
            echo '</div>';
        }

        echo '<div class="sofir-enhancement__cta">';
        echo '<h3>' . \esc_html__( 'Langkah cepat implementasi', 'sofir' ) . '</h3>';
        echo '<ol>';
        echo '<li>' . \esc_html__( 'Buat halaman "Dashboard" lalu sisipkan [sofir_user_dashboard] untuk menampilkan panel pengguna.', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Tempatkan [sofir_login_form] atau [sofir_register_form] di halaman landing/popup agar pengguna bisa login tanpa backend.', 'sofir' ) . '</li>';
        echo '<li>' . \esc_html__( 'Aktifkan opsi "Anyone can register" di Settings → General dan hubungkan dengan modul Membership/Payments untuk akses premium.', 'sofir' ) . '</li>';
        echo '</ol>';
        echo '<p>' . \esc_html__( 'Tip: gunakan filter di atas untuk mengatur limit login, waktu penguncian, atau resource hints sesuai kebutuhan brand Anda.', 'sofir' ) . '</p>';
        echo '</div>';

        echo '</section>';
    }

    public function render_payments_tab(): void {
        PaymentPanel::instance()->render();
    }

    public function render_seo_tab(): void {
        SeoPanel::instance()->render();
    }

    public function render_users_tab(): void {
        UserPanel::instance()->render();
    }

    public function render_voxel_tab(): void {
        if ( isset( $_GET['voxel_settings_saved'] ) ) {
            echo '<div class="notice notice-success is-dismissible"><p><strong>' . \esc_html__( 'Berhasil!', 'sofir' ) . '</strong> ' . \esc_html__( 'Voxel settings berhasil disimpan.', 'sofir' ) . '</p></div>';
        }
        VoxelPanel::instance()->render();
    }

    public function render_tools_tab(): void {
        if ( isset( $_POST['sofir_refresh_cpt'] ) && \check_admin_referer( 'sofir_refresh_cpt' ) ) {
            $this->fix_cpt_menus();
            \delete_option( 'sofir_cpt_definitions_version' );
            \delete_option( 'sofir_multivendor_rewrite_version' );
            \delete_option( 'sofir_multivendor_flush_notice_dismissed' );
            \flush_rewrite_rules();
            
            echo '<div class="notice notice-success"><p><strong>' . \esc_html__( 'Berhasil!', 'sofir' ) . '</strong> ' . \esc_html__( 'CPT definitions dan rewrite rules telah di-refresh. Menu CPT sekarang akan tampil dan dapat diakses di web/frontend.', 'sofir' ) . '</p></div>';
        }
        
        ?>
        <div class="sofir-tools-panel">
            <h2><?php \esc_html_e( 'SOFIR Tools', 'sofir' ); ?></h2>
            
            <div class="sofir-tool-card" style="background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3><?php \esc_html_e( 'Refresh CPT Definitions', 'sofir' ); ?></h3>
                <p><?php \esc_html_e( 'Jika menu CPT tidak tampil di sidebar admin setelah install dari Library, atau jika halaman CPT tidak dapat diakses di web/frontend (event, appointment, booking, restoran, ecourse, dll), gunakan tool ini untuk memperbarui definisi CPT dan rewrite rules.', 'sofir' ); ?></p>
                
                <form method="post">
                    <?php \wp_nonce_field( 'sofir_refresh_cpt' ); ?>
                    <input type="hidden" name="sofir_refresh_cpt" value="1" />
                    <button type="submit" class="button button-primary">
                        <?php \esc_html_e( 'Refresh CPT Definitions', 'sofir' ); ?>
                    </button>
                </form>
                
                <hr style="margin: 20px 0;" />
                
                <h4><?php \esc_html_e( 'Yang akan dilakukan:', 'sofir' ); ?></h4>
                <ul style="list-style: disc; padding-left: 20px;">
                    <li><?php \esc_html_e( 'Memperbarui setting public, show_in_menu, dan publicly_queryable untuk SEMUA CPT', 'sofir' ); ?></li>
                    <li><?php \esc_html_e( 'Memastikan CPT dapat diakses di admin dan web/frontend', 'sofir' ); ?></li>
                    <li><?php \esc_html_e( 'Flush rewrite rules untuk vendor store dan vendor product', 'sofir' ); ?></li>
                    <li><?php \esc_html_e( 'Reset version check untuk memaksa update otomatis', 'sofir' ); ?></li>
                </ul>
            </div>
            
            <div class="sofir-tool-card" style="background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3><?php \esc_html_e( 'Permalinks', 'sofir' ); ?></h3>
                <p><?php \esc_html_e( 'Jika setelah refresh CPT masih ada masalah dengan URL, kunjungi halaman Permalinks dan klik Save Changes.', 'sofir' ); ?></p>
                <a href="<?php echo \esc_url( \admin_url( 'options-permalink.php' ) ); ?>" class="button">
                    <?php \esc_html_e( 'Pergi ke Permalinks', 'sofir' ); ?>
                </a>
            </div>
        </div>
        <?php
    }

    private function get_tabs(): array {
        $tabs = [
            'content'     => \__( 'Content', 'sofir' ),
            'templates'   => \__( 'Templates', 'sofir' ),
            'library'     => \__( 'Library', 'sofir' ),
            'enhancement' => \__( 'Enhancement', 'sofir' ),
            'payments'    => \__( 'Payments', 'sofir' ),
            'seo'         => \__( 'SEO', 'sofir' ),
            'users'       => \__( 'Users', 'sofir' ),
            'voxel'       => \__( 'Voxel', 'sofir' ),
            'tools'       => \__( 'Tools', 'sofir' ),
        ];

        /** @var array<string, string> $tabs */
        $tabs = \apply_filters( 'sofir/admin/tabs', $tabs );

        return $tabs;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function get_enhancement_modules(): array {
        return [
            [
                'slug'         => 'auth',
                'icon'         => '🔑',
                'category'     => \__( 'Autentikasi', 'sofir' ),
                'title'        => \__( 'Login & Registrasi Pengguna', 'sofir' ),
                'status'       => 'active',
                'status_label' => \__( 'Aktif otomatis', 'sofir' ),
                'description'  => \__( 'Shortcode login, registrasi, serta REST API phone login untuk funnel membership.', 'sofir' ),
                'features'     => [
                    \__( 'Form login modern lengkap dengan tautan lupa password & opsi remember me.', 'sofir' ),
                    \__( 'Registrasi email atau nomor telepon saja yang tersimpan sebagai meta sofir_phone.', 'sofir' ),
                    \__( 'Auto login setelah registrasi dan dukungan redirect custom.', 'sofir' ),
                ],
                'shortcodes'   => [
                    '[sofir_login_form redirect="/dashboard"]' => \__( 'Form login dengan redirect otomatis setelah berhasil.', 'sofir' ),
                    '[sofir_register_form]' => \__( 'Form registrasi lengkap (username, email, telepon, password).', 'sofir' ),
                    '[sofir_register_form phone_only="1"]' => \__( 'Mode registrasi cepat hanya menggunakan nomor telepon.', 'sofir' ),
                    '[sofir_logout_link]' => \__( 'Tautan logout yang aman untuk area anggota.', 'sofir' ),
                ],
                'rest'         => [
                    'POST /wp-json/sofir/v1/auth/register'     => \__( 'Registrasi user baru melalui REST API dan auto-login.', 'sofir' ),
                    'POST /wp-json/sofir/v1/auth/phone-login'  => \__( 'Login menggunakan nomor telepon yang telah terdaftar.', 'sofir' ),
                ],
                'filters'      => [],
                'notes'        => '',
            ],
            [
                'slug'         => 'security',
                'icon'         => '🛡️',
                'category'     => \__( 'Keamanan', 'sofir' ),
                'title'        => \__( 'Proteksi Login & Anti-Spam', 'sofir' ),
                'status'       => 'active',
                'status_label' => \__( 'Aktif otomatis', 'sofir' ),
                'description'  => \__( 'Mengunci brute force, menambah honeypot komentar, dan memblokir upload berbahaya tanpa konfigurasi.', 'sofir' ),
                'features'     => [
                    \__( 'Mengunci login selama 15 menit setelah 5 percobaan gagal.', 'sofir' ),
                    \__( 'Honeypot otomatis pada form komentar WordPress.', 'sofir' ),
                    \__( 'Blokir upload file berbahaya (php, exe, js, bat, ps1).', 'sofir' ),
                    \__( 'Mencatat aktivitas login terakhir ke meta sofir_last_login.', 'sofir' ),
                ],
                'shortcodes'   => [],
                'rest'         => [],
                'filters'      => [
                    'sofir/security/max_login_attempts' => \__( 'Ubah jumlah percobaan login sebelum dikunci (default 5).', 'sofir' ),
                    'sofir/security/lock_minutes'       => \__( 'Atur durasi penguncian dalam menit (default 15).', 'sofir' ),
                    'sofir/security/blocked_extensions' => \__( 'Sesuaikan daftar ekstensi file yang diblokir saat upload.', 'sofir' ),
                ],
                'notes'        => \__( 'Pesan error dan notifikasi akan menampilkan informasi berbahasa Indonesia secara otomatis.', 'sofir' ),
            ],
            [
                'slug'         => 'performance',
                'icon'         => '⚡',
                'category'     => \__( 'Performa', 'sofir' ),
                'title'        => \__( 'Optimasi Front-End', 'sofir' ),
                'status'       => 'active',
                'status_label' => \__( 'Aktif otomatis', 'sofir' ),
                'description'  => \__( 'Menonaktifkan skrip tidak penting, membersihkan parameter asset, dan memastikan halaman siap untuk Core Web Vitals.', 'sofir' ),
                'features'     => [
                    \__( 'Nonaktifkan emoji dan wp-embed agar request berkurang.', 'sofir' ),
                    \__( 'Menghapus parameter ?ver pada CSS/JS untuk caching CDN.', 'sofir' ),
                    \__( 'Lazy load iframe serta optimasi atribut gambar (decoding async).', 'sofir' ),
                    \__( 'Menambahkan resource hints (preconnect/dns-prefetch) via filter.', 'sofir' ),
                ],
                'shortcodes'   => [],
                'rest'         => [],
                'filters'      => [
                    'sofir/performance/resource_hints' => \__( 'Tambahkan daftar preconnect/dns-prefetch untuk CDN, font, atau API Anda.', 'sofir' ),
                ],
                'notes'        => \__( 'Gunakan filter resource hints untuk mengoptimalkan koneksi awal (contoh: fonts.googleapis.com, maps.googleapis.com).', 'sofir' ),
            ],
            [
                'slug'         => 'dashboard',
                'icon'         => '📊',
                'category'     => \__( 'User Experience', 'sofir' ),
                'title'        => \__( 'Dashboard Pengguna', 'sofir' ),
                'status'       => 'active',
                'status_label' => \__( 'Aktif otomatis', 'sofir' ),
                'description'  => \__( 'Panel front-end untuk anggota dengan greeting personal, metrik konten, dan daftar posting terbaru.', 'sofir' ),
                'features'     => [
                    \__( 'Menampilkan statistik total konten, role membership, dan aktivitas terakhir.', 'sofir' ),
                    \__( 'Mengambil data dari semua CPT SOFIR tanpa konfigurasi tambahan.', 'sofir' ),
                    \__( 'Fallback otomatis ke [sofir_login_form] bila pengguna belum login.', 'sofir' ),
                ],
                'shortcodes'   => [
                    '[sofir_user_dashboard]' => \__( 'Panel pengguna lengkap untuk halaman "Dashboard" atau "My Account".', 'sofir' ),
                ],
                'rest'         => [],
                'filters'      => [],
                'notes'        => \__( 'Gabungkan dengan modul Membership/Payments untuk memberikan akses khusus plan tertentu.', 'sofir' ),
            ],
        ];
    }

    private function get_active_tab(): string {
        $tabs = array_keys( $this->get_tabs() );
        $tab  = isset( $_GET['tab'] ) ? \sanitize_key( \wp_unslash( $_GET['tab'] ) ) : 'content';

        if ( ! in_array( $tab, $tabs, true ) ) {
            $tab = 'content';
        }

        return $tab;
    }

    private function fix_cpt_menus(): void {
        $manager = \Sofir\Cpt\Manager::instance();
        $post_types = $manager->get_post_types();
        
        foreach ( $post_types as $slug => $definition ) {
            $needs_update = false;
            
            if ( ! isset( $definition['args']['public'] ) || ! $definition['args']['public'] ) {
                $definition['args']['public'] = true;
                $needs_update = true;
            }
            
            if ( ! isset( $definition['args']['show_in_menu'] ) || ! $definition['args']['show_in_menu'] ) {
                $definition['args']['show_in_menu'] = true;
                $needs_update = true;
            }
            
            if ( ! isset( $definition['args']['show_ui'] ) || ! $definition['args']['show_ui'] ) {
                $definition['args']['show_ui'] = true;
                $needs_update = true;
            }
            
            if ( ! isset( $definition['args']['show_in_nav_menus'] ) ) {
                $definition['args']['show_in_nav_menus'] = true;
                $needs_update = true;
            }
            
            if ( ! isset( $definition['args']['publicly_queryable'] ) ) {
                $definition['args']['publicly_queryable'] = true;
                $needs_update = true;
            }
            
            if ( ! isset( $definition['args']['can_export'] ) ) {
                $definition['args']['can_export'] = true;
                $needs_update = true;
            }
            
            if ( ! isset( $definition['args']['exclude_from_search'] ) ) {
                $definition['args']['exclude_from_search'] = false;
                $needs_update = true;
            }
            
            if ( $needs_update ) {
                $payload = $this->convert_definition_to_payload( $slug, $definition );
                $manager->save_post_type( $payload );
            }
        }
    }

    private function convert_definition_to_payload( string $slug, array $definition ): array {
        $args = $definition['args'] ?? [];
        $fields = array_keys( $definition['fields'] ?? [] );
        $taxonomies = $definition['taxonomies'] ?? [];

        $payload = [
            'slug' => $slug,
            'singular' => $args['labels']['singular_name'] ?? ucfirst( $slug ),
            'plural' => $args['labels']['name'] ?? ucfirst( $slug ) . 's',
            'menu_icon' => $args['menu_icon'] ?? 'dashicons-admin-post',
            'supports' => $args['supports'] ?? [],
            'has_archive' => ! empty( $args['has_archive'] ),
            'hierarchical' => ! empty( $args['hierarchical'] ),
            'rest_base' => $args['rest_base'] ?? $slug,
            'rewrite' => is_array( $args['rewrite'] ?? null ) ? ( $args['rewrite']['slug'] ?? $slug ) : $slug,
            'fields' => $fields,
            'taxonomies' => $taxonomies,
        ];

        $filters = [];
        foreach ( $definition['fields'] ?? [] as $field_key => $field_config ) {
            if ( ! empty( $field_config['filterable'] ) ) {
                $filters[] = $field_key;
            }
        }
        $payload['filters'] = $filters;

        if ( ! empty( $definition['template'] ) ) {
            $payload['template'] = $definition['template'];
        }

        if ( ! empty( $definition['template_lock'] ) ) {
            $payload['template_lock'] = $definition['template_lock'];
        }

        return $payload;
    }

    private function get_templates_payload(): array {
        $catalog   = TemplateManager::instance()->get_catalog();
        $sanitized = [];

        foreach ( $catalog as $group => $templates ) {
            $sanitized[ $group ] = [];

            foreach ( $templates as $template ) {
                $sanitized[ $group ][] = [
                    'slug'        => $template['slug'],
                    'title'       => $template['title'],
                    'description' => $template['description'] ?? '',
                    'category'    => $template['category'] ?? $group,
                    'context'     => $template['context'] ?? [ 'page' ],
                ];
            }
        }

        return $sanitized;
    }
}
