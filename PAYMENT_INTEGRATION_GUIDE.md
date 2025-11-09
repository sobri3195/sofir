# 💳 Payment Gateway Integration Guide

## ✅ Integrasi Lengkap

SOFIR sudah **sepenuhnya terintegrasi** dengan payment gateway lokal Indonesia:

### 🏦 Gateway yang Didukung

1. **💵 Manual Payment**
   - Transfer bank manual
   - Tidak perlu konfigurasi API
   - Instruksi pembayaran otomatis

2. **🏦 Duitku**
   - Virtual Account (BCA, BNI, Mandiri, BRI, dll)
   - E-wallet (OVO, GoPay, Dana, LinkAja)
   - Minimarket (Alfamart, Indomaret)
   - Kartu Kredit/Debit

3. **💳 Xendit**
   - Virtual Account
   - E-wallet (OVO, Dana, LinkAja, dll)
   - Kartu Kredit
   - QRIS

4. **🛒 Midtrans**
   - Snap Payment (All-in-One)
   - Semua metode dalam satu halaman
   - Sandbox mode untuk testing

---

## 🎯 Cara Menggunakan

### 1. Aktifkan Payment Gateway

**Lokasi:** WordPress Admin → SOFIR → **Payments**

Di tab Payments, Anda akan menemukan:

✅ **Overview Section** - Ringkasan fitur payment gateway  
✅ **General Settings** - Pilih mata uang (IDR, USD, MYR)  
✅ **Gateway Configuration** - Aktifkan dan konfigurasi gateway  
✅ **Webhook URLs** - URL webhook dengan tombol copy  
✅ **Transaction History** - Riwayat transaksi pembayaran  
✅ **Documentation** - Contoh kode dan API endpoints  

### 2. Konfigurasi API Keys

#### Manual Payment
- Toggle switch: ON
- Tidak perlu API key

#### Duitku
1. Toggle switch: ON
2. Isi **Merchant Code**: `D12345`
3. Isi **API Key**: `xxxxxxxxxxxxxxxx`
4. Cara mendapatkan:
   - Daftar di [duitku.com](https://duitku.com)
   - Login → Settings → API
   - Copy Merchant Code dan API Key

#### Xendit
1. Toggle switch: ON
2. Isi **API Key**: `xnd_development_xxxx` (testing) atau `xnd_production_xxxx` (live)
3. Cara mendapatkan:
   - Daftar di [dashboard.xendit.co](https://dashboard.xendit.co)
   - Settings → Developers → API Keys
   - Copy Secret Key

#### Midtrans
1. Toggle switch: ON
2. Isi **Server Key**: `SB-Mid-server-xxxxxxxx`
3. Isi **Client Key**: `SB-Mid-client-xxxxxxxx`
4. Centang **Sandbox Mode** untuk testing
5. Cara mendapatkan:
   - Daftar di [dashboard.midtrans.com](https://dashboard.midtrans.com)
   - Pilih Sandbox/Production
   - Settings → Access Keys
   - Copy kedua keys

### 3. Setup Webhook URLs

Setelah mengaktifkan gateway, salin webhook URL dan masukkan ke dashboard gateway:

**Duitku:**
```
https://yourdomain.com/wp-json/sofir/v1/payments/webhook/duitku
```
Masukkan ke: Callback URL / Return URL

**Xendit:**
```
https://yourdomain.com/wp-json/sofir/v1/payments/webhook/xendit
```
Masukkan ke: Settings → Webhooks

**Midtrans:**
```
https://yourdomain.com/wp-json/sofir/v1/payments/webhook/midtrans
```
Masukkan ke: Settings → Configuration → Notification URL

💡 **Tip:** Gunakan tombol **📋 Copy** di admin panel untuk copy URL dengan mudah.

---

## 📝 Cara Implementasi

### A. Menggunakan Shortcode

Tambahkan form pembayaran di halaman/post:

```
[sofir_payment_form amount="100000" item_name="Premium Package"]
```

**Parameter:**
- `amount` - Jumlah pembayaran (wajib)
- `item_name` - Nama produk/layanan (wajib)
- `return_url` - URL setelah pembayaran (opsional)

**Contoh lengkap:**
```
[sofir_payment_form 
    amount="250000" 
    item_name="Membership Gold" 
    return_url="/thank-you"
]
```

### B. Menggunakan REST API

**Create Payment:**
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

**Get Transactions (Admin):**
```javascript
wp.apiFetch({
    path: '/sofir/v1/payments/transactions',
    method: 'GET'
}).then(transactions => {
    console.log(transactions);
});
```

### C. Developer Hooks

**Payment Status Changed:**
```php
add_action('sofir/payment/status_changed', function($transaction_id, $status) {
    if ($status === 'completed') {
        // Aktivasi membership
        // Kirim email konfirmasi
        // Tambah loyalty points
    }
}, 10, 2);
```

**Gateway-Specific Webhooks:**
```php
// Duitku
add_action('sofir/payment/duitku_webhook', function($transaction_id, $status, $params) {
    // Custom logic untuk Duitku
}, 10, 3);

// Xendit
add_action('sofir/payment/xendit_webhook', function($transaction_id, $status, $params) {
    // Custom logic untuk Xendit
}, 10, 3);

// Midtrans
add_action('sofir/payment/midtrans_webhook', function($transaction_id, $status, $params) {
    // Custom logic untuk Midtrans
}, 10, 3);
```

**Modify Available Gateways:**
```php
add_filter('sofir/payment/gateways', function($gateways) {
    // Tambah gateway custom
    $gateways['custom'] = [
        'id' => 'custom',
        'name' => 'Custom Gateway',
        'enabled' => true,
    ];
    return $gateways;
});
```

---

## 📊 Transaction Management

### Melihat Transaksi

**Admin Panel:**
- Buka **SOFIR → Payments**
- Scroll ke bagian **Recent Transactions**
- Lihat 10 transaksi terakhir dengan status

**Status Badge:**
- ✅ **Completed** - Pembayaran berhasil
- ⏳ **Pending** - Menunggu pembayaran
- ❌ **Failed** - Pembayaran gagal/dibatalkan

**Via REST API:**
```
GET /wp-json/sofir/v1/payments/transactions
Auth: Admin (manage_options capability)
```

### Transaction Data Structure

```json
{
    "id": "TRX-123456-1234567890",
    "gateway": "duitku",
    "amount": 100000,
    "item_name": "Premium Package",
    "status": "completed",
    "user_id": 1,
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:35:00"
}
```

---

## 🔐 Security

✅ **Webhook Signature Validation** - Validasi otomatis dari gateway  
✅ **User Authentication** - Hanya user login yang bisa create payment  
✅ **Admin Only Transactions** - Hanya admin yang bisa lihat semua transaksi  
✅ **HTTPS Required** - Wajib HTTPS untuk production  
✅ **Sanitized Inputs** - Semua input di-sanitize dan escape  

---

## 🧪 Testing

### Sandbox Mode

1. **Duitku**: Gunakan sandbox credentials
2. **Xendit**: Gunakan API key development (`xnd_development_*`)
3. **Midtrans**: Centang "Sandbox Mode" di settings

### Test Cards (Midtrans Sandbox)

```
Success: 4811 1111 1111 1114
CVV: 123
Exp: 01/25
```

### Local Testing Webhook

Gunakan **ngrok** untuk expose localhost:

```bash
ngrok http 80
```

Gunakan URL ngrok sebagai webhook URL di dashboard gateway.

---

## 🐛 Troubleshooting

### Webhook tidak berfungsi?

✅ Pastikan URL webhook dapat diakses publik  
✅ Verifikasi webhook sudah dikonfigurasi di gateway  
✅ Cek WordPress debug log  
✅ Gunakan ngrok untuk local testing  

### Payment tidak redirect?

✅ Verifikasi API credentials benar  
✅ Pastikan gateway sudah enabled  
✅ User harus dalam kondisi logged in  
✅ Cek browser console untuk error  

### Manual payment tidak muncul?

✅ Pastikan "Enable Manual Payment" sudah dicentang  
✅ Clear WordPress cache  
✅ Periksa syntax shortcode  

---

## 📚 Dokumentasi Lengkap

Untuk dokumentasi lebih detail, lihat:

📖 **[Payment Module README](modules/payments/README.md)** - Quick reference  
📖 **[Payment Guide (Indonesian)](modules/payments/PAYMENT_GUIDE.md)** - Panduan lengkap  
📖 **[Payment Documentation (English)](modules/payments/PAYMENT_DOCUMENTATION.md)** - Full docs  

---

## 🎉 Summary

SOFIR menyediakan **integrasi payment gateway lengkap** dengan:

✅ 3 gateway Indonesia (Duitku, Xendit, Midtrans) + Manual  
✅ Admin UI yang user-friendly dengan toggle switches  
✅ Webhook support untuk status update otomatis  
✅ Transaction history dengan status tracking  
✅ Shortcode dan REST API untuk developer  
✅ Event hooks untuk custom logic  
✅ Security best practices  

**Mulai terima pembayaran sekarang!** 🚀
