# Integrasi SOFIR dengan Voxel Theme

Panduan lengkap menggunakan Library CPT SOFIR dengan Voxel Theme dan Elementor.

## Gambaran Umum

SOFIR sekarang menyediakan **kompatibilitas penuh** dengan Voxel Theme, memungkinkan Anda menggunakan template Library CPT SOFIR yang powerful dengan fitur direktori advanced Voxel dan page builder Elementor.

## Fitur Utama

### ✅ Pemetaan Field Otomatis
- Field SOFIR otomatis dipetakan ke tipe field Voxel
- Lokasi, jam buka, rating, harga, galeri, dan lainnya
- Field yang bisa dicari dan difilter tetap terjaga
- Behavior field native Voxel

### ✅ Dukungan Template Voxel Native
- CPT SOFIR bekerja dengan template Voxel
- Template single, archive, dan card
- Styling dan layout Voxel
- Kustomisasi theme penuh

### ✅ Widget Elementor (40 Total)
- **2 Widget Khusus Voxel Baru**:
  - Voxel Listings (Layout Grid/List/Map)
  - Voxel Search Form (Filter advanced)
- **12 SOFIR Elements** - Kompatibel dengan Voxel
- **7 Booking & Events** - Kompatibel dengan Voxel
- **16 E-Commerce** - Dukungan WC/EDD/NC/Vendor
- **3 E-Learning** - Platform kursus

### ✅ Pencarian & Filter Advanced
- Filter berbasis AJAX
- Autocomplete lokasi
- Slider rentang harga
- Filter rating
- Pemilih rentang tanggal
- Filter buka sekarang
- Filter kategori

### ✅ 11 Template Siap Pakai
Semua template Library CPT SOFIR kompatibel dengan Voxel:
1. Business Directory
2. Hotel & Accommodation
3. News & Blog
4. Events & Calendar
5. Appointments
6. E-Commerce
7. Restaurant Orders
8. Car Rental
9. Community Forum
10. Doctor Appointments
11. E-Learning Courses

## Instalasi

### Requirements
- WordPress 6.0+
- PHP 8.0+
- Plugin SOFIR (versi terbaru)
- Voxel Theme (semua versi)
- Elementor (gratis atau pro)

### Setup
1. Install dan aktifkan Voxel Theme
2. Install dan aktifkan Plugin SOFIR
3. Integrasi Voxel **otomatis aktif**
4. Buka **SOFIR → Library** untuk install template CPT

## Pemetaan Field

Field SOFIR otomatis dipetakan ke tipe field Voxel:

| Field SOFIR | Tipe Voxel | Fitur |
|-------------|------------|-------|
| `location` | location | Alamat, lat/lng, map |
| `hours` | work-hours | Hari, jam, timezone |
| `rating` | number | Min, max, bisa dicari |
| `status` | select | Pilihan, multiple |
| `price` | number | Min, max, mata uang |
| `contact` | email | Email, telp, website |
| `gallery` | image | Beberapa gambar |
| `attributes` | repeater | Field custom |
| `event_date` | date | Format, range |
| `event_capacity` | number | Min, max |
| `appointment_datetime` | date | Tanggal + waktu |
| `appointment_status` | select | Pilihan status |

## Widget Elementor

### Widget Voxel Listings

Tampilkan listing CPT dengan styling Voxel theme.

**Fitur:**
- Layout Grid, List, Masonry, Map
- Filter AJAX
- Pencarian & sorting
- Pagination
- Template card Voxel
- Kolom responsif

**Cara Pakai:**
1. Tambahkan widget "Voxel Listings" ke halaman
2. Pilih post type
3. Pilih layout (grid/list/map)
4. Aktifkan filter dan pencarian
5. Kustomisasi styling

**Pengaturan:**
- Post Type: Pilih CPT
- Posts Per Page: 12 (default)
- Order By: Date, Title, Modified, Random
- Order: ASC/DESC
- Layout: Grid/List/Masonry/Map
- Columns: 1-6 kolom
- Show Filters: Ya/Tidak
- Show Search: Ya/Tidak
- Use Voxel Templates: Ya/Tidak
- Enable AJAX: Ya/Tidak

### Widget Voxel Search Form

Form pencarian advanced dengan filter.

**Fitur:**
- Pencarian keyword
- Autocomplete lokasi
- Filter kategori
- Rentang harga
- Filter rating
- Rentang tanggal
- Filter buka sekarang
- Layout Horizontal/Vertical/Inline

**Cara Pakai:**
1. Tambahkan widget "Voxel Search Form"
2. Pilih post type
3. Aktifkan field yang diinginkan
4. Pilih layout
5. Kustomisasi button

**Pengaturan:**
- Post Type: Pilih CPT
- Redirect To: URL custom atau archive
- Show Keyword: Ya/Tidak
- Show Location: Ya/Tidak
- Show Categories: Ya/Tidak
- Show Price Range: Ya/Tidak
- Show Rating: Ya/Tidak
- Show Date Range: Ya/Tidak
- Show Open Now: Ya/Tidak
- Form Layout: Horizontal/Vertical/Inline

## Template Library CPT

### Business Directory
Sempurna untuk website direktori dengan Voxel.

**Termasuk:**
- CPT listing dengan lokasi, rating, jam buka
- Taxonomy kategori dan lokasi
- Filter pencarian advanced
- Integrasi map
- Template card Voxel

**Instalasi:**
1. Buka **SOFIR → Library**
2. Klik **Install** pada Business Directory
3. Buka **Elementor** → Tambah widget Voxel
4. Selesai! Direktori Anda siap

### Events & Calendar
Listing event dengan tampilan kalender Voxel.

**Termasuk:**
- CPT event dengan tanggal, kapasitas, lokasi
- Taxonomy kategori
- Tampilan kalender
- Form registrasi
- Tracking RSVP

### Hotel & Accommodation
Sistem booking dengan listing Voxel.

**Termasuk:**
- CPT hotel dengan harga, rating, galeri
- Taxonomy fasilitas
- Form booking
- Map lokasi
- Sistem review

### Restaurant Orders
Menu dan pemesanan restoran dengan Voxel.

**Termasuk:**
- CPT menu dengan harga, galeri
- Taxonomy kategori
- Form order
- Booking meja
- Tracking delivery

### E-Learning Courses
Platform kursus online dengan Voxel.

**Termasuk:**
- CPT course dengan harga, pelajaran
- Taxonomy kategori
- Sistem enrollment
- Tracking progress
- Generate sertifikat

## Filter AJAX

SOFIR menyediakan filter berbasis AJAX tanpa reload halaman.

### API JavaScript

```javascript
// Dengarkan update filter
jQuery('.sofir-voxel-listings').on('sofir:listings:updated', function(e, data) {
    console.log('Total hasil:', data.total);
    console.log('HTML:', data.html);
});

// Akses integrasi Voxel
window.SofirVoxel.init();
```

### Filter PHP

```php
// Kustomisasi pemetaan field
add_filter('sofir/field/meta_config', function($config, $field_key, $post_type) {
    if ($field_key === 'location') {
        $config['voxel_type'] = 'location';
        $config['voxel_searchable'] = true;
    }
    return $config;
}, 10, 3);

// Kustomisasi args CPT untuk Voxel
add_filter('sofir/cpt/register_args', function($args, $slug) {
    $args['voxel_enabled'] = true;
    return $args;
}, 10, 2);
```

## Styling

### Variabel CSS

```css
/* Styling Card Voxel */
.voxel-enabled .sofir-listing-card {
    --voxel-card-bg: #fff;
    --voxel-card-border: #e5e5e5;
    --voxel-card-hover: #f9f9f9;
}

/* Dukungan Dark Mode */
.voxel-enabled.dark-mode .sofir-listing-card {
    background: #1e1e1e;
    color: #e0e0e0;
}
```

### Custom Styling

```css
/* Override style card */
.sofir-listing-card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Custom search form */
.sofir-voxel-search-form {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px;
}
```

## Best Practices

### Performa
- Aktifkan filter AJAX untuk UX lebih baik
- Gunakan pagination untuk dataset besar
- Optimasi gambar untuk card
- Cache template Voxel

### SEO
- Gunakan modul SEO SOFIR
- Tambahkan schema markup
- Optimasi meta field
- Gunakan HTML semantik

### User Experience
- Aktifkan pencarian di halaman archive
- Tambah filter untuk navigasi mudah
- Gunakan map view untuk berbasis lokasi
- Layout responsif mobile

### Aksesibilitas
- Gunakan label ARIA
- Navigasi keyboard
- Dukungan screen reader
- Mode high contrast

## Troubleshooting

### Menu CPT Tidak Muncul
1. Buka **SOFIR → Tools**
2. Klik **Refresh CPT Definitions**
3. Flush permalink di **Settings → Permalinks**

### Field Tidak Dipetakan
1. Cek tipe field di SOFIR
2. Verifikasi konfigurasi field Voxel
3. Clear cache
4. Simpan ulang post type

### AJAX Tidak Bekerja
1. Cek console browser untuk error
2. Verifikasi keamanan nonce
3. Cek AJAX URL
4. Test dengan dev tools browser

### Template Voxel Tidak Loading
1. Verifikasi Voxel theme aktif
2. Cek assignment template
3. Clear cache Elementor
4. Simpan ulang template Elementor

## Kompatibilitas

### Voxel Theme
- ✅ Semua versi Voxel didukung
- ✅ Template Voxel kompatibel
- ✅ Filter Voxel terintegrasi
- ✅ Pencarian Voxel kompatibel

### Elementor
- ✅ Elementor Free
- ✅ Elementor Pro
- ✅ Theme Builder
- ✅ Popup Builder

### E-Commerce
- ✅ WooCommerce
- ✅ Easy Digital Downloads
- ✅ North Commerce
- ✅ Multi-Vendor

## Penggunaan Advanced

### Custom Post Types

```php
// Register CPT custom dengan dukungan Voxel
$cpt_manager = \Sofir\Cpt\Manager::instance();
$cpt_manager->save_post_type([
    'slug' => 'property',
    'singular' => 'Property',
    'plural' => 'Properties',
    'fields' => ['location', 'price', 'gallery'],
    'filters' => ['location', 'price'],
    'taxonomies' => ['property_type'],
]);

// Otomatis kompatibel dengan Voxel!
```

### Custom Widgets

```php
// Buat widget Elementor custom untuk Voxel
class Custom_Voxel_Widget extends \Sofir\Elementor\Widgets\Voxel_Listings {
    public function get_name() {
        return 'custom-voxel-widget';
    }
    
    // Override methods sesuai kebutuhan
}
```

### REST API

```php
// Dapatkan CPT kompatibel Voxel
GET /wp-json/sofir/v1/cpt/voxel

// Response:
{
    "listing": {
        "voxel_enabled": true,
        "voxel_templates": ["single", "archive", "card"],
        "fields": [...]
    }
}
```

## Dukungan

### Dokumentasi
- **English**: `/docs/voxel-integration.md`
- **Indonesian**: `/docs/integrasi-voxel.md`
- **Panduan Developer**: `/docs/voxel-api.md`

### Resources
- Demo: https://demo.sofir.id/voxel
- Video Tutorial: Segera hadir
- Forum Komunitas: https://forum.sofir.id

### Bantuan
1. Cek dokumentasi terlebih dahulu
2. Cari di forum komunitas
3. Kirim tiket support
4. Email: support@sofir.id

## Changelog

### Versi 1.0.0 (2024-01-15)
- ✅ Integrasi Voxel awal
- ✅ 2 widget Elementor baru
- ✅ Sistem pemetaan field otomatis
- ✅ Filter AJAX
- ✅ Autocomplete lokasi
- ✅ Dukungan template Voxel
- ✅ 11 template CPT kompatibel
- ✅ Dokumentasi lengkap

## Lisensi

SOFIR Voxel Integration adalah bagian dari Plugin SOFIR.
Dilisensikan di bawah GPL v3 atau lebih baru.

---

**Dibuat dengan ❤️ oleh Tim SOFIR**

Untuk informasi lebih lanjut, kunjungi: https://sofir.id
