# Dokumentasi Widget Elementor SOFIR Forms & Payments

## Ringkasan

Dokumen ini menjelaskan lengkap widget Elementor untuk modul SOFIR Forms dan Payments. Widget-widget ini menyediakan form builder dan solusi pembayaran dengan cara drag-and-drop langsung di editor Elementor.

## Kategori Widget

Semua widget diorganisir dalam kategori Elementor berikut:

1. **SOFIR Elements** - Widget umum termasuk widget Form
2. **SOFIR E-Commerce** - Widget e-commerce termasuk Payment, Donation, Subscription, dan Product Catalog

---

## 1. Widget Form

**Kategori:** SOFIR Elements  
**Nama:** `sofir-form`  
**Icon:** Form Horizontal

### Deskripsi

Menampilkan form kustom SOFIR yang dibuat di modul Forms. Mendukung semua 16 tipe field, conditional logic, dan pengiriman AJAX.

### Pengaturan

#### Tab Content

- **Select Form** - Dropdown untuk memilih form yang dipublikasikan
- **Show Form Title** - Toggle untuk menampilkan judul form
- **Show Form Description** - Toggle untuk menampilkan deskripsi form
- **AJAX Submit** - Aktifkan pengiriman form tanpa reload halaman

#### Tab Style

**Form Style:**
- Background Color - Warna latar belakang
- Padding (responsive) - Jarak dalam
- Border (type, width, color) - Border
- Border Radius - Sudut melengkung

**Field Style:**
- Text Color - Warna teks
- Background Color - Warna latar
- Border (type, width, color) - Border
- Border Radius - Sudut melengkung

**Button Style:**
- Text Color - Warna teks tombol
- Background Color - Warna latar tombol
- Typography - Tipografi
- Border Radius - Sudut melengkung
- Padding (responsive) - Jarak dalam

### Contoh Penggunaan

```
1. Tambahkan widget "Form" ke halaman Anda
2. Pilih form dari dropdown
3. Konfigurasi opsi tampilan
4. Sesuaikan styling sesuai kebutuhan
```

### Integrasi

Bekerja mulus dengan:
- Semua 16 tipe field SOFIR
- Aturan conditional logic
- Pelacakan analytics form
- Perlindungan spam
- Notifikasi email

---

## 2. Widget Payment Form

**Kategori:** SOFIR E-Commerce  
**Nama:** `sofir-payment-form`  
**Icon:** Price Table

### Deskripsi

Buat form pembayaran untuk produk atau layanan dengan jumlah, mata uang, dan gateway pembayaran yang dapat disesuaikan.

### Pengaturan

#### Tab Content

- **Item Name** - Nama produk atau layanan
- **Amount** - Jumlah pembayaran (angka)
- **Currency** - Pilih dari IDR, USD, EUR, GBP, SGD, MYR
- **Description** - Teks deskripsi pembayaran
- **Button Text** - Teks tombol submit (default: "Pay Now")
- **Show Customer Info** - Toggle field pelanggan (nama, email, telepon)
- **Enable Quantity** - Izinkan pemilihan jumlah

#### Tab Style

**Form Style:**
- Background Color - Warna latar belakang
- Padding (responsive) - Jarak dalam
- Border (type, width, color) - Border
- Border Radius - Sudut melengkung

**Button Style:**
- Text Color - Warna teks
- Background Color - Warna latar
- Typography - Tipografi

### Shortcode Setara

```
[sofir_payment_form 
    item_name="Nama Produk" 
    amount="100000" 
    currency="IDR" 
    show_customer_info="yes"]
```

### Gateway Pembayaran

Mendukung semua gateway yang dikonfigurasi:
- Manual Payment
- Duitku
- Xendit
- Midtrans

---

## 3. Widget Donation Form

**Kategori:** SOFIR E-Commerce  
**Nama:** `sofir-donation-form`  
**Icon:** Heart

### Deskripsi

Buat form donasi yang indah dengan jumlah yang disarankan dan opsi jumlah kustom.

### Pengaturan

#### Tab Content

- **Title** - Judul form (default: "Make a Donation")
- **Description** - Teks deskripsi donasi
- **Suggested Amounts** - Jumlah dipisahkan koma (contoh: "50000,100000,250000,500000")
- **Currency** - Pilih dari IDR, USD, EUR, GBP, SGD, MYR
- **Allow Custom Amount** - Toggle input jumlah kustom
- **Show Donor Info** - Toggle field donor (nama, email)
- **Button Text** - Teks tombol submit (default: "Donate Now")

#### Tab Style

**Form Style:**
- Background Color - Warna latar belakang
- Padding (responsive) - Jarak dalam
- Border (type, width, color) - Border
- Border Radius - Sudut melengkung

**Amount Buttons:**
- Text Color - Warna teks
- Background Color - Warna latar
- Active Text Color - Warna teks aktif
- Active Background Color - Warna latar aktif

**Submit Button:**
- Text Color - Warna teks
- Background Color - Warna latar
- Typography - Tipografi

### Fitur

- Jumlah yang disarankan sudah ditentukan
- Input jumlah kustom
- Pemilihan jumlah sekali klik
- Status aktif visual untuk jumlah terpilih
- Pengumpulan informasi donor
- Dukungan multi mata uang

### Shortcode Setara

```
[sofir_donation_form 
    title="Dukung Misi Kami" 
    suggested_amounts="50000,100000,250000" 
    currency="IDR" 
    allow_custom="yes"]
```

---

## 4. Widget Subscription Form

**Kategori:** SOFIR E-Commerce  
**Nama:** `sofir-subscription-form`  
**Icon:** Sync

### Deskripsi

Tampilkan paket langganan dengan harga, fitur, dan tombol berlangganan.

### Pengaturan

#### Tab Content

- **Title** - Judul form (default: "Subscribe Now")
- **Description** - Teks deskripsi langganan
- **Specific Subscription** - Pilih satu langganan atau tampilkan semua
- **Currency** - Pilih dari IDR, USD, EUR, GBP, SGD, MYR
- **Layout** - Grid, List, atau Table
- **Columns** - 1, 2, 3, atau 4 kolom (untuk layout grid)
- **Show Features** - Toggle tampilan daftar fitur
- **Button Text** - Teks tombol berlangganan (default: "Subscribe")

#### Tab Style

**Form Style:**
- Background Color - Warna latar belakang
- Padding (responsive) - Jarak dalam
- Border (type, width, color) - Border
- Border Radius - Sudut melengkung

**Plan Card:**
- Background Color - Warna latar
- Border (type, width, color) - Border
- Border Radius - Sudut melengkung
- Box Shadow - Bayangan kotak

**Button Style:**
- Text Color - Warna teks
- Background Color - Warna latar
- Typography - Tipografi

### Opsi Layout

1. **Grid** - Kartu dalam grid responsif
2. **List** - Tampilan list bertumpuk
3. **Table** - Format tabel perbandingan

### Fitur

- Beberapa paket langganan
- Daftar fitur per paket
- Dukungan penagihan berulang
- Highlighting paket unggulan
- Layout responsif
- Multi mata uang

### Shortcode Setara

```
[sofir_subscription_form 
    currency="IDR" 
    layout="grid" 
    columns="3" 
    show_features="yes"]
```

---

## 5. Widget Product Catalog

**Kategori:** SOFIR E-Commerce  
**Nama:** `sofir-product-catalog`  
**Icon:** Products

### Deskripsi

Tampilkan katalog produk dengan gambar, harga, badge sale, dan tombol beli.

### Pengaturan

#### Tab Content

- **Title** - Judul katalog (default: "Our Products")
- **Columns** - 1, 2, 3, 4, 5, atau 6 kolom
- **Products Per Page** - Jumlah produk yang ditampilkan (-1 untuk semua)
- **Order By** - Date, Title, Price, Random, Menu Order
- **Order** - Ascending atau Descending
- **Show Image** - Toggle gambar produk
- **Show Price** - Toggle tampilan harga
- **Show Sale Badge** - Toggle badge sale untuk produk diskon
- **Show Description** - Toggle deskripsi produk
- **Show Add to Cart** - Toggle tombol beli
- **Button Text** - Teks tombol beli (default: "Buy Now")

#### Tab Style

**Grid Style:**
- Gap (responsive) - Jarak antar item

**Product Card:**
- Background Color - Warna latar
- Padding (responsive) - Jarak dalam
- Border (type, width, color) - Border
- Border Radius - Sudut melengkung
- Box Shadow - Bayangan kotak

**Product Title:**
- Color - Warna
- Typography - Tipografi

**Price:**
- Color - Warna
- Typography - Tipografi

**Button:**
- Text Color - Warna teks
- Background Color - Warna latar
- Typography - Tipografi
- Border Radius - Sudut melengkung

### Fitur

- Layout grid responsif
- Gambar produk dengan efek hover
- Harga sale dan harga normal
- Badge sale overlay
- Deskripsi produk
- Fungsi add to cart
- Beberapa opsi sorting
- Dukungan pagination

### Shortcode Setara

```
[sofir_product_catalog 
    columns="3" 
    limit="12" 
    orderby="date" 
    show_price="yes" 
    show_sale_badge="yes"]
```

---

## Ringkasan Jumlah Widget

**Total Widget Forms & Payments: 5**

1. Widget Form - Tampilkan form kustom
2. Widget Payment Form - Form pembayaran tunggal
3. Widget Donation Form - Kampanye donasi
4. Widget Subscription Form - Paket langganan
5. Widget Product Catalog - Showcase produk

---

## Total Widget Elementor SOFIR

**Grand Total: 49 Widget**

- SOFIR Elements: 17 widget (termasuk Form)
- SOFIR Booking & Events: 7 widget
- SOFIR E-Commerce: 20 widget (termasuk 4 widget payment baru)
- SOFIR E-Learning: 3 widget
- SOFIR Voxel: 2 widget

---

## Assets

### File CSS

- `assets/css/forms.css` - Styling form dengan semua tipe field
- `assets/css/payments.css` - Styling form pembayaran dan katalog produk

### File JavaScript

- `assets/js/forms.js` - Fungsi form (rating, signature, conditional logic)
- `assets/js/payments.js` - Pemrosesan pembayaran dan handling AJAX

---

## Opsi Styling Umum

Semua widget mendukung:

- **Responsive Controls** - Pengaturan berbeda per perangkat
- **Color Customization** - Semua warna sepenuhnya dapat disesuaikan
- **Typography Controls** - Font family, size, weight, dll
- **Spacing Controls** - Padding, margin, gap
- **Border Controls** - Type, width, color, radius
- **Box Shadow** - Beberapa layer bayangan
- **Hover Effects** - Transisi yang halus

---

## Best Practices

### Widget Form

1. Buat form di SOFIR → Forms sebelum menggunakan widget
2. Aktifkan AJAX submit untuk UX lebih baik
3. Gunakan conditional logic untuk form kompleks
4. Test pengaturan perlindungan spam

### Widget Payment Form

1. Konfigurasi gateway pembayaran di SOFIR → Payments
2. Set mata uang yang sesuai untuk target pasar
3. Test dengan mode sandbox/test terlebih dahulu
4. Aktifkan pengumpulan info pelanggan

### Widget Donation Form

1. Set jumlah yang disarankan dengan bermakna
2. Selalu izinkan jumlah kustom
3. Gunakan teks deskripsi yang menarik
4. Buat info donor opsional untuk konversi lebih baik

### Widget Subscription Form

1. Buat langganan di SOFIR → Payments
2. List fitur yang jelas untuk setiap paket
3. Highlight paket paling populer
4. Gunakan layout grid untuk 2-4 paket

### Widget Product Catalog

1. Tambahkan gambar produk untuk semua produk
2. Set harga sale untuk menampilkan diskon
3. Gunakan 3-4 kolom untuk desktop
4. Aktifkan badge sale untuk promosi

---

## Hooks dan Filter

### Form Widget Hooks

```php
// Sebelum form dirender di Elementor
do_action('sofir/elementor/form/before_render', $form_id);

// Setelah form dirender di Elementor
do_action('sofir/elementor/form/after_render', $form_id);
```

### Payment Widget Hooks

```php
// Sebelum payment form dirender
do_action('sofir/elementor/payment_form/before_render', $settings);

// Setelah payment form dirender
do_action('sofir/elementor/payment_form/after_render', $settings);
```

### Product Catalog Hooks

```php
// Modifikasi product query
add_filter('sofir/elementor/product_catalog/query_args', function($args) {
    // Modifikasi query
    return $args;
});
```

---

## Troubleshooting

### Form Tidak Muncul di Dropdown

**Solusi:** Buat setidaknya satu form di SOFIR → Forms → Add New

### Gateway Pembayaran Tidak Bekerja

**Solusi:** Konfigurasi API keys gateway di SOFIR → Payments → Settings

### Produk Tidak Muncul

**Solusi:** Buat produk di SOFIR → Payments → Products

### Styling Tidak Diterapkan

**Solusi:** Hapus cache WordPress dan regenerate CSS di Elementor

### AJAX Submit Tidak Bekerja

**Solusi:** Cek browser console untuk error JavaScript dan pastikan jQuery dimuat

---

## Changelog

### Versi 1.0.0
- Rilis awal dengan 5 widget
- Widget form dengan dukungan 16 tipe field
- Widget payment form dengan dukungan 4 gateway
- Widget donation form dengan jumlah yang disarankan
- Widget subscription form dengan 3 layout
- Widget product catalog dengan filtering lanjutan

---

## Dukungan

Untuk informasi lebih lanjut:
- Dokumentasi utama: `SOFIR_FORMS_PAYMENTS_FEATURES.md`
- Panduan Indonesia: `FITUR_SOFIR_FORMS_PAYMENTS_ID.md`
- Dokumentasi Forms: Lihat dokumentasi modul Forms
- Dokumentasi Payments: Lihat dokumentasi modul Payments
