# WooCommerce Addon Module

Modul SOFIR untuk integrasi dan manajemen WooCommerce dengan fitur code snippet learner dan dashboard manajemen addon.

## 📋 Fitur Utama

### 1. **Admin Menu Terdedikasi**
- Menu WooCommerce Addon di admin WordPress
- 4 submenu dengan interface yang user-friendly:
  - Dashboard (statistik dan kontrol addon)
  - Code Snippets (library snippet dan learner)
  - Extensions (rekomendasi extension WooCommerce)
  - Settings (konfigurasi addon)

### 2. **Code Snippet Learner**
- **8 Built-in Snippets** siap pakai dari kategori:
  - Products (custom product field, hide add to cart)
  - Checkout (custom checkout field)
  - Inventory (stock alert)
  - Orders (custom order status)
  - Discounts (auto-apply discount)
  - Analytics (track conversion)
  - Queries (get products by category)

- **Fitur Snippet**:
  - Kategori dan pencarian
  - View dan copy code
  - Custom snippet creation
  - Code preview dengan syntax highlighting

### 3. **External Resources**
- Tautan ke:
  - WP Beaches WooCommerce (https://wpbeaches.com/tag/woocommerce/)
  - WooCommerce Official Docs
  - GitHub Repository
  - Stack Overflow

### 4. **Extension Recommendations**
- Rekomendasi extension premium dan free:
  - WooCommerce PDF Invoices
  - Subscriptions
  - Bookings
  - Advanced Custom Fields Pro
  - Elementor Pro

### 5. **Dashboard Statistics**
- Total products count
- Completed orders count
- WooCommerce version
- Addon enable/disable toggle

### 6. **Settings Management**
- Enable/disable orders sync
- Enable/disable product sync
- Enable/disable webhooks
- Webhook URL configuration

## 🚀 Instalasi

Module ini otomatis ter-register dan ter-boot saat SOFIR plugin diaktifkan.

### Requirements:
- WordPress 6.3+
- PHP 8.0+
- WooCommerce plugin aktif

## 📂 Struktur File

```
modules/woocommerce-addon/
├── manager.php           # Main manager class
├── integration.php       # WooCommerce integration hooks
├── admin.php            # Admin interface rendering
├── snippets.php         # Code snippets management
├── assets/
│   ├── admin.css        # Admin styles
│   └── admin.js         # Admin scripts
└── README.md            # Documentation
```

## 🔧 Penggunaan

### 1. Mengaktifkan Addon

```php
// Via AJAX (UI)
// Gunakan toggle switch di Dashboard

// Via Code
update_option( 'sofir_woocommerce_addon_enabled', true );
```

### 2. Mengakses Code Snippets

```
Dashboard > WC Addon > Code Snippets
```

- **Browse Snippets**: Browse 8+ built-in snippets
- **Filter by Category**: Filter berdasarkan kategori
- **Search**: Search snippets by name
- **Copy Code**: Copy ke clipboard
- **Custom Snippet**: Tambah custom snippet sendiri

### 3. Konfigurasi Settings

```
Dashboard > WC Addon > Settings
```

- Enable/disable order sync
- Enable/disable product sync
- Configure webhooks

## 🎨 Admin Interface Design

### Colors (Modern Gradient)
- Primary: `#667eea` → `#764ba2` (purple gradient)
- Text: `#1e293b` (slate)
- Background: `#f8fafc` (light)
- Border: `#e2e8f0`

### Components
- Cards dengan hover effects
- Status badges (Active/Inactive)
- Smooth animations dan transitions
- Responsive design (mobile-friendly)

## 🔌 Hooks & Filters

### Actions

```php
// WooCommerce event triggers
do_action( 'sofir/woocommerce/stock_changed', $data );
do_action( 'sofir/woocommerce/new_order', $data );

// Addon status
do_action( 'sofir_woocommerce_addon_enabled' );
do_action( 'sofir_woocommerce_addon_disabled' );
```

### Filters

```php
// Add custom post types
apply_filters( 'sofir/ecommerce/post_types', $post_types );

// Customize snippets
apply_filters( 'sofir/woocommerce/snippets', $snippets );
```

## 📡 AJAX Handlers

- `sofir_fetch_code_snippet` - Fetch single snippet
- `sofir_save_snippet` - Save custom snippet
- `sofir_toggle_addon` - Toggle addon status
- `sofir_get_addon_status` - Get addon status info

## 💾 Database Options

```php
// Addon enablement
sofir_woocommerce_addon_enabled (bool)

// Custom snippets
sofir_wc_custom_snippets (array)

// Settings
sofir_wc_enable_orders_sync (bool)
sofir_wc_enable_product_sync (bool)
sofir_wc_enable_webhooks (bool)
sofir_wc_webhook_url (string)
```

## 🔐 Security

- Nonce verification pada semua AJAX requests
- Capability checks (manage_options)
- Sanitization/escaping pada semua input
- No direct SQL queries

## 📚 Built-in Snippets

### 1. Add Custom Product Field
Menambahkan custom field ke product admin

### 2. Hide Add to Cart Button
Sembunyikan tombol add to cart untuk kategori tertentu

### 3. Add Custom Checkout Field
Tambah field custom di halaman checkout

### 4. Get Products by Category
Query products dari kategori spesifik

### 5. Stock Status Alert
Alert ketika stock produk rendah

### 6. Register Custom Order Status
Register custom status untuk order

### 7. Apply Discount by Email
Auto-apply discount untuk email tertentu

### 8. Track Conversion Events
Track order completion sebagai conversion event

## 🎯 Integrasi dengan SOFIR

Module ini terintegrasi dengan:
- **Admin Module**: Tab admin WooCommerce
- **Ecommerce Module**: Enhance post types untuk product
- **CPT Manager**: Support untuk custom post types
- **Webhooks**: WooCommerce event triggers
- **Analytics**: Track conversion events

## 🚦 Status & Roadmap

### Current Version: 1.0.0

**Completed**:
- ✅ Admin menu dengan 4 submenu
- ✅ Code snippet library (8 snippets)
- ✅ Custom snippet creation
- ✅ External resources links
- ✅ Extension recommendations
- ✅ Dashboard statistics
- ✅ Settings management
- ✅ AJAX handlers
- ✅ Modern UI design
- ✅ Responsive design

**Future Enhancements**:
- [ ] Snippet version control
- [ ] Snippet sharing/export
- [ ] Snippet rating system
- [ ] API untuk external snippet sources
- [ ] Scheduled sync dari wpbeaches.com
- [ ] Advanced snippet testing tools
- [ ] Performance monitoring
- [ ] Integration dengan WooCommerce REST API

## 💡 Tips & Best Practices

### 1. Code Snippets
- Selalu test snippet di staging environment terlebih dahulu
- Backup database sebelum menjalankan snippet
- Use custom snippet untuk kode yang tidak ada di built-in

### 2. Performance
- Monitor dengan tools seperti New Relic atau DataDog
- Optimize database queries dengan proper indexing
- Cache hasil query jika memungkinkan

### 3. Security
- Validate input dari user
- Sanitize output sebelum display
- Use nonce untuk AJAX requests
- Check user capabilities

## 🐛 Troubleshooting

### Menu tidak muncul
```php
// Check if WooCommerce aktif
if ( ! class_exists( 'WooCommerce' ) ) {
    // Module tidak akan load
}
```

### Snippets tidak ter-load
```php
// Clear browser cache
// Check browser console untuk error messages
```

### AJAX errors
```php
// Check nonce di request
// Verify manage_options capability
// Check error logs: wp-content/debug.log
```

## 📞 Support

Untuk issues atau questions:
1. Check dokumentasi ini
2. Review code di module
3. Check WordPress error logs
4. Contact SOFIR team

## 📄 License

SOFIR Plugin - All Rights Reserved

---

**Module Version**: 1.0.0  
**Last Updated**: 2025-01-22  
**Compatibility**: WordPress 6.3+, PHP 8.0+, WooCommerce 3.0+
