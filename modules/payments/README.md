# SOFIR Payment Module

Complete payment system with support for manual payments and Indonesian local payment gateways.

## 🎯 Features

- ✅ **Manual Payment** - Bank transfer with instructions
- ✅ **Duitku** - Indonesian multi-payment gateway
- ✅ **Xendit** - Virtual accounts, e-wallets, credit cards
- ✅ **Midtrans** - Snap payment with multiple methods
- ✅ **Transaction Management** - Full transaction tracking
- ✅ **Webhook Support** - Automatic status updates
- ✅ **REST API** - Programmatic payment creation
- ✅ **Shortcode** - Easy integration in pages/posts

## 📚 Documentation

### Quick Start

**1. Enable Payment Methods**
```
WordPress Admin → SOFIR Dashboard → Content Tab → Payment Settings
```

**2. Add Payment Form to Page**
```
[sofir_payment_form amount="100000" item_name="Premium Package"]
```

**3. Configure Gateway (if using)**
- Get API credentials from gateway dashboard
- Enter in SOFIR payment settings
- Setup webhook URL

### Payment Gateways

| Gateway | Type | Methods | Configuration |
|---------|------|---------|---------------|
| Manual | Bank Transfer | Manual | None required |
| Duitku | Multi-Payment | VA, E-wallet, Cards, Stores | Merchant Code + API Key |
| Xendit | Multi-Payment | VA, E-wallet, Cards, QRIS | API Key |
| Midtrans | Snap Payment | All methods in one page | Server Key + Client Key |

## 🔗 Quick Links

- **[Complete Guide (Indonesian)](./PAYMENT_GUIDE.md)** - Panduan lengkap dalam Bahasa Indonesia
- **[Full Documentation (English)](./PAYMENT_DOCUMENTATION.md)** - Complete documentation in English
- **[Manager Code](./manager.php)** - Source code with inline comments

## 🚀 Usage Examples

### Basic Payment Form

```php
[sofir_payment_form amount="50000" item_name="Digital Product"]
```

### Custom Return URL

```php
[sofir_payment_form 
    amount="250000" 
    item_name="Course" 
    return_url="/thank-you"
]
```

### REST API Payment

```javascript
wp.apiFetch({
    path: '/sofir/v1/payments/create',
    method: 'POST',
    data: {
        gateway: 'duitku',
        amount: 100000,
        item_name: 'Premium Membership'
    }
}).then(response => {
    if (response.payment_url) {
        window.location.href = response.payment_url;
    }
});
```

### Payment Status Hook

```php
add_action('sofir/payment/status_changed', function($transaction_id, $status) {
    if ($status === 'completed') {
        // Activate membership
        // Send email
        // Add loyalty points
    }
}, 10, 2);
```

## 🔧 Configuration

### Webhook URLs

Configure these URLs in your payment gateway dashboard:

```
Duitku:   https://yourdomain.com/wp-json/sofir/v1/payments/webhook/duitku
Xendit:   https://yourdomain.com/wp-json/sofir/v1/payments/webhook/xendit
Midtrans: https://yourdomain.com/wp-json/sofir/v1/payments/webhook/midtrans
```

### Environment Setup

**Sandbox/Testing:**
```php
// Use sandbox mode for testing
Duitku:   Use sandbox credentials
Xendit:   Use test API key (xnd_development_*)
Midtrans: Enable sandbox mode ✅
```

**Production:**
```php
// Use production credentials
Duitku:   Production merchant code + API key
Xendit:   Live API key (xnd_production_*)
Midtrans: Disable sandbox mode
```

## 📡 REST API Endpoints

### Create Payment
```
POST /wp-json/sofir/v1/payments/create
Auth: Logged in user

Payload:
{
    "gateway": "duitku",
    "amount": 100000,
    "item_name": "Product Name"
}
```

### Get Transactions
```
GET /wp-json/sofir/v1/payments/transactions
Auth: Admin (manage_options)

Response: Array of transaction objects
```

### Webhooks
```
POST /wp-json/sofir/v1/payments/webhook/duitku
POST /wp-json/sofir/v1/payments/webhook/xendit
POST /wp-json/sofir/v1/payments/webhook/midtrans
Auth: Public (signature validated)
```

## 🎣 Available Hooks

### Actions

```php
// Payment status changed
do_action('sofir/payment/status_changed', $transaction_id, $status);

// Gateway-specific webhooks
do_action('sofir/payment/duitku_webhook', $transaction_id, $status, $params);
do_action('sofir/payment/xendit_webhook', $transaction_id, $status, $params);
do_action('sofir/payment/midtrans_webhook', $transaction_id, $status, $params);
```

### Filters

```php
// Modify available gateways
apply_filters('sofir/payment/gateways', $gateways);
```

## 💡 Common Use Cases

### 1. E-commerce Checkout
Add payment form to product checkout page

### 2. Membership Payment
Process membership subscription payments

### 3. Event Registration
Accept payment for event tickets

### 4. Service Booking
Payment for appointment bookings

### 5. Donation Platform
Accept donations with multiple methods

## 🔐 Security

- ✅ Transactions stored securely in WordPress options
- ✅ Webhook signature validation
- ✅ User authentication required for payment creation
- ✅ Admin-only access to transaction list
- ✅ HTTPS required for production
- ✅ Sanitized and escaped inputs/outputs

## 📊 Transaction Status

| Status | Description |
|--------|-------------|
| `pending` | Payment initiated, awaiting confirmation |
| `completed` | Payment successful |
| `failed` | Payment failed or cancelled |

## 🐛 Troubleshooting

**Webhook not working?**
- Check URL is publicly accessible
- Verify webhook configured in gateway
- Check WordPress debug log
- Use ngrok for local testing

**Payment not redirecting?**
- Verify API credentials
- Check gateway is enabled
- User must be logged in
- Check browser console for errors

**Manual payment not showing?**
- Ensure "Enable Manual Payment" is checked
- Clear WordPress cache
- Check shortcode syntax

## 📖 Learn More

- **Gateway Documentation:**
  - Duitku: https://docs.duitku.com
  - Xendit: https://developers.xendit.co
  - Midtrans: https://docs.midtrans.com

- **SOFIR Documentation:**
  - [Payment Guide (ID)](./PAYMENT_GUIDE.md)
  - [Payment Documentation (EN)](./PAYMENT_DOCUMENTATION.md)

## 🔄 Version

**Current Version:** 1.0.0

**Changelog:**
- ✅ Manual payment support
- ✅ Duitku integration
- ✅ Xendit integration  
- ✅ Midtrans integration
- ✅ Transaction management
- ✅ Webhook handlers
- ✅ REST API
- ✅ Shortcode support

---

**Part of SOFIR WordPress Plugin** | Copyright © 2024
