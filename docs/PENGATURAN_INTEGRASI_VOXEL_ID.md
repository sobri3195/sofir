# Pengaturan Integrasi Voxel - Admin UI

## Gambaran Umum

Plugin SOFIR sekarang memiliki interface admin yang lengkap untuk mengkonfigurasi integrasi Voxel Theme pada setiap CPT (Custom Post Type). Ini memungkinkan Anda untuk menyesuaikan cara kerja setiap Custom Post Type dengan fitur-fitur powerful dari Voxel.

## Mengakses Pengaturan Voxel

1. Buka **SOFIR → Voxel** di admin WordPress Anda
2. Anda akan melihat daftar semua Custom Post Type yang terdaftar
3. Klik **Configure** pada CPT untuk mengakses pengaturan integrasi Voxel

## Fitur-Fitur

### 1. Enable/Disable Integration

Toggle integrasi Voxel on atau off untuk setiap CPT. Ketika diaktifkan, CPT akan:
- Auto-map field ke tipe field Voxel
- Support template Voxel
- Mengaktifkan search dan filter advanced

### 2. Pengaturan Post Type

Konfigurasi setting inti Voxel post type:

- **Voxel Post Type Key**: Identifier unik untuk post type di Voxel
- **Voxel Icon**: Icon yang ditampilkan di interface Voxel (contoh: `location-alt`, `calendar-alt`)
- **Enable Search**: Toggle fungsi search advanced
- **Enable Map**: Toggle tampilan map untuk post dengan data lokasi

### 3. Field Mapping

Map field custom SOFIR ke tipe field Voxel untuk kompatibilitas penuh:

| Tipe Field SOFIR | Tipe Field Voxel | Tampil di Card | Tampil di Single | Dapat Dicari |
|------------------|------------------|----------------|------------------|--------------|
| location         | location         | ✓              | ✓                | ✓            |
| hours            | work-hours       | ✓              | ✓                | ✓            |
| rating           | number           | ✓              | ✓                | ✓            |
| status           | select           | ✓              | ✓                | ✓            |
| price            | number           | ✓              | ✓                | ✓            |
| contact          | email            | ✓              | ✓                | -            |
| gallery          | image            | ✓              | ✓                | -            |
| attributes       | repeater         | -              | ✓                | ✓            |

**Auto-mapping** diaktifkan secara default berdasarkan tipe field SOFIR.

### 4. Template Elementor

Assign template Elementor spesifik untuk berbagai jenis halaman:

- **Archive Page**: Template untuk archive post type (tampilan listing)
- **Single Page**: Template untuk tampilan post individual
- **Add New Page**: Template untuk form submit post
- **Card Design**: Template untuk card post di grid/list
- **Login Page**: Template login custom
- **Header**: Template header custom
- **Footer**: Template footer custom
- **Order Page**: Template untuk halaman order/booking (jika applicable)
- **Dashboard Page**: Template untuk dashboard user

Setiap template dapat di-edit langsung dengan Elementor melalui tombol **Edit with Elementor**.

### 5. Filter Advanced

Aktifkan filter spesifik di form pencarian Voxel:

- **Keyword Search**: Pencarian berbasis teks
- **Location Filter**: Filter lokasi geografis
- **Category Filter**: Filter berbasis taxonomy
- **Price Range**: Filter range harga min/max
- **Rating Filter**: Filter rating bintang
- **Date Range**: Filter berbasis tanggal
- **Open Now Filter**: Filter ketersediaan real-time

### 6. Pengaturan Notifikasi

Konfigurasi notifikasi email untuk user dan admin:

#### Notifikasi User
- **New Post Published**: Notifikasi user ketika post mereka live
- **Post Status Change**: Notifikasi user tentang update status

#### Notifikasi Admin
- **New Post Submitted**: Notifikasi admin tentang submission baru

### 7. Pengaturan User Role

Kontrol role user mana yang dapat create dan edit post dari frontend:

- Administrator
- Editor
- Author
- Contributor
- Subscriber
- Custom roles

## Cara Kerja

### 1. Instalasi

Pengaturan integrasi Voxel otomatis tersedia di tab SOFIR → Voxel. Tidak perlu setup tambahan.

### 2. Proses Konfigurasi

1. **Navigate** ke SOFIR → Voxel
2. **Pilih** Custom Post Type
3. **Enable** Voxel Integration
4. **Configure** setting post type
5. **Map** field ke tipe Voxel
6. **Assign** template Elementor
7. **Enable** filter yang diinginkan
8. **Set up** notifikasi
9. **Configure** permission user role
10. **Save** settings

### 3. Auto-Detection

Ketika Voxel theme aktif, SOFIR otomatis:
- Mendeteksi instalasi Voxel
- Menampilkan notice compatibility
- Mengaktifkan fitur integrasi
- Map field menggunakan default yang intelligent

### 4. Konfigurasi Per-CPT

Setiap Custom Post Type memiliki setting independent, memungkinkan Anda untuk:
- Mix post type Voxel dan non-Voxel
- Gunakan template berbeda per post type
- Konfigurasi kombinasi filter unik
- Set notifikasi spesifik per post type

## Visibility Menu CPT

Jika menu Custom Post Type Anda tidak muncul di sidebar admin WordPress, gunakan tab **Tools**:

1. Buka **SOFIR → Tools**
2. Klik **Refresh CPT Definitions**
3. Ini akan:
   - Update semua setting visibility CPT
   - Enable akses frontend
   - Flush rewrite rules
   - Apply CPT Fix v1.0.6

## Kompatibilitas

### Post Type yang Didukung

Semua SOFIR post type kompatibel dengan Voxel, termasuk:

**Seed CPT:**
- listing (Business Directory)
- profile (User Profiles)
- article (Blog/News)
- event (Events & Calendar)
- appointment (Appointments)

**CPT dari Library Template:**
- hotel (Hotel & Accommodation)
- restaurant_order (Restaurant Orders)
- menu_item (Restaurant Menu)
- vehicle (Car Rental)
- forum_topic (Community Forum)
- doctor (Doctor Appointments)
- course (E-Learning Courses)
- lesson (Course Lessons)
- vendor_store (Marketplace Stores)
- vendor_product (Marketplace Products)

### Fitur Voxel

Bekerja seamless dengan fitur Voxel:
- Search dan filter advanced
- Map views
- Widget Elementor
- Frontend submission
- User dashboard
- Review system
- Booking system
- Integrasi membership

## Best Practices

### 1. Assignment Template

- Buat template Elementor dedicated untuk setiap post type
- Gunakan dynamic tag Voxel untuk display field
- Test responsive layout
- Optimasi untuk mobile device

### 2. Field Mapping

- Review field yang auto-mapped
- Adjust setting visibility per field
- Enable search hanya untuk field yang filterable
- Gunakan tipe field Voxel yang sesuai

### 3. Konfigurasi Filter

- Enable hanya filter yang diperlukan
- Terlalu banyak filter dapat membingungkan user
- Test kombinasi filter
- Pertimbangkan pengalaman mobile

### 4. Performance

- Batasi jumlah field yang searchable
- Gunakan caching jika memungkinkan
- Optimasi query map
- Monitor beban server

## Troubleshooting

### Menu CPT Tidak Muncul

**Solusi:** Buka SOFIR → Tools → Refresh CPT Definitions

### Field Tidak Tampil di Voxel

**Solusi:** Cek field mapping di SOFIR → Voxel → [Post Type] → Field Mapping

### Template Tidak Berfungsi

**Solusi:** 
1. Verifikasi assignment template di setting Voxel
2. Cek status template Elementor (published)
3. Pastikan tipe template sesuai dengan penggunaan

### Pencarian Tidak Berfungsi

**Solusi:**
1. Enable "Enable Search" di setting post type
2. Tandai field sebagai searchable di field mapping
3. Enable filter yang diinginkan di section Advanced Filters

## Referensi Developer

### Struktur Settings yang Disimpan

Settings disimpan di tabel options WordPress:

```php
$settings = get_option( 'sofir_voxel_' . $cpt_slug . '_settings', [] );
```

Struktur:
```php
[
    'enabled' => bool,
    'post_type_settings' => [
        'key' => string,
        'icon' => string,
        'search_enabled' => bool,
        'map_enabled' => bool,
    ],
    'field_mapping' => [
        'field_key' => [
            'voxel_type' => string,
            'show_in_card' => bool,
            'show_in_single' => bool,
            'searchable' => bool,
        ],
    ],
    'filters' => [
        'keyword' => bool,
        'location' => bool,
        // ... filter lainnya
    ],
    'templates' => [
        'archive' => int,    // ID template Elementor
        'single' => int,
        // ... template lainnya
    ],
    'notifications' => [
        'user' => [
            'new_post' => bool,
            'status_change' => bool,
        ],
        'admin' => [
            'new_post' => bool,
        ],
    ],
    'user_roles' => array,  // Array slug role
]
```

### Hooks & Filters

```php
// Sebelum menyimpan setting Voxel
do_action( 'sofir/voxel/before_save_settings', $cpt_slug, $settings );

// Setelah menyimpan setting Voxel
do_action( 'sofir/voxel/after_save_settings', $cpt_slug, $settings );

// Modifikasi tipe field Voxel
$types = apply_filters( 'sofir/voxel/field_types', $types );

// Modifikasi daftar template Voxel
$templates = apply_filters( 'sofir/voxel/elementor_templates', $templates );
```

### Akses Programmatic

```php
use Sofir\Admin\VoxelPanel;

// Dapatkan instance
$panel = VoxelPanel::instance();

// Dapatkan setting untuk CPT
$settings = get_option( 'sofir_voxel_listing_settings' );

// Cek apakah integrasi enabled
$enabled = ! empty( $settings['enabled'] );
```

## Riwayat Versi

### v1.0.0 (Current)
- Rilis awal
- Admin UI lengkap untuk integrasi Voxel
- Konfigurasi per-CPT
- System field mapping
- Assignment template
- Konfigurasi filter
- Setting notifikasi
- Manajemen user role

## Support

Untuk support dan pertanyaan:
- Cek dokumentasi di `/docs/VOXEL_INTEGRATION.md`
- Kunjungi SOFIR → Tools untuk troubleshooting CPT
- Pastikan Voxel theme aktif
- Cek versi WordPress dan PHP memenuhi requirement

---

**Plugin:** SOFIR  
**Fitur:** Pengaturan Integrasi Voxel  
**Penulis:** SOFIR Team  
**Terakhir Diupdate:** 2024
