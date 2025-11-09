# Multi-Vendor Single Page Template Fix

## Masalah
Page vendor_store dan vendor_product tidak tampil dengan benar setelah dibuat.

## Penyebab
1. **Rewrite rules belum di-flush** setelah CPT didaftarkan
2. **Filter `the_content` priority terlalu rendah** sehingga bisa di-override oleh theme/plugin lain
3. **CPT registration tidak lengkap** - kurang parameter `publicly_queryable`, `show_ui`, dll
4. **CSS tidak di-enqueue** untuk single page templates
5. **Inline styles berlebihan** yang seharusnya ada di CSS file

## Solusi yang Diterapkan

### 1. Auto-Flush Rewrite Rules
Menambahkan fungsi untuk flush rewrite rules sekali saat pertama aktivasi:

```php
public function flush_rewrite_rules_on_first_activation(): void {
    if ( \get_option( 'sofir_multivendor_rewrite_flushed' ) ) {
        return;
    }
    \flush_rewrite_rules();
    \update_option( 'sofir_multivendor_rewrite_flushed', '1' );
}
```

Hook ditambahkan: `\add_action( 'init', [ $this, 'flush_rewrite_rules_on_first_activation' ] );`

### 2. Meningkatkan Priority Filter
Filter `the_content` sekarang menggunakan priority 20 (default 10):

```php
\add_filter( 'the_content', [ $this, 'render_vendor_single_template' ], 20 );
```

### 3. Perbaikan CPT Registration
Menambahkan parameter penting untuk CPT:

```php
\register_post_type(
    'vendor_store',
    [
        'label' => \__( 'Vendor Stores', 'sofir' ),
        'public' => true,
        'publicly_queryable' => true,  // BARU
        'show_ui' => true,              // BARU
        'show_in_menu' => 'sofir-multivendor',
        'show_in_rest' => true,
        'supports' => [ 'title', 'editor', 'thumbnail', 'custom-fields' ],
        'has_archive' => true,
        'rewrite' => [ 'slug' => 'vendors', 'with_front' => false ],  // UPDATED
        'menu_icon' => 'dashicons-store',
        'capability_type' => 'post',    // BARU
        'map_meta_cap' => true,         // BARU
    ]
);
```

Parameter yang sama diterapkan ke `vendor_product`.

### 4. Enqueue CSS untuk Frontend
Menambahkan fungsi untuk load CSS di single page:

```php
public function enqueue_frontend_assets(): void {
    if ( \is_singular( [ 'vendor_store', 'vendor_product' ] ) || 
         \is_post_type_archive( [ 'vendor_store', 'vendor_product' ] ) ) {
        \wp_enqueue_style(
            'sofir-multivendor',
            \plugins_url( 'assets/css/multivendor.css', dirname( __DIR__ ) ),
            [],
            '1.0.0'
        );
    }
}
```

Hook: `\add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );`

### 5. Perbaikan Template Rendering
Memperbaiki conditional check di `render_vendor_single_template()`:

**Sebelum:**
```php
if ( ! \is_singular( [ 'vendor_store', 'vendor_product' ] ) || ! $post ) {
    return $content;
}
```

**Sesudah:**
```php
if ( ! $post || ! \is_singular() ) {
    return $content;
}

if ( ! \in_array( $post->post_type, [ 'vendor_store', 'vendor_product' ], true ) ) {
    return $content;
}
```

### 6. Cleanup Inline Styles
Menghapus inline styles yang berlebihan karena sudah ada di CSS file:

**Sebelum:**
```php
<div class="sofir-product-card" style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center;">
```

**Sesudah:**
```php
<div class="sofir-product-card">
```

### 7. Penambahan CSS untuk Single Pages
Menambahkan CSS baru di `assets/css/multivendor.css`:

```css
.sofir-vendor-single {
    max-width: 1200px;
    margin: 0 auto;
}

.sofir-vendor-single .vendor-logo {
    margin-bottom: 30px;
    text-align: center;
}

.sofir-vendor-single .vendor-logo img {
    max-width: 400px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.sofir-product-single {
    max-width: 1200px;
    margin: 0 auto;
}

.sofir-product-single .product-image img {
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.sofir-product-single .product-meta {
    background: #f9f9f9;
    padding: 30px;
    border-radius: 8px;
    margin: 30px 0;
}
```

## Cara Testing

### 1. Reset Rewrite Rules
Hapus option untuk trigger flush ulang:
```php
delete_option('sofir_multivendor_rewrite_flushed');
```

Atau reload plugin/refresh permalink settings.

### 2. Buat Vendor Store
```
1. Masuk ke wp-admin
2. Klik Multi-Vendor → Vendors → Add New
3. Isi Store Name, content, dan featured image
4. Publish
5. Klik "View Post" untuk lihat single page
```

### 3. Buat Vendor Product
```
1. Masuk ke wp-admin
2. Klik Multi-Vendor → Products → Add New
3. Isi Product Title, content, featured image
4. Isi Product Details: Price, SKU, Stock
5. Publish
6. Klik "View Post" untuk lihat single page
```

### 4. Verifikasi Tampilan
**Vendor Store Page harus menampilkan:**
- Logo vendor (featured image)
- Store description (content)
- Store owner info
- Total products count
- Grid produk-produk dari vendor tersebut

**Vendor Product Page harus menampilkan:**
- Product image (featured image)
- Product description (content)
- Product meta box dengan:
  - Price (besar, bold, biru)
  - SKU
  - Stock
  - Sold by (link ke vendor store)

## File yang Dimodifikasi
1. `modules/multivendor/manager.php` - Logika utama
2. `assets/css/multivendor.css` - Styling untuk single pages

## Catatan Penting
- **Rewrite flush hanya terjadi sekali** setelah update untuk performa
- **CSS hanya di-load di page yang relevan** untuk optimasi
- **Filter priority 20** memastikan template kita jalan setelah filter default
- **Semua inline styles dihapus** untuk maintainability yang lebih baik
- **CPT sepenuhnya public dan queryable** untuk WordPress routing

## Kompatibilitas
- ✅ WordPress 5.8+
- ✅ PHP 8.0+
- ✅ Semua theme (karena menggunakan `the_content` filter)
- ✅ Block editor (Gutenberg)
- ✅ Classic editor

## Troubleshooting

### Jika page masih tidak tampil:
1. Go to Settings → Permalinks
2. Klik "Save Changes" untuk flush rewrite rules
3. Clear browser cache
4. Test lagi

### Jika CSS tidak muncul:
1. Check apakah file `assets/css/multivendor.css` exists
2. Clear plugin/theme cache jika ada
3. Inspect element untuk lihat apakah CSS di-load

### Jika produk tidak muncul di vendor page:
1. Pastikan product memiliki `vendor_id` meta field
2. Check dengan: `get_post_meta($product_id, 'vendor_id', true)`
3. Vendor role harus assign product secara otomatis saat create
