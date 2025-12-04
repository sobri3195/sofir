# Form Builder Module - Complete Form Solution

Module ini menyediakan visual form builder lengkap dengan fitur-fitur advanced setara dengan Fluent Forms dan Paymattic.

## 🚀 Fitur Lengkap

### ✅ Form Builder
- Visual drag-and-drop form builder
- **27 tipe field** (11 basic + 16 advanced)
- Conditional logic untuk show/hide fields
- Field calculations dengan formula
- Multi-step forms dengan progress indicator
- Repeater fields untuk dynamic entries

### ✅ Form Settings
- Success messages yang bisa dikustomisasi
- Redirect ke URL atau halaman
- Custom CSS dan JavaScript
- Form scheduling (start/end dates)
- Submission limits dan restrictions

### ✅ Notifications
- Admin email notifications
- User confirmation emails
- Multiple email recipients
- Custom email templates
- Conditional notifications

### ✅ Actions & Integrations
- **Post Creation** - Auto-create posts/pages dari submissions
- **User Registration** - Register WordPress users otomatis
- **Webhooks** - Kirim data ke external APIs
- **Payment Integration** - Stripe, PayPal, Razorpay
- **PDF Generation** - Generate PDF dari submissions
- Bit Integration support

### ✅ Advanced Features
- **Save & Resume** - Users bisa save progress dan lanjutkan nanti
- **Quiz Mode** - Scoring untuk quizzes dan surveys
- **reCAPTCHA** - Spam protection dengan Google reCAPTCHA
- **Form Analytics** - Track views, submissions, conversion rates
- **Entry Management** - Advanced filtering dan bulk actions
- **CSV Export** - Export submissions ke CSV

### ✅ Security
- Nonce verification
- CSRF protection
- Input sanitization
- Output escaping
- Capability checks
- SQL injection prevention
- Spam detection

## 📋 Tipe Field (27 Total)

### Basic Fields (7)
1. **Text** - Input text biasa
2. **Email** - Input email dengan validasi
3. **Phone** - Input nomor telepon
4. **Number** - Input angka dengan min/max
5. **Textarea** - Text area multi-line
6. **URL** - Input URL dengan validasi
7. **Password** - Input password

### Choice Fields (4)
8. **Select** - Dropdown select
9. **Radio** - Radio buttons
10. **Checkbox** - Multiple checkboxes
11. **Multi-Select** - Select multiple options

### Advanced Fields (8)
12. **Date** - Date picker
13. **Time** - Time picker
14. **Date & Time** - Combined datetime picker
15. **File Upload** - File upload dengan type restrictions
16. **Rating** - Star rating (1-5)
17. **Range** - Range slider dengan min/max
18. **Calculation** - Auto-calculate dari field lain
19. **Repeater** - Dynamic repeating fields

### Content Fields (5)
20. **Hidden Field** - Hidden data
21. **HTML Block** - Custom HTML content
22. **Section Break** - Visual separator
23. **Signature** - Canvas signature pad
24. **Terms & Conditions** - Checkbox dengan custom text

### Payment Fields (2)
25. **Payment Amount** - Input jumlah pembayaran
26. **Payment Method** - Select payment method

## 🎯 Setup & Penggunaan

### 1. Create Form

```php
// Via admin menu
Admin → Forms → Add New

// Configure fields:
- Label
- Type (27 options)
- Required
- Placeholder
- Options (untuk choice fields)
- Conditional Logic
- Calculation Formula
- Min/Max Values
- File Types (untuk file upload)
```

### 2. Form Settings

#### General Settings
- Submit button text
- Multi-step form enable
- Save & resume enable
- Form scheduling (start/end dates)

#### Notification Settings
- Admin notification
- User confirmation email
- Custom email subjects dan messages
- Multiple email recipients

#### Confirmation Settings
- Show message
- Redirect to URL
- Redirect to page

#### Action Settings
- Create post from submission
- User registration
- Webhooks ke external APIs

#### Restriction Settings
- Limit total submissions
- One submission per user
- Require login
- Google reCAPTCHA

#### Payment Settings
- Payment gateway (Stripe/PayPal/Razorpay/Manual)
- Gateway credentials
- Payment currency

#### Advanced Settings
- Quiz mode
- PDF generation
- Custom CSS
- Custom JavaScript

### 3. Display Form

```php
[sofir_form id="1"]
```

### 4. Multi-Step Forms

```php
// Enable multi-step di Form Settings → General
// Group fields dengan Section Break fields

// JavaScript akan auto-generate steps dengan:
- Progress bar
- Previous/Next buttons
- Step validation
- Save progress option
```

## 💳 Payment Integration

### Stripe Setup

```php
// Form Settings → Payment
1. Enable Payment
2. Select "Stripe" gateway
3. Enter:
   - Publishable Key: pk_live_xxxxx
   - Secret Key: sk_live_xxxxx
4. Set currency (USD, EUR, GBP, IDR, INR)

// Add payment fields:
- Payment Amount field
- Payment Method field

// Webhook URL for IPN:
https://yoursite.com/?sofir_stripe_webhook
```

### PayPal Setup

```php
// Form Settings → Payment
1. Enable Payment
2. Select "PayPal" gateway
3. Enter PayPal Email
4. Enable/Disable Sandbox mode
5. Set currency

// Webhook URL for IPN:
https://yoursite.com/?sofir_paypal_ipn
```

## 🔗 Post Creation

```php
// Form Settings → Actions → Create Post
1. Enable "Create WordPress post from submission"
2. Select Post Type (Post/Page/Custom)
3. Select Post Status (Draft/Pending/Published)

// Field mapping:
- Form field dengan label "Title" → Post Title
- Form field dengan label "Content" → Post Content
- Other fields → Post Meta

// Custom mapping via hook:
add_action( 'sofir/form/submitted', function( $submission_id, $form_id, $data ) {
    $post_id = wp_insert_post( [
        'post_title' => $data['Product Name'],
        'post_content' => $data['Description'],
        'post_type' => 'product',
        'post_status' => 'publish',
    ] );
}, 10, 3 );
```

## 👤 User Registration

```php
// Form Settings → Actions → User Registration
1. Enable "Register WordPress user from submission"
2. Select User Role (Subscriber/Contributor/Author)

// Required fields:
- Username (or Email akan digunakan sebagai username)
- Email
- Password (optional, auto-generated jika kosong)

// Optional fields:
- First Name
- Last Name

// Custom registration via hook:
add_action( 'sofir/form/submitted', function( $submission_id, $form_id, $data ) {
    $user_id = wp_create_user(
        $data['Username'],
        $data['Password'],
        $data['Email']
    );
    
    if ( ! is_wp_error( $user_id ) ) {
        wp_update_user( [
            'ID' => $user_id,
            'first_name' => $data['First Name'],
            'last_name' => $data['Last Name'],
        ] );
    }
}, 10, 3 );
```

## 🌐 Webhooks

```php
// Form Settings → Actions → Webhooks
// Enter webhook URLs (one per line):
https://api.example.com/webhook
https://hooks.zapier.com/hooks/catch/xxxxx/xxxxx

// Data format sent:
{
    "form_id": 1,
    "submission_id": 123,
    "data": {
        "Name": "John Doe",
        "Email": "john@example.com",
        "Message": "Hello World"
    },
    "timestamp": "2024-01-01 12:00:00"
}

// Custom webhook handling:
add_action( 'sofir/form/submitted', function( $submission_id, $form_id, $data ) {
    wp_remote_post( 'https://api.example.com/custom', [
        'body' => json_encode( [
            'custom_field' => $data['Field Name'],
            'extra_data' => 'value',
        ] ),
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer YOUR_TOKEN',
        ],
    ] );
}, 10, 3 );
```

## 🧮 Field Calculations

```php
// Add Calculation field
// Formula examples:
{field_0} + {field_1}          // Addition
{field_0} * {field_1}          // Multiplication
{field_0} * 1.1                // 10% markup
({field_0} + {field_1}) * 0.9  // Discount

// JavaScript auto-updates result saat field berubah
```

## 📝 Conditional Logic

```php
// Per-field conditional logic
1. Enable "Conditional Logic" pada field
2. Select field to watch
3. Select operator:
   - Equals
   - Not Equals
   - Contains
   - Greater Than
   - Less Than
4. Enter value to compare

// Example:
// Show "Other Details" field only if "Category" equals "Other"
```

## 💾 Save & Resume

```php
// Form Settings → General → Save & Resume
1. Enable "Allow users to save progress and resume later"

// Users can:
- Save form progress at any time
- Resume from where they left off
- Data saved for 30 days
- Uses secure session cookies

// Manual save/load:
// JavaScript API:
SOFIR_Forms.saveProgress( form_id );
SOFIR_Forms.loadProgress( form_id );
```

## 📊 Form Analytics

```php
// Automatic tracking:
- Form views
- Form submissions
- Conversion rate

// Get analytics:
$analytics = $manager->get_form_analytics( $form_id );
/*
Array (
    [views] => 1000
    [submissions] => 250
    [conversion_rate] => 25
)
*/

// Track custom events:
add_action( 'sofir/form/viewed', function( $form_id ) {
    // Custom tracking
} );
```

## 🔒 Form Restrictions

### Require Login
```php
// Form Settings → Restrictions → Require Login
// Only logged-in users can submit
```

### Submission Limits
```php
// Form Settings → Restrictions → Limit Submissions
// Limit: 100
// Form disabled setelah 100 submissions
```

### One Submission Per User
```php
// Form Settings → Restrictions → One Submission Per User
// Logged-in users can only submit once
```

### Form Scheduling
```php
// Form Settings → General → Form Scheduling
Start: 2024-01-01 00:00
End: 2024-12-31 23:59
// Form only available during this period
```

## 📄 PDF Generation

```php
// Form Settings → Advanced → Generate PDF
1. Enable "Generate PDF from submissions"

// PDF automatically generated for each submission
// Saved to: /wp-content/uploads/sofir-forms-pdfs/

// Manual PDF generation:
$pdf_url = $manager->generate_pdf( $submission_id );

// Custom PDF template:
add_filter( 'sofir/form/pdf_template', function( $html, $submission_id ) {
    // Custom HTML template
    return $html;
}, 10, 2 );
```

## 🎓 Quiz Mode

```php
// Form Settings → Advanced → Quiz Mode
1. Enable "Enable quiz/survey scoring"

// Add correct answers to fields:
// Field settings → Correct Answer: "Paris"

// Calculate score after submission
$score = 0;
foreach ( $fields as $index => $field ) {
    if ( isset( $field['correct_answer'] ) ) {
        $user_answer = $data[ 'field_' . $index ];
        if ( $user_answer === $field['correct_answer'] ) {
            $score++;
        }
    }
}
```

## 🔐 reCAPTCHA

```php
// Form Settings → Restrictions → Google reCAPTCHA
1. Enable "Enable reCAPTCHA protection"
2. Enter Site Key: xxxxx
3. Enter Secret Key: xxxxx

// Get keys from:
https://www.google.com/recaptcha/admin

// reCAPTCHA v2 Checkbox
```

## 🎨 Custom Styling

```php
// Form Settings → Advanced → Custom CSS
.sofir-form-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.sofir-form-field {
    margin-bottom: 20px;
}

.sofir-form-field label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #333;
}

.sofir-form-field input,
.sofir-form-field textarea,
.sofir-form-field select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.sofir-form-submit button {
    background: #0073aa;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.sofir-form-submit button:hover {
    background: #005a87;
}
```

## 🔌 REST API

### Get All Forms
```http
GET /wp-json/sofir/v1/forms
```

### Get Form Details
```http
GET /wp-json/sofir/v1/forms/{id}
```

### Get Form Submissions
```http
GET /wp-json/sofir/v1/forms/{id}/submissions
Authorization: Bearer {token}
```

## 🎯 Hooks & Filters

### Actions

```php
// Form submitted
add_action( 'sofir/form/submitted', function( $submission_id, $form_id, $data ) {
    // Custom logic
}, 10, 3 );

// Payment completed
add_action( 'sofir/form/payment_completed', function( $transaction_id, $gateway, $data ) {
    // Custom logic
}, 10, 3 );

// Form viewed
add_action( 'sofir/form/viewed', function( $form_id ) {
    // Custom tracking
} );
```

### Filters

```php
// Modify submission data before save
add_filter( 'sofir/form/submission_data', function( $data, $form_id ) {
    $data['processed_at'] = current_time( 'mysql' );
    return $data;
}, 10, 2 );

// Modify notification email
add_filter( 'sofir/form/notification_email', function( $to, $form_id ) {
    if ( $form_id === 1 ) {
        return 'custom@example.com';
    }
    return $to;
}, 10, 2 );

// Modify PDF template
add_filter( 'sofir/form/pdf_template', function( $html, $submission_id ) {
    // Custom template
    return $html;
}, 10, 2 );
```

## 🔄 Migration dari Fluent Forms / Paymattic

```php
// Import Fluent Forms data
$fluent_forms = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}fluentform_forms" );

foreach ( $fluent_forms as $ff_form ) {
    $form_data = json_decode( $ff_form->form_fields, true );
    
    // Convert to SOFIR format
    $sofir_fields = [];
    foreach ( $form_data['fields'] as $field ) {
        $sofir_fields[] = [
            'label' => $field['label'],
            'type' => $field['element'],
            'required' => $field['required'] ?? false,
            'options' => $field['options'] ?? '',
        ];
    }
    
    // Create SOFIR form
    $form_id = wp_insert_post( [
        'post_title' => $ff_form->title,
        'post_type' => 'sofir_form',
        'post_status' => 'publish',
    ] );
    
    update_post_meta( $form_id, 'sofir_form_fields', $sofir_fields );
}
```

## 📚 Form Templates

Pre-built templates tersedia:
1. **Contact Form** - Basic contact form
2. **Registration Form** - User registration
3. **Survey Form** - Survey dengan rating
4. **Booking Form** - Appointment booking
5. **Payment Form** - Payment collection

## ⚡ Performance

- Lazy load forms
- AJAX submissions (no page reload)
- Optimized database queries
- Cached form data
- Minified assets

## 🐛 Troubleshooting

### Form Not Displaying
```php
// Check form ID
// Check shortcode placement
// Check theme compatibility
// Enable WP_DEBUG to see errors
```

### Submissions Not Saving
```php
// Check database permissions
// Check error logs: /wp-content/debug.log
// Disable other plugins
// Check PHP memory limit
```

### Payments Failing
```php
// Verify gateway credentials
// Check webhook URLs configured
// Test in sandbox mode first
// Check PHP cURL enabled
```

### Emails Not Sending
```php
// Test wp_mail() function
// Install SMTP plugin
// Check spam folder
// Verify email addresses valid
```

## 📈 Perbandingan dengan Plugin Lain

| Fitur | SOFIR Forms | Fluent Forms Pro | Paymattic Pro |
|-------|-------------|------------------|---------------|
| Field Types | **27** | 25 | 20 |
| Payment Gateways | **3+** | 5+ | 5+ |
| Multi-Step Forms | ✅ | ✅ | ✅ |
| Conditional Logic | ✅ | ✅ | ✅ |
| Save & Resume | ✅ | ✅ | ❌ |
| Post Creation | ✅ | ✅ | ❌ |
| User Registration | ✅ | ✅ | ❌ |
| Webhooks | ✅ | ✅ | ✅ |
| PDF Generation | ✅ | ✅ (Add-on) | ❌ |
| Quiz Mode | ✅ | ✅ | ❌ |
| Form Analytics | ✅ | ✅ | ✅ |
| Price | **FREE** | $99/year | $79/year |

## 📝 Changelog

### Version 2.0.0 - Enhanced Features
- ✅ Added 16 new field types
- ✅ Payment integration (Stripe, PayPal, Razorpay)
- ✅ Multi-step forms with progress indicator
- ✅ Save & resume functionality
- ✅ Post creation from submissions
- ✅ User registration
- ✅ Webhooks integration
- ✅ PDF generation
- ✅ Quiz mode with scoring
- ✅ Advanced conditional logic
- ✅ Form scheduling
- ✅ Submission restrictions
- ✅ reCAPTCHA protection
- ✅ Enhanced notifications
- ✅ Custom confirmations

## 📞 Support

- Documentation: `/modules/forms/README.md`
- Issues: GitHub Issues
- Support: support@sofir.com

## 📄 License

GPL-2.0+
