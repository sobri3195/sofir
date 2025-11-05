# SOFIR - Bit Integration Plugin Integration

## Overview

SOFIR seamlessly integrates with the **Bit Integration** plugin, enabling powerful automation workflows between SOFIR events and 200+ external applications including:

- Email Marketing (Mailchimp, ActiveCampaign, etc.)
- CRM Systems (HubSpot, Salesforce, etc.)
- Messaging Apps (Slack, Telegram, WhatsApp)
- Spreadsheets (Google Sheets, Airtable)
- And many more...

## Quick Start

### Installation

1. **Install Required Plugins:**
   - SOFIR (already installed)
   - Bit Integration from WordPress.org

2. **Activate Both Plugins:**
   - WordPress Admin → Plugins → Activate

3. **Verify Integration:**
   - Go to Bit Integration → Create Integration
   - Look for "SOFIR" in the trigger sources
   - If visible, integration is successful!

## Available Triggers

SOFIR provides 10 comprehensive triggers:

### 1. User Registered
Triggered when a new user registers on your site.

**Available Data:**
- User ID, Username, Email
- Display Name, First Name, Last Name
- Phone Number, User Roles

### 2. User Profile Updated
Triggered when a user updates their profile.

**Available Data:**
- User ID, Username, Email
- Display Name, First Name, Last Name

### 3. User Logged In
Triggered when a user logs into the site.

**Available Data:**
- User ID, Username, Email
- Login Timestamp

### 4. Payment Completed
Triggered when a payment is successfully completed.

**Available Data:**
- Transaction ID, Gateway (Manual/Duitku/Xendit/Midtrans)
- Amount, Item Name
- User ID, Payment Status

### 5. Form Submitted
Triggered when a form is submitted.

**Available Data:**
- Form ID, Form Data

### 6. Post Published
Triggered when a post is published.

**Available Data:**
- Post ID, Title, Type
- Author ID, Permalink

### 7. Comment Posted
Triggered when a new comment is posted.

**Available Data:**
- Comment ID, Post ID
- Author Name, Email
- Comment Content

### 8. Membership Changed
Triggered when user membership level changes.

**Available Data:**
- User ID
- Old Plan, New Plan

### 9. Appointment Created
Triggered when a new appointment is created.

**Available Data:**
- Appointment ID, DateTime, Duration
- Status, Provider, Client

### 10. Appointment Updated
Triggered when an appointment is updated.

**Available Data:**
- Appointment ID, DateTime
- New Status, Old Status

## Available Actions

SOFIR provides 3 actions that can be triggered from external apps:

### 1. Create User
Create a new WordPress user.

**Required:** Username, Email  
**Optional:** Password, Display Name, First Name, Last Name, Phone

### 2. Update User
Update existing WordPress user.

**Required:** User ID  
**Optional:** Email, Display Name, First Name, Last Name

### 3. Create Post
Create a new WordPress post.

**Required:** Post Title  
**Optional:** Post Content, Post Type, Post Status

## Use Case Examples

### Example 1: Welcome Email for New Users

**Setup:**
1. Trigger: SOFIR → User Registered
2. Action: Mailchimp → Subscribe to List
3. Map Fields:
   - `user_email` → Email
   - `first_name` → First Name
   - `last_name` → Last Name

**Result:** Every new user automatically added to your Mailchimp list.

---

### Example 2: Payment Notifications to Slack

**Setup:**
1. Trigger: SOFIR → Payment Completed
2. Action: Slack → Send Message
3. Message Template:
```
🎉 New Payment Received!
- Amount: {{amount}}
- Item: {{item_name}}
- Gateway: {{gateway}}
- Transaction: {{transaction_id}}
```

**Result:** Real-time payment notifications in your Slack channel.

---

### Example 3: Payment Tracking in Google Sheets

**Setup:**
1. Trigger: SOFIR → Payment Completed
2. Action: Google Sheets → Add Row
3. Map Fields to Columns:
   - Transaction ID → Column A
   - Gateway → Column B
   - Amount → Column C
   - Item Name → Column D

**Result:** Automatic payment database for analysis and reporting.

---

### Example 4: Appointment Reminders via Email

**Setup:**
1. Trigger: SOFIR → Appointment Created
2. Action: Email → Send Email
3. Configure:
   - To: `{{appointment_client}}`
   - Subject: "Appointment Confirmation"
   - Body: Include `{{appointment_datetime}}` and `{{appointment_provider}}`

**Result:** Automatic confirmation emails for all appointments.

## Technical Details

### Hook Integration

SOFIR uses the standard Bit Integration hook system:

```php
// Trigger execution
do_action( 'btcbi_trigger_execute', 'sofir', 'trigger_name', $data );

// Register triggers
add_filter( 'btcbi_trigger', function( $triggers ) {
    // Add SOFIR triggers
    return $triggers;
});

// Register actions
add_filter( 'btcbi_action', function( $actions ) {
    // Add SOFIR actions
    return $actions;
});
```

### Data Structure

All triggers pass data in a consistent format:

```php
[
    'field_name' => 'value',
    'field_name_2' => 'value',
    // ... more fields
]
```

### Custom Triggers

Developers can add custom triggers:

```php
// In your theme or plugin
do_action( 'btcbi_trigger_execute', 'sofir', 'custom_event', [
    'custom_field' => 'value',
    'another_field' => 'value',
]);
```

## Troubleshooting

### SOFIR Not Showing in Bit Integration

**Solution:**
1. Ensure both plugins are active
2. Clear WordPress cache
3. Logout and login again
4. Check PHP error logs

### Trigger Not Firing

**Solution:**
1. Verify integration is Active in Bit Integration
2. Check Integration Logs (Bit Integration → Logs)
3. Test the trigger manually
4. Verify field mappings are correct

### Missing Data Fields

**Solution:**
1. Ensure data exists in SOFIR (check user profile, payment details, etc.)
2. Use conditional logic in Bit Integration for optional fields
3. Set default values in action configuration

## Performance Considerations

1. **High Volume Sites:**
   - Use delay settings in Bit Integration
   - Consider batch processing for large datasets
   - Monitor API rate limits of external services

2. **Database Optimization:**
   - SOFIR triggers are optimized and run at priority 999
   - Minimal database queries
   - No blocking operations

3. **Webhook Reliability:**
   - Built-in retry mechanism
   - Error logging
   - Timeout protection (15 seconds)

## Developer API

### Register Custom Trigger

```php
add_action( 'init', function() {
    // Your custom event
    do_action( 'my_custom_event', $data );
});

// Hook into Bit Integration
add_action( 'my_custom_event', function( $data ) {
    do_action( 'btcbi_trigger_execute', 'sofir', 'my_custom_event', $data );
}, 999 );
```

### Modify Trigger Fields

```php
add_filter( 'btcbi_trigger', function( $triggers ) {
    $triggers['sofir']['triggers']['user_register']['fields'][] = [
        'key' => 'custom_meta',
        'label' => 'Custom Meta Field',
        'required' => false,
    ];
    return $triggers;
});
```

### Add Custom Action Handler

```php
add_action( 'btcbi_action_execute', function( $integration_id, $action_data ) {
    if ( $action_data['platform'] !== 'sofir' ) {
        return;
    }
    
    // Handle custom action
    // Process $action_data
}, 10, 2 );
```

## Security

1. **Data Sanitization:**
   - All user input is sanitized
   - SQL injection protection
   - XSS prevention

2. **Permissions:**
   - Integration management requires `manage_options` capability
   - Trigger execution respects WordPress user capabilities

3. **API Security:**
   - HTTPS required for webhook endpoints
   - Signature verification for payment webhooks
   - Rate limiting protection

## Support Resources

- **Documentation:** See README.md for SOFIR features
- **Bit Integration Docs:** [bit-integrations.com/docs](https://www.bit-integrations.com/docs)
- **Support Forum:** WordPress.org support forums
- **GitHub Issues:** Report bugs and feature requests

## Compatibility

- **WordPress:** 6.3+
- **PHP:** 8.0+
- **Bit Integration:** 2.0+
- **SOFIR:** 0.1.0+

## License

This integration follows the SOFIR plugin license (GPL v2 or later).

## Credits

Developed by the SOFIR Team with ❤️ for the WordPress community.
