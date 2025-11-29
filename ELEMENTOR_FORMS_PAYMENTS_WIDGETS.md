# SOFIR Elementor Forms & Payments Widgets Documentation

## Overview

This document describes the complete set of Elementor widgets for SOFIR Forms and Payments modules. These widgets provide drag-and-drop form builders and payment solutions directly within the Elementor editor.

## Widget Categories

All widgets are organized into the following Elementor categories:

1. **SOFIR Elements** - General purpose widgets including Form widget
2. **SOFIR E-Commerce** - E-commerce widgets including Payment, Donation, Subscription, and Product Catalog

---

## 1. Form Widget

**Category:** SOFIR Elements  
**Name:** `sofir-form`  
**Icon:** Form Horizontal

### Description

Display any SOFIR custom form created in the Forms module. Supports all 16 field types, conditional logic, and AJAX submissions.

### Settings

#### Content Tab

- **Select Form** - Dropdown to select from published forms
- **Show Form Title** - Toggle to display form title
- **Show Form Description** - Toggle to display form description
- **AJAX Submit** - Enable form submission without page reload

#### Style Tab

**Form Style:**
- Background Color
- Padding (responsive)
- Border (type, width, color)
- Border Radius

**Field Style:**
- Text Color
- Background Color
- Border (type, width, color)
- Border Radius

**Button Style:**
- Text Color
- Background Color
- Typography
- Border Radius
- Padding (responsive)

### Usage Example

```
1. Add "Form" widget to your page
2. Select a form from dropdown
3. Configure visibility options
4. Customize styling as needed
```

### Integration

Works seamlessly with:
- All 16 SOFIR field types
- Conditional logic rules
- Form analytics tracking
- Spam protection
- Email notifications

---

## 2. Payment Form Widget

**Category:** SOFIR E-Commerce  
**Name:** `sofir-payment-form`  
**Icon:** Price Table

### Description

Create payment forms for products or services with customizable amounts, currencies, and payment gateways.

### Settings

#### Content Tab

- **Item Name** - Name of product or service
- **Amount** - Payment amount (numeric)
- **Currency** - Select from IDR, USD, EUR, GBP, SGD, MYR
- **Description** - Payment description text
- **Button Text** - Submit button text (default: "Pay Now")
- **Show Customer Info** - Toggle customer fields (name, email, phone)
- **Enable Quantity** - Allow quantity selection

#### Style Tab

**Form Style:**
- Background Color
- Padding (responsive)
- Border (type, width, color)
- Border Radius

**Button Style:**
- Text Color
- Background Color
- Typography

### Shortcode Equivalent

```
[sofir_payment_form 
    item_name="Product Name" 
    amount="100000" 
    currency="IDR" 
    show_customer_info="yes"]
```

### Payment Gateways

Supports all configured gateways:
- Manual Payment
- Duitku
- Xendit
- Midtrans

---

## 3. Donation Form Widget

**Category:** SOFIR E-Commerce  
**Name:** `sofir-donation-form`  
**Icon:** Heart

### Description

Create beautiful donation forms with suggested amounts and custom amount options.

### Settings

#### Content Tab

- **Title** - Form title (default: "Make a Donation")
- **Description** - Donation description text
- **Suggested Amounts** - Comma-separated amounts (e.g., "50000,100000,250000,500000")
- **Currency** - Select from IDR, USD, EUR, GBP, SGD, MYR
- **Allow Custom Amount** - Toggle custom amount input
- **Show Donor Info** - Toggle donor fields (name, email)
- **Button Text** - Submit button text (default: "Donate Now")

#### Style Tab

**Form Style:**
- Background Color
- Padding (responsive)
- Border (type, width, color)
- Border Radius

**Amount Buttons:**
- Text Color
- Background Color
- Active Text Color
- Active Background Color

**Submit Button:**
- Text Color
- Background Color
- Typography

### Features

- Pre-defined suggested amounts
- Custom amount input
- One-click amount selection
- Visual active state for selected amount
- Donor information collection
- Multiple currency support

### Shortcode Equivalent

```
[sofir_donation_form 
    title="Support Our Mission" 
    suggested_amounts="50000,100000,250000" 
    currency="IDR" 
    allow_custom="yes"]
```

---

## 4. Subscription Form Widget

**Category:** SOFIR E-Commerce  
**Name:** `sofir-subscription-form`  
**Icon:** Sync

### Description

Display subscription plans with pricing, features, and subscribe buttons.

### Settings

#### Content Tab

- **Title** - Form title (default: "Subscribe Now")
- **Description** - Subscription description text
- **Specific Subscription** - Select one subscription or show all
- **Currency** - Select from IDR, USD, EUR, GBP, SGD, MYR
- **Layout** - Grid, List, or Table
- **Columns** - 1, 2, 3, or 4 columns (for grid layout)
- **Show Features** - Toggle features list display
- **Button Text** - Subscribe button text (default: "Subscribe")

#### Style Tab

**Form Style:**
- Background Color
- Padding (responsive)
- Border (type, width, color)
- Border Radius

**Plan Card:**
- Background Color
- Border (type, width, color)
- Border Radius
- Box Shadow

**Button Style:**
- Text Color
- Background Color
- Typography

### Layout Options

1. **Grid** - Cards in responsive grid
2. **List** - Stacked list view
3. **Table** - Comparison table format

### Features

- Multiple subscription plans
- Feature list per plan
- Recurring billing support
- Featured plan highlighting
- Responsive layouts
- Multiple currencies

### Shortcode Equivalent

```
[sofir_subscription_form 
    currency="IDR" 
    layout="grid" 
    columns="3" 
    show_features="yes"]
```

---

## 5. Product Catalog Widget

**Category:** SOFIR E-Commerce  
**Name:** `sofir-product-catalog`  
**Icon:** Products

### Description

Display product catalog with images, prices, sale badges, and buy buttons.

### Settings

#### Content Tab

- **Title** - Catalog title (default: "Our Products")
- **Columns** - 1, 2, 3, 4, 5, or 6 columns
- **Products Per Page** - Number of products to display (-1 for all)
- **Order By** - Date, Title, Price, Random, Menu Order
- **Order** - Ascending or Descending
- **Show Image** - Toggle product images
- **Show Price** - Toggle price display
- **Show Sale Badge** - Toggle sale badge for discounted products
- **Show Description** - Toggle product description
- **Show Add to Cart** - Toggle buy button
- **Button Text** - Buy button text (default: "Buy Now")

#### Style Tab

**Grid Style:**
- Gap (responsive)

**Product Card:**
- Background Color
- Padding (responsive)
- Border (type, width, color)
- Border Radius
- Box Shadow

**Product Title:**
- Color
- Typography

**Price:**
- Color
- Typography

**Button:**
- Text Color
- Background Color
- Typography
- Border Radius

### Features

- Responsive grid layout
- Product images with hover effects
- Sale price and regular price
- Sale badge overlay
- Product descriptions
- Add to cart functionality
- Multiple sorting options
- Pagination support

### Shortcode Equivalent

```
[sofir_product_catalog 
    columns="3" 
    limit="12" 
    orderby="date" 
    show_price="yes" 
    show_sale_badge="yes"]
```

---

## Widget Count Summary

**Total Forms & Payments Widgets: 5**

1. Form Widget - Display custom forms
2. Payment Form Widget - Single payment forms
3. Donation Form Widget - Donation campaigns
4. Subscription Form Widget - Subscription plans
5. Product Catalog Widget - Product showcase

---

## Total SOFIR Elementor Widgets

**Grand Total: 49 Widgets**

- SOFIR Elements: 17 widgets (including Form)
- SOFIR Booking & Events: 7 widgets
- SOFIR E-Commerce: 20 widgets (including 4 new payment widgets)
- SOFIR E-Learning: 3 widgets
- SOFIR Voxel: 2 widgets

---

## Assets

### CSS Files

- `assets/css/forms.css` - Form styling with all field types
- `assets/css/payments.css` - Payment forms and product catalog styling

### JavaScript Files

- `assets/js/forms.js` - Form functionality (rating, signature, conditional logic)
- `assets/js/payments.js` - Payment processing and AJAX handling

---

## Common Styling Options

All widgets support:

- **Responsive Controls** - Different settings per device
- **Color Customization** - All colors fully customizable
- **Typography Controls** - Font family, size, weight, etc.
- **Spacing Controls** - Padding, margin, gap
- **Border Controls** - Type, width, color, radius
- **Box Shadow** - Multiple shadow layers
- **Hover Effects** - Smooth transitions

---

## Best Practices

### Form Widget

1. Create forms in SOFIR → Forms before using widget
2. Enable AJAX submit for better UX
3. Use conditional logic for complex forms
4. Test spam protection settings

### Payment Form Widget

1. Configure payment gateways in SOFIR → Payments
2. Set appropriate currency for target market
3. Test with sandbox/test mode first
4. Enable customer info collection

### Donation Form Widget

1. Set meaningful suggested amounts
2. Always allow custom amounts
3. Use compelling description text
4. Make donor info optional for better conversion

### Subscription Form Widget

1. Create subscriptions in SOFIR → Payments
2. List clear features for each plan
3. Highlight most popular plan
4. Use grid layout for 2-4 plans

### Product Catalog Widget

1. Add product images for all products
2. Set sale prices to show discounts
3. Use 3-4 columns for desktop
4. Enable sale badges for promotions

---

## Hooks and Filters

### Form Widget Hooks

```php
// Before form render in Elementor
do_action('sofir/elementor/form/before_render', $form_id);

// After form render in Elementor
do_action('sofir/elementor/form/after_render', $form_id);
```

### Payment Widget Hooks

```php
// Before payment form render
do_action('sofir/elementor/payment_form/before_render', $settings);

// After payment form render
do_action('sofir/elementor/payment_form/after_render', $settings);
```

### Product Catalog Hooks

```php
// Modify product query
add_filter('sofir/elementor/product_catalog/query_args', function($args) {
    // Modify query
    return $args;
});
```

---

## Troubleshooting

### Forms Not Appearing in Dropdown

**Solution:** Create at least one form in SOFIR → Forms → Add New

### Payment Gateway Not Working

**Solution:** Configure gateway API keys in SOFIR → Payments → Settings

### Products Not Showing

**Solution:** Create products in SOFIR → Payments → Products

### Styling Not Applied

**Solution:** Clear WordPress cache and regenerate CSS in Elementor

### AJAX Submit Not Working

**Solution:** Check browser console for JavaScript errors and ensure jQuery is loaded

---

## Changelog

### Version 1.0.0
- Initial release with 5 widgets
- Form widget with 16 field types support
- Payment form widget with 4 gateway support
- Donation form widget with suggested amounts
- Subscription form widget with 3 layouts
- Product catalog widget with advanced filtering

---

## Support

For more information:
- Main documentation: `SOFIR_FORMS_PAYMENTS_FEATURES.md`
- Indonesian guide: `FITUR_SOFIR_FORMS_PAYMENTS_ID.md`
- Forms documentation: See Forms module documentation
- Payments documentation: See Payments module documentation
