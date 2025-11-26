# CPT Menu Visibility Fix - Summary

## Problem
Menu CPT tidak muncul di WordPress admin setelah diinstall dari Library SOFIR. Hal ini terjadi karena CPT yang disimpan melalui `save_post_type()` tidak menyertakan setting `show_in_menu`, `show_ui`, dan `show_in_nav_menus`.

## Root Cause
1. Method `save_post_type()` di `sofir-cpt-manager.php` tidak menyimpan setting visibility menu
2. Saat CPT diinstall dari Library, hanya field yang didefinisikan di template yang disimpan
3. Default settings di `register_dynamic_post_types()` hanya diterapkan saat registrasi, bukan saat save
4. `check_and_update_definitions()` hanya memperbaiki seed CPTs (listing, profile, article, event, appointment), tidak CPTs dari library

## Solution Implemented

### 1. Fix `save_post_type()` Method
**File:** `/home/engine/project/includes/sofir-cpt-manager.php`

Menambahkan setting visibility ke args array:
```php
'show_ui'           => true,
'show_in_menu'      => true,
'show_in_nav_menus' => true,
```

Sekarang semua CPT yang disimpan akan otomatis memiliki menu visibility yang benar.

### 2. Add Menu Fix on Library Installation
**File:** `/home/engine/project/includes/class-admin-library-panel.php`

**Changes:**
- Menambahkan method `ensure_cpt_menus_visible()` untuk memastikan semua CPT memiliki setting menu yang benar
- Menambahkan method `convert_definition_to_payload()` untuk mengkonversi definition ke payload format
- Memanggil `ensure_cpt_menus_visible()` setelah install template dari library
- Memanggil `ensure_cpt_menus_visible()` setelah import CPT package

**Benefit:** CPT yang baru diinstall dari library atau diimport akan langsung memiliki menu yang visible.

### 3. Enhance Tools Tab Refresh Function
**File:** `/home/engine/project/includes/class-admin-manager.php`

**Changes:**
- Menambahkan method `fix_cpt_menus()` untuk memperbaiki semua CPT yang ada
- Menambahkan method `convert_definition_to_payload()` untuk konversi data
- Memanggil `fix_cpt_menus()` saat user klik "Refresh CPT Definitions"
- Update deskripsi tool untuk lebih jelas tentang fungsi fix menu

**Benefit:** User yang sudah punya CPT terinstall tanpa menu bisa menggunakan Tools → Refresh CPT Definitions untuk memperbaikinya.

## How It Works

### For New Installations
1. User install template dari Library tab
2. `save_post_type()` dipanggil dengan setting baru yang include `show_in_menu: true`
3. `ensure_cpt_menus_visible()` memverifikasi semua CPT
4. Menu langsung muncul di WordPress admin sidebar

### For Existing Installations
1. User pergi ke SOFIR → Tools tab
2. Klik "Refresh CPT Definitions"
3. `fix_cpt_menus()` akan:
   - Loop semua CPT yang terdaftar
   - Check apakah `show_in_menu`, `show_ui`, `show_in_nav_menus` ada dan true
   - Jika tidak, update CPT dengan setting yang benar
4. Flush rewrite rules
5. Menu CPT akan muncul

## Testing Scenarios

### Scenario 1: Install New Template from Library
✅ Install Business Directory dari Library  
✅ Menu "Listing" muncul di sidebar  
✅ CPT bisa diakses dan digunakan  

### Scenario 2: Import CPT Package
✅ Import file ZIP/JSON dari export  
✅ Menu CPT yang diimport muncul  
✅ Data dan settings ter-restore dengan benar  

### Scenario 3: Fix Existing CPT Without Menu
✅ CPT sudah terinstall tapi menu tidak muncul  
✅ Pergi ke SOFIR → Tools  
✅ Klik "Refresh CPT Definitions"  
✅ Menu CPT muncul setelah refresh  

## Files Modified

1. `/home/engine/project/includes/sofir-cpt-manager.php`
   - Line 667-681: Updated `save_post_type()` args array

2. `/home/engine/project/includes/class-admin-library-panel.php`
   - Line 296: Added `ensure_cpt_menus_visible()` call after template install
   - Line 214: Added `ensure_cpt_menus_visible()` call after import
   - Line 314-332: Added `ensure_cpt_menus_visible()` method
   - Line 334-370: Added `convert_definition_to_payload()` method

3. `/home/engine/project/includes/class-admin-manager.php`
   - Line 169: Added `fix_cpt_menus()` call in refresh handler
   - Line 184: Updated tool description
   - Line 198: Updated list item to mention "SEMUA CPT"
   - Line 244-271: Added `fix_cpt_menus()` method
   - Line 273-309: Added `convert_definition_to_payload()` method

## Version Notes

- Updated version tracking: Will set `sofir_cpt_definitions_version` to `1.0.3` after fix
- Backward compatible: Works with existing CPT definitions
- Safe to run multiple times: Idempotent operations

## User Instructions

### Untuk User Baru
Tidak ada action yang diperlukan. Menu CPT akan otomatis muncul setelah install dari Library.

### Untuk User yang Sudah Install CPT Sebelumnya
1. Pergi ke **SOFIR → Tools** di WordPress admin
2. Klik tombol **"Refresh CPT Definitions"**
3. Menu CPT akan muncul di sidebar

## Benefits

✅ **Immediate Fix**: Menu langsung muncul untuk instalasi baru  
✅ **User-Friendly**: Satu klik untuk fix existing installations  
✅ **Comprehensive**: Memperbaiki semua CPT, bukan hanya seed CPTs  
✅ **Safe**: Tidak mengubah data atau content yang sudah ada  
✅ **Future-Proof**: Prevent masalah yang sama untuk CPT baru  

## Technical Details

### Show in Menu Settings
```php
'show_ui'           => true,  // Show admin UI
'show_in_menu'      => true,  // Show in admin menu (sidebar)
'show_in_nav_menus' => true,  // Available for nav menus
```

These settings control CPT visibility in WordPress admin interface.

### Update Flow
```
save_post_type() → Update args with menu settings → Save to database → Register CPT → Menu appears
```

### Fix Flow
```
User clicks Refresh → fix_cpt_menus() → Loop all CPTs → Check & update settings → Save → Flush rewrites → Menu appears
```

## Conclusion

Masalah menu CPT tidak muncul setelah install dari Library sudah teratasi dengan 3 layer solution:
1. **Prevention**: `save_post_type()` sekarang include menu settings by default
2. **Auto-fix**: `ensure_cpt_menus_visible()` dipanggil otomatis setelah install/import
3. **Manual fix**: User bisa gunakan Tools tab untuk fix existing CPTs

Solusi ini komprehensif, aman, dan mudah digunakan.
