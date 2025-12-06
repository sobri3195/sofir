<?php
namespace Sofir\WooCommerceAddon\Addons;

class Bulk_Order_Form extends Addon_Base {
    public function get_id(): string {
        return 'bulk-order-form';
    }

    public function get_name(): string {
        return __( 'Bulk Order Form', 'sofir' );
    }

    public function get_description(): string {
        return __( 'Help buyers quickly order large quantities of products with purchase lists and quick order forms.', 'sofir' );
    }

    public function get_category(): string {
        return 'sales';
    }

    public function get_icon(): string {
        return 'dashicons-list-view';
    }

    public function get_settings(): array {
        return [
            'enable_bulk_form' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Bulk Order Form', 'sofir' ),
                'default' => true,
            ],
            'min_quantity' => [
                'type' => 'number',
                'label' => __( 'Minimum Quantity', 'sofir' ),
                'default' => 10,
                'min' => 1,
            ],
            'show_price_breaks' => [
                'type' => 'checkbox',
                'label' => __( 'Show Price Breaks', 'sofir' ),
                'default' => true,
            ],
            'enable_csv_upload' => [
                'type' => 'checkbox',
                'label' => __( 'Enable CSV Upload', 'sofir' ),
                'default' => true,
            ],
            'purchase_lists_enabled' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Purchase Lists', 'sofir' ),
                'default' => true,
            ],
            'form_columns' => [
                'type' => 'select',
                'label' => __( 'Form Columns', 'sofir' ),
                'options' => [
                    'image' => __( 'Product Image', 'sofir' ),
                    'sku' => __( 'SKU', 'sofir' ),
                    'name' => __( 'Product Name', 'sofir' ),
                    'price' => __( 'Price', 'sofir' ),
                    'stock' => __( 'Stock Status', 'sofir' ),
                    'quantity' => __( 'Quantity', 'sofir' ),
                    'total' => __( 'Total', 'sofir' ),
                ],
                'default' => ['image', 'name', 'price', 'quantity', 'total'],
                'multiple' => true,
            ],
        ];
    }

    public function enable(): void {
        parent::enable();
        
        \add_shortcode( 'sofir_bulk_order_form', [ $this, 'render_bulk_order_form' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \add_action( 'init', [ $this, 'register_purchase_lists_cpt' ] );
        \add_action( 'woocommerce_before_cart', [ $this, 'show_bulk_order_link' ] );
        \add_action( 'woocommerce_account_bulk-orders_endpoint', [ $this, 'render_bulk_orders_account' ] );
        
        // Add bulk order endpoint to my account
        \add_action( 'init', [ $this, 'add_bulk_orders_endpoint' ] );
        \add_filter( 'woocommerce_account_menu_items', [ $this, 'add_bulk_orders_menu_item' ] );
    }

    public function disable(): void {
        parent::disable();
        
        \remove_shortcode( 'sofir_bulk_order_form' );
        \remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        \remove_action( 'init', [ $this, 'register_purchase_lists_cpt' ] );
        \remove_action( 'woocommerce_before_cart', [ $this, 'show_bulk_order_link' ] );
        \remove_action( 'woocommerce_account_bulk-orders_endpoint', [ $this, 'render_bulk_orders_account' ] );
        \remove_action( 'init', [ $this, 'add_bulk_orders_endpoint' ] );
        \remove_filter( 'woocommerce_account_menu_items', [ $this, 'add_bulk_orders_menu_item' ] );
    }

    public function enqueue_scripts(): void {
        if ( \get_option( 'sofir_wc_addon_bulk_order_form_enable_bulk_form', true ) ) {
            \wp_enqueue_style(
                'sofir-bulk-order-form',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/bulk-order-form.css',
                [],
                '1.0.0'
            );
            
            \wp_enqueue_script(
                'sofir-bulk-order-form',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/bulk-order-form.js',
                [ 'jquery' ],
                '1.0.0',
                true
            );
            
            \wp_localize_script( 'sofir-bulk-order-form', 'sofirBulkOrder', [
                'ajaxurl' => \admin_url( 'admin-ajax.php' ),
                'nonce' => \wp_create_nonce( 'sofir_bulk_order_nonce' ),
                'i18n' => [
                    'adding_to_cart' => __( 'Adding to cart...', 'sofir' ),
                    'added_to_cart' => __( 'Added to cart', 'sofir' ),
                    'error' => __( 'Error', 'sofir' ),
                    'min_quantity' => \get_option( 'sofir_wc_addon_bulk_order_form_min_quantity', 10 ),
                ],
            ] );
        }
    }

    public function render_bulk_order_form( $atts ): string {
        if ( ! \get_option( 'sofir_wc_addon_bulk_order_form_enable_bulk_form', true ) ) {
            return '';
        }

        $atts = \shortcode_atts( [
            'categories' => '',
            'per_page' => 20,
            'show_search' => 'yes',
            'show_filters' => 'yes',
        ], $atts );

        ob_start();
        ?>
        <div class="sofir-bulk-order-form">
            <div class="sofir-bulk-order-header">
                <h2><?php _e( 'Bulk Order Form', 'sofir' ); ?></h2>
                <p><?php _e( 'Quickly add multiple products to your cart', 'sofir' ); ?></p>
            </div>

            <?php if ( $atts['show_search'] === 'yes' ): ?>
            <div class="sofir-bulk-order-search">
                <input type="text" id="sofir-bulk-search" placeholder="<?php _e( 'Search products...', 'sofir' ); ?>">
                <button type="button" id="sofir-bulk-search-btn"><?php _e( 'Search', 'sofir' ); ?></button>
            </div>
            <?php endif; ?>

            <?php if ( \get_option( 'sofir_wc_addon_bulk_order_form_enable_csv_upload', true ) ): ?>
            <div class="sofir-bulk-order-upload">
                <h3><?php _e( 'Upload CSV', 'sofir' ); ?></h3>
                <input type="file" id="sofir-csv-upload" accept=".csv">
                <button type="button" id="sofir-csv-upload-btn"><?php _e( 'Upload CSV', 'sofir' ); ?></button>
                <small><?php _e( 'CSV format: SKU, Quantity', 'sofir' ); ?></small>
            </div>
            <?php endif; ?>

            <div class="sofir-bulk-order-table">
                <table>
                    <thead>
                        <tr>
                            <?php 
                            $columns = \get_option( 'sofir_wc_addon_bulk_order_form_form_columns', ['image', 'name', 'price', 'quantity', 'total'] );
                            foreach ( $columns as $column ):
                                switch ( $column ) {
                                    case 'image':
                                        echo '<th>' . __( 'Image', 'sofir' ) . '</th>';
                                        break;
                                    case 'sku':
                                        echo '<th>' . __( 'SKU', 'sofir' ) . '</th>';
                                        break;
                                    case 'name':
                                        echo '<th>' . __( 'Product', 'sofir' ) . '</th>';
                                        break;
                                    case 'price':
                                        echo '<th>' . __( 'Price', 'sofir' ) . '</th>';
                                        break;
                                    case 'stock':
                                        echo '<th>' . __( 'Stock', 'sofir' ) . '</th>';
                                        break;
                                    case 'quantity':
                                        echo '<th>' . __( 'Quantity', 'sofir' ) . '</th>';
                                        break;
                                    case 'total':
                                        echo '<th>' . __( 'Total', 'sofir' ) . '</th>';
                                        break;
                                }
                            endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody id="sofir-bulk-products">
                        <!-- Products will be loaded here -->
                    </tbody>
                </table>
            </div>

            <div class="sofir-bulk-order-actions">
                <button type="button" id="sofir-add-all-to-cart" class="button alt">
                    <?php _e( 'Add All to Cart', 'sofir' ); ?>
                </button>
                <?php if ( \get_option( 'sofir_wc_addon_bulk_order_form_purchase_lists_enabled', true ) && \is_user_logged_in() ): ?>
                <button type="button" id="sofir-save-purchase-list" class="button">
                    <?php _e( 'Save as Purchase List', 'sofir' ); ?>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function register_purchase_lists_cpt(): void {
        if ( ! \get_option( 'sofir_wc_addon_bulk_order_form_purchase_lists_enabled', true ) ) {
            return;
        }

        \register_post_type( 'sofir_purchase_list', [
            'label' => __( 'Purchase Lists', 'sofir' ),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'supports' => [ 'title', 'author' ],
            'show_in_menu' => 'sofir-woocommerce-addon',
        ] );
    }

    public function add_bulk_orders_endpoint(): void {
        \add_rewrite_endpoint( 'bulk-orders', EP_ROOT | EP_PAGES );
    }

    public function add_bulk_orders_menu_item( $items ): array {
        $items['bulk-orders'] = __( 'Bulk Orders', 'sofir' );
        return $items;
    }

    public function render_bulk_orders_account(): void {
        if ( ! \get_option( 'sofir_wc_addon_bulk_order_form_purchase_lists_enabled', true ) ) {
            return;
        }

        $user_id = \get_current_user_id();
        $purchase_lists = \get_posts( [
            'post_type' => 'sofir_purchase_list',
            'author' => $user_id,
            'posts_per_page' => -1,
        ] );

        echo '<h2>' . __( 'Bulk Orders & Purchase Lists', 'sofir' ) . '</h2>';
        
        if ( ! empty( $purchase_lists ) ) {
            echo '<div class="sofir-purchase-lists">';
            foreach ( $purchase_lists as $list ) {
                echo '<div class="purchase-list-item">';
                echo '<h3>' . \get_the_title( $list->ID ) . '</h3>';
                echo '<p>' . __( 'Created:', 'sofir' ) . ' ' . \get_the_date( '', $list->ID ) . '</p>';
                echo '<button class="button load-purchase-list" data-list-id="' . $list->ID . '">' . __( 'Load List', 'sofir' ) . '</button>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>' . __( 'No purchase lists found.', 'sofir' ) . '</p>';
        }
    }

    public function show_bulk_order_link(): void {
        if ( ! \get_option( 'sofir_wc_addon_bulk_order_form_enable_bulk_form', true ) ) {
            return;
        }

        echo '<div class="sofir-bulk-order-link">';
        echo '<a href="' . \get_permalink( \get_option( 'sofir_bulk_order_page' ) ) . '" class="button">' . __( 'Bulk Order Form', 'sofir' ) . '</a>';
        echo '</div>';
    }
}