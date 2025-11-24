# Integrasi Elementor & E-Commerce SOFIR

## 📋 Overview

SOFIR sekarang dilengkapi dengan integrasi Elementor dan support penuh untuk WooCommerce, Easy Digital Downloads (EDD), dan North Commerce.

## 🎨 Elementor Widgets

### SOFIR Elements (12 Widgets)

1. **Post Feed** - Tampilkan post dalam grid/list/masonry
2. **Term Feed** - Tampilkan kategori/tags
3. **Search Form** - Form pencarian dengan filter
4. **Map** - Peta interaktif dengan markers
5. **Contact Info** - Info kontak dari post
6. **Review Stats** - Statistik rating & review
7. **Visit Chart** - Grafik kunjungan
8. **Ring Chart** - Chart statistik berbentuk ring
9. **Countdown** - Timer countdown
10. **Create Post** - Form buat post dari frontend
11. **Dynamic Data** - Tampilkan data dinamis
12. **Appointment Form** - Form booking appointment

### WooCommerce Elements (5 Widgets)

1. **WooCommerce Products** - Grid produk dengan filter
2. **WooCommerce Cart** - Keranjang belanja
3. **WooCommerce Checkout** - Halaman checkout
4. **WooCommerce Categories** - Grid kategori produk
5. **WooCommerce Account** - Halaman akun customer

### Easy Digital Downloads Elements (5 Widgets)

1. **EDD Products** - Grid produk digital
2. **EDD Cart** - Keranjang download
3. **EDD Checkout** - Checkout form
4. **EDD Categories** - Kategori download
5. **EDD Download Button** - Tombol purchase individual

### North Commerce Elements (4 Widgets)

1. **North Products** - Grid produk North Commerce
2. **North Cart** - Keranjang belanja
3. **North Checkout** - Form checkout
4. **North Categories** - Kategori produk

## 🛒 Integrasi E-Commerce

### WooCommerce Integration

**Custom Fields:**
- `wc_price` - Harga produk
- `wc_sale_price` - Harga diskon
- `wc_stock` - Stok produk
- `wc_sku` - SKU produk

**Webhook Events:**
- `woocommerce_new_order` - Order baru dibuat
- `woocommerce_order_completed` - Order selesai
- `woocommerce_stock_changed` - Stok berubah
- `woocommerce_low_stock` - Stok menipis

**Loyalty Integration:**
- Auto award points saat order completed
- Configurable points per purchase

### EDD Integration

**Custom Fields:**
- `edd_price` - Harga download
- `edd_sales` - Total penjualan
- `edd_earnings` - Total pendapatan

**Webhook Events:**
- `edd_purchase_completed` - Purchase selesai
- `edd_new_payment` - Payment baru
- `edd_payment_status_changed` - Status payment berubah

**Loyalty Integration:**
- Auto award points saat purchase completed

### North Commerce Integration

**Custom Fields:**
- `nc_price` - Harga produk
- `nc_sale_price` - Harga diskon
- `nc_stock` - Stok produk
- `nc_sku` - SKU produk

**Webhook Events:**
- `north_new_order` - Order baru
- `north_order_completed` - Order selesai
- `north_stock_changed` - Stok berubah
- `north_low_stock` - Stok menipis

## 📚 Library CPT dengan Web Demo

Setiap template CPT siap pakai sekarang dilengkapi dengan link demo website:

### 6 Ready-to-Use Templates dengan Demo:

1. **🏢 Business Directory**
   - Demo: https://demo.sofir.id/business-directory
   - CPT: listing + 2 taxonomies
   - Fields: location, rating, hours, price, contact, gallery

2. **🏨 Hotel & Accommodation**
   - Demo: https://demo.sofir.id/accommodation
   - CPT: listing (customized for hotels)
   - Fields: price, gallery, rating, location, attributes

3. **📰 News & Blog**
   - Demo: https://demo.sofir.id/news-blog
   - CPT: article
   - Fields: attributes, author, categories

4. **📅 Events & Calendar**
   - Demo: https://demo.sofir.id/events
   - CPT: event + 2 taxonomies
   - Fields: event_date, capacity, location, gallery, status

5. **⏰ Appointments & Booking**
   - Demo: https://demo.sofir.id/appointments
   - CPT: appointment + 1 taxonomy
   - Fields: datetime, duration, status, provider, client

6. **🛒 Toko Online / E-Commerce**
   - Demo: https://demo.sofir.id/online-store
   - CPT: vendor_store + vendor_product + 3 taxonomies
   - Fields: price, SKU, stock, vendor_id

### Fitur Demo:

- **View Demo Button** - Klik untuk lihat live demo
- **Modal Preview** - (Coming soon) Preview dalam modal
- **One-Click Install** - Install complete template
- **Sample Content** - Template sudah include sample pages & menu

## 🎯 Cara Menggunakan

### Menggunakan Elementor Widgets

1. Edit halaman dengan Elementor
2. Cari kategori "SOFIR Elements" atau "SOFIR E-Commerce"
3. Drag & drop widget ke canvas
4. Konfigurasi settings di panel sebelah kiri
5. Publish!

### Mengintegrasikan dengan WooCommerce

```php
// Plugin otomatis detect WooCommerce
// Custom fields otomatis tersedia di CPT Manager
// Webhooks otomatis aktif

// Manual trigger webhook
do_action( 'sofir/webhooks/woocommerce', 'custom_event', $data );
```

### Mengintegrasikan dengan EDD

```php
// Plugin otomatis detect EDD
// Custom fields otomatis tersedia di CPT Manager
// Webhooks otomatis aktif

// Manual trigger webhook
do_action( 'sofir/webhooks/edd', 'custom_event', $data );
```

### Menggunakan Library CPT dengan Demo

1. Pergi ke **SOFIR → Library**
2. Lihat 6 template siap pakai
3. Klik **View Demo** untuk lihat preview live
4. Klik **Install Sekarang** untuk install complete
5. Template akan otomatis membuat:
   - Custom Post Types dengan fields
   - Taxonomies dengan filters
   - Sample pages (single & archive)
   - Navigation menu dengan links

## 🔌 Custom Development

### Membuat Elementor Widget Kustom

```php
namespace YourPlugin\Elementor;

use Sofir\Elementor\BaseWidget;

class Custom_Widget extends BaseWidget {
    public function get_name() {
        return 'your-widget-name';
    }

    public function get_title() {
        return __( 'Your Widget', 'your-domain' );
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'your-domain' ),
            ]
        );

        // Add controls here

        $this->end_controls_section();
        $this->add_style_controls();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        // Render output
        echo '<div>' . esc_html( $settings['your_field'] ) . '</div>';
    }
}
```

### Menambahkan E-Commerce Integration Custom

```php
// Hook ke sofir/cpt/fields_catalog untuk tambah custom fields
add_filter( 'sofir/cpt/fields_catalog', function( $fields ) {
    $fields['custom_price'] = [
        'name' => __( 'Custom Price', 'your-domain' ),
        'type' => 'price',
        'meta_key' => '_custom_price',
    ];
    
    return $fields;
} );

// Hook ke sofir/directory/post_types untuk tambah post type
add_filter( 'sofir/directory/post_types', function( $post_types ) {
    $post_types[] = 'custom_product';
    return $post_types;
} );
```

## 🎨 Widget Styling

Semua Elementor widgets support styling dari panel Elementor:

- **Text Color** - Warna teks
- **Typography** - Font, size, weight, line-height
- **Spacing** - Margin & padding
- **Background** - Warna & gambar background
- **Border** - Border style & radius
- **Shadow** - Box shadow

## 📖 Hook & Filter Reference

### Elementor Hooks

```php
// Tambah widget custom
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
    $widgets_manager->register( new Your_Custom_Widget() );
} );

// Tambah kategori custom
add_action( 'elementor/elements/categories_registered', function( $elements_manager ) {
    $elements_manager->add_category(
        'your-category',
        [
            'title' => __( 'Your Category', 'your-domain' ),
            'icon' => 'fa fa-plug',
        ]
    );
} );
```

### E-Commerce Filters

```php
// Filter post types e-commerce
add_filter( 'sofir/blocks/post_types', function( $post_types ) {
    $post_types[] = 'custom_product';
    return $post_types;
} );

// Filter fields e-commerce
add_filter( 'sofir/cpt/fields_catalog', function( $fields ) {
    // Add custom fields
    return $fields;
} );
```

## 🚀 Performance Tips

1. **Cache Elementor** - Enable Elementor CSS cache
2. **Lazy Load Products** - Use pagination untuk products
3. **Optimize Images** - Compress product images
4. **Use CDN** - Serve assets dari CDN
5. **Minimize Widgets** - Gunakan widgets seperlunya

## 🐛 Troubleshooting

### Widgets Tidak Muncul di Elementor

1. Cek apakah Elementor terinstall & aktif
2. Clear Elementor cache: Tools → Regenerate CSS
3. Clear browser cache

### E-Commerce Integration Tidak Bekerja

1. Cek apakah plugin e-commerce terinstall & aktif
2. Cek versi plugin minimum:
   - WooCommerce 4.0+
   - EDD 2.9+
   - North Commerce 1.0+

### Demo Link Tidak Bekerja

1. Demo link adalah contoh, ganti dengan URL demo Anda
2. Edit `/includes/class-admin-library-panel.php`
3. Update `demo_url` untuk setiap template

## 📝 Changelog

### Version 1.0.0

- ✅ 12 SOFIR Elementor widgets
- ✅ 5 WooCommerce widgets
- ✅ 5 EDD widgets
- ✅ 4 North Commerce widgets
- ✅ Complete WooCommerce integration
- ✅ Complete EDD integration
- ✅ Complete North Commerce integration
- ✅ Web demo untuk 6 CPT templates
- ✅ View Demo buttons di Library tab
- ✅ Webhook integration untuk semua e-commerce
- ✅ Loyalty points integration
- ✅ Custom fields untuk semua e-commerce

## 🎓 Tutorial Video

Coming soon: Video tutorial untuk:
- Membuat halaman e-commerce dengan Elementor
- Setup WooCommerce dengan SOFIR
- Custom widget development
- CPT template installation

## 💬 Support

Butuh bantuan? Hubungi:
- Email: support@sofir.id
- Docs: https://docs.sofir.id
- Forum: https://forum.sofir.id
