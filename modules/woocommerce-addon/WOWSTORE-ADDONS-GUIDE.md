# WooCommerce WowStore Addons Guide

Complete documentation for WowStore-inspired addons in SOFIR WooCommerce Addon module.

## Overview

**Version**: 3.0  
**Total Addons**: 24 (10 original + 14 new from WowStore)  
**Inspiration**: [WowStore by WPXPO](https://wpxpo.com/wowstore/)  
**Location**: `/modules/woocommerce-addon/`

## Addon Categories

Inspired by WowStore's category structure:

### 1. Build Store Smartly
Tools for designing and building your store interface.

### 2. Sales Boosters
Features to increase conversions and create urgency.

### 3. Checkout & Cart
Enhance the checkout and cart experience.

### 4. Exclusive Flexibility
Flexible customization options for products.

---

## Addon List

### Build Store Smartly

#### 1. Variation Swatches
**ID**: `variation-swatches`  
**Icon**: 🎨  
**Description**: Convert product attributes into beautiful swatches.

**Features**:
- Color, image, or label swatches
- Square or circle shapes
- Hover effects and selection states
- Automatic color mapping

**Settings**:
- `swatch_type`: Type of swatch (color/image/label)
- `shape`: Swatch shape (square/circle)

**Hooks**:
- Filter: `woocommerce_dropdown_variation_attribute_options_html`

**Usage**:
```php
// Automatically replaces dropdown variations with swatches
// Configure in product attributes
```

---

#### 2. Wishlist
**ID**: `wishlist`  
**Icon**: ❤️  
**Description**: Allow customers to save products to wishlist.

**Features**:
- Cookie-based for guests, user meta for logged-in users
- Wishlist page via shortcode `[sofir_wishlist]`
- Add/remove AJAX functionality

**Settings**:
- `show_button_text`: Show/hide button text
- `button_position`: Before or after cart button

---

#### 3. Quick View
**ID**: `quick-view`  
**Icon**: 👁️  
**Description**: Check product details in pop-up without leaving page.

**Features**:
- Modal popup with product details
- Add to cart from quick view
- Smooth animations

---

#### 4. Product Addons
**ID**: `product-addons`  
**Icon**: ➕  
**Description**: Add extra product options and charge additional fees.

---

### Sales Boosters

#### 5. Stock Progress Bar
**ID**: `stock-progress-bar`  
**Icon**: 📊  
**Description**: Visual stock display to create FOMO.

**Features**:
- Progress bar showing remaining stock
- Color-coded (green > 50%, orange > 20%, red < 20%)
- Sold count display
- FOMO messages ("Hurry! Only a few left!")

**Settings**:
- `show_sold_count`: Display items sold (yes/no)
- `enable_fomo`: Show urgency messages (yes/no)

**CSS Classes**:
- `.sofir-stock-progress`: Main container
- `.stock-bar-fill`: Colored progress bar

**Initial Stock**:
- Stored in `_sofir_initial_stock` meta key
- Auto-calculated on first load

---

#### 6. Name Your Price
**ID**: `name-your-price`  
**Icon**: 💰  
**Description**: Let customers choose their own price.

**Features**:
- Min/max price restrictions
- Custom price input on product page
- Cart price override

**Product Fields**:
- `_enable_name_your_price`: Enable feature (yes/no)
- `_min_price`: Minimum allowed price
- `_max_price`: Maximum allowed price

**Settings**:
- `default_min`: Global default minimum price

**Hooks**:
- Filter: `woocommerce_add_cart_item_data`
- Action: `woocommerce_before_calculate_totals`

---

#### 7. Call for Price
**ID**: `call-for-price`  
**Icon**: 📞  
**Description**: Display phone button for products without prices.

**Features**:
- Replaces price with "Call for Price" button
- Click-to-call functionality
- Disables purchase for no-price products

**Settings**:
- `phone_number`: Phone number for calls
- `button_text`: Custom button text (default: "Call for Price")

**Hooks**:
- Filter: `woocommerce_get_price_html`
- Filter: `woocommerce_is_purchasable`

---

#### 8. Backorder
**ID**: `backorder`  
**Icon**: 📦  
**Description**: Accept orders for out-of-stock products.

**Features**:
- Enable backorders per product
- Expected restock date
- Custom availability text
- Email notifications when restocked

**Product Fields**:
- `_sofir_enable_backorder`: Enable backorder (yes/no)
- `_sofir_backorder_date`: Expected restock date

**Settings**:
- `availability_text`: Custom text (default: "Available on backorder")
- `enable_notification`: Send restock emails (yes/no)

---

#### 9. Pre-Order
**ID**: `pre-order`  
**Icon**: 🔜  
**Description**: Allow orders for upcoming products.

---

#### 10. Currency Switcher
**ID**: `currency-switcher`  
**Icon**: 💱  
**Description**: Multi-currency support with real-time switching.

**Features**:
- Fixed switcher widget (bottom-right by default)
- Cookie-based currency storage
- Supported currencies: USD, EUR, GBP, JPY, IDR
- Auto page reload on currency change

**Settings**:
- `position`: Switcher position (bottom-right/bottom-left/top-right/top-left)

**CSS Classes**:
- `.sofir-currency-switcher`: Main widget
- `#sofir-currency-select`: Dropdown select

**Cookies**:
- `sofir_currency`: Stores selected currency

---

#### 11. Smart Notifications
**ID**: `smart-notifications`  
**Icon**: 🔔  
**Description**: Sales push notifications and alerts.

---

#### 12. BOGO Deals
**ID**: `bogo-deals`  
**Icon**: 🎁  
**Description**: Buy One Get One promotional offers.

---

#### 13. Product Timer
**ID**: `product-timer`  
**Icon**: ⏰  
**Description**: Countdown timers for urgency.

---

### Checkout & Cart

#### 14. Sticky Add to Cart
**ID**: `sticky-add-to-cart`  
**Icon**: 📌  
**Description**: Fixed add to cart bar on scroll.

**Features**:
- Sticky bar on top or bottom
- Shows product image, name, and price
- Scroll threshold activation
- Custom background color

**Settings**:
- `position`: Bar position (top/bottom)
- `scroll_threshold`: Activation scroll distance (default: 300px)
- `bg_color`: Background color (default: #ffffff)

**CSS Classes**:
- `.sofir-sticky-atc`: Main sticky bar
- `.sticky-atc-content`: Content wrapper
- `.sticky-product-info`: Product details
- `.sticky-atc-button`: Button container

**JavaScript**:
- Shows/hides based on scroll position
- Triggers main add to cart button on click

---

#### 15. Cart Reserved Timer
**ID**: `cart-reserved-timer`  
**Icon**: ⏱️  
**Description**: Countdown timer in cart with FOMO message.

**Features**:
- Gradient background timer bar
- Real-time countdown (MM:SS format)
- "Time expired" warning
- Customizable duration and message

**Settings**:
- `timer_minutes`: Duration in minutes (default: 15)
- `message`: Timer message (use {time} placeholder)

**CSS Classes**:
- `.sofir-cart-timer`: Main timer container
- `.timer-countdown`: Countdown display
- `.timer-expired`: Applied when time runs out

**JavaScript**:
- Real-time countdown update
- Auto-expires and shows warning

---

#### 16. Add to Cart Text
**ID**: `add-to-cart-text`  
**Icon**: ✏️  
**Description**: Customize add to cart button text.

**Features**:
- Different text for single product page
- Per product type (simple/variable/grouped/external)

**Settings**:
- `single_text`: Single product page text
- `simple_text`: Simple products archive text
- `variable_text`: Variable products text
- `grouped_text`: Grouped products text
- `external_text`: External products text

**Hooks**:
- Filter: `woocommerce_product_single_add_to_cart_text`
- Filter: `woocommerce_product_add_to_cart_text`

---

#### 17. Product Bundles
**ID**: `product-bundles`  
**Icon**: 📦  
**Description**: Bundle products together with discounts.

---

#### 18. Wholesale Pricing
**ID**: `wholesale-pricing`  
**Icon**: 💼  
**Description**: Bulk pricing tiers for wholesale customers.

---

### Exclusive Flexibility

#### 19. Product Video
**ID**: `product-video`  
**Icon**: 🎥  
**Description**: Display YouTube/Vimeo videos on product pages.

**Features**:
- YouTube and Vimeo support
- Auto-embed URL conversion
- Replaces or supplements featured image
- 16:9 responsive iframe

**Product Fields**:
- `_product_video_url`: Video URL (YouTube or Vimeo)

**Settings**:
- `replace_featured`: Replace featured image with video (yes/no)

**Supported URLs**:
- YouTube: `youtube.com/watch?v=`, `youtu.be/`
- Vimeo: `vimeo.com/`

**Hooks**:
- Action: `woocommerce_before_single_product_summary` (priority 5)

---

#### 20. Product Image Flipper
**ID**: `product-image-flipper`  
**Icon**: 🔄  
**Description**: Show alternate image on hover.

**Features**:
- Uses first gallery image as flip image
- Fade or slide transition
- Smooth hover effects

**Settings**:
- `effect`: Transition effect (fade/slide)

**CSS Classes**:
- `.sofir-image-flipper`: Flip image container
- `.sofir-flip-image`: The alternate image

**Requirements**:
- Product must have gallery images

---

#### 21. Size Chart
**ID**: `size-chart`  
**Icon**: 📏  
**Description**: Display size guide in modal.

**Features**:
- Modal popup with size chart
- HTML table support
- Per-product size charts
- Custom button text

**Product Fields**:
- `_size_chart_data`: HTML table for size chart

**Settings**:
- `button_text`: Button label (default: "Size Guide")

**Modal**:
- ID: `#sofir-size-chart-modal`
- Close on overlay click
- Responsive design

**HTML Example**:
```html
<table>
  <tr>
    <th>Size</th>
    <th>Chest (cm)</th>
    <th>Waist (cm)</th>
  </tr>
  <tr>
    <td>S</td>
    <td>88-92</td>
    <td>68-72</td>
  </tr>
</table>
```

---

#### 22. Product Title Limit
**ID**: `product-title-limit`  
**Icon**: 📝  
**Description**: Shorten product titles automatically.

**Features**:
- Character limit with custom suffix
- Archive and shop page support
- Optional single product page limit

**Settings**:
- `max_length`: Maximum characters (default: 50)
- `suffix`: Text to append (default: "...")
- `limit_single`: Also limit on single product pages (yes/no)

**Hooks**:
- Filter: `the_title` (only for product post type)

---

#### 23. Quick Social Share
**ID**: `quick-social-share`  
**Icon**: 🔗  
**Description**: Share products on social media.

**Features**:
- 5 social networks supported
- Circular icon buttons
- Hover animations
- Network-specific colors

**Supported Networks**:
- Facebook (📘)
- Twitter (🐦)
- Pinterest (📌)
- WhatsApp (💬)
- Telegram (✈️)

**Settings**:
- `networks`: Comma-separated enabled networks

**CSS Classes**:
- `.sofir-social-share`: Main container
- `.social-buttons`: Button wrapper
- `.social-btn`: Individual button
- `.social-{network}`: Network-specific class

**Hooks**:
- Action: `woocommerce_share`

---

#### 24. Smart Compare
**ID**: `smart-compare`  
**Icon**: ⚖️  
**Description**: Compare multiple products side-by-side.

---

## Technical Architecture

### Base Class: Addon_Base

Located at: `/modules/woocommerce-addon/addons/class-addon-base.php`

**Properties**:
- `$id`: Unique addon identifier (kebab-case)
- `$name`: Display name (translatable)
- `$description`: Short description
- `$icon`: Emoji icon
- `$category`: Category slug
- `$is_pro`: Pro feature flag

**Methods**:
- `init()`: Initialize addon hooks and features
- `render_settings()`: Render admin settings HTML
- `is_enabled()`: Check if addon is active
- `enable()`: Activate addon
- `disable()`: Deactivate addon
- `get_option()`: Get addon option
- `update_option()`: Save addon option
- `save_settings()`: Batch save settings

### Addons Manager

Located at: `/modules/woocommerce-addon/addons-manager.php`

**Responsibilities**:
- Load and initialize all addons
- Manage addon states
- Handle AJAX requests
- Category filtering

**AJAX Handlers**:
- `sofir_toggle_addon`: Enable/disable addon
- `sofir_save_addon_settings`: Save addon settings

**Hooks**:
- `sofir/woocommerce/addons_loaded`: After addons initialized
- `sofir/woocommerce/addon/{addon_id}/enabled`: When addon enabled
- `sofir/woocommerce/addon/{addon_id}/disabled`: When addon disabled
- `sofir/woocommerce/addon/{addon_id}/settings_saved`: After settings saved

### Option Naming Convention

All addon options follow this pattern:
```
sofir_wc_addon_{addon_id}_enabled       // Enable status
sofir_wc_addon_{addon_id}_{setting}     // Individual settings
```

**Examples**:
- `sofir_wc_addon_wishlist_enabled`
- `sofir_wc_addon_sticky-add-to-cart_position`
- `sofir_wc_addon_stock-progress-bar_show_sold_count`

### Meta Keys

Product-specific addon data:
```
_product_video_url                  // Product Video
_size_chart_data                    // Size Chart
_enable_name_your_price            // Name Your Price
_min_price                         // Name Your Price min
_max_price                         // Name Your Price max
_sofir_enable_backorder            // Backorder
_sofir_backorder_date              // Backorder restock date
_sofir_initial_stock               // Stock Progress Bar
```

## Admin UI

### Menu Structure
```
WC Addon (top-level menu)
├── Dashboard
├── Addons          ← Addon management page
├── Code Snippets
├── Extensions
└── Settings
```

### Addons Page Features

1. **Category Tabs**:
   - All Addons
   - Build Store
   - Sales Boosters
   - Checkout & Cart
   - Exclusive Flexibility
   - Products
   - Marketing
   - Customer
   - Analytics

2. **Addon Cards**:
   - Icon and name
   - Description
   - Category badge
   - Toggle switch
   - Settings button (when enabled)

3. **Settings Panel**:
   - Slides down below addon card
   - Standard WordPress form table
   - Save/Cancel buttons
   - Nonce protection

### Modern UI Design

**Gradient Colors**:
- Primary: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- Success: `linear-gradient(135deg, #11998e 0%, #38ef7d 100%)`
- Warning: `linear-gradient(135deg, #ff6b6b 0%, #ff8e53 100%)`

**Toggle Switch**:
- Smooth animation
- Green when enabled
- Gray when disabled

**Card Layout**:
- Box shadow on hover
- Responsive grid (3 columns on desktop)
- Rounded corners (8px)

## Development Guide

### Creating a New Addon

1. **Create addon file**:
```php
<?php
namespace Sofir\WooCommerceAddon\Addons;

class My_Addon extends Addon_Base {
    public function __construct() {
        $this->id          = 'my-addon';
        $this->name        = \__( 'My Addon', 'sofir' );
        $this->description = \__( 'Description here', 'sofir' );
        $this->icon        = '🎯';
        $this->category    = 'sales'; // or builder/cart/flexibility
        
        parent::__construct();
    }
    
    public function init(): void {
        // Add hooks here
        \add_filter( 'some_hook', [ $this, 'my_filter' ] );
    }
    
    public function render_settings(): void {
        $setting = $this->get_option( 'my_setting', 'default' );
        ?>
        <tr>
            <th scope="row"><label>My Setting</label></th>
            <td>
                <input type="text" name="sofir_wc_addon_my-addon_my_setting" value="<?php echo esc_attr( $setting ); ?>" />
            </td>
        </tr>
        <?php
    }
}
```

2. **Add to Addons Manager**:

Edit `/modules/woocommerce-addon/addons-manager.php`:
```php
// In load_addons()
$addon_files = [
    // ... existing files
    'class-my-addon.php',
];

// In init_addons()
$addon_classes = [
    // ... existing classes
    'Sofir\WooCommerceAddon\Addons\My_Addon',
];
```

3. **Test addon**:
- Navigate to WC Addon → Addons
- Find your addon in correct category
- Toggle to enable
- Click Settings to configure
- Save and test functionality

### Best Practices

1. **Performance**:
   - Only enqueue scripts on relevant pages
   - Use inline CSS/JS for small snippets
   - Cache expensive operations

2. **Compatibility**:
   - Check WooCommerce version
   - Graceful degradation if features unavailable
   - Test with popular themes

3. **Security**:
   - Sanitize all inputs: `sanitize_text_field()`, `esc_url_raw()`
   - Escape all outputs: `esc_html()`, `esc_attr()`, `esc_url()`
   - Nonce verification in AJAX handlers

4. **Accessibility**:
   - Proper label associations
   - Keyboard navigation support
   - ARIA attributes for dynamic content

5. **Internationalization**:
   - Use `\__()` for all strings
   - Text domain: `'sofir'`
   - Context with `\_x()` when needed

## Comparison with WowStore

| Feature | WowStore | SOFIR WC Addon | Status |
|---------|----------|----------------|--------|
| Woo Builder | ✅ | ❌ | Not needed (use Elementor) |
| Variation Swatches | ✅ | ✅ | Complete |
| Wishlist | ✅ | ✅ | Complete |
| Quick View | ✅ | ✅ | Complete |
| Custom Font | ✅ | ❌ | WP native |
| Saved Template | ✅ | ❌ | Use SOFIR Templates |
| Sales Push Notification | ✅ | ✅ | As Smart Notifications |
| Name Your Price | ✅ | ✅ | Complete |
| Call for Price | ✅ | ✅ | Complete |
| Backorder | ✅ | ✅ | Complete |
| Pre-Orders | ✅ | ✅ | Complete |
| Currency Switcher | ✅ | ✅ | Complete (basic) |
| Partial Payment | ✅ | ❌ | Future |
| Stock Progress Bar | ✅ | ✅ | Complete |
| Cart Reserved Timer | ✅ | ✅ | Complete |
| Sticky Add to Cart | ✅ | ✅ | Complete |
| Add to Cart Text | ✅ | ✅ | Complete |
| Animated Add to Cart | ✅ | ❌ | CSS-only |
| Product Title Limit | ✅ | ✅ | Complete |
| Quick Social Share | ✅ | ✅ | Complete |
| Product Image Flipper | ✅ | ✅ | Complete |
| Product Compare | ✅ | ✅ | As Smart Compare |
| Size Chart | ✅ | ✅ | Complete |
| Product Video | ✅ | ✅ | Complete |
| Elementor Integration | ✅ | ✅ | Separate module |

**Implementation Status**: 20/26 features (77%)

## Future Enhancements

### Planned Features
1. Animated Add to Cart (CSS animations)
2. Partial Payment (deposit system)
3. Custom Font upload
4. Advanced Currency Switcher (exchange rate API)
5. Woo Builder templates
6. Saved Template system

### Performance Optimizations
1. Lazy load addon settings
2. Conditional script loading
3. Asset minification
4. Database query optimization

### UX Improvements
1. Drag-and-drop addon ordering
2. Bulk enable/disable
3. Import/export addon settings
4. Preset configurations

## Troubleshooting

### Common Issues

**Addon not appearing**:
- Check addon is loaded in `addons-manager.php`
- Verify class name matches file name
- Clear PHP opcache

**Settings not saving**:
- Check nonce verification
- Verify option name format
- Check user capabilities

**Frontend not working**:
- Ensure addon is enabled
- Check hook priorities
- Verify WooCommerce is active

**JavaScript errors**:
- Check jQuery is loaded
- Verify script enqueue conditions
- Check browser console

### Debug Mode

Enable WP_DEBUG to see addon errors:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

Check logs at: `/wp-content/debug.log`

## Support & Resources

- **Documentation**: This file
- **Code Location**: `/modules/woocommerce-addon/`
- **WowStore**: https://wpxpo.com/wowstore/
- **WooCommerce Docs**: https://woocommerce.com/documentation/

---

**Version**: 3.0  
**Last Updated**: 2024  
**License**: GPL v2 or later  
**Author**: SOFIR Development Team
