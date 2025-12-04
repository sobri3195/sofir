# 🧪 Payment Test Mode - SOFIR Plugin

## Deskripsi
Test Mode memungkinkan Anda untuk **mencoba semua fitur payment gateway tanpa API key yang valid**. Sangat berguna untuk:
- Testing integrasi pembayaran sebelum mendaftar di payment gateway
- Development dan debugging
- Demo kepada client
- Quality assurance

## Fitur Test Mode

### ✅ Yang Bisa Dilakukan
- ✓ Membuat transaksi pembayaran simulasi
- ✓ Melihat flow pembayaran lengkap
- ✓ Testing webhook notification
- ✓ Melihat status transaksi di dashboard
- ✓ Simulasi sukses/gagal payment (90% sukses rate)
- ✓ Auto-complete dalam 10 detik
- ✓ Response data yang realistis

### ❌ Yang Tidak Bisa Dilakukan
- ✗ Transaksi uang asli
- ✗ Koneksi ke server payment gateway
- ✗ Verifikasi signature/token dari gateway
- ✗ Payment page asli dari gateway

## Cara Menggunakan

### 1. Aktifkan Test Mode
1. Buka **SOFIR Control Center → Payments**
2. Pilih payment gateway (Duitku/Xendit/Midtrans)
3. Centang **"🧪 Test Mode"**
4. Aktifkan gateway dengan toggle switch
5. Klik **Save Payment Settings**

### 2. Buat Test Payment
Gunakan shortcode di halaman atau post:
```
[sofir_payment_form amount="50000" item_name="Test Payment"]
```

### 3. Proses Pembayaran
1. Pilih payment gateway di form
2. Klik "Proceed to Payment"
3. Sistem akan generate data mock:
   - Duitku: VA Number, Reference, Bank Info
   - Xendit: Invoice ID, Invoice URL, QR Code
   - Midtrans: Snap Token, Payment Methods
4. Payment akan otomatis complete dalam 10 detik

### 4. Lihat Hasil
- Buka **SOFIR Control Center → Payments → Recent Transactions**
- Transaction akan muncul dengan badge **🧪 TEST**
- Status akan berubah menjadi "✅ Completed" atau "❌ Failed"

## Mock Response Examples

### Duitku Test Mode
```json
{
  "status": "success",
  "payment_method": "duitku",
  "test_mode": true,
  "reference": "MOCK-DUITKU-12345",
  "va_number": "8808123456789",
  "bank_info": {
    "bank": "BCA Virtual Account (Test)",
    "account_number": "8808123456789",
    "account_name": "SOFIR Payment Test"
  },
  "instructions": "🧪 TEST MODE - Payment akan auto-complete dalam 10 detik"
}
```

### Xendit Test Mode
```json
{
  "status": "success",
  "payment_method": "xendit",
  "test_mode": true,
  "invoice_id": "MOCK-XEN-54321",
  "invoice_url": "[admin_url]/admin.php?page=sofir-dashboard&tab=payments&test_payment=TRX-...",
  "payment_methods": ["Bank Transfer", "E-Wallet", "Credit Card", "Retail Outlets"],
  "qr_code": "https://placehold.co/300x300/667eea/ffffff?text=Xendit+QR+Test"
}
```

### Midtrans Test Mode
```json
{
  "status": "success",
  "payment_method": "midtrans",
  "test_mode": true,
  "snap_token": "MOCK-SNAP-98765",
  "payment_methods": ["Credit Card", "Bank Transfer", "GoPay", "ShopeePay", "Alfamart", "Indomaret"],
  "redirect_url": "[admin_url]/admin.php?page=sofir-dashboard&tab=payments&test_payment=TRX-..."
}
```

## Webhook Simulation

Test mode juga mensimulasikan webhook callback:

### Success Webhook (90% probability)
```php
// Duitku
do_action('sofir/payment/duitku_webhook', $transaction_id, '00', [
    'merchantOrderId' => $transaction_id,
    'resultCode' => '00',
    'reference' => 'MOCK-REF-123456',
    'test_mode' => true
]);

// Xendit
do_action('sofir/payment/xendit_webhook', $transaction_id, 'PAID', [
    'external_id' => $transaction_id,
    'status' => 'PAID',
    'id' => 'MOCK-INV-123456',
    'test_mode' => true
]);

// Midtrans
do_action('sofir/payment/midtrans_webhook', $transaction_id, 'settlement', [
    'order_id' => $transaction_id,
    'transaction_status' => 'settlement',
    'transaction_id' => 'MOCK-TRX-123456',
    'test_mode' => true
]);
```

### Failed Webhook (10% probability)
Payment akan di-mark sebagai "failed" untuk mensimulasikan error handling.

## Developer API

### Manual Trigger Test Payment
```javascript
// Trigger payment completion immediately (tanpa tunggu 10 detik)
fetch('/wp-json/sofir/v1/payments/test/trigger', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': wpApiSettings.nonce
  },
  body: JSON.stringify({
    transaction_id: 'TRX-123456-1234567890'
  })
});
```

### Check Transaction Status
```javascript
fetch('/wp-json/sofir/v1/payments/transactions', {
  headers: {
    'X-WP-Nonce': wpApiSettings.nonce
  }
})
.then(res => res.json())
.then(data => {
  // Filter test transactions
  const testTransactions = data.filter(t => t.test_mode === true);
  console.log('Test Transactions:', testTransactions);
});
```

## Hooks untuk Developer

### Filter Mock Response
```php
add_filter('sofir/payment/mock_response', function($response, $gateway, $transaction_id) {
    // Customize mock response
    $response['custom_field'] = 'custom_value';
    return $response;
}, 10, 3);
```

### After Mock Payment Processed
```php
add_action('sofir/payment/mock_processed', function($transaction_id, $gateway, $is_success) {
    if ($is_success) {
        // Do something on success
        error_log("Mock payment succeeded: {$transaction_id}");
    } else {
        // Handle failure
        error_log("Mock payment failed: {$transaction_id}");
    }
}, 10, 3);
```

## Testing Checklist

Gunakan checklist ini untuk memastikan semua berfungsi:

- [ ] Test Mode dapat diaktifkan untuk setiap gateway
- [ ] Payment form muncul dengan benar
- [ ] Gateway selection berfungsi
- [ ] Mock response berisi data yang benar
- [ ] Transaction tersimpan di database
- [ ] Badge "🧪 TEST" muncul di tabel transaksi
- [ ] Status berubah menjadi "completed" setelah 10 detik
- [ ] Webhook hook dipanggil dengan benar
- [ ] Transaction dapat dilihat di Recent Transactions
- [ ] Test mode badge terlihat jelas

## FAQ

### Q: Apakah test mode memerlukan API key?
**A:** Tidak. Test mode berjalan sepenuhnya tanpa API key. Anda bisa langsung aktifkan dan coba.

### Q: Berapa lama payment test akan complete?
**A:** Otomatis 10 detik setelah payment dibuat. Anda juga bisa trigger manual via REST API.

### Q: Apakah test transaction tersimpan di database?
**A:** Ya, tersimpan di `sofir_payment_transactions` option dengan flag `test_mode: true`.

### Q: Bagaimana cara membedakan test vs real transaction?
**A:** Test transaction memiliki:
- `test_mode: true` di data transaction
- Badge "🧪 TEST" di admin dashboard
- Reference/ID yang diawali dengan "MOCK-"

### Q: Apakah test mode aman untuk production?
**A:** Ya, aman. Test mode tidak akan pernah koneksi ke payment gateway atau memproses uang asli.

### Q: Bagaimana cara disable test mode?
**A:** Uncheck "🧪 Test Mode" di settings dan save. Setelah itu Anda perlu input API key asli.

## Troubleshooting

### Payment tidak auto-complete setelah 10 detik
- Pastikan WP-Cron berjalan (test dengan plugin WP Crontrol)
- Check error log WordPress
- Coba trigger manual via REST API

### Badge TEST tidak muncul
- Clear browser cache
- Refresh halaman admin
- Pastikan transaction memiliki `test_mode: true`

### Webhook tidak dipanggil
- Check apakah hook action terdaftar dengan benar
- Lihat WordPress debug log
- Pastikan priority hook tidak conflict

## Support

Jika ada pertanyaan atau menemukan bug:
1. Check WordPress debug log
2. Verify WP-Cron berjalan
3. Test dengan plugin lain di-disable
4. Report issue dengan detail lengkap

---

**Version:** 1.0.0  
**Last Updated:** 2024  
**Author:** SOFIR Plugin Team
