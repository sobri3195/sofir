# SOFIR - Bit Integration Plugin Guide

## Daftar Isi
- [Pengenalan](#pengenalan)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Trigger yang Tersedia](#trigger-yang-tersedia)
- [Action yang Tersedia](#action-yang-tersedia)
- [Contoh Penggunaan](#contoh-penggunaan)
- [Troubleshooting](#troubleshooting)

## Pengenalan

SOFIR terintegrasi penuh dengan plugin **Bit Integration** untuk memberikan kemampuan otomasi workflow yang canggih. Dengan integrasi ini, Anda dapat menghubungkan event dari SOFIR dengan berbagai aplikasi eksternal seperti:

- Email marketing (Mailchimp, ActiveCampaign, dll)
- CRM (HubSpot, Salesforce, dll)
- Messaging (Slack, Telegram, WhatsApp)
- Spreadsheet (Google Sheets, Airtable)
- Dan 200+ layanan lainnya

## Instalasi

### 1. Install Plugin yang Diperlukan

```bash
# Install SOFIR Plugin (sudah ada)
# Install Bit Integration Plugin
```

1. Download plugin **Bit Integration** dari WordPress.org atau [bit-integrations.com](https://www.bit-integrations.com)
2. Upload dan aktifkan plugin melalui WordPress Admin → Plugins
3. Pastikan kedua plugin (SOFIR & Bit Integration) aktif

### 2. Verifikasi Integrasi

1. Buka **Bit Integration → Integrations**
2. Klik **Create Integration**
3. Di bagian **Trigger**, cari "SOFIR"
4. Jika SOFIR muncul dalam daftar, integrasi berhasil!

## Konfigurasi

### Membuat Integration Baru

1. **Buka Bit Integration**
   - Dashboard WordPress → Bit Integration → Create Integration

2. **Pilih Trigger (SOFIR Event)**
   - Pilih "SOFIR" sebagai trigger source
   - Pilih event yang ingin Anda gunakan (misal: "User Registered")

3. **Pilih Action (Tujuan)**
   - Pilih aplikasi tujuan (misal: Mailchimp, Google Sheets)
   - Konfigurasi koneksi ke aplikasi tersebut

4. **Map Fields**
   - Hubungkan data dari SOFIR ke field di aplikasi tujuan
   - Gunakan dropdown untuk memilih field yang tersedia

5. **Save & Activate**
   - Simpan integration
   - Toggle untuk mengaktifkan

## Trigger yang Tersedia

### 1. User Registered
**Event:** `user_register`  
**Deskripsi:** Dipicu ketika user baru mendaftar

**Data Fields:**
- `user_id` - ID pengguna
- `user_login` - Username
- `user_email` - Email pengguna
- `display_name` - Nama tampilan
- `user_roles` - Role pengguna (comma-separated)
- `first_name` - Nama depan
- `last_name` - Nama belakang
- `phone` - Nomor telepon

**Contoh Use Case:**
- Kirim email welcome otomatis via Mailchimp
- Tambahkan user baru ke Google Sheets
- Notifikasi ke Slack channel

---

### 2. User Profile Updated
**Event:** `user_update`  
**Deskripsi:** Dipicu ketika user mengupdate profile mereka

**Data Fields:**
- `user_id` - ID pengguna
- `user_login` - Username
- `user_email` - Email pengguna
- `display_name` - Nama tampilan
- `first_name` - Nama depan
- `last_name` - Nama belakang

**Contoh Use Case:**
- Update data user di CRM (HubSpot, Salesforce)
- Sinkronisasi ke mailing list
- Log perubahan ke database eksternal

---

### 3. User Logged In
**Event:** `user_login`  
**Deskripsi:** Dipicu ketika user login ke website

**Data Fields:**
- `user_id` - ID pengguna
- `user_login` - Username
- `user_email` - Email pengguna
- `login_time` - Waktu login (MySQL datetime)

**Contoh Use Case:**
- Track login activity ke Google Analytics
- Kirim notifikasi login ke Telegram
- Update last seen di CRM

---

### 4. Payment Completed
**Event:** `payment_completed`  
**Deskripsi:** Dipicu ketika pembayaran berhasil diselesaikan

**Data Fields:**
- `transaction_id` - ID transaksi unik
- `gateway` - Payment gateway (manual/duitku/xendit/midtrans)
- `amount` - Jumlah pembayaran
- `item_name` - Nama produk/layanan
- `user_id` - ID pembeli
- `status` - Status pembayaran (completed)

**Contoh Use Case:**
- Kirim invoice via email
- Tambahkan transaksi ke spreadsheet
- Notifikasi ke Slack untuk setiap penjualan
- Trigger fulfillment process

---

### 5. Form Submitted
**Event:** `form_submission`  
**Deskripsi:** Dipicu ketika form disubmit

**Data Fields:**
- `form_id` - ID form
- `form_data` - Data form (JSON object)

**Contoh Use Case:**
- Kirim data ke CRM
- Simpan ke Google Sheets
- Kirim notifikasi ke admin

---

### 6. Post Published
**Event:** `post_publish`  
**Deskripsi:** Dipicu ketika post dipublish

**Data Fields:**
- `post_id` - ID post
- `post_title` - Judul post
- `post_type` - Tipe post
- `post_author` - ID penulis
- `permalink` - URL post

**Contoh Use Case:**
- Share otomatis ke social media
- Notifikasi ke subscriber
- Trigger newsletter

---

### 7. Comment Posted
**Event:** `comment_post`  
**Deskripsi:** Dipicu ketika komentar baru diposting

**Data Fields:**
- `comment_id` - ID komentar
- `post_id` - ID post yang dikomentari
- `comment_author` - Nama penulis komentar
- `comment_author_email` - Email penulis
- `comment_content` - Isi komentar

**Contoh Use Case:**
- Moderasi otomatis dengan AI
- Notifikasi ke admin/author
- Simpan ke database eksternal

---

### 8. Membership Changed
**Event:** `membership_changed`  
**Deskripsi:** Dipicu ketika membership user berubah

**Data Fields:**
- `user_id` - ID pengguna
- `old_plan` - Plan sebelumnya
- `new_plan` - Plan baru

**Contoh Use Case:**
- Update role di Discord/Slack
- Kirim email konfirmasi upgrade
- Trigger access ke konten premium

---

### 9. Appointment Created
**Event:** `appointment_created`  
**Deskripsi:** Dipicu ketika appointment baru dibuat

**Data Fields:**
- `appointment_id` - ID appointment
- `appointment_datetime` - Tanggal & waktu appointment
- `appointment_duration` - Durasi (menit)
- `appointment_status` - Status (pending/confirmed/cancelled)
- `appointment_provider` - Provider/staff
- `appointment_client` - Client/customer

**Contoh Use Case:**
- Kirim reminder email/SMS
- Tambahkan ke Google Calendar
- Notifikasi ke staff

---

### 10. Appointment Updated
**Event:** `appointment_updated`  
**Deskripsi:** Dipicu ketika appointment diupdate

**Data Fields:**
- `appointment_id` - ID appointment
- `appointment_datetime` - Tanggal & waktu baru
- `appointment_status` - Status baru
- `old_status` - Status sebelumnya

**Contoh Use Case:**
- Kirim notifikasi perubahan
- Update calendar eksternal
- Log changes

---

## Action yang Tersedia

SOFIR juga menyediakan action yang dapat dipanggil dari aplikasi lain:

### 1. Create User
**Action:** `sofir_create_user`  
**Required Fields:**
- `user_login` - Username
- `user_email` - Email

**Optional Fields:**
- `user_pass` - Password (auto-generated jika kosong)
- `display_name` - Nama tampilan
- `first_name` - Nama depan
- `last_name` - Nama belakang
- `phone` - Nomor telepon

---

### 2. Update User
**Action:** `sofir_update_user`  
**Required Fields:**
- `user_id` - ID user yang akan diupdate

**Optional Fields:**
- `user_email` - Email baru
- `display_name` - Nama tampilan
- `first_name` - Nama depan
- `last_name` - Nama belakang

---

### 3. Create Post
**Action:** `sofir_create_post`  
**Required Fields:**
- `post_title` - Judul post

**Optional Fields:**
- `post_content` - Konten post
- `post_type` - Tipe post (default: post)
- `post_status` - Status (default: draft)

---

## Contoh Penggunaan

### Contoh 1: Email Welcome untuk User Baru

**Setup:**
1. Trigger: SOFIR → User Registered
2. Action: Mailchimp → Subscribe to List
3. Field Mapping:
   - `user_email` → Mailchimp Email
   - `first_name` → Mailchimp First Name
   - `last_name` → Mailchimp Last Name

**Hasil:** Setiap user baru otomatis ditambahkan ke mailing list Mailchimp

---

### Contoh 2: Notifikasi Payment ke Slack

**Setup:**
1. Trigger: SOFIR → Payment Completed
2. Action: Slack → Send Message
3. Message Template:
```
🎉 Pembayaran Baru!
- Jumlah: {{amount}}
- Item: {{item_name}}
- Gateway: {{gateway}}
- Transaction ID: {{transaction_id}}
```

**Hasil:** Setiap pembayaran sukses, team dapat notifikasi real-time di Slack

---

### Contoh 3: Simpan Payment ke Google Sheets

**Setup:**
1. Trigger: SOFIR → Payment Completed
2. Action: Google Sheets → Add Row
3. Field Mapping:
   - `transaction_id` → Column A
   - `gateway` → Column B
   - `amount` → Column C
   - `item_name` → Column D
   - `user_id` → Column E

**Hasil:** Database payment otomatis di Google Sheets untuk analisis

---

### Contoh 4: Auto-Create User dari Form External

**Setup:**
1. Trigger: Google Forms → Form Submitted
2. Action: SOFIR → Create User
3. Field Mapping:
   - Form Email → `user_email`
   - Form Name → `display_name`
   - Form Phone → `phone`

**Hasil:** User WordPress otomatis terbuat dari form eksternal

---

## Troubleshooting

### Integration Tidak Muncul

**Problem:** SOFIR tidak muncul di list trigger Bit Integration

**Solusi:**
1. Pastikan kedua plugin aktif
2. Refresh cache WordPress (Clear Cache + Logout/Login)
3. Cek PHP error log untuk pesan error
4. Update kedua plugin ke versi terbaru

---

### Trigger Tidak Berjalan

**Problem:** Event terjadi tapi action tidak ter-trigger

**Solusi:**
1. Cek status integration (harus Active)
2. Test trigger manually dari Bit Integration
3. Cek Bit Integration logs:
   - Dashboard → Bit Integration → Integration Logs
4. Pastikan field mapping sudah benar
5. Verifikasi API credentials aplikasi tujuan

---

### Data Tidak Lengkap

**Problem:** Beberapa field kosong saat di-map

**Solusi:**
1. Pastikan data tersebut ada di SOFIR
   - User: Cek profile user
   - Payment: Cek transaction details
2. Test dengan data sample terlebih dahulu
3. Gunakan conditional logic untuk handle empty fields

---

### Rate Limiting

**Problem:** Integration gagal karena terlalu banyak request

**Solusi:**
1. Gunakan delay di Bit Integration settings
2. Batch process untuk high-volume triggers
3. Cek rate limit API aplikasi tujuan

---

## Custom Development

### Menambahkan Custom Trigger

Anda bisa menambahkan trigger custom menggunakan hook:

```php
// Di theme functions.php atau plugin custom
do_action( 'btcbi_trigger_execute', 'sofir', 'custom_event', $data );
```

**Contoh:**
```php
add_action( 'woocommerce_order_status_completed', function( $order_id ) {
    $order = wc_get_order( $order_id );
    
    $data = [
        'order_id' => $order_id,
        'total' => $order->get_total(),
        'customer_email' => $order->get_billing_email(),
    ];
    
    do_action( 'btcbi_trigger_execute', 'sofir', 'wc_order_completed', $data );
});
```

---

### Menambahkan Custom Field

Filter untuk menambahkan field ke existing trigger:

```php
add_filter( 'btcbi_trigger', function( $triggers ) {
    if ( isset( $triggers['sofir']['triggers']['user_register']['fields'] ) ) {
        $triggers['sofir']['triggers']['user_register']['fields'][] = [
            'key' => 'custom_field',
            'label' => 'Custom Field',
            'required' => false,
        ];
    }
    return $triggers;
});
```

---

## Support

Jika Anda memerlukan bantuan lebih lanjut:

1. **SOFIR Documentation**: Lihat file README.md
2. **Bit Integration Docs**: [bit-integrations.com/docs](https://www.bit-integrations.com/docs)
3. **WordPress Support Forum**: Posting pertanyaan Anda
4. **GitHub Issues**: Laporkan bug atau request feature

---

## Changelog

### Version 0.1.0
- ✅ Initial release
- ✅ 10 triggers tersedia
- ✅ 3 actions tersedia
- ✅ Full Bit Integration compatibility
- ✅ Support untuk semua payment gateways (Manual, Duitku, Xendit, Midtrans)
- ✅ Support untuk appointment system
- ✅ Support untuk membership system

---

## Lisensi

Integrasi ini mengikuti lisensi plugin SOFIR (GPL v2 or later).
