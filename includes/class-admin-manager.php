<?php
namespace Sofir\Admin;

use Sofir\Admin\ContentPanel;
use Sofir\Admin\SeoPanel;
use Sofir\Admin\TemplatesPanel;
use Sofir\Admin\LibraryPanel;
use Sofir\Admin\UserPanel;
use Sofir\Admin\PaymentPanel;
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
        \add_action( 'sofir/admin/tab/tools', [ $this, 'render_tools_tab' ] );

        ContentPanel::instance()->boot();
        LibraryPanel::instance()->boot();
        PaymentPanel::instance()->boot();
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
        echo '<p>' . \esc_html__( 'Aktifkan modul login, keamanan, performa, dan dashboard pengguna.', 'sofir' ) . '</p>';
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

    public function render_tools_tab(): void {
        if ( isset( $_POST['sofir_refresh_cpt'] ) && \check_admin_referer( 'sofir_refresh_cpt' ) ) {
            \delete_option( 'sofir_cpt_definitions_version' );
            \delete_option( 'sofir_multivendor_rewrite_version' );
            \delete_option( 'sofir_multivendor_flush_notice_dismissed' );
            \flush_rewrite_rules();
            
            echo '<div class="notice notice-success"><p><strong>' . \esc_html__( 'Berhasil!', 'sofir' ) . '</strong> ' . \esc_html__( 'CPT definitions dan rewrite rules telah di-refresh. Menu CPT sekarang akan tampil.', 'sofir' ) . '</p></div>';
        }
        
        ?>
        <div class="sofir-tools-panel">
            <h2><?php \esc_html_e( 'SOFIR Tools', 'sofir' ); ?></h2>
            
            <div class="sofir-tool-card" style="background: #fff; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <h3><?php \esc_html_e( 'Refresh CPT Definitions', 'sofir' ); ?></h3>
                <p><?php \esc_html_e( 'Jika menu CPT (Listing, Profile, Article, Event, Appointment) tidak tampil di sidebar admin, atau jika halaman vendor tidak tampil, gunakan tool ini untuk memperbarui definisi CPT dan rewrite rules.', 'sofir' ); ?></p>
                
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
                    <li><?php \esc_html_e( 'Memperbarui setting show_in_menu untuk semua CPT bawaan', 'sofir' ); ?></li>
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
            'tools'       => \__( 'Tools', 'sofir' ),
        ];

        /** @var array<string, string> $tabs */
        $tabs = \apply_filters( 'sofir/admin/tabs', $tabs );

        return $tabs;
    }

    private function get_active_tab(): string {
        $tabs = array_keys( $this->get_tabs() );
        $tab  = isset( $_GET['tab'] ) ? \sanitize_key( \wp_unslash( $_GET['tab'] ) ) : 'content';

        if ( ! in_array( $tab, $tabs, true ) ) {
            $tab = 'content';
        }

        return $tab;
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
