# Fix: Restore Missing CPT Menus

## Masalah

Menu CPT (Custom Post Types) untuk listing, profile, article, event, dan appointment hilang dari sidebar admin WordPress setelah instalasi pertama plugin SOFIR.

## Penyebab

CPT dapat dihapus melalui admin panel (SOFIR → Content → Delete). Ketika user menghapus CPT melalui interface ini, definisi CPT dihapus dari database option `sofir_cpt_definitions`, sehingga menu tidak lagi muncul di sidebar admin.

## Solusi

Menambahkan fitur **Restore Default CPTs** yang memungkinkan user mengembalikan 5 CPT default beserta taxonomies-nya dengan satu klik.

## Perubahan yang Dilakukan

### 1. **CptManager** (`includes/sofir-cpt-manager.php`)

Menambahkan 2 method public baru:

```php
public function restore_default_post_types(): void
public function restore_default_taxonomies(): void
```

Method ini akan:
- Mengambil seed data default dari `get_seed_post_types()` dan `get_seed_taxonomies()`
- Menyimpan kembali ke array `$this->post_types` dan `$this->taxonomies`
- Update option di database
- Fire action hooks untuk extensibility

### 2. **ContentPanel** (`includes/class-admin-content-panel.php`)

**Handler Baru:**
```php
public function handle_restore_default_cpts(): void
```

Handler ini akan:
1. Memanggil `restore_default_post_types()`
2. Memanggil `restore_default_taxonomies()`
3. Flush rewrite rules untuk memastikan permalink bekerja
4. Redirect dengan success notice

**UI Changes:**
- Tombol "🔄 Restore Default CPTs" di bagian atas "Registered Post Types"
- Konfirmasi dialog sebelum restore
- Deskripsi yang jelas tentang apa yang akan di-restore
- Success message yang informatif

**Registrasi Hook:**
```php
\add_action( 'admin_post_sofir_restore_default_cpts', [ $this, 'handle_restore_default_cpts' ] );
```

## CPT yang Di-restore

### Post Types:
1. **listing** - Business/Location directory
   - Fields: location, hours, rating, status, price, contact, gallery, attributes
   - Filters: location, rating, status, price, attribute, open_now
   - Taxonomies: listing_category, listing_location

2. **profile** - User profiles
   - Fields: location, contact, status, attributes
   - Filters: location, status
   - Taxonomies: profile_category

3. **article** - Blog/News content
   - Fields: attributes
   - Filters: attribute

4. **event** - Event management
   - Fields: event_date, event_capacity, location, contact, gallery, status, attributes
   - Filters: event_after, location, capacity_min, status
   - Taxonomies: event_category, event_tag

5. **appointment** - Appointment booking
   - Fields: appointment_datetime, appointment_duration, appointment_status, appointment_provider, appointment_client, contact, attributes
   - Filters: appointment_after, appointment_status, provider_id, client_id
   - Taxonomies: appointment_service

### Taxonomies:
1. **listing_category** (hierarchical) → listing
2. **listing_location** (flat) → listing
3. **profile_category** (flat) → profile
4. **event_category** (hierarchical) → event
5. **event_tag** (flat) → event
6. **appointment_service** (hierarchical) → appointment

## Cara Penggunaan

1. **Akses Admin Panel:**
   ```
   WordPress Admin → SOFIR → Content
   ```

2. **Klik Tombol Restore:**
   - Scroll ke section "Registered Post Types"
   - Klik tombol "🔄 Restore Default CPTs"
   - Konfirmasi dialog akan muncul

3. **Konfirmasi:**
   ```
   "Apakah Anda yakin ingin mengembalikan CPT default 
   (listing, profile, article, event, appointment)? 
   CPT yang sudah ada tidak akan dihapus."
   ```

4. **Success:**
   - Success notice akan muncul
   - Refresh halaman untuk melihat menu CPT di sidebar
   - Semua 5 CPT default dan taxonomies-nya sudah tersedia

## Keamanan

- **Nonce verification** untuk mencegah CSRF attack
- **Capability check** `manage_options` - hanya admin yang dapat restore
- **Sanitization** pada semua input
- **Confirmation dialog** sebelum eksekusi

## Event Hooks

### Actions:
```php
do_action( 'sofir/cpt/before_restore_defaults' );
do_action( 'sofir/cpt/restored_defaults', $defaults );
do_action( 'sofir/taxonomy/before_restore_defaults' );
do_action( 'sofir/taxonomy/restored_defaults', $defaults );
```

## Catatan Penting

1. **Non-Destructive:** Restore tidak akan menghapus CPT yang sudah ada, hanya menambahkan yang hilang
2. **Rewrite Flush:** Setelah restore, rewrite rules di-flush otomatis untuk URL berfungsi dengan baik
3. **Preserve Data:** Post/content yang sudah ada tidak akan terhapus
4. **Taxonomy Restore:** Taxonomies juga di-restore bersamaan dengan CPT

## Testing

```php
// Manual test via WP CLI
wp eval 'Sofir\Cpt\Manager::instance()->restore_default_post_types();'
wp eval 'flush_rewrite_rules();'

// Check registered CPTs
wp post-type list --format=table

// Check specific CPT
wp post-type get listing --format=json
```

## Backward Compatibility

✅ Tidak ada breaking changes
✅ Existing CPT definitions tetap terjaga
✅ Custom CPT yang dibuat user tidak terpengaruh
✅ Kompatibel dengan semua theme dan plugin WordPress

## Troubleshooting

**Menu masih belum muncul setelah restore:**
1. Refresh halaman admin (Ctrl/Cmd + R)
2. Clear cache browser
3. Periksa user capability (must be Administrator)
4. Cek error log: `wp-content/debug.log`

**Permalink 404:**
```php
// Flush rewrite rules manual via WP CLI
wp rewrite flush
```

**Verifikasi CPT registered:**
```php
// Via admin panel
SOFIR → Content → Registered Post Types

// Via WP CLI
wp post-type list
```

## Update Memory

Menambahkan best practice untuk CPT restoration:
- Selalu flush rewrite rules setelah restore
- Provide confirmation dialog untuk user safety
- Include informative success message
- Make restoration non-destructive
