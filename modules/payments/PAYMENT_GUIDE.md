# Panduan Sistem Pembayaran SOFIR

## Daftar Isi
- [Gambaran Umum](#gambaran-umum)
- [Metode Pembayaran](#metode-pembayaran)
- [Konfigurasi Payment Gateway](#konfigurasi-payment-gateway)
- [Penggunaan Shortcode](#penggunaan-shortcode)
- [REST API](#rest-api)
- [Webhook](#webhook)
- [Event Hooks](#event-hooks)
- [Contoh Implementasi](#contoh-implementasi)

---

## Gambaran Umum

Plugin SOFIR menyediakan sistem pembayaran lengkap dengan dukungan untuk:

1. **Pembayaran Manual** - Transfer bank manual dengan instruksi pembayaran
2. **Duitku** - Payment gateway lokal Indonesia
3. **Xendit** - Payment gateway untuk berbagai metode pembayaran
4. **Midtrans** - Payment gateway populer di Indonesia

Semua transaksi disimpan dengan aman dan dapat dilacak melalui REST API.

---

## Metode Pembayaran

### 1. Pembayaran Manual

Pembayaran manual memungkinkan pelanggan melakukan transfer bank secara mandiri.

**Fitur:**
- Tidak memerlukan konfigurasi API
- Instruksi pembayaran otomatis
- Cocok untuk transfer bank manual
- Status transaksi dapat diupdate manual oleh admin

**Status Default:** ✅ Aktif (enabled by default)

### 2. Duitku

Payment gateway lokal Indonesia dengan berbagai metode pembayaran.

**Fitur:**
- Virtual Account (BCA, Mandiri, BNI, BRI, dll)
- E-wallet (OVO, Dana, LinkAja, ShopeePay)
- Credit Card
- Convenience Store (Alfamart, Indomaret)

**Konfigurasi Diperlukan:**
- Merchant Code
- API Key
- Webhook callback URL

### 3. Xendit

Platform pembayaran yang mendukung berbagai metode.

**Fitur:**
- Virtual Account
- E-wallet
- Credit Card
- QRIS
- Retail Outlets

**Konfigurasi Diperlukan:**
- API Key (Secret Key)
- Webhook URL

### 4. Midtrans

Payment gateway populer dengan integrasi Snap.

**Fitur:**
- Snap Payment (berbagai metode dalam satu halaman)
- Virtual Account
- Credit Card
- E-wallet
- Convenience Store

**Konfigurasi Diperlukan:**
- Server Key
- Client Key
- Mode (Sandbox/Production)

---

## Konfigurasi Payment Gateway

### Mengakses Halaman Konfigurasi

1. Login ke WordPress Admin
2. Navigasi ke **SOFIR Dashboard** → **Content Tab**
3. Scroll ke bagian **Payment Settings**

### Konfigurasi Umum

```
Currency: IDR (default untuk Indonesia)
```

### Konfigurasi Duitku

**Langkah-langkah:**

1. **Daftar Akun Duitku**
   - Kunjungi: https://duitku.com
   - Daftar sebagai merchant
   - Verifikasi akun

2. **Dapatkan API Credentials**
   - Login ke Duitku Dashboard
   - Buka menu **Settings** → **API**
   - Copy `Merchant Code` dan `API Key`

3. **Konfigurasi di SOFIR**
   ```
   Duitku Merchant Code: [Paste merchant code]
   Duitku API Key: [Paste API key]
   ✅ Enable Duitku
   ```

4. **Setup Webhook**
   - URL Webhook: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/duitku`
   - Paste URL ini di Duitku Dashboard → Callback URL

### Konfigurasi Xendit

**Langkah-langkah:**

1. **Daftar Akun Xendit**
   - Kunjungi: https://xendit.co
   - Daftar sebagai merchant
   - Verifikasi dokumen bisnis

2. **Dapatkan API Key**
   - Login ke Xendit Dashboard
   - Buka menu **Settings** → **Developers** → **API Keys**
   - Generate dan copy `Secret API Key`

3. **Konfigurasi di SOFIR**
   ```
   Xendit API Key: [Paste secret API key]
   ✅ Enable Xendit
   ```

4. **Setup Webhook**
   - URL Webhook: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/xendit`
   - Paste URL ini di Xendit Dashboard → Webhooks

### Konfigurasi Midtrans

**Langkah-langkah:**

1. **Daftar Akun Midtrans**
   - Kunjungi: https://midtrans.com
   - Daftar sebagai merchant
   - Verifikasi akun

2. **Dapatkan API Keys**
   - Login ke Midtrans Dashboard
   - Buka menu **Settings** → **Access Keys**
   - Copy `Server Key` dan `Client Key`

3. **Konfigurasi di SOFIR**
   ```
   Midtrans Server Key: [Paste server key]
   Midtrans Client Key: [Paste client key]
   ✅ Sandbox Mode (untuk testing)
   ✅ Enable Midtrans
   ```

4. **Setup Webhook**
   - URL Webhook: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/midtrans`
   - Paste URL ini di Midtrans Dashboard → Settings → Notification URL

---

## Penggunaan Shortcode

### Shortcode Dasar

```
[sofir_payment_form amount="100000" item_name="Premium Membership" return_url="/thank-you"]
```

### Parameter Shortcode

| Parameter | Deskripsi | Wajib | Default |
|-----------|-----------|-------|---------|
| `amount` | Jumlah pembayaran (angka) | Ya | 0 |
| `item_name` | Nama produk/layanan | Tidak | '' |
| `return_url` | URL setelah pembayaran | Tidak | home_url |

### Contoh Penggunaan

#### 1. Form Pembayaran Sederhana

```
[sofir_payment_form amount="50000" item_name="E-book Digital Marketing"]
```

#### 2. Form dengan Return URL Custom

```
[sofir_payment_form 
    amount="250000" 
    item_name="Course Premium" 
    return_url="/dashboard/my-courses"
]
```

#### 3. Form di Halaman Checkout

```
[sofir_payment_form 
    amount="1500000" 
    item_name="Annual Membership" 
    return_url="/membership/success"
]
```

### Output HTML

Shortcode akan menghasilkan HTML seperti ini:

```html
<div class="sofir-payment-form" data-amount="100000" data-item="Premium Membership" data-return="/thank-you">
    <h3>Select Payment Method</h3>
    
    <!-- Pilihan metode pembayaran -->
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
    
    <!-- Total pembayaran -->
    <div class="sofir-payment-total">
        <strong>Total:</strong> <span>IDR 100,000.00</span>
    </div>
    
    <!-- Tombol submit -->
    <button type="button" class="button button-primary sofir-payment-submit">
        Proceed to Payment
    </button>
</div>
```

---

## REST API

### Endpoints

#### 1. Create Payment

Membuat transaksi pembayaran baru.

**Endpoint:** `POST /wp-json/sofir/v1/payments/create`

**Authentication:** User harus login

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

Mendapatkan daftar semua transaksi.

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

Endpoint untuk menerima notifikasi dari payment gateway.

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

## Webhook

### Webhook Flow

```
Payment Gateway → Webhook URL → Update Transaction Status → Trigger Hooks
```

### Konfigurasi Webhook di Gateway

#### Duitku

1. Login ke Duitku Dashboard
2. Menu **Settings** → **Callback URL**
3. Masukkan: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/duitku`
4. Save

#### Xendit

1. Login ke Xendit Dashboard
2. Menu **Settings** → **Webhooks**
3. Click **Add Webhook**
4. URL: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/xendit`
5. Events: Select all payment events
6. Save

#### Midtrans

1. Login ke Midtrans Dashboard
2. Menu **Settings** → **Configuration**
3. **Payment Notification URL**: `https://yourdomain.com/wp-json/sofir/v1/payments/webhook/midtrans`
4. Save

### Testing Webhook

Untuk testing webhook di local development:

1. **Gunakan ngrok**
   ```bash
   ngrok http 80
   ```

2. **Gunakan URL ngrok di webhook config**
   ```
   https://abc123.ngrok.io/wp-json/sofir/v1/payments/webhook/duitku
   ```

3. **Lakukan test payment dari gateway dashboard**

---

## Event Hooks

### Action Hooks

Plugin menyediakan action hooks untuk customize behavior:

#### 1. Payment Status Changed

Triggered ketika status pembayaran berubah.

```php
add_action('sofir/payment/status_changed', function($transaction_id, $status) {
    if ($status === 'completed') {
        // Kirim email konfirmasi
        // Update user membership
        // Add loyalty points
        error_log("Payment completed: $transaction_id");
    }
}, 10, 2);
```

#### 2. Duitku Webhook

Triggered ketika menerima webhook dari Duitku.

```php
add_action('sofir/payment/duitku_webhook', function($transaction_id, $status, $params) {
    error_log("Duitku webhook: $transaction_id - Status: $status");
    // Custom logic
}, 10, 3);
```

#### 3. Xendit Webhook

Triggered ketika menerima webhook dari Xendit.

```php
add_action('sofir/payment/xendit_webhook', function($transaction_id, $status, $params) {
    error_log("Xendit webhook: $transaction_id - Status: $status");
    // Custom logic
}, 10, 3);
```

#### 4. Midtrans Webhook

Triggered ketika menerima webhook dari Midtrans.

```php
add_action('sofir/payment/midtrans_webhook', function($transaction_id, $status, $params) {
    error_log("Midtrans webhook: $transaction_id - Status: $status");
    // Custom logic
}, 10, 3);
```

### Filter Hooks

#### 1. Modify Payment Gateways

Customize daftar payment gateway yang tersedia.

```php
add_filter('sofir/payment/gateways', function($gateways) {
    // Tambah custom gateway
    $gateways['custom'] = [
        'id' => 'custom',
        'name' => 'Custom Gateway',
        'enabled' => true
    ];
    
    return $gateways;
});
```

---

## Contoh Implementasi

### 1. Halaman Checkout Sederhana

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

### 2. Integrasi dengan Membership

```php
// Aktifkan membership setelah pembayaran sukses
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

### 3. Custom Payment Form dengan JavaScript

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

### 5. Admin Dashboard untuk Transaksi

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

### Masalah Umum

#### 1. Webhook tidak berfungsi

**Solusi:**
- Pastikan webhook URL dapat diakses publik (tidak localhost)
- Gunakan ngrok untuk testing di local
- Check error log: `wp-content/debug.log`
- Verifikasi URL webhook di gateway dashboard

#### 2. Payment tidak redirect ke gateway

**Solusi:**
- Check API credentials sudah benar
- Pastikan gateway sudah enabled
- Check browser console untuk JavaScript errors
- Verifikasi user sudah login

#### 3. Transaction status tidak update

**Solusi:**
- Verifikasi webhook URL sudah terdaftar di gateway
- Check webhook signature/authentication
- Test webhook dengan tool seperti Postman
- Enable WordPress debug mode

#### 4. Manual payment tidak muncul

**Solusi:**
- Pastikan "Enable Manual Payment" tercentang
- Clear WordPress cache
- Check shortcode syntax

---

## Status Transaksi

| Status | Deskripsi |
|--------|-----------|
| `pending` | Pembayaran menunggu konfirmasi |
| `completed` | Pembayaran berhasil |
| `failed` | Pembayaran gagal |

---

## Security Best Practices

1. **Jangan hardcode API keys di code**
   - Gunakan environment variables
   - Atau simpan di database terenkripsi

2. **Validate webhook signature**
   - Implementasi signature verification untuk setiap gateway
   - Contoh sudah ada di webhook handlers

3. **Use HTTPS**
   - Wajib untuk production
   - Diperlukan oleh semua payment gateway

4. **Log semua transaksi**
   - Sudah otomatis di-handle oleh plugin
   - Simpan log untuk audit trail

5. **Limit webhook access**
   - Whitelist IP address gateway
   - Implementasi rate limiting

---

## Testing Mode

### Duitku Sandbox

```
URL: https://sandbox.duitku.com
Test Cards: Tersedia di dashboard Duitku
```

### Xendit Test Mode

```
API Key: Gunakan test API key (dimulai dengan xnd_development_)
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
- Documentation: Folder `/modules/payments/`

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
