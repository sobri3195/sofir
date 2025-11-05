# SOFIR Payment Features

## 🎯 Overview

SOFIR plugin includes a complete payment processing system with support for manual payments and Indonesian local payment gateways. Perfect for e-commerce, membership sites, service bookings, and donations.

---

## 💳 Supported Payment Methods

### 1. Manual Payment ✅
**Default: Enabled**

Perfect for:
- Bank transfers
- Cash on delivery
- Check payments
- Custom payment instructions

**Configuration:**
- ✅ No API required
- ✅ Works out of the box
- ✅ Custom instructions support

---

### 2. Duitku Payment Gateway 🇮🇩

**Payment Methods:**
- 💳 Credit Card (Visa, Mastercard, JCB)
- 🏦 Virtual Account (BCA, Mandiri, BNI, BRI, Permata, CIMB, Danamon)
- 📱 E-wallet (OVO, Dana, LinkAja, ShopeePay)
- 🏪 Convenience Store (Alfamart, Indomaret)

**Features:**
- ✅ Multi-payment methods in one gateway
- ✅ Instant payment confirmation
- ✅ Automatic webhook notifications
- ✅ Sandbox mode for testing
- ✅ Local Indonesian support

**Required:**
- Merchant Code
- API Key
- Webhook URL configuration

**Website:** https://duitku.com

---

### 3. Xendit Payment Gateway 🇮🇩

**Payment Methods:**
- 🏦 Virtual Account (all major banks)
- 💳 Credit/Debit Card
- 📱 E-wallet (OVO, Dana, LinkAja, ShopeePay)
- 🔲 QRIS (QR Code Indonesian Standard)
- 🏪 Retail Outlets (Alfamart, Indomaret)

**Features:**
- ✅ Developer-friendly API
- ✅ Comprehensive documentation
- ✅ Multiple payment channels
- ✅ Automatic reconciliation
- ✅ Webhook notifications
- ✅ Test mode with test keys

**Required:**
- API Key (Secret Key)
- Webhook URL configuration

**Website:** https://xendit.co

---

### 4. Midtrans Payment Gateway 🇮🇩

**Payment Methods (Snap UI):**
- 💳 Credit Card (3D Secure)
- 🏦 Virtual Account (all major banks)
- 📱 E-wallet (GoPay, ShopeePay)
- 🏪 Convenience Store (Alfamart, Indomaret)
- 📲 Bank Transfer
- 💸 Akulaku (PayLater)

**Features:**
- ✅ Snap Payment UI (all methods in one page)
- ✅ Mobile-optimized checkout
- ✅ Fraud detection system
- ✅ Installment support
- ✅ Recurring payments
- ✅ Sandbox mode

**Required:**
- Server Key
- Client Key
- Webhook URL configuration

**Website:** https://midtrans.com

---

## 🚀 Key Features

### Transaction Management
- ✅ Automatic transaction tracking
- ✅ Unique transaction IDs
- ✅ Status updates (pending, completed, failed)
- ✅ Transaction history
- ✅ User transaction linking

### Webhook Support
- ✅ Automatic payment confirmation
- ✅ Real-time status updates
- ✅ Signature validation
- ✅ Retry mechanism
- ✅ Event hooks for custom logic

### Easy Integration
- ✅ **Shortcode:** `[sofir_payment_form]`
- ✅ **REST API:** `/wp-json/sofir/v1/payments/create`
- ✅ **JavaScript:** `wp.apiFetch()` integration
- ✅ **Hooks:** WordPress action/filter hooks

### Developer-Friendly
- ✅ Complete documentation
- ✅ Code examples
- ✅ Testing mode support
- ✅ Event hooks
- ✅ Filter hooks
- ✅ REST API endpoints

---

## 📋 Quick Start

### 1. Enable Payment Method

```
WordPress Admin → SOFIR Dashboard → Content Tab → Payment Settings
```

**For Manual Payment:**
- ✅ Check "Enable Manual Payment"
- Save settings

**For Payment Gateway:**
- Get API credentials from gateway dashboard
- Enter credentials in SOFIR settings
- ✅ Enable the gateway
- Configure webhook URL
- Save settings

---

### 2. Add Payment Form to Page

**Using Shortcode:**
```
[sofir_payment_form amount="100000" item_name="Premium Membership"]
```

**With Custom Return URL:**
```
[sofir_payment_form 
    amount="250000" 
    item_name="Annual Subscription" 
    return_url="/thank-you"
]
```

---

### 3. Configure Webhook (for gateways)

**Webhook URLs:**
```
Duitku:   https://yourdomain.com/wp-json/sofir/v1/payments/webhook/duitku
Xendit:   https://yourdomain.com/wp-json/sofir/v1/payments/webhook/xendit
Midtrans: https://yourdomain.com/wp-json/sofir/v1/payments/webhook/midtrans
```

Copy the relevant URL and paste it in your payment gateway dashboard settings.

---

## 💻 Code Examples

### Basic Payment Form
```php
// In your template or page
echo do_shortcode('[sofir_payment_form amount="50000" item_name="Digital Product"]');
```

### REST API Payment
```javascript
// JavaScript payment creation
wp.apiFetch({
    path: '/sofir/v1/payments/create',
    method: 'POST',
    data: {
        gateway: 'duitku',
        amount: 100000,
        item_name: 'Premium Package'
    }
}).then(response => {
    if (response.payment_url) {
        window.location.href = response.payment_url;
    }
});
```

### Payment Status Hook
```php
// Execute code when payment is completed
add_action('sofir/payment/status_changed', function($transaction_id, $status) {
    if ($status === 'completed') {
        // Activate membership
        // Send confirmation email
        // Add loyalty points
        // Update order status
    }
}, 10, 2);
```

### Custom Gateway Logic
```php
// Gateway-specific webhook handling
add_action('sofir/payment/duitku_webhook', function($transaction_id, $status, $params) {
    error_log("Duitku payment: $transaction_id - Status: $status");
    
    // Custom logic here
    // Send SMS notification
    // Update inventory
    // etc.
}, 10, 3);
```

---

## 🎯 Use Cases

### E-commerce
- Product checkout
- Shopping cart payment
- Order processing
- Digital downloads

### Membership Sites
- Subscription payments
- Plan upgrades
- Recurring billing
- Access control

### Service Booking
- Appointment payments
- Service deposits
- Booking confirmations
- Cancellation refunds

### Event Registration
- Ticket sales
- Event registration fees
- Workshop payments
- Conference tickets

### Donations
- One-time donations
- Recurring donations
- Fundraising campaigns
- Cause-based giving

---

## 🔒 Security Features

- ✅ Secure transaction storage
- ✅ Webhook signature validation
- ✅ User authentication required
- ✅ HTTPS enforced for production
- ✅ Sanitized inputs
- ✅ Escaped outputs
- ✅ CSRF protection
- ✅ Admin-only transaction access

---

## 📊 Transaction Tracking

### Transaction Data
```php
[
    'id' => 'TRX-123456-1699200000',
    'gateway' => 'duitku',
    'amount' => 100000,
    'item_name' => 'Premium Membership',
    'status' => 'completed',
    'user_id' => 1,
    'created_at' => '2024-11-05 10:00:00',
    'updated_at' => '2024-11-05 10:05:00'
]
```

### Transaction Status
- **pending** - Payment initiated, awaiting confirmation
- **completed** - Payment successful
- **failed** - Payment failed or cancelled

### Admin Dashboard
View all transactions in WordPress admin:
```
SOFIR Dashboard → Transactions
```

Access via REST API (admin only):
```
GET /wp-json/sofir/v1/payments/transactions
```

---

## 🧪 Testing Mode

### Duitku Sandbox
```
Mode: Sandbox
URL: https://sandbox.duitku.com
Credentials: Use sandbox merchant code and API key
```

### Xendit Test Mode
```
API Key: Use test key (starts with xnd_development_)
Test Card: 4000000000000002 (Success)
Documentation: https://developers.xendit.co/api-reference/
```

### Midtrans Sandbox
```
Mode: Enable Sandbox in settings
Test Cards:
  - Success: 4811 1111 1111 1114
  - Failure: 4911 1111 1111 1113
CVV: 123
Expiry: Any future date
```

---

## 📖 Documentation

### Complete Guides
- **[Payment Guide (Indonesian)](./modules/payments/PAYMENT_GUIDE.md)** - Panduan lengkap bahasa Indonesia
- **[Payment Documentation (English)](./modules/payments/PAYMENT_DOCUMENTATION.md)** - Full English documentation
- **[Quick README](./modules/payments/README.md)** - Quick reference guide

### Source Code
- **[Payment Manager](./modules/payments/manager.php)** - Main payment handler
- **[JavaScript](./assets/js/payments.js)** - Frontend payment script

---

## 🔗 Gateway Resources

### Official Documentation
- **Duitku:** https://docs.duitku.com
- **Xendit:** https://developers.xendit.co
- **Midtrans:** https://docs.midtrans.com

### Registration
- **Duitku:** https://duitku.com
- **Xendit:** https://xendit.co
- **Midtrans:** https://midtrans.com

---

## ❓ FAQ

### Q: Do I need all payment gateways?
**A:** No, you can enable only what you need. Manual payment works without any gateway.

### Q: Can I use multiple gateways?
**A:** Yes, enable as many as you want. Users will choose during checkout.

### Q: Is sandbox mode available?
**A:** Yes, all gateways support testing mode with sandbox credentials.

### Q: How are transactions stored?
**A:** Securely in WordPress options table with full data tracking.

### Q: Can I customize the payment form?
**A:** Yes, via CSS styling, custom templates, or REST API integration.

### Q: What about refunds?
**A:** Process refunds directly in your payment gateway dashboard.

### Q: Is PCI compliance required?
**A:** No, payments go through gateway's secure pages. No card data touches your server.

---

## 🆘 Support

### Common Issues

**Webhook not working?**
- Verify webhook URL in gateway dashboard
- Check URL is publicly accessible (not localhost)
- Use ngrok for local testing
- Check WordPress debug log

**Payment not redirecting?**
- Verify API credentials are correct
- Check gateway is enabled in settings
- Ensure user is logged in
- Check browser console for errors

**Transaction not updating?**
- Verify webhook is configured correctly
- Test webhook with gateway's testing tool
- Check signature validation
- Enable WordPress debug mode

### Get Help
- Check documentation in `/modules/payments/`
- Review code examples above
- Test in sandbox mode first
- Contact gateway support for gateway-specific issues

---

## 📌 Summary

| Feature | Status | Notes |
|---------|--------|-------|
| Manual Payment | ✅ | No configuration needed |
| Duitku Gateway | ✅ | Indonesian multi-payment |
| Xendit Gateway | ✅ | Developer-friendly API |
| Midtrans Gateway | ✅ | Snap payment UI |
| Transaction Tracking | ✅ | Full history and status |
| Webhook Support | ✅ | Auto status updates |
| REST API | ✅ | Programmatic access |
| Shortcode | ✅ | Easy integration |
| Event Hooks | ✅ | Custom logic support |
| Test Mode | ✅ | Sandbox for all gateways |

---

## 🎉 Getting Started

**Ready to accept payments?**

1. Choose your payment method(s)
2. Configure in SOFIR settings
3. Add payment form to your page
4. Test in sandbox mode
5. Go live! 🚀

---

**Version:** 1.0.0  
**Status:** ✅ Production Ready  
**Support:** Full documentation included

---

# Fitur Pembayaran SOFIR (Bahasa Indonesia)

## 🎯 Gambaran Umum

Plugin SOFIR menyertakan sistem pemrosesan pembayaran lengkap dengan dukungan untuk pembayaran manual dan payment gateway lokal Indonesia. Sempurna untuk e-commerce, situs membership, booking layanan, dan donasi.

---

## 💳 Metode Pembayaran yang Didukung

### 1. Pembayaran Manual ✅
**Default: Aktif**

Cocok untuk:
- Transfer bank
- Cash on delivery (COD)
- Pembayaran cek
- Instruksi pembayaran custom

**Konfigurasi:**
- ✅ Tidak perlu API
- ✅ Langsung bisa digunakan
- ✅ Dukungan instruksi custom

---

### 2. Duitku Payment Gateway 🇮🇩

**Metode Pembayaran:**
- 💳 Kartu Kredit (Visa, Mastercard, JCB)
- 🏦 Virtual Account (BCA, Mandiri, BNI, BRI, Permata, CIMB, Danamon)
- 📱 E-wallet (OVO, Dana, LinkAja, ShopeePay)
- 🏪 Convenience Store (Alfamart, Indomaret)

**Fitur:**
- ✅ Banyak metode pembayaran dalam satu gateway
- ✅ Konfirmasi pembayaran instan
- ✅ Notifikasi webhook otomatis
- ✅ Mode sandbox untuk testing
- ✅ Support lokal Indonesia

**Diperlukan:**
- Merchant Code
- API Key
- Konfigurasi Webhook URL

**Website:** https://duitku.com

---

### 3. Xendit Payment Gateway 🇮🇩

**Metode Pembayaran:**
- 🏦 Virtual Account (semua bank besar)
- 💳 Kartu Kredit/Debit
- 📱 E-wallet (OVO, Dana, LinkAja, ShopeePay)
- 🔲 QRIS (QR Code Indonesian Standard)
- 🏪 Retail Outlets (Alfamart, Indomaret)

**Fitur:**
- ✅ API ramah developer
- ✅ Dokumentasi lengkap
- ✅ Banyak channel pembayaran
- ✅ Rekonsiliasi otomatis
- ✅ Notifikasi webhook
- ✅ Mode test dengan test keys

**Diperlukan:**
- API Key (Secret Key)
- Konfigurasi Webhook URL

**Website:** https://xendit.co

---

### 4. Midtrans Payment Gateway 🇮🇩

**Metode Pembayaran (Snap UI):**
- 💳 Kartu Kredit (3D Secure)
- 🏦 Virtual Account (semua bank besar)
- 📱 E-wallet (GoPay, ShopeePay)
- 🏪 Convenience Store (Alfamart, Indomaret)
- 📲 Bank Transfer
- 💸 Akulaku (PayLater)

**Fitur:**
- ✅ Snap Payment UI (semua metode dalam satu halaman)
- ✅ Checkout mobile-optimized
- ✅ Sistem deteksi fraud
- ✅ Dukungan cicilan
- ✅ Pembayaran berulang
- ✅ Mode sandbox

**Diperlukan:**
- Server Key
- Client Key
- Konfigurasi Webhook URL

**Website:** https://midtrans.com

---

## 🚀 Fitur Utama

### Manajemen Transaksi
- ✅ Tracking transaksi otomatis
- ✅ ID transaksi unik
- ✅ Update status (pending, completed, failed)
- ✅ Riwayat transaksi
- ✅ Linking transaksi ke user

### Dukungan Webhook
- ✅ Konfirmasi pembayaran otomatis
- ✅ Update status real-time
- ✅ Validasi signature
- ✅ Mekanisme retry
- ✅ Event hooks untuk logika custom

### Integrasi Mudah
- ✅ **Shortcode:** `[sofir_payment_form]`
- ✅ **REST API:** `/wp-json/sofir/v1/payments/create`
- ✅ **JavaScript:** Integrasi `wp.apiFetch()`
- ✅ **Hooks:** WordPress action/filter hooks

### Ramah Developer
- ✅ Dokumentasi lengkap
- ✅ Contoh kode
- ✅ Dukungan mode testing
- ✅ Event hooks
- ✅ Filter hooks
- ✅ REST API endpoints

---

## 📋 Mulai Cepat

### 1. Aktifkan Metode Pembayaran

```
WordPress Admin → SOFIR Dashboard → Tab Content → Payment Settings
```

**Untuk Manual Payment:**
- ✅ Centang "Enable Manual Payment"
- Simpan pengaturan

**Untuk Payment Gateway:**
- Dapatkan API credentials dari dashboard gateway
- Masukkan credentials di pengaturan SOFIR
- ✅ Aktifkan gateway
- Konfigurasi webhook URL
- Simpan pengaturan

---

### 2. Tambah Form Pembayaran ke Halaman

**Menggunakan Shortcode:**
```
[sofir_payment_form amount="100000" item_name="Membership Premium"]
```

**Dengan Custom Return URL:**
```
[sofir_payment_form 
    amount="250000" 
    item_name="Langganan Tahunan" 
    return_url="/terima-kasih"
]
```

---

### 3. Konfigurasi Webhook (untuk gateway)

**URL Webhook:**
```
Duitku:   https://domainanda.com/wp-json/sofir/v1/payments/webhook/duitku
Xendit:   https://domainanda.com/wp-json/sofir/v1/payments/webhook/xendit
Midtrans: https://domainanda.com/wp-json/sofir/v1/payments/webhook/midtrans
```

Copy URL yang sesuai dan paste di pengaturan dashboard payment gateway Anda.

---

## 💻 Contoh Kode

### Form Pembayaran Dasar
```php
// Di template atau halaman Anda
echo do_shortcode('[sofir_payment_form amount="50000" item_name="Produk Digital"]');
```

### REST API Payment
```javascript
// Membuat pembayaran dengan JavaScript
wp.apiFetch({
    path: '/sofir/v1/payments/create',
    method: 'POST',
    data: {
        gateway: 'duitku',
        amount: 100000,
        item_name: 'Paket Premium'
    }
}).then(response => {
    if (response.payment_url) {
        window.location.href = response.payment_url;
    }
});
```

### Hook Status Pembayaran
```php
// Eksekusi kode saat pembayaran selesai
add_action('sofir/payment/status_changed', function($transaction_id, $status) {
    if ($status === 'completed') {
        // Aktifkan membership
        // Kirim email konfirmasi
        // Tambah poin loyalitas
        // Update status order
    }
}, 10, 2);
```

---

## 🎯 Use Case

### E-commerce
- Checkout produk
- Pembayaran keranjang belanja
- Pemrosesan order
- Download digital

### Situs Membership
- Pembayaran langganan
- Upgrade paket
- Billing berulang
- Kontrol akses

### Booking Layanan
- Pembayaran appointment
- Deposit layanan
- Konfirmasi booking
- Refund pembatalan

### Registrasi Event
- Penjualan tiket
- Biaya registrasi event
- Pembayaran workshop
- Tiket konferensi

### Donasi
- Donasi satu kali
- Donasi berulang
- Kampanye fundraising
- Donasi berbasis tujuan

---

## 📖 Dokumentasi

### Panduan Lengkap
- **[Panduan Pembayaran (Indonesia)](./modules/payments/PAYMENT_GUIDE.md)** - Panduan lengkap
- **[Payment Documentation (English)](./modules/payments/PAYMENT_DOCUMENTATION.md)** - Full English docs
- **[Quick README](./modules/payments/README.md)** - Referensi cepat

### Source Code
- **[Payment Manager](./modules/payments/manager.php)** - Handler pembayaran utama
- **[JavaScript](./assets/js/payments.js)** - Script frontend payment

---

## 📌 Ringkasan

| Fitur | Status | Catatan |
|-------|--------|---------|
| Pembayaran Manual | ✅ | Tanpa konfigurasi |
| Gateway Duitku | ✅ | Multi-payment Indonesia |
| Gateway Xendit | ✅ | API ramah developer |
| Gateway Midtrans | ✅ | UI Snap payment |
| Tracking Transaksi | ✅ | Riwayat dan status lengkap |
| Dukungan Webhook | ✅ | Update status otomatis |
| REST API | ✅ | Akses programmatic |
| Shortcode | ✅ | Integrasi mudah |
| Event Hooks | ✅ | Dukungan logika custom |
| Mode Test | ✅ | Sandbox untuk semua gateway |

---

**Versi:** 1.0.0  
**Status:** ✅ Production Ready  
**Dukungan:** Dokumentasi lengkap tersedia
