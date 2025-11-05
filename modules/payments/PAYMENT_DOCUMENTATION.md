# SOFIR Payment System Documentation

## Table of Contents
- [Overview](#overview)
- [Payment Methods](#payment-methods)
- [Payment Gateway Configuration](#payment-gateway-configuration)
- [Shortcode Usage](#shortcode-usage)
- [REST API](#rest-api)
- [Webhooks](#webhooks)
- [Event Hooks](#event-hooks)
- [Implementation Examples](#implementation-examples)

---

## Overview

SOFIR plugin provides a comprehensive payment system with support for:

1. **Manual Payment** - Manual bank transfer with payment instructions
2. **Duitku** - Local Indonesian payment gateway
3. **Xendit** - Multi-payment gateway platform
4. **Midtrans** - Popular Indonesian payment gateway

All transactions are securely stored and trackable via REST API.

---

## Payment Methods

### 1. Manual Payment

Manual payment allows customers to make bank transfers independently.

**Features:**
- No API configuration required
- Automatic payment instructions
- Suitable for manual bank transfers
- Transaction status can be manually updated by admin

**Default Status:** ✅ Enabled by default

### 2. Duitku

Local Indonesian payment gateway with various payment methods.

**Features:**
- Virtual Account (BCA, Mandiri, BNI, BRI, etc.)
- E-wallet (OVO, Dana, LinkAja, ShopeePay)
- Credit Card
- Convenience Store (Alfamart, Indomaret)

**Required Configuration:**
- Merchant Code
- API Key
- Webhook callback URL

### 3. Xendit

Payment platform supporting various methods.

**Features:**
- Virtual Account
- E-wallet
- Credit Card
- QRIS
- Retail Outlets

**Required Configuration:**
- API Key (Secret Key)
- Webhook URL

### 4. Midtrans

Popular payment gateway with Snap integration.

**Features:**
- Snap Payment (multiple methods on one page)
- Virtual Account
- Credit Card
- E-wallet
- Convenience Store

**Required Configuration:**
- Server Key
- Client Key
- Mode (Sandbox/Production)

---

## Payment Gateway Configuration

### Accessing Configuration Page

1. Login to WordPress Admin
2. Navigate to **SOFIR Dashboard** → **Content Tab**
3. Scroll to **Payment Settings** section

### General Configuration

```
Currency: IDR (default for Indonesia)
```

### Duitku Configuration

**Steps:**

1. **Register Duitku Account**
   - Visit: https://duitku.com
   - Register as merchant
   - Verify account

2. **Get API Credentials**
   - Login to Duitku Dashboard
   - Open **Settings** → **API**
   - Copy `Merchant Code` and `API Key`

3. **Configure in SOFIR**
   ```
   Duitku Merchant Code: [Paste merchant code]
   Duitku API Key: [Paste API key]
   ✅ Enable Duitku
   ```

4. **Setup Webhook**
   - Webhook URL: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/duitku`
   - Paste this URL in Duitku Dashboard → Callback URL

### Xendit Configuration

**Steps:**

1. **Register Xendit Account**
   - Visit: https://xendit.co
   - Register as merchant
   - Verify business documents

2. **Get API Key**
   - Login to Xendit Dashboard
   - Open **Settings** → **Developers** → **API Keys**
   - Generate and copy `Secret API Key`

3. **Configure in SOFIR**
   ```
   Xendit API Key: [Paste secret API key]
   ✅ Enable Xendit
   ```

4. **Setup Webhook**
   - Webhook URL: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/xendit`
   - Paste this URL in Xendit Dashboard → Webhooks

### Midtrans Configuration

**Steps:**

1. **Register Midtrans Account**
   - Visit: https://midtrans.com
   - Register as merchant
   - Verify account

2. **Get API Keys**
   - Login to Midtrans Dashboard
   - Open **Settings** → **Access Keys**
   - Copy `Server Key` and `Client Key`

3. **Configure in SOFIR**
   ```
   Midtrans Server Key: [Paste server key]
   Midtrans Client Key: [Paste client key]
   ✅ Sandbox Mode (for testing)
   ✅ Enable Midtrans
   ```

4. **Setup Webhook**
   - Webhook URL: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/midtrans`
   - Paste this URL in Midtrans Dashboard → Settings → Notification URL

---

## Shortcode Usage

### Basic Shortcode

```
[sofir_payment_form amount="100000" item_name="Premium Membership" return_url="/thank-you"]
```

### Shortcode Parameters

| Parameter | Description | Required | Default |
|-----------|-------------|----------|---------|
| `amount` | Payment amount (number) | Yes | 0 |
| `item_name` | Product/service name | No | '' |
| `return_url` | URL after payment | No | home_url |

### Usage Examples

#### 1. Simple Payment Form

```
[sofir_payment_form amount="50000" item_name="Digital Marketing E-book"]
```

#### 2. Form with Custom Return URL

```
[sofir_payment_form 
    amount="250000" 
    item_name="Premium Course" 
    return_url="/dashboard/my-courses"
]
```

#### 3. Checkout Page Form

```
[sofir_payment_form 
    amount="1500000" 
    item_name="Annual Membership" 
    return_url="/membership/success"
]
```

### HTML Output

The shortcode will generate HTML like this:

```html
<div class="sofir-payment-form" data-amount="100000" data-item="Premium Membership" data-return="/thank-you">
    <h3>Select Payment Method</h3>
    
    <!-- Payment method options -->
    <label class="sofir-payment-option">
        <input type="radio" name="payment_gateway" value="manual" />
        <span>Manual Payment</span>
    </label>
    
    <label class="sofir-payment-option">
        <input type="radio" name="payment_gateway" value="duitku" />
        <span>Duitku</span>
    </label>
    
    <label class="sofir-payment-option">
        <input type="radio" name="payment_gateway" value="xendit" />
        <span>Xendit</span>
    </label>
    
    <label class="sofir-payment-option">
        <input type="radio" name="payment_gateway" value="midtrans" />
        <span>Midtrans</span>
    </label>
    
    <!-- Payment total -->
    <div class="sofir-payment-total">
        <strong>Total:</strong> <span>IDR 100,000.00</span>
    </div>
    
    <!-- Submit button -->
    <button type="button" class="button button-primary sofir-payment-submit">
        Proceed to Payment
    </button>
</div>
```

---

## REST API

### Endpoints

#### 1. Create Payment

Create a new payment transaction.

**Endpoint:** `POST /wp-json/sofir/v1/payments/create`

**Authentication:** User must be logged in

**Parameters:**
```json
{
    "gateway": "duitku|xendit|midtrans|manual",
    "amount": 100000,
    "item_name": "Premium Membership"
}
```

**Response (Manual Payment):**
```json
{
    "status": "success",
    "payment_method": "manual",
    "transaction_id": "TRX-123456-1699200000",
    "instructions": "Please transfer to our bank account and send proof of payment."
}
```

**Response (Gateway Payment):**
```json
{
    "status": "redirect",
    "payment_url": "https://sandbox.duitku.com/checkout/...",
    "transaction_id": "TRX-123456-1699200000"
}
```

#### 2. Get Transactions

Get list of all transactions.

**Endpoint:** `GET /wp-json/sofir/v1/payments/transactions`

**Authentication:** Requires `manage_options` capability (Admin only)

**Response:**
```json
[
    {
        "id": "TRX-123456-1699200000",
        "gateway": "duitku",
        "amount": 100000,
        "item_name": "Premium Membership",
        "status": "completed",
        "user_id": 1,
        "created_at": "2024-11-05 10:00:00",
        "updated_at": "2024-11-05 10:05:00"
    }
]
```

#### 3. Webhook Endpoints

Endpoints for receiving notifications from payment gateways.

**Duitku Webhook:**
```
POST /wp-json/sofir/v1/payments/webhook/duitku
```

**Xendit Webhook:**
```
POST /wp-json/sofir/v1/payments/webhook/xendit
```

**Midtrans Webhook:**
```
POST /wp-json/sofir/v1/payments/webhook/midtrans
```

---

## Webhooks

### Webhook Flow

```
Payment Gateway → Webhook URL → Update Transaction Status → Trigger Hooks
```

### Configure Webhook in Gateway

#### Duitku

1. Login to Duitku Dashboard
2. Menu **Settings** → **Callback URL**
3. Enter: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/duitku`
4. Save

#### Xendit

1. Login to Xendit Dashboard
2. Menu **Settings** → **Webhooks**
3. Click **Add Webhook**
4. URL: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/xendit`
5. Events: Select all payment events
6. Save

#### Midtrans

1. Login to Midtrans Dashboard
2. Menu **Settings** → **Configuration**
3. **Payment Notification URL**: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/midtrans`
4. Save

### Testing Webhook

For testing webhooks in local development:

1. **Use ngrok**
   ```bash
   ngrok http 80
   ```

2. **Use ngrok URL in webhook config**
   ```
   https://abc123.ngrok.io/wp-json/sofir/v1/payments/webhook/duitku
   ```

3. **Perform test payment from gateway dashboard**

---

## Event Hooks

### Action Hooks

Plugin provides action hooks for customizing behavior:

#### 1. Payment Status Changed

Triggered when payment status changes.

```php
add_action('sofir/payment/status_changed', function($transaction_id, $status) {
    if ($status === 'completed') {
        // Send confirmation email
        // Update user membership
        // Add loyalty points
        error_log("Payment completed: $transaction_id");
    }
}, 10, 2);
```

#### 2. Duitku Webhook

Triggered when receiving webhook from Duitku.

```php
add_action('sofir/payment/duitku_webhook', function($transaction_id, $status, $params) {
    error_log("Duitku webhook: $transaction_id - Status: $status");
    // Custom logic
}, 10, 3);
```

#### 3. Xendit Webhook

Triggered when receiving webhook from Xendit.

```php
add_action('sofir/payment/xendit_webhook', function($transaction_id, $status, $params) {
    error_log("Xendit webhook: $transaction_id - Status: $status");
    // Custom logic
}, 10, 3);
```

#### 4. Midtrans Webhook

Triggered when receiving webhook from Midtrans.

```php
add_action('sofir/payment/midtrans_webhook', function($transaction_id, $status, $params) {
    error_log("Midtrans webhook: $transaction_id - Status: $status");
    // Custom logic
}, 10, 3);
```

### Filter Hooks

#### 1. Modify Payment Gateways

Customize available payment gateways.

```php
add_filter('sofir/payment/gateways', function($gateways) {
    // Add custom gateway
    $gateways['custom'] = [
        'id' => 'custom',
        'name' => 'Custom Gateway',
        'enabled' => true
    ];
    
    return $gateways;
});
```

---

## Implementation Examples

### 1. Simple Checkout Page

**File:** `page-checkout.php`

```php
<?php
/**
 * Template Name: Checkout Page
 */

get_header();

// Get cart total
$total = isset($_GET['total']) ? floatval($_GET['total']) : 0;
$items = isset($_GET['items']) ? sanitize_text_field($_GET['items']) : 'Order';

?>

<div class="checkout-page">
    <h1>Checkout</h1>
    
    <div class="order-summary">
        <h2>Order Summary</h2>
        <p><strong>Items:</strong> <?php echo esc_html($items); ?></p>
        <p><strong>Total:</strong> IDR <?php echo number_format($total, 0, ',', '.'); ?></p>
    </div>
    
    <div class="payment-section">
        <h2>Select Payment Method</h2>
        <?php echo do_shortcode('[sofir_payment_form amount="' . $total . '" item_name="' . $items . '" return_url="/order-confirmation"]'); ?>
    </div>
</div>

<?php
get_footer();
```

### 2. Membership Integration

```php
// Activate membership after successful payment
add_action('sofir/payment/status_changed', 'activate_membership_on_payment', 10, 2);

function activate_membership_on_payment($transaction_id, $status) {
    if ($status !== 'completed') {
        return;
    }
    
    // Get transaction details
    $transactions = get_option('sofir_payment_transactions', []);
    if (!isset($transactions[$transaction_id])) {
        return;
    }
    
    $transaction = $transactions[$transaction_id];
    $user_id = $transaction['user_id'];
    
    // Activate membership
    update_user_meta($user_id, 'membership_status', 'active');
    update_user_meta($user_id, 'membership_expires', strtotime('+1 year'));
    
    // Send notification email
    $user = get_userdata($user_id);
    wp_mail(
        $user->user_email,
        'Membership Activated',
        'Your premium membership has been activated!'
    );
}
```

### 3. Custom Payment Form with JavaScript

```html
<div id="custom-payment-form">
    <select id="payment-method">
        <option value="">Select Payment Method</option>
        <option value="manual">Manual Transfer</option>
        <option value="duitku">Duitku (Multi Payment)</option>
        <option value="xendit">Xendit</option>
        <option value="midtrans">Midtrans Snap</option>
    </select>
    
    <button id="pay-now">Pay Now - IDR 100,000</button>
</div>

<script>
document.getElementById('pay-now').addEventListener('click', function() {
    var gateway = document.getElementById('payment-method').value;
    
    if (!gateway) {
        alert('Please select a payment method');
        return;
    }
    
    wp.apiFetch({
        path: '/sofir/v1/payments/create',
        method: 'POST',
        data: {
            gateway: gateway,
            amount: 100000,
            item_name: 'Premium Package'
        }
    }).then(function(response) {
        if (response.status === 'redirect' && response.payment_url) {
            window.location.href = response.payment_url;
        } else if (response.status === 'success') {
            alert(response.instructions || 'Payment initiated successfully');
        }
    }).catch(function(error) {
        alert('Error: ' + error.message);
    });
});
</script>
```

### 4. Display Transaction History

```php
<?php
/**
 * Display user's transaction history
 */

// Get current user transactions
$all_transactions = get_option('sofir_payment_transactions', []);
$user_transactions = array_filter($all_transactions, function($transaction) {
    return $transaction['user_id'] === get_current_user_id();
});

if (!empty($user_transactions)) :
?>
<table class="transaction-history">
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Item</th>
            <th>Amount</th>
            <th>Gateway</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($user_transactions as $transaction) : ?>
        <tr>
            <td><?php echo esc_html($transaction['id']); ?></td>
            <td><?php echo esc_html($transaction['item_name']); ?></td>
            <td>IDR <?php echo number_format($transaction['amount'], 0, ',', '.'); ?></td>
            <td><?php echo esc_html(ucfirst($transaction['gateway'])); ?></td>
            <td>
                <span class="status-<?php echo esc_attr($transaction['status']); ?>">
                    <?php echo esc_html(ucfirst($transaction['status'])); ?>
                </span>
            </td>
            <td><?php echo esc_html($transaction['created_at']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
else :
    echo '<p>No transactions found.</p>';
endif;
```

### 5. Admin Dashboard for Transactions

```php
<?php
/**
 * Add admin menu for viewing transactions
 */

add_action('admin_menu', function() {
    add_submenu_page(
        'sofir-dashboard',
        'Payment Transactions',
        'Transactions',
        'manage_options',
        'sofir-transactions',
        'render_transactions_page'
    );
});

function render_transactions_page() {
    $transactions = get_option('sofir_payment_transactions', []);
    
    // Sort by date (newest first)
    usort($transactions, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    ?>
    <div class="wrap">
        <h1>Payment Transactions</h1>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User</th>
                    <th>Item</th>
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction) : 
                    $user = get_userdata($transaction['user_id']);
                ?>
                <tr>
                    <td><code><?php echo esc_html($transaction['id']); ?></code></td>
                    <td><?php echo esc_html($user ? $user->display_name : 'Unknown'); ?></td>
                    <td><?php echo esc_html($transaction['item_name']); ?></td>
                    <td><strong>IDR <?php echo number_format($transaction['amount'], 0, ',', '.'); ?></strong></td>
                    <td><?php echo esc_html(ucfirst($transaction['gateway'])); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo esc_attr($transaction['status']); ?>">
                            <?php echo esc_html(ucfirst($transaction['status'])); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html($transaction['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending { background: #fef7e0; color: #9c6d1f; }
        .status-completed { background: #e0f7ef; color: #1f9c5f; }
        .status-failed { background: #fee; color: #c00; }
    </style>
    <?php
}
```

---

## Troubleshooting

### Common Issues

#### 1. Webhook not working

**Solution:**
- Ensure webhook URL is publicly accessible (not localhost)
- Use ngrok for testing locally
- Check error log: `wp-content/debug.log`
- Verify webhook URL in gateway dashboard

#### 2. Payment not redirecting to gateway

**Solution:**
- Check API credentials are correct
- Ensure gateway is enabled
- Check browser console for JavaScript errors
- Verify user is logged in

#### 3. Transaction status not updating

**Solution:**
- Verify webhook URL is registered in gateway
- Check webhook signature/authentication
- Test webhook with tools like Postman
- Enable WordPress debug mode

#### 4. Manual payment not showing

**Solution:**
- Ensure "Enable Manual Payment" is checked
- Clear WordPress cache
- Check shortcode syntax

---

## Transaction Status

| Status | Description |
|--------|-------------|
| `pending` | Payment awaiting confirmation |
| `completed` | Payment successful |
| `failed` | Payment failed |

---

## Security Best Practices

1. **Don't hardcode API keys in code**
   - Use environment variables
   - Or store in encrypted database

2. **Validate webhook signature**
   - Implement signature verification for each gateway
   - Examples already in webhook handlers

3. **Use HTTPS**
   - Required for production
   - Required by all payment gateways

4. **Log all transactions**
   - Automatically handled by plugin
   - Keep logs for audit trail

5. **Limit webhook access**
   - Whitelist gateway IP addresses
   - Implement rate limiting

---

## Testing Mode

### Duitku Sandbox

```
URL: https://sandbox.duitku.com
Test Cards: Available in Duitku dashboard
```

### Xendit Test Mode

```
API Key: Use test API key (starts with xnd_development_)
Test Cards: 4000000000000002 (Success)
```

### Midtrans Sandbox

```
Sandbox Mode: ✅ Enabled
Test Cards:
- 4811 1111 1111 1114 (Success)
- 4911 1111 1111 1113 (Failure)
```

---

## Support & Resources

### Official Documentation

- **Duitku:** https://docs.duitku.com
- **Xendit:** https://developers.xendit.co
- **Midtrans:** https://docs.midtrans.com

### SOFIR Support

- GitHub Issues: [Repository URL]
- Email: support@sofir.com
- Documentation: `/modules/payments/` folder

---

## Changelog

### Version 1.0.0
- ✅ Manual payment support
- ✅ Duitku integration
- ✅ Xendit integration
- ✅ Midtrans integration
- ✅ Transaction management
- ✅ Webhook handlers
- ✅ REST API endpoints
- ✅ Shortcode support

---

## License

Copyright © 2024 SOFIR Plugin. All rights reserved.
