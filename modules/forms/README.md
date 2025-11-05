# Form Builder Module

Module ini menyediakan visual form builder lengkap untuk membuat custom forms tanpa coding.

## Fitur

- ✅ Visual drag-and-drop form builder
- ✅ 11 tipe field lengkap
- ✅ Form submission tracking
- ✅ Email notifications
- ✅ Custom success messages
- ✅ REST API endpoints
- ✅ Shortcode untuk display form
- ✅ Export submissions

## Setup

Module sudah otomatis aktif setelah plugin SOFIR diaktifkan.

Akses via admin menu: **Forms**

## Tipe Field

Module mendukung 11 tipe field:

1. **Text** - Input text biasa
2. **Email** - Input email dengan validasi
3. **Phone** - Input nomor telepon
4. **Number** - Input angka
5. **Textarea** - Text area multi-line
6. **Select** - Dropdown select
7. **Radio** - Radio buttons
8. **Checkbox** - Multiple checkboxes
9. **Date** - Date picker
10. **Time** - Time picker
11. **File** - File upload

## Penggunaan

### 1. Create Form

1. Buka **Forms → Add New**
2. Masukkan form name
3. Klik **Add Field** untuk tambah field
4. Configure setiap field:
   - Label
   - Type
   - Required (checkbox)
   - Placeholder
   - Options (untuk select/radio/checkbox)
5. Configure form settings:
   - Success message
   - Submit button text
   - Notification email
6. Klik **Save Form**
7. Copy shortcode yang muncul

### 2. Display Form

Paste shortcode di halaman/post:

```php
[sofir_form id="1"]
```

Form akan muncul dengan semua field yang sudah dikonfigurasi.

### 3. View Submissions

1. Buka **Forms → Submissions**
2. View semua submissions dari semua forms
3. Klik submission untuk detail lengkap

## Form Settings

### Success Message

Pesan yang ditampilkan setelah form berhasil disubmit.

Default: "Thank you for your submission!"

### Submit Button Text

Text pada button submit.

Default: "Submit"

### Notification Email

Email address untuk menerima notifikasi submissions.

Default: Admin email WordPress

## Shortcode

### Basic Usage

```php
[sofir_form id="1"]
```

**Parameters:**
- `id` (required) - Form ID

## REST API

### Get All Forms

```bash
GET /wp-json/sofir/v1/forms
```

**Response:**
```json
[
  {
    "id": 1,
    "title": "Contact Form",
    "shortcode": "[sofir_form id=\"1\"]"
  }
]
```

### Get Form Details

```bash
GET /wp-json/sofir/v1/forms/1
```

**Response:**
```json
{
  "id": 1,
  "title": "Contact Form",
  "fields": [...],
  "settings": {...}
}
```

### Get Form Submissions

```bash
GET /wp-json/sofir/v1/forms/1/submissions
Authorization: Bearer {admin_token}
```

**Response:**
```json
[
  {
    "id": 10,
    "date": "2024-01-01 00:00:00",
    "data": {
      "Name": "John Doe",
      "Email": "john@example.com",
      "Message": "Hello"
    }
  }
]
```

## Hooks

### Actions

**Form submitted:**
```php
do_action( 'sofir/form/submitted', $submission_id, $form_id, $submission_data );
```

**Example:**
```php
add_action( 'sofir/form/submitted', function( $submission_id, $form_id, $data ) {
    // Send to external API
    wp_remote_post( 'https://api.example.com/webhook', [
        'body' => json_encode( $data ),
    ]);
}, 10, 3 );
```

### Filters

**Modify form data before save:**
```php
add_filter( 'sofir/form/submission_data', function( $data, $form_id ) {
    // Add timestamp
    $data['submitted_at'] = current_time( 'mysql' );
    return $data;
}, 10, 2 );
```

**Modify notification email:**
```php
add_filter( 'sofir/form/notification_email', function( $to, $form_id ) {
    // Send to different email for specific form
    if ( $form_id === 1 ) {
        return 'custom@example.com';
    }
    return $to;
}, 10, 2 );
```

## Custom Post Types

### sofir_form

Forms dengan metadata:
- `sofir_form_fields` - Array of field definitions
- `sofir_form_settings` - Form settings

### sofir_submission

Form submissions dengan metadata:
- `form_id` - Parent form ID
- `submission_data` - Submitted data
- `submission_ip` - Submitter IP address
- `submission_user_agent` - User agent
- `submission_user_id` - User ID (if logged in)

## Email Notifications

### Default Template

```
You have received a new form submission:

Field 1: Value 1
Field 2: Value 2
...
```

### Custom Template

```php
add_filter( 'sofir/form/notification_message', function( $message, $form_id, $data ) {
    $custom = "New submission from " . $data['Name'] . "\n\n";
    
    foreach ( $data as $label => $value ) {
        $custom .= $label . ': ' . $value . "\n";
    }
    
    return $custom;
}, 10, 3 );
```

## Form Validation

### Built-in Validation

- Email field: Valid email format
- Required fields: Must be filled
- Number field: Must be numeric
- Date field: Valid date format
- Time field: Valid time format

### Custom Validation

```php
add_filter( 'sofir/form/validate_submission', function( $is_valid, $data, $form_id ) {
    // Custom validation logic
    if ( $form_id === 1 ) {
        if ( strlen( $data['Phone'] ) < 10 ) {
            wp_die( 'Phone number must be at least 10 digits' );
        }
    }
    
    return $is_valid;
}, 10, 3 );
```

## Styling

### Default CSS Classes

```css
.sofir-form-container {}
.sofir-custom-form {}
.sofir-form-field {}
.sofir-form-field label {}
.sofir-form-field input {}
.sofir-form-field textarea {}
.sofir-form-field select {}
.sofir-form-submit {}
.sofir-form-message {}
```

### Custom Styling Example

```css
.sofir-form-field {
    margin-bottom: 20px;
}

.sofir-form-field label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.sofir-form-field input,
.sofir-form-field textarea,
.sofir-form-field select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.sofir-form-submit button {
    background: #0073aa;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
```

## Advanced Usage

### Conditional Fields

```php
add_filter( 'sofir/form/fields', function( $fields, $form_id ) {
    // Show "Other" field only if "Other" is selected
    foreach ( $fields as $key => $field ) {
        if ( $field['label'] === 'Category' ) {
            $fields[$key]['conditional'] = true;
        }
    }
    
    return $fields;
}, 10, 2 );
```

### Multi-Step Forms

```php
// Coming soon in future version
```

### File Upload Handling

```php
add_action( 'sofir/form/submitted', function( $submission_id, $form_id, $data ) {
    if ( ! empty( $_FILES ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        
        foreach ( $_FILES as $field_name => $file ) {
            $upload = wp_handle_upload( $file, [ 'test_form' => false ] );
            
            if ( ! isset( $upload['error'] ) ) {
                update_post_meta( $submission_id, $field_name . '_file', $upload['url'] );
            }
        }
    }
}, 10, 3 );
```

## Integration

### Bit Integration

Forms otomatis trigger Bit Integration webhook saat submitted:

```php
do_action( 'btcbi_trigger_execute', [
    'platform' => 'sofir',
    'trigger' => 'form_submission',
    'data' => [
        'form_id' => $form_id,
        'submission_id' => $submission_id,
        'fields' => $submission_data,
    ],
] );
```

### Google Sheets

Export submissions ke Google Sheets:

```php
add_action( 'sofir/form/submitted', function( $submission_id, $form_id, $data ) {
    if ( class_exists( '\Sofir\GSheets\Manager' ) ) {
        // Auto-sync to sheets
    }
}, 10, 3 );
```

## Security

- ✅ Nonce verification
- ✅ CSRF protection
- ✅ Input sanitization
- ✅ Output escaping
- ✅ Capability checks
- ✅ SQL injection prevention

## Troubleshooting

### Form Not Displaying

1. Check shortcode ID correct
2. Check form status published
3. Check theme/plugin conflicts
4. Enable WP_DEBUG

### Submissions Not Saving

1. Check database permissions
2. Check error logs
3. Check hooks not blocking save
4. Disable other plugins

### Email Not Sending

1. Check SMTP configuration
2. Check email address valid
3. Test with simple wp_mail()
4. Use SMTP plugin

## Roadmap

- [ ] Drag & drop form builder UI
- [ ] Conditional logic
- [ ] Multi-step forms
- [ ] Form templates library
- [ ] Export submissions to CSV
- [ ] Form analytics
- [ ] Spam protection (reCAPTCHA)
- [ ] Payment integration
- [ ] Form calculations

## License

GPL-2.0+

## Support

- Documentation: `/modules/forms/README.md`
- Issues: GitHub Issues
- Support: support@sofir.com
