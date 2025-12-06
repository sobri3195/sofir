<?php
namespace Sofir\WooCommerceAddon;

class Snippets {
    private static ?Snippets $instance = null;
    private array $built_in_snippets = [];

    public static function instance(): Snippets {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        $this->load_built_in_snippets();
    }

    private function load_built_in_snippets(): void {
        $this->built_in_snippets = [
            'custom-product-field' => [
                'id' => 'custom-product-field',
                'name' => \__( 'Add Custom Product Field', 'sofir' ),
                'category' => 'products',
                'code' => "// Hook into WooCommerce product admin
add_action( 'woocommerce_product_options_general_product_data', 'add_custom_product_field' );
add_action( 'woocommerce_process_product_meta', 'save_custom_product_field' );

function add_custom_product_field() {
    woocommerce_wp_text_input( [
        'id' => '_custom_field',
        'label' => __( 'Custom Field', 'woocommerce' ),
        'placeholder' => 'Enter value',
        'desc_tip' => true,
        'description' => __( 'A custom product field', 'woocommerce' ),
    ] );
}

function save_custom_product_field( \$product_id ) {
    \$value = isset( \$_POST['_custom_field'] ) ? sanitize_text_field( \$_POST['_custom_field'] ) : '';
    update_post_meta( \$product_id, '_custom_field', \$value );
}",
                'description' => 'Add custom fields to WooCommerce products',
            ],
            'hide-add-to-cart' => [
                'id' => 'hide-add-to-cart',
                'name' => \__( 'Hide Add to Cart Button', 'sofir' ),
                'category' => 'products',
                'code' => "// Hide Add to Cart for specific product categories
add_filter( 'woocommerce_product_add_to_cart_text', 'hide_add_to_cart_button', 10, 2 );

function hide_add_to_cart_button( \$text, \$product ) {
    // Check if product is in 'excluded-category'
    if ( has_term( 'excluded-category', 'product_cat', \$product->get_id() ) ) {
        return ''; // Hide button
    }
    return \$text;
}",
                'description' => 'Hide Add to Cart button for specific categories',
            ],
            'custom-checkout-field' => [
                'id' => 'custom-checkout-field',
                'name' => \__( 'Add Custom Checkout Field', 'sofir' ),
                'category' => 'checkout',
                'code' => "// Add custom field to checkout
add_action( 'woocommerce_checkout_fields', 'add_custom_checkout_field' );

function add_custom_checkout_field( \$fields ) {
    \$fields['billing']['billing_company_id'] = [
        'type' => 'text',
        'label' => __( 'Company ID', 'woocommerce' ),
        'placeholder' => _x( 'Company ID', 'placeholder', 'woocommerce' ),
        'required' => true,
        'clear' => true,
    ];
    return \$fields;
}

// Save custom field
add_action( 'woocommerce_checkout_process', 'validate_custom_checkout_field' );
function validate_custom_checkout_field() {
    if ( empty( \$_POST['post_data']['billing_company_id'] ) ) {
        wc_add_notice( __( 'Company ID is required', 'woocommerce' ), 'error' );
    }
}",
                'description' => 'Add custom fields to WooCommerce checkout',
            ],
            'product-by-category' => [
                'id' => 'product-by-category',
                'name' => \__( 'Get Products by Category', 'sofir' ),
                'category' => 'queries',
                'code' => "// Get products from specific category
function get_products_by_category( \$category_slug, \$limit = 10 ) {
    \$args = [
        'post_type' => 'product',
        'posts_per_page' => \$limit,
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => \$category_slug,
            ],
        ],
    ];
    
    \$products = new WP_Query( \$args );
    return \$products->posts;
}

// Usage:
\$products = get_products_by_category( 'clothing' );
foreach ( \$products as \$product ) {
    \$prod = wc_get_product( \$product->ID );
    echo \$prod->get_name() . ' - ' . \$prod->get_price();
}",
                'description' => 'Query products by category',
            ],
            'product-stock-alert' => [
                'id' => 'product-stock-alert',
                'name' => \__( 'Stock Status Alert', 'sofir' ),
                'category' => 'inventory',
                'code' => "// Alert when product stock is low
add_action( 'woocommerce_product_set_stock', 'check_low_stock' );

function check_low_stock( \$product ) {
    if ( ! \$product instanceof WC_Product ) {
        return;
    }
    
    \$stock = \$product->get_stock_quantity();
    \$min_stock = 5;
    
    if ( \$stock <= \$min_stock ) {
        // Send admin notification
        \$admin_email = get_option( 'admin_email' );
        wp_mail(
            \$admin_email,
            'Low Stock Alert: ' . \$product->get_name(),
            'Product ' . \$product->get_name() . ' stock is low: ' . \$stock . ' units'
        );
    }
}",
                'description' => 'Send alert when product stock is low',
            ],
            'custom-order-status' => [
                'id' => 'custom-order-status',
                'name' => \__( 'Register Custom Order Status', 'sofir' ),
                'category' => 'orders',
                'code' => "// Register custom order status
add_action( 'init', 'register_custom_order_status' );

function register_custom_order_status() {
    register_post_status(
        'wc-custom-processing',
        [
            'label' => __( 'Custom Processing', 'woocommerce' ),
            'public' => false,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Custom Processing <span class=\"count\">(%s)</span>', 'Custom Processing <span class=\"count\">(%s)</span>' ),
        ]
    );
}

// Add to order dropdown
add_filter( 'wc_order_statuses', 'add_custom_order_status' );
function add_custom_order_status( \$statuses ) {
    \$statuses['wc-custom-processing'] = __( 'Custom Processing', 'woocommerce' );
    return \$statuses;
}",
                'description' => 'Register custom WooCommerce order status',
            ],
            'apply-discount-email' => [
                'id' => 'apply-discount-email',
                'name' => \__( 'Apply Discount by Email', 'sofir' ),
                'category' => 'discounts',
                'code' => "// Auto-apply discount for specific emails
add_filter( 'woocommerce_coupon_get_discount_amount', 'auto_apply_discount', 10, 5 );

function auto_apply_discount( \$discount, \$discounting, \$cart_item, \$is_cart, \$coupon ) {
    \$customer_email = WC()->customer->get_email();
    \$discount_emails = [ 'vip@example.com', 'loyal@example.com' ];
    
    if ( in_array( \$customer_email, \$discount_emails ) ) {
        \$discount = \$discount * 1.5; // Give 50% extra discount
    }
    
    return \$discount;
}",
                'description' => 'Auto-apply discount for specific customer emails',
            ],
            'track-conversion' => [
                'id' => 'track-conversion',
                'name' => \__( 'Track Conversion Events', 'sofir' ),
                'category' => 'analytics',
                'code' => "// Track conversion events
add_action( 'woocommerce_order_status_completed', 'track_order_conversion' );

function track_order_conversion( \$order_id ) {
    \$order = wc_get_order( \$order_id );
    
    // Track with Google Analytics
    do_action( 'sofir/analytics/track_event', [
        'event' => 'purchase',
        'value' => \$order->get_total(),
        'currency' => \$order->get_currency(),
        'transaction_id' => \$order_id,
        'customer_email' => \$order->get_billing_email(),
    ] );
}",
                'description' => 'Track order completion as conversion event',
            ],
        ];
    }

    public function get_all_snippets(): array {
        $custom = $this->get_custom_snippets();
        return array_merge( $this->built_in_snippets, $custom );
    }

    public function get_snippet( string $id ): ?array {
        if ( isset( $this->built_in_snippets[ $id ] ) ) {
            return $this->built_in_snippets[ $id ];
        }

        $custom = $this->get_custom_snippets();
        return $custom[ $id ] ?? null;
    }

    public function get_custom_snippets(): array {
        $custom = \get_option( 'sofir_wc_custom_snippets', [] );
        return is_array( $custom ) ? $custom : [];
    }

    public function save_snippet( string $name, string $code ): bool {
        $custom = $this->get_custom_snippets();
        $id = 'custom-' . sanitize_key( $name ) . '-' . time();

        $custom[ $id ] = [
            'id' => $id,
            'name' => $name,
            'code' => $code,
            'category' => 'custom',
            'created' => current_time( 'mysql' ),
        ];

        return \update_option( 'sofir_wc_custom_snippets', $custom );
    }

    public function get_categories(): array {
        $categories = [];
        foreach ( $this->get_all_snippets() as $snippet ) {
            if ( isset( $snippet['category'] ) && ! in_array( $snippet['category'], $categories, true ) ) {
                $categories[] = $snippet['category'];
            }
        }
        return $categories;
    }

    public function get_extensions(): array {
        return [
            [
                'name' => 'WooCommerce PDF Invoices & Packing Slips',
                'description' => 'Generate PDF invoices and packing slips for your orders',
                'rating' => '4.8',
                'price' => \__( 'Free', 'sofir' ),
                'type' => 'Official',
                'url' => 'https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/',
            ],
            [
                'name' => 'Subscriptions for WooCommerce',
                'description' => 'Sell subscription products and services in WooCommerce',
                'rating' => '4.7',
                'price' => \__( 'From $249/year', 'sofir' ),
                'type' => 'Official',
                'url' => 'https://woocommerce.com/products/woocommerce-subscriptions/',
            ],
            [
                'name' => 'WooCommerce Bookings',
                'description' => 'Allow customers to book appointments or rent items',
                'rating' => '4.6',
                'price' => \__( 'From $249/year', 'sofir' ),
                'type' => 'Official',
                'url' => 'https://woocommerce.com/products/woocommerce-bookings/',
            ],
            [
                'name' => 'Advanced Custom Fields Pro',
                'description' => 'Add custom fields to products and orders',
                'rating' => '4.9',
                'price' => \__( 'From $99/year', 'sofir' ),
                'type' => 'Premium',
                'url' => 'https://www.advancedcustomfields.com/',
            ],
            [
                'name' => 'Elementor Pro',
                'description' => 'Design WooCommerce pages with Elementor',
                'rating' => '4.8',
                'price' => \__( 'From $99/year', 'sofir' ),
                'type' => 'Premium',
                'url' => 'https://elementor.com/pricing/',
            ],
        ];
    }
}
