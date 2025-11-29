# Library Template SOFIR Elementor

Library template profesional untuk Elementor, terinspirasi dari sistem desain Slider Revolution.

## Ringkasan

Library Template SOFIR Elementor menyediakan 40+ template profesional yang sudah jadi dan dapat dengan mudah diimpor ke dalam halaman Elementor. Template ini mencakup semua kebutuhan umum termasuk popup, kartu, halaman, single post, archive, header, dan footer.

## Fitur Utama

### 🎨 Kategori Template

1. **Popup (6 template)**
   - Newsletter Subscription - Form berlangganan email
   - Promotional Offer - Penawaran promosi dengan countdown
   - Exit Intent Offer - Penawaran khusus saat akan keluar
   - Video Presentation - Popup video layar penuh
   - Announcement Banner - Banner pengumuman penting
   - Quick Contact Form - Form kontak cepat

2. **Cards (8 template)**
   - Modern Post Card - Kartu artikel modern
   - Premium Product Card - Kartu produk premium
   - Team Member Card - Kartu anggota tim
   - Minimal Service Card - Kartu layanan minimal
   - Creative Pricing Card - Kartu harga kreatif
   - Testimonial Card - Kartu testimoni
   - Event Card - Kartu acara
   - Portfolio Card - Kartu portfolio

3. **Pages (8 template)**
   - Hero Slider Landing - Landing page dengan hero slider
   - About Company - Halaman tentang perusahaan
   - Services Showcase - Showcase layanan
   - Modern Contact Page - Halaman kontak modern
   - Portfolio Gallery - Galeri portfolio
   - Pricing Plans - Halaman paket harga
   - FAQ Page - Halaman pertanyaan umum
   - Coming Soon - Halaman segera hadir

4. **Single Templates (4 template)**
   - Modern Single Post - Post tunggal modern
   - Magazine Style Post - Post gaya majalah
   - Product Single Layout - Layout produk tunggal
   - Portfolio Item - Item portfolio

5. **Archive Templates (5 template)**
   - Blog Archive Grid - Archive blog grid
   - Blog Archive Masonry - Archive blog masonry
   - Shop Archive - Archive toko
   - Portfolio Archive - Archive portfolio
   - Directory Listing Archive - Archive listing direktori

6. **Headers (3 template)**
   - Transparent Header - Header transparan
   - Header with Top Bar - Header dengan top bar
   - Centered Header - Header tengah

7. **Footers (3 template)**
   - Footer 4 Columns - Footer 4 kolom
   - Footer Minimal - Footer minimal
   - Footer with CTA - Footer dengan CTA

### ✨ Fitur Unggulan

- **Import Satu Klik**: Import template dengan sekali klik
- **Preview Cantik**: Gambar preview besar untuk setiap template
- **Cari & Filter**: Temukan template yang tepat dengan cepat
- **Sistem Tag**: Template diberi tag untuk memudahkan pencarian
- **Desain Responsif**: Semua template full responsive
- **Desain Profesional**: Terinspirasi dari Slider Revolution
- **Background Gradient**: Skema warna gradient modern
- **Efek Hover**: Animasi dan transisi yang halus
- **Integrasi Ikon**: Dukungan penuh Font Awesome

## Cara Menggunakan

### Mengakses Library Template

1. Buka halaman apa saja di editor Elementor
2. Cari tombol SOFIR Templates di panel Elementor (ikon folder)
3. Klik tombol untuk membuka library template

### Menelusuri Template

1. **Browse by Category**: Klik pada tab (Popups, Cards, Pages, dll.)
2. **Search**: Gunakan kotak pencarian untuk menemukan template spesifik
3. **Filter by Tags**: Klik pada tag untuk menemukan template terkait

### Import Template

1. Hover di atas kartu template
2. Klik tombol "Insert" untuk import langsung
3. Atau klik "Preview" untuk melihat preview lebih besar dulu
4. Template akan ditambahkan ke halaman Anda

### Kustomisasi Template

Setelah import:

1. Semua elemen dapat diedit di Elementor
2. Ubah warna, font, gambar, dan teks
3. Tambah atau hapus section sesuai kebutuhan
4. Atur spacing dan layout
5. Simpan sebagai template sendiri untuk digunakan ulang

## Struktur Template

### Template Popup

Template popup mencakup:
- Background menarik (gradient)
- Tombol call-to-action yang jelas
- Countdown timer (popup promosi)
- Integrasi form
- Tombol close
- Pemberitahuan privasi

### Template Card

Template card memiliki:
- Efek hover dan animasi
- Section gambar/ikon
- Area judul dan deskripsi
- Tombol aksi
- Informasi meta (tanggal, penulis, dll.)
- Tag dan kategori

### Template Page

Template halaman penuh mencakup:
- Hero section dengan slider
- Section fitur
- Showcase layanan/produk
- Testimoni
- Section call-to-action
- Area footer

### Template Single Post

Template single post mencakup:
- Header featured image
- Post meta (penulis, tanggal, kategori)
- Area konten
- Box bio penulis
- Tombol social sharing
- Section related posts
- Section komentar

### Template Archive

Template archive memiliki:
- Section header/judul
- Layout post grid/masonry
- Sidebar dengan widget
- Fungsi pencarian
- Filter kategori
- Pagination

## Prinsip Desain

### Skema Warna

Gradient default: `#667eea` → `#764ba2` (gradient ungu)

Gradient lain yang digunakan:
- `#fa709a` → `#fee140` (pink ke kuning)
- `#f093fb` → `#f5576c` (ungu ke merah)

### Tipografi

- **Heading**: Bold, ukuran font besar (weight 900)
- **Body**: Font bersih, mudah dibaca (16-18px)
- **Button**: Bold, uppercase, dengan efek hover

### Spacing

- **Cards**: Border radius 20-25px
- **Section**: Padding vertikal 80px
- **Elemen**: Gap 15-30px

## Detail Teknis

### Struktur File

```
modules/elementor/
├── templates-manager.php          # Class manager utama
├── templates/
│   ├── library.php                # Definisi template
│   └── data/                      # Data template JSON
│       ├── newsletter-popup.json
│       ├── promo-popup.json
│       ├── post-card-modern.json
│       ├── product-card-premium.json
│       ├── hero-slider-page.json
│       ├── blog-archive-grid.json
│       ├── single-post-modern.json
│       └── ...
assets/
├── css/
│   └── elementor-templates.css    # Style library template
└── js/
    └── elementor-templates.js     # JavaScript library template
```

### PHP Class: Templates_Manager

**Lokasi**: `modules/elementor/templates-manager.php`

**Method**:
- `boot()` - Inisialisasi hook
- `register_templates()` - Localize data template
- `enqueue_assets()` - Load CSS dan JS
- `import_template()` - Handle AJAX import
- `get_all_templates()` - Ambil library template
- `get_template_data()` - Load JSON template

### JavaScript: SofirTemplateLibrary

**Lokasi**: `assets/js/elementor-templates.js`

**Method**:
- `init()` - Inisialisasi library
- `openTemplateLibrary()` - Tampilkan modal
- `renderTemplates()` - Display kartu template
- `insertTemplate()` - Import template via AJAX
- `previewTemplate()` - Tampilkan preview penuh
- `filterTemplates()` - Fungsi pencarian

## Kustomisasi

### Menambahkan Template Baru

1. Buat file JSON baru di `modules/elementor/templates/data/`
2. Tambahkan definisi template ke `modules/elementor/templates/library.php`
3. Tambahkan gambar preview ke `assets/images/elementor-templates/`

Contoh definisi template:

```php
[
    'id'          => 'template-saya',
    'title'       => __( 'Template Saya', 'sofir' ),
    'description' => __( 'Deskripsi template', 'sofir' ),
    'preview'     => SOFIR_PLUGIN_URL . 'assets/images/elementor-templates/template-saya.jpg',
    'type'        => 'card',
    'pro'         => false,
    'tags'        => [ 'bisnis', 'kreatif' ],
]
```

### Memodifikasi Style

Edit `assets/css/elementor-templates.css`:

- `.sofir-template-modal` - Container modal utama
- `.sofir-template-card` - Style kartu template
- `.sofir-template-overlay` - Overlay hover
- `.sofir-modal-tabs` - Tab kategori

### Mengubah Warna

Update gradient di `library.php` dan CSS:

```css
background: linear-gradient(135deg, #WARNA_1 0%, #WARNA_2 100%);
```

## Kompatibilitas Browser

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performa

- Template dimuat secara lazy-load
- Gambar preview dioptimalkan
- Import AJAX untuk loading cepat
- Ukuran bundle JavaScript minimal

## Dukungan

Untuk masalah atau pertanyaan:
- Cek syntax JSON template
- Verifikasi Elementor aktif
- Hapus cache browser
- Test dengan tema WordPress default

## Inspirasi

Desain terinspirasi dari:
- Sistem template Slider Revolution
- Tren gradient modern
- Standar desain web profesional
- Interface yang user-friendly

## Pengembangan Masa Depan

Fitur yang direncanakan:
- Lebih banyak kategori template
- Paket template
- Template PRO
- Sinkronisasi template dengan cloud
- Builder template custom
- Export/import template
- Rating template
- Template komunitas

## Log Perubahan

### Versi 1.0.0
- Rilis awal
- 40+ template profesional
- 7 kategori template
- Pencarian dan filter
- Import satu klik
- Fungsi preview
- Desain responsif
- UI gradient modern

## Kredit

- Sistem Desain: Terinspirasi dari Slider Revolution
- Ikon: Font Awesome
- Skema Warna: Tren desain web modern
- Layout: Standar template profesional

## Cara Mengakses Template

### Di Editor Elementor

1. Buka halaman/post di Elementor
2. Klik ikon folder "SOFIR Templates" di panel kiri
3. Pilih kategori template (Popup, Card, Page, dll.)
4. Gunakan pencarian jika perlu
5. Klik "Preview" untuk melihat template
6. Klik "Insert" untuk menambahkan ke halaman

### Tips Penggunaan

1. **Gunakan Template sebagai Starting Point**
   - Import template yang paling dekat dengan kebutuhan
   - Kustomisasi sesuai brand Anda
   - Simpan sebagai template pribadi

2. **Kombinasikan Template**
   - Gunakan header dari satu template
   - Body dari template lain
   - Footer dari template ketiga

3. **Sesuaikan dengan Brand**
   - Ganti warna gradient dengan warna brand
   - Gunakan font konsisten
   - Gunakan gambar/foto sendiri

4. **Optimasi untuk SEO**
   - Ganti semua teks placeholder
   - Tambahkan alt text pada gambar
   - Atur heading hierarchy dengan benar

5. **Test Responsiveness**
   - Preview di berbagai ukuran layar
   - Sesuaikan spacing untuk mobile
   - Test di device sesungguhnya

## Contoh Penggunaan

### Landing Page Bisnis

1. Import "Hero Slider Landing"
2. Import "Services Showcase" untuk section layanan
3. Import "Pricing Card Creative" untuk harga
4. Import "Footer 4 Columns"

### Blog Post

1. Import "Single Post Modern"
2. Sesuaikan warna dengan tema blog
3. Tambahkan sidebar widget
4. Aktifkan related posts

### Toko Online

1. Import "Shop Archive" untuk halaman shop
2. Import "Product Card Premium" untuk showcase
3. Import "Single Product Layout" untuk produk
4. Import "Footer with CTA"

### Website Portofolio

1. Import "Portfolio Gallery Page"
2. Import "Portfolio Card" untuk items
3. Import "Team Member Card" untuk tim
4. Import "Centered Header"

## FAQ

**Q: Apakah template gratis?**
A: Ya, semua template dalam versi 1.0.0 gratis. Template PRO akan datang di versi mendatang.

**Q: Apakah bisa dimodifikasi?**
A: Ya, semua template 100% dapat dikustomisasi di Elementor.

**Q: Apakah responsive?**
A: Ya, semua template sudah responsive untuk desktop, tablet, dan mobile.

**Q: Apakah perlu plugin tambahan?**
A: Hanya perlu Elementor (versi free sudah cukup).

**Q: Bagaimana cara menambah template sendiri?**
A: Ikuti panduan di section "Menambahkan Template Baru" di dokumentasi ini.

**Q: Apakah bisa di-export?**
A: Ya, setelah diimport, template menjadi bagian dari halaman Elementor dan bisa di-export seperti biasa.
