# WooCommerce Code Snippet Learner Guide

Panduan lengkap untuk menggunakan fitur Code Snippet Learner dalam SOFIR WooCommerce Addon.

## 📚 Pengenalan

Code Snippet Learner adalah tool yang membantu developer WordPress mempelajari dan mempraktikkan WooCommerce hooks, filters, dan best practices melalui:

1. **Built-in Snippet Library** - 8+ snippet siap pakai
2. **External Source Integration** - Fetch dari WP Beaches dan source lainnya
3. **Custom Snippet Creation** - Buat snippet custom sendiri
4. **Code Management** - Save, export, dan organize snippet
5. **Learning Resources** - Links ke tutorial dan dokumentasi

## 🎯 Fitur-Fitur

### 1. Built-in Snippets Library

#### Kategori Snippet

**Products** (2 snippets)
- Add Custom Product Field
- Hide Add to Cart Button

**Checkout** (1 snippet)
- Add Custom Checkout Field

**Queries** (1 snippet)
- Get Products by Category

**Inventory** (1 snippet)
- Stock Status Alert

**Orders** (1 snippet)
- Register Custom Order Status

**Discounts** (1 snippet)
- Apply Discount by Email

**Analytics** (1 snippet)
- Track Conversion Events

### 2. Snippet Management

#### Search & Filter
```
Dashboard > WC Addon > Code Snippets
```

- **Category Filter**: Filter by category (all, products, checkout, etc)
- **Search**: Search by snippet name or code
- **View**: View full code dengan syntax highlighting
- **Copy**: Copy code to clipboard dengan satu klik

#### Custom Snippet
```php
// Add custom snippet
1. Klik "+ Add Custom Snippet"
2. Masukkan nama snippet
3. Paste code
4. Klik "Save Snippet"

// Snippet akan tersimpan di options:
// sofir_wc_custom_snippets
```

### 3. External Source Integration

#### WP Beaches Integration

Fetch snippets dari WP Beaches WooCommerce tutorials:

```php
// Automatic parsing
- Extract title dari article
- Extract excerpt dari content
- Extract category dari tags
- Save to local cache

// Update setiap 7 hari (transient)
```

#### Supported Sources
1. **WP Beaches** (wpbeaches.com/tag/woocommerce/)
2. **SiteGround Tutorials** (planning)
3. **WPKube** (planning)

### 4. Code Snippet Examples

#### Contoh 1: Add Custom Product Field

```php
// Hook into WooCommerce product admin
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

function save_custom_product_field( $product_id ) {
    $value = isset( $_POST['_custom_field'] ) ? sanitize_text_field( $_POST['_custom_field'] ) : '';
    update_post_meta( $product_id, '_custom_field', $value );
}
```

**Use Case**: 
- Tambah field khusus ke product (warranty, brand, dll)
- Display di admin product page
- Save sebagai post meta

**Learning Points**:
- `woocommerce_product_options_general_product_data` hook
- `woocommerce_process_product_meta` hook
- `woocommerce_wp_text_input()` function
- `update_post_meta()` untuk save data

#### Contoh 2: Add Custom Checkout Field

```php
// Add custom field to checkout
add_action( 'woocommerce_checkout_fields', 'add_custom_checkout_field' );

function add_custom_checkout_field( $fields ) {
    $fields['billing']['billing_company_id'] = [
        'type' => 'text',
        'label' => __( 'Company ID', 'woocommerce' ),
        'placeholder' => _x( 'Company ID', 'placeholder', 'woocommerce' ),
        'required' => true,
        'clear' => true,
    ];
    return $fields;
}

// Validate field
add_action( 'woocommerce_checkout_process', 'validate_custom_checkout_field' );
function validate_custom_checkout_field() {
    if ( empty( $_POST['post_data']['billing_company_id'] ) ) {
        wc_add_notice( __( 'Company ID is required', 'woocommerce' ), 'error' );
    }
}
```

**Use Case**:
- Collect extra info di checkout (company, tax ID, dll)
- Validate required field
- Save sebagai order meta

**Learning Points**:
- `woocommerce_checkout_fields` filter
- Field validation
- Error handling dengan `wc_add_notice()`
- `woocommerce_checkout_process` hook

#### Contoh 3: Low Stock Alert

```php
// Alert when product stock is low
add_action( 'woocommerce_product_set_stock', 'check_low_stock' );

function check_low_stock( $product ) {
    if ( ! $product instanceof WC_Product ) {
        return;
    }
    
    $stock = $product->get_stock_quantity();
    $min_stock = 5;
    
    if ( $stock <= $min_stock ) {
        // Send admin notification
        $admin_email = get_option( 'admin_email' );
        wp_mail(
            $admin_email,
            'Low Stock Alert: ' . $product->get_name(),
            'Product ' . $product->get_name() . ' stock is low: ' . $stock . ' units'
        );
    }
}
```

**Use Case**:
- Monitor stock levels
- Trigger email saat stock rendah
- Automatic notification system

**Learning Points**:
- `woocommerce_product_set_stock` hook
- `WC_Product` class methods
- `wp_mail()` function
- Stock quantity checking

#### Contoh 4: Custom Order Status

```php
// Register custom order status
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
            'label_count' => _n_noop( 
                'Custom Processing <span class="count">(%s)</span>', 
                'Custom Processing <span class="count">(%s)</span>' 
            ),
        ]
    );
}

// Add to order dropdown
add_filter( 'wc_order_statuses', 'add_custom_order_status' );
function add_custom_order_status( $statuses ) {
    $statuses['wc-custom-processing'] = __( 'Custom Processing', 'woocommerce' );
    return $statuses;
}
```

**Use Case**:
- Create custom workflow (pre-processing, quality check, dll)
- Add status ke order management
- Custom business logic

**Learning Points**:
- `register_post_status()` function
- `wc_order_statuses` filter
- WooCommerce order workflow
- Custom status configuration

#### Contoh 5: Get Products by Category

```php
// Get products from specific category
function get_products_by_category( $category_slug, $limit = 10 ) {
    $args = [
        'post_type' => 'product',
        'posts_per_page' => $limit,
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category_slug,
            ],
        ],
    ];
    
    $products = new WP_Query( $args );
    return $products->posts;
}

// Usage:
$products = get_products_by_category( 'clothing' );
foreach ( $products as $product ) {
    $prod = wc_get_product( $product->ID );
    echo $prod->get_name() . ' - ' . $prod->get_price();
}
```

**Use Case**:
- Query products dari kategori
- Display di custom template
- Filtering dan sorting

**Learning Points**:
- `WP_Query` class
- `tax_query` untuk taxonomy filtering
- `wc_get_product()` function
- Product data methods

## 🎓 Learning Path

### Beginner
1. **Start with Built-in Snippets**
   - Review "Add Custom Product Field"
   - Review "Get Products by Category"
   - Understand hooks dan filters

2. **Understand WooCommerce Hooks**
   - `woocommerce_product_options_*` - Product admin
   - `woocommerce_checkout_*` - Checkout fields
   - `woocommerce_order_*` - Order events

3. **Copy & Modify**
   - Copy snippet ke functions.php
   - Modify untuk kasus spesifik
   - Test di staging environment

### Intermediate
1. **Create Custom Snippets**
   - Combine hooks dari built-in snippets
   - Create untuk business logic spesifik
   - Save ke library

2. **Explore External Resources**
   - Browse WP Beaches tutorials
   - Learn best practices
   - Save interesting snippets

3. **Advanced Hooks**
   - Database operations
   - Custom APIs
   - Performance optimization

### Advanced
1. **Contribute Snippets**
   - Create reusable snippets
   - Share dengan community
   - Document best practices

2. **Performance Tuning**
   - Optimize queries
   - Cache strategies
   - Database indexing

3. **Security Hardening**
   - Sanitization
   - Validation
   - Capability checks

## 📋 Snippet Template

### Built-in Snippet Structure

```php
[
    'id' => 'snippet-id',                          // Unique ID
    'name' => 'Snippet Name',                      // Display name
    'category' => 'products',                      // Category
    'code' => '// PHP code...',                    // Code content
    'description' => 'Brief description',          // Short desc
    'hooks_used' => ['hook1', 'hook2'],           // Hooks referenced
    'difficulty' => 'beginner',                    // beginner/intermediate/advanced
    'use_cases' => ['use case 1', 'use case 2'], // Use cases
    'tags' => ['tag1', 'tag2'],                   // Tags
]
```

### Custom Snippet Structure

```php
[
    'id' => 'custom-snippet-id',
    'name' => 'Custom Snippet Name',
    'code' => '// PHP code...',
    'category' => 'custom',
    'created' => '2025-01-22 12:00:00',
]
```

## 🚀 Advanced Usage

### 1. Export Snippet

```php
// Export as JSON
$json = Learner::instance()->export_snippet( $snippet_id, 'json' );

// Export as PHP file
$php = Learner::instance()->export_snippet( $snippet_id, 'php' );

// Export as text
$txt = Learner::instance()->export_snippet( $snippet_id, 'txt' );
```

### 2. Rate & Comment Snippet

```php
// Rate snippet
Learner::instance()->rate_snippet( $snippet_id, 5 );

// Get average rating
$rating = Learner::instance()->get_snippet_average_rating( $snippet_id );

// Add comment
Learner::instance()->add_snippet_comment( $snippet_id, 'Great snippet!' );

// Get comments
$comments = Learner::instance()->get_snippet_comments( $snippet_id );
```

### 3. Save Snippet Locally

```php
// Save external snippet locally
Learner::instance()->save_snippet_locally( $snippet_id, $snippet_data );

// Get saved snippets
$saved = Learner::instance()->get_saved_snippets();
```

### 4. Fetch from External Sources

```php
// Fetch dari WP Beaches
$snippets = Learner::instance()->fetch_snippets_from_source( 'wpbeaches', 'hooks' );

// Snippets di-cache selama 7 hari
```

## 💡 Best Practices

### 1. Testing
- **Always test di staging terlebih dahulu**
- Use development database
- Check browser console untuk errors
- Monitor error logs

### 2. Security
- Sanitize input: `sanitize_text_field()`
- Escape output: `esc_html()`, `esc_attr()`
- Check capabilities: `current_user_can()`
- Use nonce: `wp_verify_nonce()`

### 3. Performance
- Avoid heavy DB queries
- Use transients untuk caching
- Lazy load external resources
- Monitor query count

### 4. Code Quality
- Follow WordPress coding standards
- Document code dengan comments
- Use descriptive variable names
- Keep functions small dan focused

### 5. Version Control
- Backup sebelum implement snippet
- Version code di Git
- Test sebelum production deploy
- Rollback plan jika ada issue

## 🔧 Implementation Workflow

### Step 1: Explore Snippet
1. Browse code snippet library
2. Find relevant snippet untuk use case
3. Review code dan understand logic

### Step 2: Test di Staging
1. Copy code
2. Paste di functions.php atau custom plugin
3. Test di staging environment
4. Verify functionality

### Step 3: Adapt & Customize
1. Modify untuk kasus spesifik
2. Add error handling
3. Add logging untuk debugging
4. Test edge cases

### Step 4: Deploy to Production
1. Final testing di staging
2. Create backup
3. Deploy ke production
4. Monitor untuk issues
5. Have rollback plan

### Step 5: Document & Share
1. Save snippet ke library
2. Add comments di code
3. Share learnings dengan team
4. Update documentation

## 🐛 Troubleshooting

### Snippet tidak work
```
1. Check error logs: /wp-content/debug.log
2. Check WooCommerce version compatibility
3. Check PHP version requirements
4. Verify hooks digunakan masih ada di version yang digunakan
5. Check conflicts dengan plugins lain
```

### Code tidak ter-execute
```
1. Verify plugin/theme meng-load file
2. Check add_action/add_filter syntax
3. Verify hook name benar
4. Check priority settings
5. Debug dengan error_log()
```

### Performance issues
```
1. Monitor query count
2. Check untuk N+1 query problems
3. Use cache untuk results
4. Optimize database queries
5. Consider background processing dengan WP-Cron
```

## 📚 Additional Resources

### External Learning Resources
- [WP Beaches WooCommerce](https://wpbeaches.com/tag/woocommerce/)
- [WooCommerce Official Docs](https://www.woocommerce.com/document/)
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WooCommerce Code Reference](https://woocommerce.github.io/code-reference/)

### Related Documentation
- WooCommerce Addon README
- SOFIR Admin Panel Guide
- WordPress Coding Standards
- WooCommerce Hook Reference

## 📞 Support & Feedback

Untuk questions atau suggestions:
1. Check dokumentasi ini terlebih dahulu
2. Review code comments di module
3. Check WordPress/WooCommerce documentation
4. Contact SOFIR support team

---

**Last Updated**: 2025-01-22  
**Version**: 1.0.0
