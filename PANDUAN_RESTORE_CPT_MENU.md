# Panduan Mengembalikan Menu CPT yang Hilang

## 🎯 Tujuan

Panduan ini akan membantu Anda mengembalikan menu CPT (Custom Post Type) yang hilang dari sidebar admin WordPress, seperti:
- 📍 **Listings** - Direktori bisnis/lokasi
- 👤 **Profiles** - Profil pengguna
- 📰 **Articles** - Artikel/berita
- 📅 **Events** - Manajemen event
- ⏰ **Appointments** - Booking appointment

## ❓ Mengapa Menu Hilang?

Menu CPT bisa hilang karena:
1. Tidak sengaja terhapus melalui admin panel SOFIR
2. Data plugin corrupt atau terhapus
3. Error saat instalasi pertama kali

## ✨ Solusi: Fitur Restore Default CPTs

Plugin SOFIR sekarang dilengkapi dengan tombol **"Restore Default CPTs"** yang bisa mengembalikan semua CPT default dengan 1 klik!

## 📋 Langkah-Langkah Restore

### 1. Buka SOFIR Control Center
```
Dashboard WordPress → SOFIR → Content
```

### 2. Cari Tombol Restore
- Scroll ke bawah ke section **"Registered Post Types"**
- Anda akan melihat tombol **"🔄 Restore Default CPTs"** dengan background abu-abu
- Di bawah tombol ada penjelasan:
  ```
  Mengembalikan 5 CPT default: listing, profile, article, event, 
  dan appointment beserta taxonomies-nya.
  ```

### 3. Klik Tombol Restore
- Klik tombol **"🔄 Restore Default CPTs"**
- Akan muncul konfirmasi dialog:
  ```
  Apakah Anda yakin ingin mengembalikan CPT default 
  (listing, profile, article, event, appointment)? 
  CPT yang sudah ada tidak akan dihapus.
  ```

### 4. Konfirmasi
- Klik **OK** untuk melanjutkan
- Tunggu beberapa detik hingga proses selesai

### 5. Selesai!
- Anda akan melihat pesan sukses:
  ```
  Default CPTs and taxonomies have been restored successfully. 
  Please refresh the page to see the menu items.
  ```
- **Refresh halaman** (tekan F5 atau Ctrl+R)
- Menu CPT sudah muncul di sidebar!

## 🎉 Menu yang Akan Muncul

Setelah restore berhasil, Anda akan melihat menu baru di sidebar admin:

### 1. 📍 Listings
- **Icon:** Location pin
- **Menu:** Add New, Categories, Locations
- **Fields:** Location, Hours, Rating, Status, Price, Contact, Gallery
- **Filters:** Location search, Rating filter, Status, Price, Open now

### 2. 👤 Profiles  
- **Icon:** ID card
- **Menu:** Add New, Categories
- **Fields:** Location, Contact, Status, Custom attributes
- **Filters:** Location search, Status

### 3. 📰 Articles
- **Icon:** Document
- **Menu:** Add New, All Articles
- **Fields:** Title, Editor, Thumbnail, Author, Comments
- **Filters:** Custom attributes

### 4. 📅 Events
- **Icon:** Calendar
- **Menu:** Add New, Categories, Tags
- **Fields:** Event Date, Capacity, Location, Contact, Gallery, Status
- **Filters:** Date range, Capacity, Location, Status

### 5. ⏰ Appointments
- **Icon:** Clock
- **Menu:** Add New, Services
- **Fields:** Date/Time, Duration, Status, Provider, Client, Contact
- **Filters:** Date range, Status, Provider, Client

## 📊 Taxonomies yang Di-restore

Setiap CPT dilengkapi dengan taxonomy untuk kategorisasi:

| CPT | Taxonomies | Type |
|-----|------------|------|
| Listing | Listing Category, Listing Location | Hierarchical, Flat |
| Profile | Profile Category | Flat |
| Article | - | - |
| Event | Event Category, Event Tag | Hierarchical, Flat |
| Appointment | Service | Hierarchical |

## ⚠️ Penting untuk Diketahui

### ✅ Aman & Non-Destructive
- Restore **TIDAK** akan menghapus CPT yang sudah ada
- Hanya menambahkan CPT default yang hilang
- Data/post yang sudah ada **TETAP AMAN**

### 🔄 Rewrite Rules
- Plugin otomatis flush rewrite rules setelah restore
- Permalink akan langsung berfungsi
- Tidak perlu setting ulang permalink manual

### 👥 Hak Akses
- Hanya user dengan role **Administrator** yang bisa restore
- User lain tidak akan melihat tombol restore

## 🐛 Troubleshooting

### Menu Masih Belum Muncul?

**Solusi 1: Refresh Browser**
```
Tekan Ctrl + R (Windows/Linux)
Tekan Cmd + R (Mac)
```

**Solusi 2: Clear Cache**
```
1. Buka DevTools (F12)
2. Klik kanan tombol refresh
3. Pilih "Empty Cache and Hard Reload"
```

**Solusi 3: Cek Role User**
```
1. Dashboard → Users → Your Profile
2. Pastikan Role = Administrator
```

**Solusi 4: Manual Flush Rewrite (via WP CLI)**
```bash
wp rewrite flush
```

### Permalink 404 Error?

Jika setelah restore permalink mengarah ke 404:
```bash
# Via WP CLI
wp rewrite flush --hard

# Via Admin Panel
Settings → Permalinks → Save Changes
```

### Error "Nonce tidak valid"?

- Refresh halaman dan coba lagi
- Pastikan tidak ada duplikasi tab admin
- Clear cookies browser

### Verifikasi CPT Registered

**Via Admin Panel:**
```
SOFIR → Content → Registered Post Types
```

**Via WP CLI:**
```bash
wp post-type list --format=table
```

**Via Code:**
```php
$cpts = get_post_types(['public' => true], 'names');
print_r($cpts);
```

## 📝 FAQ

### Q: Apakah data saya akan hilang?
**A:** TIDAK. Restore hanya mengembalikan definisi CPT, tidak menghapus data.

### Q: Apakah CPT custom saya akan terhapus?
**A:** TIDAK. Restore hanya menambahkan CPT default, CPT custom tetap aman.

### Q: Berapa lama proses restore?
**A:** Biasanya hanya 2-3 detik.

### Q: Apakah perlu setting ulang setelah restore?
**A:** TIDAK. Semua setting sudah otomatis ter-konfigurasi.

### Q: Bisa restore sebagian saja?
**A:** Saat ini belum support. Restore akan mengembalikan semua 5 CPT default.

### Q: Apakah bisa restore berkali-kali?
**A:** BISA. Restore bersifat idempotent (aman dijalankan berkali-kali).

## 🔧 Developer: Manual Restore via Code

Jika Anda developer dan ingin restore via code:

```php
// Restore CPTs
$manager = \Sofir\Cpt\Manager::instance();
$manager->restore_default_post_types();
$manager->restore_default_taxonomies();

// Flush rewrite rules
flush_rewrite_rules();

// Success!
echo 'CPTs restored successfully!';
```

## 📞 Butuh Bantuan?

Jika masalah masih berlanjut:
1. Aktifkan WP_DEBUG di wp-config.php
2. Cek file wp-content/debug.log
3. Screenshot error message
4. Hubungi support SOFIR dengan informasi:
   - WordPress version
   - PHP version
   - SOFIR plugin version
   - Error message/screenshot
   - User role

## 🎓 Tips & Best Practices

1. **Backup Dulu:** Selalu backup database sebelum restore
2. **Test di Staging:** Jika ada, test restore di staging dulu
3. **Dokumentasi:** Catat CPT custom yang Anda buat
4. **Regular Check:** Periksa menu CPT secara berkala
5. **Update Plugin:** Pastikan SOFIR selalu update ke versi terbaru

## ✨ Kesimpulan

Fitur **Restore Default CPTs** adalah solusi cepat dan aman untuk mengembalikan menu CPT yang hilang. Proses restore:
- ✅ Cepat (2-3 detik)
- ✅ Aman (non-destructive)
- ✅ Lengkap (5 CPT + taxonomies)
- ✅ Otomatis (tidak perlu konfigurasi)

Selamat menggunakan SOFIR! 🚀
