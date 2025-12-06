<?php
namespace Sofir\WooCommerceAddon\Addons;

class WCFM_Integration extends Addon_Base {
    public function get_id(): string {
        return 'wcfm-integration';
    }

    public function get_name(): string {
        return __( 'WCFM Integration', 'sofir' );
    }

    public function get_description(): string {
        return __( 'Integrate with WCFM for multi-vendor wholesale pricing and conversation management.', 'sofir' );
    }

    public function get_category(): string {
        return 'integration';
    }

    public function get_icon(): string {
        return 'dashicons-networking';
    }

    public function get_settings(): array {
        return [
            'enable_wcfm_integration' => [
                'type' => 'checkbox',
                'label' => __( 'Enable WCFM Integration', 'sofir' ),
                'default' => false,
                'description' => __( 'Requires WCFM – Frontend Manager plugin', 'sofir' ),
            ],
            'vendor_wholesale_pricing' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Vendor Wholesale Pricing', 'sofir' ),
                'default' => true,
                'description' => __( 'Allow vendors to set wholesale prices for their products', 'sofir' ),
            ],
            'vendor_conversations' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Vendor Conversations', 'sofir' ),
                'default' => true,
                'description' => __( 'Allow vendors to manage conversations with customers', 'sofir' ),
            ],
            'vendor_bulk_orders' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Vendor Bulk Orders', 'sofir' ),
                'default' => true,
                'description' => __( 'Allow vendors to receive and process bulk orders', 'sofir' ),
            ],
            'vendor_quote_requests' => [
                'type' => 'checkbox',
                'label' => __( 'Enable Vendor Quote Requests', 'sofir' ),
                'default' => true,
                'description' => __( 'Allow vendors to respond to quote requests', 'sofir' ),
            ],
            'commission_rate' => [
                'type' => 'number',
                'label' => __( 'Commission Rate (%)', 'sofir' ),
                'default' => 10,
                'min' => 0,
                'max' => 100,
                'step' => 0.1,
                'description' => __( 'Commission rate for vendor transactions', 'sofir' ),
            ],
            'min_vendor_payout' => [
                'type' => 'number',
                'label' => __( 'Minimum Vendor Payout', 'sofir' ),
                'default' => 50,
                'min' => 0,
                'step' => 0.01,
                'description' => __( 'Minimum amount before vendor can request payout', 'sofir' ),
            ],
            'payout_schedule' => [
                'type' => 'select',
                'label' => __( 'Payout Schedule', 'sofir' ),
                'options' => [
                    'daily' => __( 'Daily', 'sofir' ),
                    'weekly' => __( 'Weekly', 'sofir' ),
                    'biweekly' => __( 'Bi-weekly', 'sofir' ),
                    'monthly' => __( 'Monthly', 'sofir' ),
                ],
                'default' => 'weekly',
            ],
            'auto_approve_vendors' => [
                'type' => 'checkbox',
                'label' => __( 'Auto Approve New Vendors', 'sofir' ),
                'default' => false,
            ],
            'vendor_dashboard_tabs' => [
                'type' => 'select',
                'label' => __( 'Vendor Dashboard Tabs', 'sofir' ),
                'options' => [
                    'wholesale' => __( 'Wholesale Pricing', 'sofir' ),
                    'conversations' => __( 'Conversations', 'sofir' ),
                    'bulk_orders' => __( 'Bulk Orders', 'sofir' ),
                    'quotes' => __( 'Quote Requests', 'sofir' ),
                    'analytics' => __( 'Analytics', 'sofir' ),
                ],
                'default' => ['wholesale', 'conversations', 'bulk_orders', 'quotes'],
                'multiple' => true,
            ],
        ];
    }

    public function enable(): void {
        parent::enable();
        
        // Check if WCFM is active
        if ( ! $this->is_wcfm_active() ) {
            return;
        }

        \add_action( 'wcfmmp_after_product_factory', [ $this, 'add_vendor_wholesale_fields' ] );
        \add_action( 'wcfmmp_product_manage_factory_saved', [ $this, 'save_vendor_wholesale_fields' ], 10, 2 );
        \add_action( 'wcfm_product_manage', [ $this, 'add_vendor_wholesale_tab' ], 100 );
        \add_filter( 'wcfm_marketplace_settings_fields', [ $this, 'add_vendor_settings_fields' ] );
        \add_action( 'wcfm_marketplace_settings_saved', [ $this, 'save_vendor_settings' ], 10, 2 );
        \add_filter( 'woocommerce_get_price', [ $this, 'apply_vendor_wholesale_pricing' ], 10, 2 );
        \add_action( 'wcfm_dashboard', [ $this, 'add_vendor_dashboard_sections' ] );
        \add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_vendor_assets' ] );
        \add_action( 'wcfmmp_validate_product', [ $this, 'validate_wholesale_product' ], 10, 2 );
        \add_filter( 'wcfm_is_allow_sale', [ $this, 'allow_wholesale_sale' ], 10, 3 );
        \add_action( 'wcfm_vendor_orders', [ $this, 'add_bulk_order_info' ] );
        \add_filter( 'wcfmmp_get_commission_by_vendor', [ $this, 'calculate_vendor_commission' ], 10, 3 );
        \add_action( 'wcfm_vendor_dashboard_navigation', [ $this, 'add_dashboard_navigation_items' ] );
        \add_action( 'wcfmmp_vendor_payout_processed', [ $this, 'process_vendor_payout' ], 10, 2 );
        \add_shortcode( 'wcfm_vendor_wholesale_products', [ $this, 'render_vendor_wholesale_products' ] );
        \add_action( 'wp_ajax_wcfm_save_wholesale_pricing', [ $this, 'ajax_save_wholesale_pricing' ] );
        \add_action( 'wp_ajax_wcfm_get_vendor_conversations', [ $this, 'ajax_get_vendor_conversations' ] );
        \add_action( 'wp_ajax_wcfm_vendor_send_message', [ $this, 'ajax_vendor_send_message' ] );
    }

    public function disable(): void {
        parent::disable();
        
        \remove_action( 'wcfmmp_after_product_factory', [ $this, 'add_vendor_wholesale_fields' ] );
        \remove_action( 'wcfmmp_product_manage_factory_saved', [ $this, 'save_vendor_wholesale_fields' ], 10 );
        \remove_action( 'wcfm_product_manage', [ $this, 'add_vendor_wholesale_tab' ], 100 );
        \remove_filter( 'wcfm_marketplace_settings_fields', [ $this, 'add_vendor_settings_fields' ] );
        \remove_action( 'wcfm_marketplace_settings_saved', [ $this, 'save_vendor_settings' ], 10 );
        \remove_filter( 'woocommerce_get_price', [ $this, 'apply_vendor_wholesale_pricing' ], 10 );
        \remove_action( 'wcfm_dashboard', [ $this, 'add_vendor_dashboard_sections' ] );
        \remove_action( 'wp_enqueue_scripts', [ $this, 'enqueue_vendor_assets' ] );
        \remove_action( 'wcfmmp_validate_product', [ $this, 'validate_wholesale_product' ], 10 );
        \remove_filter( 'wcfm_is_allow_sale', [ $this, 'allow_wholesale_sale' ], 10 );
        \remove_action( 'wcfm_vendor_orders', [ $this, 'add_bulk_order_info' ] );
        \remove_filter( 'wcfmmp_get_commission_by_vendor', [ $this, 'calculate_vendor_commission' ], 10 );
        \remove_action( 'wcfm_vendor_dashboard_navigation', [ $this, 'add_dashboard_navigation_items' ] );
        \remove_action( 'wcfmmp_vendor_payout_processed', [ $this, 'process_vendor_payout' ], 10 );
        \remove_shortcode( 'wcfm_vendor_wholesale_products' );
        \remove_action( 'wp_ajax_wcfm_save_wholesale_pricing', [ $this, 'ajax_save_wholesale_pricing' ] );
        \remove_action( 'wp_ajax_wcfm_get_vendor_conversations', [ $this, 'ajax_get_vendor_conversations' ] );
        \remove_action( 'wp_ajax_wcfm_vendor_send_message', [ $this, 'ajax_vendor_send_message' ] );
    }

    private function is_wcfm_active(): bool {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        return \is_plugin_active( 'wc-frontend-manager-ultimate/wc_frontend_manager_ultimate.php' ) ||
               \is_plugin_active( 'wc-multivendor-marketplace/wc-multivendor-marketplace.php' );
    }

    public function add_vendor_wholesale_tab(): void {
        if ( ! \get_option( 'sofir_wc_addon_wcfm_integration_vendor_wholesale_pricing', true ) ) {
            return;
        }

        global $WCFM;
        ?>
        <div class="page_collapsible" id="wcfm_wholesale_form">
            <span class="wcfmfa fa-dollar-sign"></span>
            <span class="wcfm_dashboard_item_title"><?php _e( 'Wholesale Pricing', 'sofir' ); ?></span>
        </div>
        <div class="wcfm-container" id="wcfm_wholesale_form_expander">
            <div id="wcfm_wholesale_container" class="wcfm-container-wrap">
                <?php $this->render_wholesale_pricing_form(); ?>
            </div>
        </div>
        <?php
    }

    private function render_wholesale_pricing_form(): void {
        global $WCFM, $product;
        
        $product_id = $product ? $product->get_id() : 0;
        $wholesale_pricing = $this->get_product_wholesale_pricing( $product_id );
        ?>
        <div class="wcfm_ele wcfm_wholesale_pricing">
            <label for="wcfm_wholesale_enabled"><?php _e( 'Enable Wholesale Pricing', 'sofir' ); ?></label>
            <input type="checkbox" id="wcfm_wholesale_enabled" name="wholesale_enabled" value="yes" <?php checked( $wholesale_pricing['enabled'], 'yes' ); ?> />
        </div>

        <div class="wcfm_wholesale_rules wcfm_ele">
            <label><?php _e( 'Wholesale Rules', 'sofir' ); ?></label>
            <div id="wcfm_wholesale_rules_container">
                <?php
                if ( ! empty( $wholesale_pricing['rules'] ) ) {
                    foreach ( $wholesale_pricing['rules'] as $index => $rule ) {
                        $this->render_wholesale_rule_row( $index, $rule );
                    }
                } else {
                    $this->render_wholesale_rule_row( 0 );
                }
                ?>
            </div>
            <button type="button" id="add_wholesale_rule" class="wcfm_add_button wcfm_submit_button"><?php _e( 'Add Rule', 'sofir' ); ?></button>
        </div>
        <?php
    }

    private function render_wholesale_rule_row( $index, $rule = [] ): void {
        $min_qty = $rule['min_qty'] ?? 1;
        $max_qty = $rule['max_qty'] ?? '';
        $price_type = $rule['price_type'] ?? 'fixed';
        $price_value = $rule['price_value'] ?? '';
        ?>
        <div class="wholesale_rule_row" data-index="<?php echo $index; ?>">
            <div class="wcfm_ele">
                <label><?php _e( 'Min Quantity', 'sofir' ); ?></label>
                <input type="number" name="wholesale_rules[<?php echo $index; ?>][min_qty]" value="<?php echo $min_qty; ?>" min="1" />
            </div>
            <div class="wcfm_ele">
                <label><?php _e( 'Max Quantity', 'sofir' ); ?></label>
                <input type="number" name="wholesale_rules[<?php echo $index; ?>][max_qty]" value="<?php echo $max_qty; ?>" min="1" placeholder="<?php _e( 'Unlimited', 'sofir' ); ?>" />
            </div>
            <div class="wcfm_ele">
                <label><?php _e( 'Price Type', 'sofir' ); ?></label>
                <select name="wholesale_rules[<?php echo $index; ?>][price_type]">
                    <option value="fixed" <?php selected( $price_type, 'fixed' ); ?>><?php _e( 'Fixed Price', 'sofir' ); ?></option>
                    <option value="percentage" <?php selected( $price_type, 'percentage' ); ?>><?php _e( 'Percentage Discount', 'sofir' ); ?></option>
                </select>
            </div>
            <div class="wcfm_ele">
                <label><?php _e( 'Price Value', 'sofir' ); ?></label>
                <input type="number" name="wholesale_rules[<?php echo $index; ?>][price_value]" value="<?php echo $price_value; ?>" step="0.01" min="0" />
            </div>
            <button type="button" class="remove_wholesale_rule wcfm_delete_button"><?php _e( 'Remove', 'sofir' ); ?></button>
        </div>
        <?php
    }

    public function add_vendor_wholesale_fields(): void {
        // Additional wholesale fields for product factory
    }

    public function save_vendor_wholesale_fields( $product_id, $product ): void {
        if ( ! isset( $_POST['wholesale_enabled'] ) ) {
            return;
        }

        $wholesale_enabled = $_POST['wholesale_enabled'] === 'yes' ? 'yes' : 'no';
        $wholesale_rules = [];

        if ( isset( $_POST['wholesale_rules'] ) && is_array( $_POST['wholesale_rules'] ) ) {
            foreach ( $_POST['wholesale_rules'] as $rule ) {
                if ( ! empty( $rule['min_qty'] ) && ! empty( $rule['price_value'] ) ) {
                    $wholesale_rules[] = [
                        'min_qty' => intval( $rule['min_qty'] ),
                        'max_qty' => ! empty( $rule['max_qty'] ) ? intval( $rule['max_qty'] ) : 0,
                        'price_type' => sanitize_text_field( $rule['price_type'] ),
                        'price_value' => floatval( $rule['price_value'] ),
                    ];
                }
            }
        }

        $wholesale_pricing = [
            'enabled' => $wholesale_enabled,
            'rules' => $wholesale_rules,
        ];

        \update_post_meta( $product_id, '_vendor_wholesale_pricing', $wholesale_pricing );
    }

    public function add_vendor_settings_fields( $vendor_settings ): array {
        if ( ! \get_option( 'sofir_wc_addon_wcfm_integration_enable_wcfm_integration', false ) ) {
            return $vendor_settings;
        }

        $vendor_settings['wholesale_settings'] = [
            'label' => __( 'Wholesale Settings', 'sofir' ),
            'type' => 'title',
            'class' => 'wcfm_custom_title',
            'desc' => __( 'Configure wholesale pricing and bulk order settings', 'sofir' ),
        ];

        $vendor_settings['wholesale_enabled'] = [
            'label' => __( 'Enable Wholesale', 'sofir' ),
            'type' => 'checkbox',
            'class' => 'wcfm-checkbox',
            'desc' => __( 'Allow customers to request wholesale pricing', 'sofir' ),
        ];

        $vendor_settings['min_wholesale_qty'] = [
            'label' => __( 'Minimum Wholesale Quantity', 'sofir' ),
            'type' => 'number',
            'class' => 'wcfm-text',
            'desc' => __( 'Minimum quantity required for wholesale pricing', 'sofir' ),
        ];

        return $vendor_settings;
    }

    public function save_vendor_settings( $vendor_id, $vendor_data ): void {
        if ( isset( $vendor_data['wholesale_enabled'] ) ) {
            \update_user_meta( $vendor_id, '_vendor_wholesale_enabled', $vendor_data['wholesale_enabled'] );
        }
        if ( isset( $vendor_data['min_wholesale_qty'] ) ) {
            \update_user_meta( $vendor_id, '_vendor_min_wholesale_qty', $vendor_data['min_wholesale_qty'] );
        }
    }

    public function apply_vendor_wholesale_pricing( $price, $product ): float {
        if ( ! \get_option( 'sofir_wc_addon_wcfm_integration_vendor_wholesale_pricing', true ) ) {
            return $price;
        }

        $vendor_id = $this->get_product_vendor_id( $product->get_id() );
        if ( ! $vendor_id ) {
            return $price;
        }

        $wholesale_pricing = $this->get_product_wholesale_pricing( $product->get_id() );
        if ( $wholesale_pricing['enabled'] !== 'yes' || empty( $wholesale_pricing['rules'] ) ) {
            return $price;
        }

        // Check if current user qualifies for wholesale pricing
        $user_quantity = $this->get_user_cart_quantity( $product->get_id() );
        
        foreach ( $wholesale_pricing['rules'] as $rule ) {
            if ( $user_quantity >= $rule['min_qty'] && ( $rule['max_qty'] == 0 || $user_quantity <= $rule['max_qty'] ) ) {
                if ( $rule['price_type'] === 'fixed' ) {
                    return $rule['price_value'];
                } else {
                    return $price * ( 1 - ( $rule['price_value'] / 100 ) );
                }
            }
        }

        return $price;
    }

    private function get_product_wholesale_pricing( $product_id ): array {
        $wholesale_pricing = \get_post_meta( $product_id, '_vendor_wholesale_pricing', true );
        return is_array( $wholesale_pricing ) ? $wholesale_pricing : [ 'enabled' => 'no', 'rules' => [] ];
    }

    private function get_product_vendor_id( $product_id ): int {
        if ( ! function_exists( 'wcfm_get_vendor_id_by_product' ) ) {
            return 0;
        }
        return wcfm_get_vendor_id_by_product( $product_id );
    }

    private function get_user_cart_quantity( $product_id ): int {
        $quantity = 0;
        if ( isset( WC()->cart ) ) {
            foreach ( WC()->cart->get_cart() as $cart_item ) {
                if ( $cart_item['product_id'] == $product_id ) {
                    $quantity += $cart_item['quantity'];
                }
            }
        }
        return $quantity;
    }

    public function add_vendor_dashboard_sections(): void {
        if ( ! \get_option( 'sofir_wc_addon_wcfm_integration_enable_wcfm_integration', false ) ) {
            return;
        }

        $tabs = \get_option( 'sofir_wc_addon_wcfm_integration_vendor_dashboard_tabs', ['wholesale', 'conversations', 'bulk_orders', 'quotes'] );
        
        if ( in_array( 'wholesale', $tabs ) ) {
            echo '<div class="wcfm_dashboard_section">';
            echo '<h3>' . __( 'Wholesale Overview', 'sofir' ) . '</h3>';
            $this->render_wholesale_overview();
            echo '</div>';
        }

        if ( in_array( 'conversations', $tabs ) ) {
            echo '<div class="wcfm_dashboard_section">';
            echo '<h3>' . __( 'Recent Conversations', 'sofir' ) . '</h3>';
            $this->render_vendor_conversations();
            echo '</div>';
        }
    }

    private function render_wholesale_overview(): void {
        $vendor_id = \get_current_user_id();
        $wholesale_orders = $this->get_vendor_wholesale_orders( $vendor_id );
        $total_revenue = $this->get_vendor_wholesale_revenue( $vendor_id );
        
        echo '<div class="wcfm_dashboard_stats">';
        echo '<div class="wcfm_dashboard_stat_block">';
        echo '<span class="wcfm_dashboard_stat_title">' . __( 'Wholesale Orders', 'sofir' ) . '</span>';
        echo '<span class="wcfm_dashboard_stat_value">' . count( $wholesale_orders ) . '</span>';
        echo '</div>';
        echo '<div class="wcfm_dashboard_stat_block">';
        echo '<span class="wcfm_dashboard_stat_title">' . __( 'Wholesale Revenue', 'sofir' ) . '</span>';
        echo '<span class="wcfm_dashboard_stat_value">' . wc_price( $total_revenue ) . '</span>';
        echo '</div>';
        echo '</div>';
    }

    private function render_vendor_conversations(): void {
        $vendor_id = \get_current_user_id();
        $conversations = $this->get_vendor_conversations( $vendor_id, 5 );
        
        if ( ! empty( $conversations ) ) {
            echo '<ul class="wcfm_dashboard_conversations">';
            foreach ( $conversations as $conversation ) {
                echo '<li>';
                echo '<strong>' . get_the_title( $conversation->ID ) . '</strong><br>';
                echo '<small>' . __( 'Last message:', 'sofir' ) . ' ' . wc_format_datetime( $conversation->post_modified ) . '</small>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . __( 'No conversations yet.', 'sofir' ) . '</p>';
        }
    }

    private function get_vendor_wholesale_orders( $vendor_id ): array {
        return get_posts( [
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'meta_key' => '_vendor_id',
            'meta_value' => $vendor_id,
            'meta_query' => [
                [
                    'key' => '_order_type',
                    'value' => 'wholesale',
                ],
            ],
        ] );
    }

    private function get_vendor_wholesale_revenue( $vendor_id ): float {
        $orders = $this->get_vendor_wholesale_orders( $vendor_id );
        $total = 0;
        
        foreach ( $orders as $order ) {
            $order_obj = wc_get_order( $order->ID );
            if ( $order_obj ) {
                $total += $order_obj->get_total();
            }
        }
        
        return $total;
    }

    private function get_vendor_conversations( $vendor_id, $limit = 10 ): array {
        return get_posts( [
            'post_type' => 'sofir_conversation',
            'posts_per_page' => $limit,
            'meta_key' => '_vendor_id',
            'meta_value' => $vendor_id,
            'orderby' => 'modified',
            'order' => 'DESC',
        ] );
    }

    public function enqueue_vendor_assets(): void {
        if ( ! \get_option( 'sofir_wc_addon_wcfm_integration_enable_wcfm_integration', false ) ) {
            return;
        }

        if ( \is_user_logged_in() && \current_user_can( 'wcfm_vendor' ) ) {
            \wp_enqueue_style(
                'sofir-wcfm-integration',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/wcfm-integration.css',
                [],
                '1.0.0'
            );
            
            \wp_enqueue_script(
                'sofir-wcfm-integration',
                SOFIR_PLUGIN_URL . '/modules/woocommerce-addon/assets/wcfm-integration.js',
                [ 'jquery' ],
                '1.0.0',
                true
            );
            
            \wp_localize_script( 'sofir-wcfm-integration', 'sofirWCFM', [
                'ajaxurl' => \admin_url( 'admin-ajax.php' ),
                'nonce' => \wp_create_nonce( 'sofir_wcfm_nonce' ),
                'i18n' => [
                    'confirm_delete' => __( 'Are you sure you want to delete this rule?', 'sofir' ),
                    'saving' => __( 'Saving...', 'sofir' ),
                    'saved' => __( 'Wholesale pricing saved successfully', 'sofir' ),
                ],
            ] );
        }
    }

    public function add_dashboard_navigation_items(): void {
        $tabs = \get_option( 'sofir_wc_addon_wcfm_integration_vendor_dashboard_tabs', ['wholesale', 'conversations', 'bulk_orders', 'quotes'] );
        
        if ( in_array( 'wholesale', $tabs ) ) {
            ?>
            <li class="wcfm_menu_item">
                <a href="#wcfm_wholesale_form" id="wcfm_wholesale_form_menu"><?php _e( 'Wholesale', 'sofir' ); ?></a>
            </li>
            <?php
        }
    }

    public function render_vendor_wholesale_products( $atts ): string {
        $atts = shortcode_atts( [
            'vendor_id' => 0,
            'columns' => 3,
        ], $atts );

        $vendor_id = $atts['vendor_id'] ?: \get_current_user_id();
        $products = $this->get_vendor_wholesale_products( $vendor_id );

        ob_start();
        if ( ! empty( $products ) ) {
            echo '<div class="wcfm_vendor_wholesale_products">';
            foreach ( $products as $product ) {
                $this->render_wholesale_product_card( $product );
            }
            echo '</div>';
        } else {
            echo '<p>' . __( 'No wholesale products available.', 'sofir' ) . '</p>';
        }
        return ob_get_clean();
    }

    private function get_vendor_wholesale_products( $vendor_id ): array {
        return get_posts( [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_key' => '_vendor_id',
            'meta_value' => $vendor_id,
            'meta_query' => [
                [
                    'key' => '_vendor_wholesale_pricing',
                    'value' => 'a:2:{s:7:"enabled";s:3:"yes";s:5:"rules";a:0:{}',
                    'compare' => 'LIKE',
                ],
            ],
        ] );
    }

    private function render_wholesale_product_card( $product ): void {
        $product_obj = wc_get_product( $product->ID );
        $wholesale_pricing = $this->get_product_wholesale_pricing( $product->ID );
        
        echo '<div class="wholesale_product_card">';
        echo '<h4>' . get_the_title( $product->ID ) . '</h4>';
        echo '<div class="wholesale_price_info">';
        echo '<span class="regular_price">' . $product_obj->get_price_html() . '</span>';
        if ( ! empty( $wholesale_pricing['rules'] ) ) {
            echo '<span class="wholesale_badge">' . __( 'Wholesale Available', 'sofir' ) . '</span>';
        }
        echo '</div>';
        echo '</div>';
    }

    public function ajax_save_wholesale_pricing(): void {
        \check_ajax_referer( 'sofir_wcfm_nonce', 'nonce' );

        if ( ! \current_user_can( 'wcfm_vendor' ) ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
        $wholesale_rules = isset( $_POST['wholesale_rules'] ) ? $_POST['wholesale_rules'] : [];

        if ( ! $product_id ) {
            \wp_send_json_error( [ 'message' => __( 'Invalid product ID', 'sofir' ) ] );
        }

        $wholesale_pricing = [
            'enabled' => 'yes',
            'rules' => $this->sanitize_wholesale_rules( $wholesale_rules ),
        ];

        \update_post_meta( $product_id, '_vendor_wholesale_pricing', $wholesale_pricing );
        \wp_send_json_success( [ 'message' => __( 'Wholesale pricing saved', 'sofir' ) ] );
    }

    private function sanitize_wholesale_rules( $rules ): array {
        $sanitized = [];
        
        if ( is_array( $rules ) ) {
            foreach ( $rules as $rule ) {
                if ( ! empty( $rule['min_qty'] ) && ! empty( $rule['price_value'] ) ) {
                    $sanitized[] = [
                        'min_qty' => intval( $rule['min_qty'] ),
                        'max_qty' => ! empty( $rule['max_qty'] ) ? intval( $rule['max_qty'] ) : 0,
                        'price_type' => sanitize_text_field( $rule['price_type'] ),
                        'price_value' => floatval( $rule['price_value'] ),
                    ];
                }
            }
        }
        
        return $sanitized;
    }

    public function ajax_get_vendor_conversations(): void {
        \check_ajax_referer( 'sofir_wcfm_nonce', 'nonce' );

        if ( ! \current_user_can( 'wcfm_vendor' ) ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $vendor_id = \get_current_user_id();
        $conversations = $this->get_vendor_conversations( $vendor_id );
        
        $data = [];
        foreach ( $conversations as $conversation ) {
            $data[] = [
                'id' => $conversation->ID,
                'title' => get_the_title( $conversation->ID ),
                'date' => wc_format_datetime( $conversation->post_modified ),
                'status' => get_post_meta( $conversation->ID, '_conversation_status', true ),
            ];
        }
        
        \wp_send_json_success( $data );
    }

    public function ajax_vendor_send_message(): void {
        \check_ajax_referer( 'sofir_wcfm_nonce', 'nonce' );

        if ( ! \current_user_can( 'wcfm_vendor' ) ) {
            \wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
        }

        $conversation_id = isset( $_POST['conversation_id'] ) ? intval( $_POST['conversation_id'] ) : 0;
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

        if ( ! $conversation_id || empty( $message ) ) {
            \wp_send_json_error( [ 'message' => __( 'Invalid parameters', 'sofir' ) ] );
        }

        $message_id = $this->create_conversation_message( $conversation_id, $message, true );
        
        if ( $message_id ) {
            \wp_send_json_success( [ 'message' => __( 'Message sent', 'sofir' ), 'message_id' => $message_id ] );
        } else {
            \wp_send_json_error( [ 'message' => __( 'Error sending message', 'sofir' ) ] );
        }
    }

    private function create_conversation_message( $conversation_id, $message, $is_admin = false ): int {
        return \wp_insert_post( [
            'post_type' => 'sofir_message',
            'post_content' => $message,
            'post_status' => 'publish',
            'meta_input' => [
                '_conversation_id' => $conversation_id,
                '_is_admin' => $is_admin ? 1 : 0,
                '_vendor_id' => \get_current_user_id(),
            ],
        ] );
    }

    public function calculate_vendor_commission( $commission, $vendor_id, $order ): float {
        $commission_rate = \get_option( 'sofir_wc_addon_wcfm_integration_commission_rate', 10 );
        
        // Apply different commission rates for wholesale orders
        if ( get_post_meta( $order->get_id(), '_order_type', true ) === 'wholesale' ) {
            $commission_rate = $commission_rate * 0.8; // 20% discount for wholesale
        }
        
        return $commission * ( $commission_rate / 100 );
    }

    public function process_vendor_payout( $vendor_id, $payout_data ): void {
        // Additional processing for vendor payouts with wholesale tracking
    }

    public function validate_wholesale_product( $product_id, $product_data ): void {
        if ( isset( $_POST['wholesale_enabled'] ) && $_POST['wholesale_enabled'] === 'yes' ) {
            if ( ! isset( $_POST['wholesale_rules'] ) || empty( $_POST['wholesale_rules'] ) ) {
                \wcfm()->message->add_message( __( 'Please add at least one wholesale rule when wholesale pricing is enabled', 'sofir' ), 'error' );
            }
        }
    }

    public function allow_wholesale_sale( $allow, $product, $vendor_id ): bool {
        $vendor_wholesale_enabled = \get_user_meta( $vendor_id, '_vendor_wholesale_enabled', true );
        
        if ( $vendor_wholesale_enabled === 'no' ) {
            return false;
        }
        
        return $allow;
    }

    public function add_bulk_order_info( $order_id ): void {
        $order = wc_get_order( $order_id );
        if ( $order && get_post_meta( $order_id, '_order_type', true ) === 'wholesale' ) {
            echo '<div class="wcfm_bulk_order_info">';
            echo '<h4>' . __( 'Bulk Order Information', 'sofir' ) . '</h4>';
            echo '<p>' . __( 'This is a bulk wholesale order with special pricing applied.', 'sofir' ) . '</p>';
            echo '</div>';
        }
    }
}
