# CPT Menu & Multi-Vendor Pages Fix

## Masalah yang Diperbaiki

### 1. Menu CPT Tidak Tampil
Menu untuk Custom Post Types bawaan (Listing, Profile, Article, Event, Appointment) tidak tampil di sidebar admin WordPress.

**Penyebab:**
- Setting `show_in_menu` tidak eksplisit di seed post types
- Definisi yang tersimpan di database mungkin tidak memiliki `show_in_menu => true`

**Solusi:**
- Menambahkan `show_in_menu => true` secara eksplisit di semua seed post types
- Membuat sistem version check untuk auto-update definisi yang sudah tersimpan
- Menambahkan tab "Tools" di SOFIR Control Center untuk manual refresh

### 2. Halaman Vendor Store & Vendor Product Tidak Tampil
Halaman single dan archive untuk vendor_store dan vendor_product tidak tampil di frontend.

**Penyebab:**
- Rewrite rules mungkin belum di-flush dengan benar
- One-time flush tidak cukup untuk beberapa kasus

**Solusi:**
- Mengubah flush rewrite rules menjadi version-based system
- Menambahkan admin notice dengan tombol untuk pergi ke Permalinks
- Auto-flush pada update version

## File yang Dimodifikasi

### 1. `/includes/sofir-cpt-manager.php`
**Perubahan:**
- ✅ Menambahkan `show_in_menu => true` di seed post types (listing, profile, article, event, appointment)
- ✅ Menambahkan method `check_and_update_definitions()` untuk version-based update
- ✅ Menambahkan hook pada `init` untuk auto-update

**Kode yang ditambahkan:**
```php
'show_in_menu' => true, // Di setiap seed post type

public function check_and_update_definitions(): void {
    $version = \get_option( 'sofir_cpt_definitions_version', '0' );
    $current_version = '1.0.2';

    if ( $version !== $current_version ) {
        // Update show_in_menu untuk semua CPT seed
        // Flush rewrite rules
        // Update version
    }
}
```

### 2. `/modules/multivendor/manager.php`
**Perubahan:**
- ✅ Mengubah `flush_rewrite_rules_on_first_activation()` menjadi `flush_rewrite_rules_on_activation()`
- ✅ Menambahkan version-based flush system
- ✅ Menambahkan `show_flush_rewrite_notice()` untuk admin notice
- ✅ Menambahkan hook `admin_notices`

**Kode yang ditambahkan:**
```php
public function flush_rewrite_rules_on_activation(): void {
    $version = \get_option( 'sofir_multivendor_rewrite_version', '0' );
    $current_version = '1.0.1';

    if ( $version !== $current_version ) {
        \flush_rewrite_rules();
        \update_option( 'sofir_multivendor_rewrite_version', $current_version );
        \delete_option( 'sofir_multivendor_flush_notice_dismissed' );
    }
}

public function show_flush_rewrite_notice(): void {
    // Tampilkan notice dengan link ke Permalinks
    // Bisa di-dismiss oleh user
}
```

### 3. `/includes/class-admin-manager.php`
**Perubahan:**
- ✅ Menambahkan tab "Tools" di SOFIR Control Center
- ✅ Menambahkan `render_tools_tab()` dengan form refresh CPT
- ✅ Menambahkan action handler untuk refresh

**Fitur Tools Tab:**
- Form untuk force refresh CPT definitions
- Tombol untuk pergi ke Permalinks
- Penjelasan lengkap tentang apa yang akan dilakukan
- Success notice setelah refresh

### 4. `/includes/sofir-cpt-force-refresh.php` (NEW)
**File baru:**
- Helper function untuk force refresh CPT definitions
- Bisa digunakan programmatically jika diperlukan

## Cara Penggunaan

### Metode 1: Otomatis (Recommended)
Plugin akan otomatis mendeteksi dan memperbarui definisi CPT saat:
- Plugin di-activate
- User membuka halaman admin WordPress
- Version berubah dari yang tersimpan di database

### Metode 2: Manual via Tools Tab
1. Buka **SOFIR → Tools**
2. Klik tombol **"Refresh CPT Definitions"**
3. Menu CPT akan langsung tampil di sidebar

### Metode 3: Via Permalinks
1. Buka **Settings → Permalinks**
2. Klik **"Save Changes"** (tanpa perubahan apapun)
3. Rewrite rules akan di-flush

## Testing

### Test CPT Menu
```bash
# Buka admin WordPress
# Cek sidebar untuk menu:
- Listings (icon: location-alt)
- Profiles (icon: id)
- Articles (icon: media-document)
- Events (icon: calendar)
- Appointments (icon: clock)
```

### Test Vendor Pages
```bash
# Buka URL berikut:
/vendors/          # Archive vendor_store
/products/         # Archive vendor_product
/vendors/test/     # Single vendor_store
/products/test/    # Single vendor_product
```

## Database Options

Plugin menggunakan options berikut untuk tracking:
- `sofir_cpt_definitions_version` - Version untuk CPT definitions
- `sofir_multivendor_rewrite_version` - Version untuk multivendor rewrite
- `sofir_multivendor_flush_notice_dismissed` - Status dismiss notice

## Rollback

Jika ada masalah, rollback bisa dilakukan dengan:
```php
delete_option( 'sofir_cpt_definitions_version' );
delete_option( 'sofir_multivendor_rewrite_version' );
flush_rewrite_rules();
```

## FAQ

**Q: Menu CPT masih tidak tampil setelah refresh?**
A: Coba clear browser cache dan reload halaman admin. Jika masih tidak tampil, buka Settings → Permalinks dan klik Save Changes.

**Q: Halaman vendor menampilkan 404?**
A: Pastikan sudah flush rewrite rules via Tools tab atau Permalinks. Jika masih 404, cek apakah ada conflict dengan plugin lain.

**Q: Apakah aman untuk refresh berkali-kali?**
A: Ya, sangat aman. Refresh hanya memperbarui setting di database dan flush rewrite rules.

**Q: Apakah data akan hilang?**
A: Tidak. Refresh hanya memperbarui definisi CPT, tidak menghapus data post yang sudah ada.

## Version History

- **1.0.2** (Current) - Fix CPT menu dengan version-based update
- **1.0.1** - Fix vendor pages dengan improved flush system
- **1.0.0** - Initial release
