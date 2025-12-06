# WooCommerce Modular Addons Guide

**Version**: 2.0  
**Last Updated**: January 2025  
**Inspired By**: WPClever & WPXPO

## 📋 Overview

SOFIR WooCommerce Modular Addons adalah sistem addon modular yang memungkinkan Anda mengaktifkan/menonaktifkan fitur WooCommerce dengan mudah melalui toggle switches. Sistem ini terinspirasi dari plugin populer seperti WPClever dan WPXPO.

## 🚀 Fitur Utama

### 1. **Modular Architecture**
- ✅ 10+ Addons built-in
- ✅ Toggle ON/OFF per addon
- ✅ Per-addon settings
- ✅ Category filtering
- ✅ Modern admin UI

### 2. **Available Addons**

#### **Products Category** (5 Addons)

| Addon | Icon | Description |
|-------|------|-------------|
| **Product Bundles** | 📦 | Create product bundles with discounted pricing |
| **Quick View** | 👁️ | Quick view popup for products without leaving shop page |
| **Smart Compare** | ⚖️ | Compare products side-by-side |
| **Pre-Order** | 📅 | Allow pre-orders for out-of-stock products |
| **Wholesale Pricing** | 💼 | B2B wholesale pricing tiers for bulk buyers |

#### **Marketing Category** (3 Addons)

| Addon | Icon | Description |
|-------|------|-------------|
| **BOGO Deals** | 🎁 | Buy One Get One deals with flexible rules |
| **Product Timer** | ⏰ | Add countdown timers to create urgency |
| **Smart Notifications** | 🔔 | Sales popup notifications to increase trust |

#### **Customer Category** (1 Addon)

| Addon | Icon | Description |
|-------|------|-------------|
| **Wishlist** | ❤️ | Allow customers to save products to wishlist |

#### **Products Category** (1 Addon)

| Addon | Icon | Description |
|-------|------|-------------|
| **Product Addons** | ➕ | Add extra product options and custom fields |

## 🎯 Admin Interface

### Menu Structure

```
WC Addon (Main Menu)
├── Dashboard - Statistics & quick links
├── Addons - Modular addons with toggle switches ⭐ NEW
├── Code Snippets - Code library & learner
├── Extensions - Extension recommendations
└── Settings - General settings
```

### Addons Page Features

1. **Category Filter Tabs**
   - All Addons (total count)
   - Products (5 addons)
   - Marketing (3 addons)
   - Customer (1 addon)

2. **Addon Cards**
   - Addon icon & name
   - Enable/disable toggle switch
   - Description & category badge
   - Settings button (when enabled)
   - Pro badge (for premium addons)

3. **Settings Panel**
   - Per-addon settings form
   - Save/Cancel buttons
   - Slide animation

## 🔧 Technical Details

### Addon Base Class

All addons extend `Addon_Base` class:

```php
namespace Sofir\WooCommerceAddon\Addons;

abstract class Addon_Base {
    protected string $id;
    protected string $name;
    protected string $description;
    protected string $icon;
    protected string $category;
    protected bool $is_pro = false;

    abstract public function init(): void;
    abstract public function render_settings(): void;
    
    public function is_enabled(): bool;
    public function enable(): void;
    public function disable(): void;
    protected function get_option( string $key, $default = '' );
    protected function update_option( string $key, $value ): bool;
}
```

### Addons Manager

Central manager class for all addons:

```php
namespace Sofir\WooCommerceAddon;

class Addons_Manager {
    public function get_addons(): array;
    public function get_addon( string $id );
    public function get_addons_by_category( string $category ): array;
    public function get_categories(): array;
}
```

### Hooks & Filters

#### Actions

```php
// After addons loaded
do_action( 'sofir/woocommerce/addons_loaded', $addons );

// After addon enabled
do_action( "sofir/woocommerce/addon/{$id}/enabled" );

// After addon disabled
do_action( "sofir/woocommerce/addon/{$id}/disabled" );

// After addon settings saved
do_action( "sofir/woocommerce/addon/{$id}/settings_saved", $data );
```

#### AJAX Actions

```php
// Toggle addon ON/OFF
wp_ajax_sofir_toggle_addon

// Save addon settings
wp_ajax_sofir_save_addon_settings
```

### Database Options

```php
// Addon enablement
sofir_wc_addon_{$addon_id}_enabled (bool)

// Addon settings
sofir_wc_addon_{$addon_id}_{$setting_key} (mixed)
```

## 📦 Addon Details

### 1. Product Bundles

**Features:**
- Create product bundles with multiple products
- Automatic bundle discount calculation
- Display bundle products on product page
- Bundle price display with savings
- Bundle badge on products

**Settings:**
- Show Bundle Badge (yes/no)
- Bundle Badge Text (string)

**Product Meta:**
- `_sofir_is_bundle` (yes/no)
- `_sofir_bundle_products` (array of product IDs)
- `_sofir_bundle_discount` (percentage)

### 2. BOGO Deals

**Features:**
- Buy 1 Get 1 Free
- Buy 2 Get 1 Free
- Buy 3 Get 1 Free
- Buy 1 Get 1 at X% OFF
- Automatic cart price calculation
- BOGO message on product page

**Settings:**
- Show BOGO Badge (yes/no)
- Badge Position (price/thumbnail)

**Product Meta:**
- `_sofir_bogo_enabled` (yes/no)
- `_sofir_bogo_type` (bogo/buy2get1/buy3get1/percentage)
- `_sofir_bogo_discount` (percentage for percentage type)

### 3. Wishlist

**Features:**
- Add to wishlist button on shop & single product
- Wishlist page with shortcode `[sofir_wishlist]`
- Session-based (cookie) for guests
- User meta storage for logged-in users
- AJAX add/remove

**Settings:**
- Show Button Text (yes/no)
- Button Position (before_cart/after_cart)

**Shortcode:**
```php
[sofir_wishlist]
```

### 4. Quick View

**Features:**
- Quick view modal popup
- AJAX product loading
- Add to cart from modal
- View full details link
- Customizable animation

**Settings:**
- Button Text (string)
- Show Icon (yes/no)
- Modal Animation (fade/slide/zoom)

### 5. Smart Compare

**Features:**
- Compare up to 4 products (configurable)
- Side-by-side comparison table
- Compare button on shop & single product
- Cookie-based storage
- Shortcode `[sofir_compare]`

**Settings:**
- Button Text (string)
- Maximum Products (2-10)

**Shortcode:**
```php
[sofir_compare]
```

### 6. Pre-Order

**Features:**
- Allow pre-orders when out of stock
- Expected availability date
- Pre-order fee (percentage)
- Custom add to cart button text
- Pre-order notice message

**Settings:**
- Button Text (string)
- Require Payment (yes/no)

**Product Meta:**
- `_sofir_preorder_enabled` (yes/no)
- `_sofir_preorder_date` (date)
- `_sofir_preorder_fee` (percentage)

### 7. Product Timer

**Features:**
- Countdown timer on products
- Custom timer text
- Automatic hide when expired
- Display on shop & single product
- Real-time JavaScript countdown

**Settings:**
- Default Timer Text (string)
- Timer Style (gradient/simple/modern)

**Product Meta:**
- `_sofir_timer_enabled` (yes/no)
- `_sofir_timer_end_date` (datetime)
- `_sofir_timer_text` (string)

### 8. Wholesale Pricing

**Features:**
- Up to 3 pricing tiers
- Quantity-based pricing
- Wholesale pricing table
- Require login option
- Price range display

**Settings:**
- Show Pricing Table (yes/no)
- Require Login (yes/no)

**Product Meta:**
- `_sofir_wholesale_enabled` (yes/no)
- `_sofir_wholesale_qty_1` (integer)
- `_sofir_wholesale_price_1` (float)
- ... (up to tier 3)

### 9. Smart Notifications

**Features:**
- Recent order notifications
- Anonymized customer names
- Time ago display
- Configurable delay & interval
- Position customization
- Display on specific pages

**Settings:**
- Initial Delay (seconds)
- Display Interval (seconds)
- Orders Limit (1-50)
- Position (bottom-left/bottom-right/top-left/top-right)
- Display On (home/shop/product checkboxes)

### 10. Product Addons

**Features:**
- Add custom fields to products
- Text, checkbox, dropdown field types
- Extra price per addon
- Required/optional addons
- Display in cart & checkout
- Automatic price calculation

**Settings:**
- Show in Summary (yes/no)

**Product Meta:**
- `_sofir_product_addons` (array of addon objects)

## 🎨 UI Design

### Colors

```css
/* Primary Gradient */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Text Colors */
--text-primary: #1e293b;
--text-secondary: #64748b;

/* Background Colors */
--bg-light: #f8fafc;
--bg-white: #ffffff;

/* Border Colors */
--border-light: #e2e8f0;
--border-primary: #667eea;
```

### Components

```css
/* Toggle Switch */
.addon-toggle
  - Width: 52px
  - Height: 28px
  - Border-radius: 28px
  - Transition: 0.4s

/* Card */
.sofir-addon-card
  - Border: 2px solid
  - Border-radius: 12px
  - Hover: translateY(-4px)
  - Box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15)

/* Badge */
.pro-badge
  - Background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%)
  - Padding: 2px 8px
  - Border-radius: 4px
  - Font-size: 11px
```

## 🔐 Security

### Nonce Verification

All AJAX requests use WordPress nonce:

```php
check_ajax_referer( 'sofir_wc_addon_nonce', 'nonce' );
```

### Capability Checks

Admin pages require `manage_options` capability:

```php
if ( ! current_user_can( 'manage_options' ) ) {
    wp_send_json_error( [ 'message' => __( 'Permission denied', 'sofir' ) ] );
}
```

### Data Sanitization

All inputs are sanitized:

```php
$addon_id = sanitize_text_field( $_POST['addon_id'] );
$settings = array_map( 'sanitize_text_field', $_POST['settings'] );
```

## 📚 Development Guide

### Creating New Addon

1. **Create Addon Class**

```php
<?php
namespace Sofir\WooCommerceAddon\Addons;

class My_Addon extends Addon_Base {

    public function __construct() {
        $this->id          = 'my_addon';
        $this->name        = __( 'My Addon', 'sofir' );
        $this->description = __( 'Addon description', 'sofir' );
        $this->icon        = '🔥';
        $this->category    = 'products';

        parent::__construct();
    }

    public function init(): void {
        // Add hooks and filters here
        add_action( 'woocommerce_single_product_summary', [ $this, 'my_function' ] );
    }

    public function my_function(): void {
        // Your addon logic
    }

    public function render_settings(): void {
        ?>
        <tr>
            <th scope="row"><?php esc_html_e( 'My Setting', 'sofir' ); ?></th>
            <td>
                <input type="text" name="sofir_wc_addon_my_addon_setting" 
                       value="<?php echo esc_attr( $this->get_option( 'setting', 'default' ) ); ?>">
            </td>
        </tr>
        <?php
    }
}
```

2. **Register in Addons Manager**

Add to `addons-manager.php`:

```php
// In load_addons() method
'class-my-addon.php',

// In init_addons() method
'Sofir\WooCommerceAddon\Addons\My_Addon',
```

### Using Addon Options

```php
// Get option
$value = $this->get_option( 'setting_key', 'default_value' );

// Update option
$this->update_option( 'setting_key', $value );

// Delete option
$this->delete_option( 'setting_key' );
```

### Addon Hooks

```php
// Check if addon enabled
if ( ! $addon->is_enabled() ) {
    return;
}

// Enable programmatically
$addon->enable();

// Disable programmatically
$addon->disable();

// Save settings
$addon->save_settings( [
    'setting_1' => 'value1',
    'setting_2' => 'value2',
] );
```

## 🐛 Troubleshooting

### Addon Not Loading

**Issue**: Addon tidak muncul di daftar

**Solution**:
1. Check file exists di `/addons/class-addon-name.php`
2. Check registered di `addons-manager.php`
3. Check class extends `Addon_Base`
4. Clear PHP opcache

### Toggle Not Working

**Issue**: Toggle switch tidak merespon

**Solution**:
1. Check browser console untuk JavaScript errors
2. Verify AJAX URL: `admin-ajax.php`
3. Check nonce validity
4. Verify `manage_options` capability

### Settings Not Saving

**Issue**: Settings tidak tersimpan

**Solution**:
1. Check form `name` attribute format: `sofir_wc_addon_{$id}_{$key}`
2. Verify nonce: `sofir_addon_settings`
3. Check `render_settings()` method
4. Check `save_settings()` implementation

## 📞 Support & Resources

### Documentation
- Main README: `/modules/woocommerce-addon/README.md`
- Snippet Learner Guide: `/modules/woocommerce-addon/SNIPPET_LEARNER_GUIDE.md`

### External Resources
- WPClever: https://wpclever.net/downloads/category/plugins/
- WPXPO: https://www.wpxpo.com/
- WooCommerce Docs: https://woocommerce.com/document/

### Test Site
- URL: https://ulasan.web.id/wp-login.php
- Username: admin
- Password: admin

## 🔄 Version History

### Version 2.0 (Current) - January 2025
- ✅ Modular addon system
- ✅ 10 built-in addons
- ✅ Toggle switches per addon
- ✅ Category filtering
- ✅ Per-addon settings
- ✅ Modern admin UI

### Version 1.0 - December 2024
- ✅ Initial WooCommerce addon module
- ✅ Code snippets library
- ✅ Dashboard statistics
- ✅ Extension recommendations
- ✅ Basic settings

## 📄 License

SOFIR Plugin - All Rights Reserved

---

**Module**: WooCommerce Addon  
**Version**: 2.0  
**Compatibility**: WordPress 6.3+, PHP 8.0+, WooCommerce 3.0+  
**Last Updated**: January 2025
