# SOFIR Shortcodes Reference

Complete reference for all available shortcodes in SOFIR plugin.

## 📋 Table of Contents

1. [Directory & Listings](#directory--listings)
2. [Forms](#forms)
3. [Google Sheets](#google-sheets)
4. [Multi-Vendor](#multi-vendor)
5. [Membership](#membership)
6. [Payments](#payments)
7. [Authentication](#authentication)
8. [Content Display](#content-display)

---

## Directory & Listings

### [sofir_directory]

Display directory listings with filters and map.

**Parameters:**
- `post_type` - Post type to display (default: 'listing')
- `posts_per_page` - Number of posts (default: 10)
- `map` - Show map (default: 'yes')
- `filters` - Show filters (default: 'yes')

**Examples:**
```php
[sofir_directory]
[sofir_directory post_type="listing" posts_per_page="20"]
[sofir_directory map="no" filters="yes"]
```

### [sofir_map]

Display interactive map with markers.

**Parameters:**
- `post_type` - Post type (default: 'listing')
- `zoom` - Map zoom level (default: 12)
- `height` - Map height (default: '400px')

**Examples:**
```php
[sofir_map]
[sofir_map post_type="listing" zoom="14" height="600px"]
```

---

## Forms

### [sofir_form]

Display custom form.

**Parameters:**
- `id` - Form ID (required)

**Examples:**
```php
[sofir_form id="1"]
[sofir_form id="5"]
```

### [sofir_contact_form]

Display pre-built contact form (via Gutenberg block).

**Note:** Better to use `[sofir_form]` with custom form builder.

---

## Google Sheets

### [sofir_sheets_export]

Display export to Google Sheets button.

**Parameters:**
- `type` - Data type: users, orders, posts (required)
- `text` - Button text (default: 'Export to Sheets')

**Examples:**
```php
[sofir_sheets_export type="users"]
[sofir_sheets_export type="orders" text="Export Orders"]
[sofir_sheets_export type="posts" text="Backup Posts"]
```

---

## Multi-Vendor

### [sofir_vendor_dashboard]

Display vendor dashboard for logged-in vendors.

**Parameters:** None

**Examples:**
```php
[sofir_vendor_dashboard]
```

**Shows:**
- Total earnings
- Total products
- Product list with edit links

---

### [sofir_vendor_products]

Display products from specific vendor.

**Parameters:**
- `vendor_id` - Vendor store ID (required)
- `limit` - Number of products (default: 12)

**Examples:**
```php
[sofir_vendor_products vendor_id="10"]
[sofir_vendor_products vendor_id="10" limit="20"]
```

---

### [sofir_vendors_list]

Display list of all vendors.

**Parameters:**
- `limit` - Number of vendors (default: 12)

**Examples:**
```php
[sofir_vendors_list]
[sofir_vendors_list limit="20"]
```

---

### [sofir_become_vendor]

Display vendor application form.

**Parameters:** None

**Examples:**
```php
[sofir_become_vendor]
```

---

## Membership

### [sofir_pricing_table]

Display membership pricing table.

**Parameters:**
- `plan` - Plan ID (optional)
- `columns` - Number of columns (default: 3)

**Examples:**
```php
[sofir_pricing_table]
[sofir_pricing_table columns="4"]
```

### [sofir_protected_content]

Protect content for members only.

**Parameters:**
- `roles` - Allowed roles (comma-separated)
- `message` - Message for non-members

**Examples:**
```php
[sofir_protected_content roles="subscriber,member"]
This content is for members only.
[/sofir_protected_content]

[sofir_protected_content roles="premium" message="Upgrade to premium to access"]
Premium content here.
[/sofir_protected_content]
```

---

## Payments

### [sofir_payment_form]

Display payment form.

**Parameters:**
- `amount` - Payment amount (required)
- `item_name` - Item/product name (optional)
- `return_url` - URL after payment (optional)
- `gateway` - Specific gateway: manual, duitku, xendit, midtrans (optional)

**Examples:**
```php
[sofir_payment_form amount="100000"]
[sofir_payment_form amount="250000" item_name="Premium Membership"]
[sofir_payment_form amount="500000" item_name="Product XYZ" return_url="/thank-you"]
[sofir_payment_form amount="100000" gateway="duitku"]
```

---

## Authentication

### [sofir_login_form]

Display login form.

**Parameters:**
- `redirect` - Redirect URL after login

**Examples:**
```php
[sofir_login_form]
[sofir_login_form redirect="/dashboard"]
```

### [sofir_register_form]

Display registration form.

**Parameters:**
- `redirect` - Redirect URL after registration

**Examples:**
```php
[sofir_register_form]
[sofir_register_form redirect="/welcome"]
```

### [sofir_profile_form]

Display user profile edit form.

**Parameters:** None

**Examples:**
```php
[sofir_profile_form]
```

---

## Content Display

### [sofir_posts]

Display posts list.

**Parameters:**
- `post_type` - Post type (default: 'post')
- `posts_per_page` - Number of posts (default: 10)
- `layout` - Layout: grid, list (default: 'grid')
- `category` - Category slug
- `tag` - Tag slug

**Examples:**
```php
[sofir_posts]
[sofir_posts post_type="article" posts_per_page="6"]
[sofir_posts layout="list" category="news"]
[sofir_posts post_type="event" posts_per_page="12" layout="grid"]
```

### [sofir_terms]

Display taxonomy terms.

**Parameters:**
- `taxonomy` - Taxonomy name (required)
- `hide_empty` - Hide empty terms (default: true)

**Examples:**
```php
[sofir_terms taxonomy="category"]
[sofir_terms taxonomy="listing_category" hide_empty="false"]
```

---

## Advanced Usage

### Combining Shortcodes

You can combine multiple shortcodes in one page:

```php
<h2>Become a Vendor</h2>
[sofir_become_vendor]

<h2>Our Vendors</h2>
[sofir_vendors_list limit="8"]

<h2>Featured Products</h2>
[sofir_vendor_products vendor_id="10" limit="4"]
```

### With Custom HTML

```php
<div class="custom-wrapper">
    <div class="left-column">
        [sofir_directory map="no"]
    </div>
    <div class="right-column">
        [sofir_map height="800px"]
    </div>
</div>
```

### Conditional Display

```php
[sofir_protected_content roles="vendor"]
    [sofir_vendor_dashboard]
[/sofir_protected_content]

[sofir_protected_content roles="subscriber"]
    <p>You need to be a vendor to access this page.</p>
    [sofir_become_vendor]
[/sofir_protected_content]
```

---

## Custom Shortcodes

### Creating Custom Shortcode

You can create custom shortcodes using hooks:

```php
add_shortcode( 'my_custom_shortcode', function( $atts ) {
    $atts = shortcode_atts( [
        'param1' => 'default',
        'param2' => 'default',
    ], $atts );
    
    // Your logic here
    
    return '<div>Custom content</div>';
} );
```

### Using SOFIR Functions in Custom Shortcode

```php
add_shortcode( 'my_vendors', function() {
    if ( class_exists( '\Sofir\Multivendor\Manager' ) ) {
        $manager = \Sofir\Multivendor\Manager::instance();
        // Use manager methods
    }
    
    return '<div>My custom vendors display</div>';
} );
```

---

## Blocks vs Shortcodes

Many features are available as both Gutenberg blocks and shortcodes:

| Feature | Block Name | Shortcode |
|---------|------------|-----------|
| Form | `sofir/contact-form` | `[sofir_form]` |
| Map | `sofir/map` | `[sofir_map]` |
| Login/Register | `sofir/login-register` | `[sofir_login_form]` |
| Payment | `sofir/payment-form` | `[sofir_payment_form]` |

**Recommendation:** Use blocks in Gutenberg editor, shortcodes in Classic editor or widgets.

---

## Troubleshooting

### Shortcode Not Working

1. Check shortcode name spelling
2. Check required parameters provided
3. Check plugin activated
4. Check no typos in attributes
5. Enable WP_DEBUG to see errors

### Shortcode Showing as Text

1. Check shortcode inside post content, not title
2. Check no extra spaces around brackets
3. Check shortcode registered properly
4. Check theme supports shortcodes

### Styling Issues

Add custom CSS to fix styling:

```css
.sofir-vendor-dashboard {
    max-width: 1200px;
    margin: 0 auto;
}

.sofir-form-container {
    padding: 20px;
    background: #f5f5f5;
}
```

---

## Quick Reference Table

| Shortcode | Module | Login Required | Parameters |
|-----------|--------|----------------|------------|
| `[sofir_directory]` | Directory | No | post_type, posts_per_page, map, filters |
| `[sofir_form]` | Forms | No | id (required) |
| `[sofir_sheets_export]` | Google Sheets | Yes (Admin) | type (required), text |
| `[sofir_vendor_dashboard]` | Multi-Vendor | Yes (Vendor) | None |
| `[sofir_vendor_products]` | Multi-Vendor | No | vendor_id (required), limit |
| `[sofir_vendors_list]` | Multi-Vendor | No | limit |
| `[sofir_become_vendor]` | Multi-Vendor | Yes | None |
| `[sofir_payment_form]` | Payments | No | amount (required), item_name, return_url, gateway |
| `[sofir_login_form]` | Auth | No | redirect |
| `[sofir_register_form]` | Auth | No | redirect |
| `[sofir_protected_content]` | Membership | No | roles, message |

---

## Documentation Links

- **Forms Module:** `/modules/forms/README.md`
- **Google Sheets Module:** `/modules/gsheets/README.md`
- **Multi-Vendor Module:** `/modules/multivendor/README.md`
- **Blocks Documentation:** `/modules/blocks/BLOCKS_DOCUMENTATION.md`
- **Complete Features:** `/READY_TO_USE_FEATURES.md`

---

**Version:** 0.1.0  
**Last Updated:** 2024  
**Plugin:** SOFIR - Smart Optimized Framework for Integrated Rendering
