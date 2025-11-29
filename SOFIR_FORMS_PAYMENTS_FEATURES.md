# SOFIR Forms & Payments - Advanced Features

## Overview

SOFIR Forms and Payments modules have been significantly enhanced to compete with Fluent Forms and Paymattic, offering professional-grade form building and payment processing capabilities.

---

## 🎨 SOFIR Forms - Features

### Form Builder

#### Field Types (16 Total)
1. **Text** - Single line text input
2. **Email** - Email validation
3. **Phone (Tel)** - Phone number input
4. **Number** - Numeric input with min/max
5. **Textarea** - Multi-line text
6. **Select** - Dropdown selection
7. **Radio** - Single choice radio buttons
8. **Checkbox** - Multiple choice checkboxes
9. **Date** - Date picker
10. **Time** - Time picker
11. **File Upload** - File attachment with media library integration
12. **Rating** - 5-star rating system ⭐ NEW
13. **Hidden Field** - Hidden values ⭐ NEW
14. **HTML Block** - Custom HTML content ⭐ NEW
15. **Section Break** - Visual form dividers ⭐ NEW
16. **Signature** - Touch/mouse signature pad ⭐ NEW

### Advanced Features

#### 1. Form Templates Library ⭐ NEW
Pre-built professional forms ready to use:
- **Contact Form** - Name, email, message
- **Registration Form** - Complete user registration
- **Survey Form** - With rating and feedback
- **Booking Form** - Date, time, people selection
- **Payment Form** - Payment method selection

Usage:
```php
// Access templates programmatically
$manager = \Sofir\Forms\Manager::instance();
$templates = $manager->get_form_templates();
```

#### 2. Conditional Logic ⭐ NEW
Show/hide fields based on other field values:
- Field dependency rules
- Multiple condition support
- Real-time field visibility

```html
<div data-conditional-field="payment_method" data-conditional-value="credit_card">
    <!-- Shows only when payment_method = credit_card -->
</div>
```

#### 3. Form Analytics ⭐ NEW
Track form performance:
- **Form Views** - Total view count
- **Submissions** - Total submissions
- **Conversion Rate** - View-to-submission ratio

```php
$analytics = $manager->get_form_analytics( $form_id );
// Returns: views, submissions, conversion_rate
```

#### 4. Spam Protection ⭐ NEW
Built-in anti-spam measures:
- **Honeypot Fields** - Invisible trap for bots
- **Keyword Filtering** - Block spam keywords
- **IP Tracking** - Monitor submission sources

```php
$is_spam = $manager->check_spam( $submission_data );
```

#### 5. CSV Export ⭐ NEW
Export form submissions:
```php
$manager->export_submissions_csv( $form_id );
```

Exports include:
- Submission ID
- Submission date
- All form field data
- IP address
- User agent

#### 6. Form Duplication ⭐ NEW
Clone existing forms instantly:
```php
$new_form_id = $manager->duplicate_form( $original_form_id );
```

Copies:
- All fields
- Settings
- Conditional logic
- Styling

#### 7. Draft Submissions ⭐ NEW
Auto-cleanup of old drafts (30+ days):
- Scheduled daily via WP Cron
- Prevents database bloat

#### 8. Enhanced Email Notifications
- Admin notification emails
- Custom email templates
- File attachment support
- User confirmation emails

#### 9. Advanced Form Styling
- Custom CSS support
- Color picker integration
- Responsive design
- Mobile-optimized

---

## 💳 SOFIR Payments - Features

### Payment Gateways

#### Supported Gateways (4)
1. **Manual Payment** - Bank transfer instructions
2. **Duitku** - Indonesian payment gateway
3. **Xendit** - Multi-payment Indonesian gateway
4. **Midtrans** - Payment aggregator

#### Coming Soon ⚡
- **Stripe** - Global card processing
- **PayPal** - Worldwide payment platform

### Advanced Features

#### 1. Payment Dashboard ⭐ NEW
Beautiful analytics dashboard with gradient cards:
- **Total Revenue** - All-time earnings
- **Completed Payments** - Successful transactions
- **Pending Payments** - Awaiting confirmation
- **Total Transactions** - All payment attempts

#### 2. Product Catalog ⭐ NEW
Custom post type for products:
- Product images (thumbnails)
- Regular & sale pricing
- Product descriptions
- Stock management ready

Shortcode:
```
[sofir_product_catalog columns="3" limit="12"]
```

#### 3. Coupon System ⭐ NEW
Discount code management:
- **Percentage** or **Fixed Amount** discounts
- Expiry date control
- Usage limit per coupon
- Usage tracking

Custom post type: `sofir_coupon`

PHP Usage:
```php
$result = $manager->apply_coupon( 'SAVE20', $amount );
// Returns: valid, discount, new_amount, message
```

#### 4. Subscription Management ⭐ NEW
Recurring payment support:
- Monthly/Yearly billing cycles
- Auto-renewal processing
- Subscription status tracking
- Payment retry logic

Custom post type: `sofir_subscription`

Shortcode:
```
[sofir_subscription_form currency="USD"]
```

Features:
- 3 pre-built plans (Basic, Pro, Enterprise)
- Plan comparison
- Popular plan highlighting
- Custom plan creation

#### 5. Donation Forms ⭐ NEW
Optimized for fundraising:
- Suggested donation amounts
- Custom amount input
- Donor information collection
- Tax receipt generation ready

Shortcode:
```
[sofir_donation_form title="Support Us" suggested_amounts="10,25,50,100" currency="USD"]
```

#### 6. Invoice Generation ⭐ NEW
Automatic invoice creation:
```php
$invoice_id = $manager->generate_invoice( $transaction_id );
```

Custom post type: `sofir_invoice`

Stores:
- Transaction reference
- Invoice amount
- Issue date
- Payment status

#### 7. Payment Analytics ⭐ NEW
Comprehensive payment insights:
```php
$analytics = $manager->get_payment_analytics();
```

Returns:
- **Total Revenue** - All completed payments
- **Total Transactions** - All attempts
- **Status Breakdown** - Completed/Pending/Failed/Refunded
- **Gateway Stats** - Performance by gateway
- **Monthly Revenue** - Time-series data
- **Conversion Rate** - Success percentage

#### 8. Transaction Management
Enhanced transaction tracking:
- Real-time status updates
- Transaction history
- Detailed transaction view
- Refund management (coming soon)

#### 9. Multi-Currency Support (In Development)
Currently supports:
- IDR (Indonesian Rupiah)
- USD (US Dollar)

Coming soon:
- EUR, GBP, SGD, MYR, PHP, THB

#### 10. Webhook Handling
Automatic payment status updates:
- Duitku webhooks
- Xendit webhooks
- Midtrans webhooks

Endpoints:
- `/wp-json/sofir/v1/payments/webhook/duitku`
- `/wp-json/sofir/v1/payments/webhook/xendit`
- `/wp-json/sofir/v1/payments/webhook/midtrans`

---

## 📦 Custom Post Types

### Forms Module
- `sofir_form` - Form definitions
- `sofir_submission` - Form submissions

### Payments Module
- `sofir_product` - Product catalog
- `sofir_coupon` - Discount coupons
- `sofir_subscription` - Subscription plans
- `sofir_invoice` - Payment invoices

---

## 🎯 Shortcodes

### Forms
```
[sofir_form id="123"]
```

### Payments
```
[sofir_payment_form amount="100" item_name="Product" return_url="/thank-you"]
[sofir_donation_form title="Support Us" suggested_amounts="10,25,50,100"]
[sofir_subscription_form currency="USD"]
[sofir_product_catalog columns="3" limit="12"]
```

---

## 🔧 REST API Endpoints

### Forms
- `GET /sofir/v1/forms` - List all forms
- `GET /sofir/v1/forms/{id}` - Get form details
- `GET /sofir/v1/forms/{id}/submissions` - Get form submissions

### Payments
- `POST /sofir/v1/payments/create` - Create payment
- `GET /sofir/v1/payments/transactions` - List transactions
- `POST /sofir/v1/payments/webhook/{gateway}` - Webhook handler

---

## 🎨 CSS Classes

### Forms
- `.sofir-form-container` - Form wrapper
- `.sofir-form-field` - Field container
- `.sofir-rating-field` - Rating stars
- `.sofir-signature-pad` - Signature canvas
- `.sofir-section-break` - Section divider
- `.sofir-html-block` - HTML content block

### Payments
- `.sofir-payment-form` - Payment form wrapper
- `.sofir-donation-form` - Donation form
- `.sofir-subscription-form` - Subscription plans
- `.sofir-product-catalog` - Product grid
- `.sofir-product-card` - Individual product

---

## 🔨 Developer Hooks

### Forms

#### Actions
```php
do_action( 'sofir/form/submitted', $submission_id, $form_id, $data );
```

#### Filters
```php
apply_filters( 'sofir/form/validation_rules', $rules, $form_id );
apply_filters( 'sofir/form/email_template', $template, $form_id );
```

### Payments

#### Actions
```php
do_action( 'sofir/payment/status_changed', $transaction_id, $status );
do_action( 'sofir/payment/subscription_renewal', $sub_id, $amount, $gateway, $user_id );
do_action( 'sofir/payment/duitku_webhook', $transaction_id, $status, $data );
do_action( 'sofir/payment/xendit_webhook', $transaction_id, $status, $data );
do_action( 'sofir/payment/midtrans_webhook', $transaction_id, $status, $data );
```

#### Filters
```php
apply_filters( 'sofir/payment/gateways', $gateways );
```

---

## 📊 Statistics

### Forms Module
- **16 Field Types** (11 standard + 5 advanced)
- **5 Form Templates**
- **3 REST API Endpoints**
- **Unlimited Forms** - No restrictions
- **CSV Export** - All submissions
- **Analytics Dashboard**

### Payments Module
- **4 Payment Gateways** (+ 2 coming soon)
- **4 Custom Post Types**
- **4 Shortcodes**
- **7 REST API Endpoints**
- **Coupon System**
- **Subscription Management**
- **Invoice Generation**
- **Analytics Dashboard**

---

## 🚀 Performance Features

### Forms
- **AJAX Form Submission** (optional)
- **Lazy Field Loading**
- **Optimized Database Queries**
- **Auto-cleanup Old Drafts**
- **Spam Protection**

### Payments
- **Transaction Caching**
- **Webhook Verification**
- **Secure Payment Processing**
- **Daily Subscription Check**
- **Gateway Fallback**

---

## 🔒 Security Features

### Forms
- Nonce verification
- CSRF protection
- Sanitization of all inputs
- File upload restrictions
- IP logging
- Honeypot spam protection

### Payments
- Webhook signature verification
- Transaction ID validation
- Secure gateway communication
- PCI compliance ready
- Encrypted sensitive data

---

## 📱 Mobile Responsive

Both Forms and Payments modules are fully responsive:
- Touch-friendly rating stars
- Signature pad touch support
- Mobile-optimized layouts
- Responsive product grids
- Stack form fields on mobile

---

## 🎓 Usage Examples

### Create a Contact Form with Rating
```php
// In form builder, add fields:
// 1. Text - "Name"
// 2. Email - "Email Address"
// 3. Rating - "Rate Your Experience"
// 4. Textarea - "Message"

// Display form:
echo do_shortcode('[sofir_form id="123"]');
```

### Create a Donation Campaign
```php
echo do_shortcode('[sofir_donation_form 
    title="Support Our Cause" 
    description="Help us make a difference"
    suggested_amounts="25,50,100,250"
    currency="USD"
]');
```

### Create a Product with Coupon
```php
// 1. Create product via admin
// 2. Create coupon "LAUNCH20" with 20% discount
// 3. Display products:
echo do_shortcode('[sofir_product_catalog columns="3"]');

// Apply coupon programmatically:
$manager = \Sofir\Payments\Manager::instance();
$result = $manager->apply_coupon( 'LAUNCH20', 100 );
// New amount: $80
```

---

## 🎯 Comparison with Competitors

### vs Fluent Forms
✅ All field types covered
✅ Conditional logic
✅ Analytics dashboard
✅ CSV export
✅ Spam protection
✅ Form templates
⚡ Multi-step forms (coming soon)
⚡ reCAPTCHA integration (coming soon)

### vs Paymattic
✅ Multiple gateways
✅ Product catalog
✅ Coupon system
✅ Subscription management
✅ Donation forms
✅ Analytics dashboard
✅ Invoice generation
⚡ Stripe/PayPal (coming soon)
⚡ Tax calculation (coming soon)

---

## 📝 Changelog

### Version 1.0.0 (Latest)
- ✅ Added 5 new field types (Rating, Hidden, HTML, Section, Signature)
- ✅ Added form templates library (5 templates)
- ✅ Added conditional logic support
- ✅ Added form analytics (views, submissions, conversion rate)
- ✅ Added CSV export for submissions
- ✅ Added form duplication feature
- ✅ Added spam protection (honeypot + keyword filtering)
- ✅ Added product catalog CPT
- ✅ Added coupon system with validation
- ✅ Added subscription management
- ✅ Added donation forms
- ✅ Added invoice generation
- ✅ Added payment analytics dashboard
- ✅ Added payment dashboard with gradient cards
- ✅ Enhanced transaction management
- ✅ Added daily cron jobs for cleanup/renewals

---

## 🛠️ Technical Requirements

### Server
- PHP 8.0+
- WordPress 6.0+
- MySQL 5.7+ or MariaDB 10.2+

### PHP Extensions
- GD or ImageMagick (for signatures)
- cURL (for payment gateways)
- JSON
- mbstring

### WordPress Dependencies
- jQuery
- jQuery UI (Datepicker, Sortable)
- WP REST API

---

## 📞 Support & Documentation

For detailed documentation, visit:
- Forms: `modules/forms/README.md`
- Payments: `modules/payments/README.md`

For technical support:
- Check GitHub issues
- Read inline code documentation
- Review hook reference

---

## 🎉 Conclusion

SOFIR Forms & Payments now offer enterprise-grade features that rival premium plugins like Fluent Forms and Paymattic, providing a complete solution for:
- Professional form building
- Advanced payment processing
- E-commerce functionality
- Subscription management
- Donation campaigns
- Analytics & reporting

All integrated seamlessly into the SOFIR ecosystem!
