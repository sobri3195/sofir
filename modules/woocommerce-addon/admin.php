<?php
namespace Sofir\WooCommerceAddon;

class Admin {
    private static ?Admin $instance = null;

    public static function instance(): Admin {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function render_dashboard(): void {
        $enabled = \get_option( 'sofir_woocommerce_addon_enabled', false );
        $product_count = wp_count_posts( 'product' );
        $order_count = wp_count_posts( 'shop_order' );
        $products = isset( $product_count->publish ) ? $product_count->publish : 0;
        $orders = isset( $order_count->{'wc-completed'} ) ? $order_count->{'wc-completed'} : 0;

        ?>
        <div class="wrap sofir-wc-addon-wrapper">
            <h1><?php echo esc_html( \__( 'WooCommerce Addon', 'sofir' ) ); ?></h1>

            <div class="sofir-wc-dashboard">
                <div class="dashboard-header">
                    <div class="header-content">
                        <h2><?php echo esc_html( \__( 'Addon Status', 'sofir' ) ); ?></h2>
                        <p class="description"><?php echo esc_html( \__( 'Manage your WooCommerce integration and extensions', 'sofir' ) ); ?></p>
                    </div>
                    <div class="status-badge <?php echo $enabled ? 'active' : 'inactive'; ?>">
                        <span class="badge-text">
                            <?php echo $enabled ? esc_html( \__( 'Active', 'sofir' ) ) : esc_html( \__( 'Inactive', 'sofir' ) ); ?>
                        </span>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">📦</div>
                        <div class="stat-content">
                            <h3><?php echo esc_html( $products ); ?></h3>
                            <p><?php echo esc_html( \__( 'Products', 'sofir' ) ); ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">🛒</div>
                        <div class="stat-content">
                            <h3><?php echo esc_html( $orders ); ?></h3>
                            <p><?php echo esc_html( \__( 'Completed Orders', 'sofir' ) ); ?></p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">⚙️</div>
                        <div class="stat-content">
                            <h3><?php echo esc_html( \__( 'Version', 'sofir' ) ); ?></h3>
                            <p><?php echo esc_html( \get_option( 'woocommerce_db_version', 'Unknown' ) ); ?></p>
                        </div>
                    </div>
                </div>

                <div class="addon-controls">
                    <label class="toggle-switch">
                        <input type="checkbox" id="addon-toggle" class="toggle-input" <?php checked( $enabled ); ?> />
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">
                        <?php echo esc_html( \__( 'Enable WooCommerce Addon Features', 'sofir' ) ); ?>
                    </span>
                </div>

                <div class="quick-links">
                    <h3><?php echo esc_html( \__( 'Quick Links', 'sofir' ) ); ?></h3>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=sofir-woocommerce-addon-snippets' ) ); ?>" class="link-card">
                        <span class="icon">📝</span>
                        <span class="text"><?php echo esc_html( \__( 'Code Snippets', 'sofir' ) ); ?></span>
                    </a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=sofir-woocommerce-addon-extensions' ) ); ?>" class="link-card">
                        <span class="icon">🔌</span>
                        <span class="text"><?php echo esc_html( \__( 'Extensions', 'sofir' ) ); ?></span>
                    </a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=sofir-woocommerce-addon-settings' ) ); ?>" class="link-card">
                        <span class="icon">⚙️</span>
                        <span class="text"><?php echo esc_html( \__( 'Settings', 'sofir' ) ); ?></span>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_snippets_page(): void {
        $snippets = Snippets::instance()->get_all_snippets();
        $categories = Snippets::instance()->get_categories();

        ?>
        <div class="wrap sofir-wc-addon-wrapper">
            <h1><?php echo esc_html( \__( 'WooCommerce Code Snippets', 'sofir' ) ); ?></h1>

            <div class="snippets-container">
                <div class="snippets-sidebar">
                    <div class="category-filter">
                        <h3><?php echo esc_html( \__( 'Categories', 'sofir' ) ); ?></h3>
                        <div class="category-list">
                            <a href="#" class="category-item active" data-category="all">
                                <?php echo esc_html( \__( 'All Snippets', 'sofir' ) ); ?>
                            </a>
                            <?php foreach ( $categories as $cat ) : ?>
                                <a href="#" class="category-item" data-category="<?php echo esc_attr( $cat ); ?>">
                                    <?php echo esc_html( ucfirst( $cat ) ); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="external-resources">
                        <h3><?php echo esc_html( \__( 'External Resources', 'sofir' ) ); ?></h3>
                        <ul>
                            <li>
                                <a href="https://wpbeaches.com/tag/woocommerce/" target="_blank" rel="noopener noreferrer">
                                    📚 <?php echo esc_html( \__( 'WP Beaches WooCommerce', 'sofir' ) ); ?>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.woocommerce.com/document/" target="_blank" rel="noopener noreferrer">
                                    📖 <?php echo esc_html( \__( 'WooCommerce Docs', 'sofir' ) ); ?>
                                </a>
                            </li>
                            <li>
                                <a href="https://github.com/woocommerce/woocommerce" target="_blank" rel="noopener noreferrer">
                                    🔧 <?php echo esc_html( \__( 'WooCommerce GitHub', 'sofir' ) ); ?>
                                </a>
                            </li>
                            <li>
                                <a href="https://stackoverflow.com/questions/tagged/woocommerce" target="_blank" rel="noopener noreferrer">
                                    ❓ <?php echo esc_html( \__( 'Stack Overflow', 'sofir' ) ); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="snippets-main">
                    <div class="snippets-header">
                        <input type="text" id="snippet-search" class="snippet-search" placeholder="<?php echo esc_attr( \__( 'Search snippets...', 'sofir' ) ); ?>" />
                        <button class="button button-primary" id="add-snippet-btn">
                            <?php echo esc_html( \__( '+ Add Custom Snippet', 'sofir' ) ); ?>
                        </button>
                    </div>

                    <div class="snippets-grid" id="snippets-grid">
                        <?php if ( empty( $snippets ) ) : ?>
                            <div class="no-snippets">
                                <p><?php echo esc_html( \__( 'No snippets found. Create your first snippet!', 'sofir' ) ); ?></p>
                            </div>
                        <?php else : ?>
                            <?php foreach ( $snippets as $snippet ) : ?>
                                <div class="snippet-card" data-category="<?php echo esc_attr( $snippet['category'] ); ?>">
                                    <div class="snippet-header">
                                        <h4><?php echo esc_html( $snippet['name'] ); ?></h4>
                                        <span class="snippet-category"><?php echo esc_html( ucfirst( $snippet['category'] ) ); ?></span>
                                    </div>
                                    <p class="snippet-description"><?php echo esc_html( substr( $snippet['code'], 0, 100 ) . '...' ); ?></p>
                                    <div class="snippet-actions">
                                        <button class="btn-small view-snippet" data-id="<?php echo esc_attr( $snippet['id'] ); ?>">
                                            <?php echo esc_html( \__( 'View', 'sofir' ) ); ?>
                                        </button>
                                        <button class="btn-small copy-snippet" data-id="<?php echo esc_attr( $snippet['id'] ); ?>">
                                            <?php echo esc_html( \__( 'Copy', 'sofir' ) ); ?>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Snippet Modal -->
            <div id="snippet-modal" class="snippet-modal">
                <div class="modal-content">
                    <button class="close-modal">&times;</button>
                    <h2 id="modal-title"><?php echo esc_html( \__( 'Code Snippet', 'sofir' ) ); ?></h2>
                    <div id="modal-body" class="modal-body">
                        <pre id="snippet-code"><code></code></pre>
                        <div class="modal-actions">
                            <button class="button button-primary" id="copy-code-btn">
                                <?php echo esc_html( \__( 'Copy Code', 'sofir' ) ); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function render_extensions_page(): void {
        $extensions = Snippets::instance()->get_extensions();

        ?>
        <div class="wrap sofir-wc-addon-wrapper">
            <h1><?php echo esc_html( \__( 'WooCommerce Extensions', 'sofir' ) ); ?></h1>

            <div class="extensions-info">
                <p><?php echo esc_html( \__( 'Recommended extensions to enhance your WooCommerce store with SOFIR', 'sofir' ) ); ?></p>
            </div>

            <div class="extensions-grid">
                <?php foreach ( $extensions as $ext ) : ?>
                    <div class="extension-card">
                        <div class="ext-header">
                            <h3><?php echo esc_html( $ext['name'] ); ?></h3>
                            <span class="ext-rating">
                                ⭐ <?php echo esc_html( $ext['rating'] ); ?>
                            </span>
                        </div>
                        <p class="ext-description"><?php echo esc_html( $ext['description'] ); ?></p>
                        <div class="ext-details">
                            <span class="ext-price">
                                <?php echo esc_html( $ext['price'] ); ?>
                            </span>
                            <span class="ext-type">
                                <?php echo esc_html( $ext['type'] ); ?>
                            </span>
                        </div>
                        <a href="<?php echo esc_url( $ext['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary">
                            <?php echo esc_html( \__( 'Learn More', 'sofir' ) ); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }

    public function render_settings_page(): void {
        $settings = [
            'enable_orders_sync' => \get_option( 'sofir_wc_enable_orders_sync', true ),
            'enable_product_sync' => \get_option( 'sofir_wc_enable_product_sync', true ),
            'enable_webhooks' => \get_option( 'sofir_wc_enable_webhooks', false ),
            'webhook_url' => \get_option( 'sofir_wc_webhook_url', '' ),
        ];

        ?>
        <div class="wrap sofir-wc-addon-wrapper">
            <h1><?php echo esc_html( \__( 'WooCommerce Addon Settings', 'sofir' ) ); ?></h1>

            <form method="post" action="options.php" class="wc-addon-settings-form">
                <?php settings_fields( 'sofir_wc_addon_settings' ); ?>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="enable-orders-sync">
                                    <?php echo esc_html( \__( 'Enable Orders Sync', 'sofir' ) ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="checkbox" id="enable-orders-sync" name="sofir_wc_enable_orders_sync" value="1" <?php checked( $settings['enable_orders_sync'] ); ?> />
                                <p class="description">
                                    <?php echo esc_html( \__( 'Synchronize WooCommerce orders with SOFIR', 'sofir' ) ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="enable-product-sync">
                                    <?php echo esc_html( \__( 'Enable Product Sync', 'sofir' ) ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="checkbox" id="enable-product-sync" name="sofir_wc_enable_product_sync" value="1" <?php checked( $settings['enable_product_sync'] ); ?> />
                                <p class="description">
                                    <?php echo esc_html( \__( 'Synchronize WooCommerce products with SOFIR', 'sofir' ) ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="enable-webhooks">
                                    <?php echo esc_html( \__( 'Enable Webhooks', 'sofir' ) ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="checkbox" id="enable-webhooks" name="sofir_wc_enable_webhooks" value="1" <?php checked( $settings['enable_webhooks'] ); ?> />
                                <p class="description">
                                    <?php echo esc_html( \__( 'Send WooCommerce events to external webhooks', 'sofir' ) ); ?>
                                </p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="webhook-url">
                                    <?php echo esc_html( \__( 'Webhook URL', 'sofir' ) ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="url" id="webhook-url" name="sofir_wc_webhook_url" value="<?php echo esc_attr( $settings['webhook_url'] ); ?>" class="regular-text" />
                                <p class="description">
                                    <?php echo esc_html( \__( 'URL to receive WooCommerce events (e.g., https://example.com/webhook)', 'sofir' ) ); ?>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function render_admin_tab(): void {
        $enabled = \get_option( 'sofir_woocommerce_addon_enabled', false );

        ?>
        <div class="sofir-admin-tab-woocommerce-addon">
            <h3><?php echo esc_html( \__( 'WooCommerce Addon', 'sofir' ) ); ?></h3>

            <table class="wp-list-table widefat striped">
                <tbody>
                    <tr>
                        <td><?php echo esc_html( \__( 'Status', 'sofir' ) ); ?></td>
                        <td>
                            <span class="status-badge <?php echo $enabled ? 'active' : 'inactive'; ?>">
                                <?php echo $enabled ? esc_html( \__( 'Active', 'sofir' ) ) : esc_html( \__( 'Inactive', 'sofir' ) ); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo esc_html( \__( 'WooCommerce Version', 'sofir' ) ); ?></td>
                        <td><?php echo esc_html( \get_option( 'woocommerce_db_version', 'Unknown' ) ); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=sofir-woocommerce-addon' ) ); ?>" class="button">
                                <?php echo esc_html( \__( 'Manage Addon', 'sofir' ) ); ?>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function render_addons_page(): void {
        $addons_manager = Addons_Manager::instance();
        $addons = $addons_manager->get_addons();
        $categories = $addons_manager->get_categories();

        $current_category = isset( $_GET['category'] ) ? \sanitize_text_field( $_GET['category'] ) : 'all';

        if ( isset( $_POST['save_addon_settings'] ) && \check_admin_referer( 'sofir_addon_settings' ) ) {
            $addon_id = \sanitize_text_field( $_POST['addon_id'] );
            $addon = $addons_manager->get_addon( $addon_id );

            if ( $addon ) {
                $settings = [];
                foreach ( $_POST as $key => $value ) {
                    if ( strpos( $key, 'sofir_wc_addon_' . $addon_id . '_' ) === 0 ) {
                        $setting_key = str_replace( 'sofir_wc_addon_' . $addon_id . '_', '', $key );
                        $settings[ $setting_key ] = \sanitize_text_field( $value );
                    }
                }
                $addon->save_settings( $settings );
                echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved successfully.', 'sofir' ) . '</p></div>';
            }
        }

        ?>
        <div class="wrap sofir-wc-addon-wrapper sofir-addons-page">
            <h1><?php esc_html_e( 'WooCommerce Addons', 'sofir' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Enable or disable modular WooCommerce features. Inspired by WPClever and WPXPO.', 'sofir' ); ?></p>

            <div class="sofir-addons-filters">
                <div class="category-tabs">
                    <a href="<?php echo esc_url( \admin_url( 'admin.php?page=sofir-woocommerce-addon-addons&category=all' ) ); ?>" 
                       class="category-tab <?php echo $current_category === 'all' ? 'active' : ''; ?>">
                        <?php esc_html_e( 'All Addons', 'sofir' ); ?>
                        <span class="count"><?php echo count( $addons ); ?></span>
                    </a>
                    <?php foreach ( $categories as $cat_id => $cat_name ) :
                        $cat_addons = $addons_manager->get_addons_by_category( $cat_id );
                        if ( empty( $cat_addons ) ) {
                            continue;
                        }
                    ?>
                    <a href="<?php echo esc_url( \admin_url( 'admin.php?page=sofir-woocommerce-addon-addons&category=' . $cat_id ) ); ?>" 
                       class="category-tab <?php echo $current_category === $cat_id ? 'active' : ''; ?>">
                        <?php echo esc_html( $cat_name ); ?>
                        <span class="count"><?php echo count( $cat_addons ); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="sofir-addons-grid">
                <?php
                $filtered_addons = $current_category === 'all' ? $addons : $addons_manager->get_addons_by_category( $current_category );

                foreach ( $filtered_addons as $addon ) :
                    $is_enabled = $addon->is_enabled();
                    $addon_class = $is_enabled ? 'enabled' : 'disabled';
                ?>
                <div class="sofir-addon-card <?php echo esc_attr( $addon_class ); ?>" data-addon-id="<?php echo esc_attr( $addon->get_id() ); ?>">
                    <div class="addon-header">
                        <div class="addon-icon"><?php echo $addon->get_icon(); ?></div>
                        <div class="addon-title-wrap">
                            <h3 class="addon-title"><?php echo esc_html( $addon->get_name() ); ?></h3>
                            <?php if ( $addon->is_pro() ) : ?>
                            <span class="pro-badge">PRO</span>
                            <?php endif; ?>
                        </div>
                        <label class="addon-toggle">
                            <input type="checkbox" 
                                   class="addon-toggle-input" 
                                   data-addon-id="<?php echo esc_attr( $addon->get_id() ); ?>"
                                   <?php checked( $is_enabled ); ?>
                                   <?php echo $addon->is_pro() ? 'disabled' : ''; ?>>
                            <span class="addon-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="addon-body">
                        <p class="addon-description"><?php echo esc_html( $addon->get_description() ); ?></p>
                        <span class="addon-category"><?php echo esc_html( $categories[ $addon->get_category() ] ?? $addon->get_category() ); ?></span>
                    </div>
                    <div class="addon-footer">
                        <?php if ( $is_enabled && ! $addon->is_pro() ) : ?>
                        <button type="button" class="button button-secondary sofir-addon-settings-btn" data-addon-id="<?php echo esc_attr( $addon->get_id() ); ?>">
                            ⚙️ <?php esc_html_e( 'Settings', 'sofir' ); ?>
                        </button>
                        <?php elseif ( $addon->is_pro() ) : ?>
                        <a href="#" class="button button-primary disabled">
                            🔒 <?php esc_html_e( 'Upgrade to Pro', 'sofir' ); ?>
                        </a>
                        <?php endif; ?>
                    </div>

                    <?php if ( $is_enabled && ! $addon->is_pro() ) : ?>
                    <div class="addon-settings-panel" style="display: none;">
                        <h4><?php echo esc_html( $addon->get_name() ); ?> <?php esc_html_e( 'Settings', 'sofir' ); ?></h4>
                        <form method="post" action="">
                            <?php \wp_nonce_field( 'sofir_addon_settings' ); ?>
                            <input type="hidden" name="addon_id" value="<?php echo esc_attr( $addon->get_id() ); ?>">
                            <table class="form-table">
                                <tbody>
                                    <?php $addon->render_settings(); ?>
                                </tbody>
                            </table>
                            <p class="submit">
                                <button type="submit" name="save_addon_settings" class="button button-primary">
                                    <?php esc_html_e( 'Save Settings', 'sofir' ); ?>
                                </button>
                                <button type="button" class="button sofir-addon-settings-close">
                                    <?php esc_html_e( 'Cancel', 'sofir' ); ?>
                                </button>
                            </p>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
}
