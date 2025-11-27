# CPT Menu Visibility Fix - Version 1.0.4

## Masalah yang Diperbaiki

Menu Custom Post Type (CPT) tidak muncul di admin sidebar setelah:
- Install dari Library tab (ready-to-use templates)
- Import dari file JSON/ZIP
- Upgrade plugin dari versi sebelumnya

## Solusi yang Diterapkan

### 1. **Auto-Fix pada Init Hook**
File: `includes/sofir-cpt-manager.php`

Fungsi `check_and_update_definitions()` sekarang:
- Berjalan otomatis saat `init` hook (priority 0)
- Memeriksa SEMUA CPT (bukan hanya seed CPTs)
- Memastikan setiap CPT memiliki:
  - `show_in_menu` = true
  - `show_ui` = true
  - `show_in_nav_menus` = true
- Version tracking: `1.0.4`

### 2. **Fix saat Plugin Activation**
File: `includes/sofir-bootstrap-lifecycle.php`

Saat plugin diaktivasi:
- Reset version tracking dengan `delete_option('sofir_cpt_definitions_version')`
- Trigger auto-fix saat init hook berikutnya
- Flush rewrite rules

### 3. **Fix saat Import/Install**
File: `includes/class-admin-library-panel.php`

Fungsi `ensure_cpt_menus_visible()`:
- Dipanggil setelah import CPT dari file
- Dipanggil setelah install ready-to-use template
- Memeriksa dan memperbaiki semua CPT
- Update version ke `1.0.4`

### 4. **Manual Fix via Tools Tab**
File: `includes/class-admin-manager.php`

SOFIR → Tools → Refresh CPT Definitions:
- Fix manual untuk user
- Reset version tracking
- Flush rewrite rules
- Clear notices

## Cara Kerja Version System

```php
// Di init hook (priority 0):
$current_version = '1.0.4';
$saved_version = get_option('sofir_cpt_definitions_version', '0');

if ($saved_version !== $current_version) {
    // Fix SEMUA CPT
    foreach ($this->post_types as $slug => $definition) {
        // Pastikan show_in_menu, show_ui, show_in_nav_menus = true
    }
    update_option('sofir_cpt_definitions_version', $current_version);
}
```

## Perbaikan dari Versi Sebelumnya

### Versi 1.0.2 (Masalah)
- Hanya fix seed CPTs
- CPT dari Library tidak diperbaiki
- Version tracking tidak konsisten

### Versi 1.0.3 (Parsial)
- Masih hanya fix seed CPTs
- Version tidak diupdate di semua tempat

### Versi 1.0.4 (Lengkap) ✅
- Fix SEMUA CPT (seed + library + custom)
- Version konsisten di semua file
- Triple protection:
  1. Auto-fix saat init
  2. Fix saat activation
  3. Fix saat import/install
  4. Manual fix via Tools

## Cara Menggunakan

### Otomatis (Recommended)
Tidak perlu lakukan apa-apa! Fix akan berjalan otomatis saat:
- Plugin diaktivasi
- Setiap kali halaman admin dimuat
- Install template dari Library
- Import dari file

### Manual (Jika Diperlukan)
1. Pergi ke **SOFIR → Tools**
2. Klik **"Refresh CPT Definitions"**
3. Menu CPT akan muncul di sidebar admin

### Backup Plan
Jika masih tidak muncul:
1. Pergi ke **Settings → Permalinks**
2. Klik **"Save Changes"** (tanpa mengubah apa-apa)
3. Refresh halaman admin

## Files yang Diubah

1. `includes/sofir-cpt-manager.php` - Logic auto-fix untuk SEMUA CPT
2. `includes/class-admin-library-panel.php` - Version update ke 1.0.4
3. `includes/sofir-bootstrap-lifecycle.php` - Reset version saat activation
4. `includes/class-admin-manager.php` - Tools tab (sudah ada)

## Testing

### Test Case 1: Install Ready Template
1. SOFIR → Library
2. Install template "Business Directory"
3. ✅ Menu "Listings" muncul di sidebar

### Test Case 2: Import dari File
1. Export CPT dari website A
2. Import di website B
3. ✅ Menu CPT muncul di sidebar

### Test Case 3: Manual Refresh
1. SOFIR → Tools
2. Click "Refresh CPT Definitions"
3. ✅ Success notice + menu muncul

### Test Case 4: Plugin Activation
1. Deactivate plugin
2. Activate plugin
3. ✅ Menu CPT muncul di sidebar

## Catatan Developer

- Version system menggunakan option `sofir_cpt_definitions_version`
- Current version: `1.0.4`
- Priority init hook: 0 (sebelum register_post_type)
- Tidak perlu manual flush rewrite rules (otomatis)
- Compatible dengan multi-site
- Tidak mengubah data yang sudah ada, hanya menambahkan setting yang kurang

## Troubleshooting

**Q: Menu masih tidak muncul setelah fix?**
A: Pergi ke Settings → Permalinks → Save Changes

**Q: Menu hilang setelah update plugin?**
A: Version tracking otomatis mendeteksi dan fix

**Q: Cara force refresh manual?**
A: SOFIR → Tools → Refresh CPT Definitions

**Q: Apakah aman untuk site production?**
A: Ya! Fix hanya menambahkan setting, tidak mengubah/menghapus data
