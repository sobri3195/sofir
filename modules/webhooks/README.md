# SOFIR Webhooks Module

## Overview

The Webhooks module provides comprehensive webhook integration capabilities, including native webhook management and full Bit Integration plugin compatibility.

## Structure

```
webhooks/
├── manager.php           # Core webhook manager
├── bit-integration.php   # Bit Integration plugin compatibility
└── README.md            # This file
```

## Components

### 1. Webhook Manager (`manager.php`)

**Purpose**: Native webhook management system for SOFIR

**Features**:
- Create/delete webhooks via REST API
- Test webhook functionality
- Webhook logging and retry mechanism
- JSON payload support
- Custom authentication headers

**REST Endpoints**:
- `GET /wp-json/sofir/v1/webhooks` - List all webhooks
- `POST /wp-json/sofir/v1/webhooks` - Create webhook
- `DELETE /wp-json/sofir/v1/webhooks/{id}` - Delete webhook
- `POST /wp-json/sofir/v1/webhooks/test` - Test webhook
- `GET /wp-json/sofir/v1/webhooks/triggers` - List available triggers

**Triggers Supported**:
- User registration
- User profile update
- User login
- Payment completed
- Post published
- Comment posted
- Form submission
- Membership changed

### 2. Bit Integration (`bit-integration.php`)

**Purpose**: Full compatibility with Bit Integration plugin

**Features**:
- 10 comprehensive triggers
- 3 powerful actions
- 200+ external app connections
- Visual workflow builder
- No coding required

**Triggers** (10 total):
1. User Registered
2. User Profile Updated
3. User Logged In
4. Payment Completed (all gateways)
5. Form Submitted
6. Post Published
7. Comment Posted
8. Membership Changed
9. Appointment Created
10. Appointment Updated

**Actions** (3 total):
1. Create User
2. Update User
3. Create Post

**Documentation**: See root directory
- `BIT_INTEGRATION_GUIDE.md` (Indonesian)
- `BIT_INTEGRATION_README.md` (English)
- `BIT_INTEGRATION_IMPLEMENTATION.md` (Technical)
- `BIT_INTEGRATION_TEST.md` (Testing)

## Usage

### Using Native Webhooks

```php
// Create webhook via code
$webhooks = \Sofir\Webhooks\Manager::instance();

// Or use REST API
POST /wp-json/sofir/v1/webhooks
{
  "name": "My Webhook",
  "url": "https://example.com/webhook",
  "trigger": "user_register",
  "active": true
}
```

### Using Bit Integration

1. Install Bit Integration plugin
2. Go to WordPress Admin → Bit Integration
3. Click "Create Integration"
4. Select "SOFIR" as trigger source
5. Choose your desired trigger
6. Select destination app (e.g., Mailchimp)
7. Map fields and save

## Trigger Events

All webhook triggers fire through SOFIR's action hook system:

```php
// Payment completed
do_action( 'sofir/payment/status_changed', $transaction_id, $status );

// User registered
do_action( 'user_register', $user_id );

// Form submitted
do_action( 'sofir/form/submission', $form_id, $form_data );

// Appointment created
do_action( 'sofir/appointment/created', $appointment_id );
```

## Custom Triggers

### For Native Webhooks

Add custom trigger in webhook manager:

```php
add_filter( 'sofir/webhook/triggers', function( $triggers ) {
    $triggers[] = [
        'key' => 'custom_event',
        'label' => 'Custom Event',
        'description' => 'Triggered on custom action',
    ];
    return $triggers;
});

// Trigger it
do_action( 'sofir/webhook/triggered', 'custom_event', $data );
```

### For Bit Integration

Execute trigger directly:

```php
do_action( 'btcbi_trigger_execute', 'sofir', 'custom_event', [
    'field1' => 'value1',
    'field2' => 'value2',
]);
```

## Data Format

All webhooks send JSON payloads:

```json
{
  "event": "user_register",
  "timestamp": 1699123456,
  "data": {
    "user_id": 123,
    "user_email": "user@example.com",
    "user_login": "username"
  }
}
```

## Performance

- Webhooks execute asynchronously
- 15-second timeout per webhook
- Automatic retry on failure (native webhooks)
- Priority 999 for Bit Integration (after core processing)
- No blocking operations
- Minimal database queries

## Security

- All webhook URLs validated
- Data sanitized before sending
- HTTPS recommended
- WordPress capabilities respected
- API authentication supported
- No sensitive data exposed unnecessarily

## Debugging

### Enable Debug Mode

```php
// wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

### Check Logs

- Native webhooks: Check `wp-content/debug.log`
- Bit Integration: WordPress Admin → Bit Integration → Logs

### Test Webhook

```bash
# Test endpoint
curl -X POST https://yoursite.com/wp-json/sofir/v1/webhooks/test \
  -H "Content-Type: application/json" \
  -d '{"url":"https://webhook.site/your-unique-url"}'
```

## Compatibility

- WordPress 6.3+
- PHP 8.0+
- Bit Integration 2.0+ (optional)
- SOFIR 0.1.0+

## Popular Integrations

Via Bit Integration, connect SOFIR with:

- **Email Marketing**: Mailchimp, ActiveCampaign, ConvertKit
- **Messaging**: Slack, Telegram, WhatsApp, Discord
- **Spreadsheets**: Google Sheets, Airtable
- **CRM**: HubSpot, Salesforce, Zoho
- **Calendar**: Google Calendar, Outlook
- **Analytics**: Google Analytics, Mixpanel
- **E-Commerce**: WooCommerce, Shopify
- And 200+ more services

## Support

- **Documentation**: See root directory for detailed guides
- **Issues**: Report via GitHub
- **Forum**: WordPress.org support forums

## Credits

Developed by SOFIR Team (Sobri + Firman)  
License: GPL v2 or later
