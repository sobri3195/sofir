# Panduan Blok Gutenberg SOFIR

Dokumentasi lengkap 40 blok Gutenberg SOFIR dalam Bahasa Indonesia.

## Daftar Isi

1. [Blok Inti](#blok-inti) (28 blok)
2. [Blok Tambahan](#blok-tambahan) (12 blok)
3. [Cara Penggunaan](#cara-penggunaan)
4. [Contoh Implementasi](#contoh-implementasi)

---

## Blok Inti

### 1. Action (`sofir/action`)
Tombol call-to-action yang dapat dikustomisasi.

**Atribut:**
- `actionLabel` - Teks tombol (default: 'Click Me')
- `actionUrl` - URL tujuan
- `actionClass` - CSS class tambahan

**Contoh:**
```html
<!-- wp:sofir/action {"actionLabel":"Daftar Sekarang","actionUrl":"/signup"} /-->
```

---

### 2. Cart Summary (`sofir/cart-summary`)
Ringkasan keranjang belanja dengan item dan total.

**Kegunaan:** Halaman checkout, sidebar keranjang

---

### 3. Countdown (`sofir/countdown`)
Timer hitung mundur ke tanggal tertentu.

**Atribut:**
- `targetDate` - Tanggal target (format ISO)
- `format` - Format tampilan (dhms = hari:jam:menit:detik)

**Contoh:**
```html
<!-- wp:sofir/countdown {"targetDate":"2024-12-31T23:59:59"} /-->
```

---

### 4. Create Post (`sofir/create-post`)
Form untuk membuat postingan dari frontend (perlu login).

**Atribut:**
- `postType` - Jenis post (default: 'post')
- `buttonLabel` - Teks tombol submit

**Kegunaan:** Dashboard user, halaman submit konten

---

### 5. Dashboard (`sofir/dashboard`)
Dashboard user dengan statistik dan aktivitas terbaru.

**Atribut:**
- `title` - Judul dashboard
- `showStats` - Tampilkan statistik (true/false)
- `showRecent` - Tampilkan post terbaru (true/false)

**Fitur:**
- Jumlah post user
- Jumlah komentar
- Tanggal registrasi
- Post terbaru
- Link ke profil dan buat post

---

### 6. Gallery (`sofir/gallery`)
Galeri gambar dengan layout grid responsif.

**Atribut:**
- `imageIds` - Array ID gambar
- `columns` - Jumlah kolom (default: 3)

**Contoh:**
```html
<!-- wp:sofir/gallery {"imageIds":[123,456,789],"columns":4} /-->
```

---

### 7. Login Register (`sofir/login-register`)
Form login dan registrasi gabungan dengan tab.

**Atribut:**
- `showRegister` - Tampilkan tab register (true/false)
- `redirectUrl` - URL redirect setelah login

**Fitur Khusus:**
- Mendukung registrasi dengan nomor HP saja
- Tab switcher antara login dan register
- Redirect URL kustom

---

### 8. Map (`sofir/map`)
Peta interaktif dengan marker lokasi.

**Atribut:**
- `postType` - Post type untuk lokasi (default: 'listing')
- `zoom` - Level zoom peta (default: 12)
- `height` - Tinggi peta (default: '400px')

**Kegunaan:** Directory, listing lokasi, peta bisnis

---

### 9. Messages (`sofir/messages`)
Sistem pesan antar user (perlu login).

**Fitur:**
- Daftar pesan
- Form kirim pesan
- Update real-time

---

### 10. Navbar (`sofir/navbar`)
Menu navigasi responsif dengan mobile toggle.

**Atribut:**
- `menuId` - ID menu WordPress
- `mobileBreakpoint` - Breakpoint mobile (default: 768px)

---

### 11. Order (`sofir/order`)
Tampilan detail pesanan (perlu login).

**Atribut:**
- `orderId` - ID pesanan

**Kegunaan:** Halaman order, riwayat pembelian

---

### 12. Popup Kit (`sofir/popup-kit`)
Modal popup dengan tombol trigger.

**Atribut:**
- `triggerText` - Teks tombol trigger
- `popupTitle` - Judul popup
- `popupContent` - Konten HTML popup

**Contoh Penggunaan:**
- Formulir pop-up
- Detail produk
- Video lightbox
- Newsletter signup

---

### 13. Post Feed (`sofir/post-feed`)
Tampilan daftar post dengan layout grid atau list.

**Atribut:**
- `postType` - Jenis post (default: 'post')
- `postsPerPage` - Jumlah post (default: 10)
- `layout` - Layout (grid/list)

**Fitur:**
- Thumbnail gambar
- Judul dan excerpt
- Link ke post
- Support custom post type

---

### 14. Print Template (`sofir/print-template`)
Tombol print untuk halaman.

**Kegunaan:** Invoice, sertifikat, dokumen

---

### 15. Product Form (`sofir/product-form`)
Form submit produk dari frontend.

**Field:**
- Nama produk
- Deskripsi
- Harga

---

### 16. Product Price (`sofir/product-price`)
Tampilan harga produk dengan mata uang.

**Atribut:**
- `productId` - ID post produk
- `showCurrency` - Tampilkan simbol mata uang (true/false)

---

### 17. Quick Search (`sofir/quick-search`)
Pencarian instan dengan AJAX.

**Atribut:**
- `postType` - Post type yang dicari
- `placeholder` - Teks placeholder

**Fitur:**
- Hasil real-time
- Tidak perlu reload halaman
- Dropdown hasil pencarian

---

### 18. Review Stats (`sofir/review-stats`)
Statistik review dengan rating bintang.

**Atribut:**
- `postId` - ID post (default: post saat ini)

**Tampilan:**
- Rating rata-rata
- Bintang visual
- Jumlah review

---

### 19. Ring Chart (`sofir/ring-chart`)
Grafik donat untuk visualisasi data.

**Atribut:**
- `data` - Array data chart
- `title` - Judul chart

---

### 20. Sales Chart (`sofir/sales-chart`)
Grafik garis untuk data penjualan.

**Atribut:**
- `period` - Periode (week/month/year)
- `title` - Judul chart

---

### 21. Search Form (`sofir/search-form`)
Form pencarian dengan filter lanjutan.

**Atribut:**
- `postType` - Post type
- `advancedFilters` - Aktifkan filter taksonomi (true/false)

**Fitur:**
- Pencarian teks
- Filter kategori/tags
- Submit ke hasil pencarian

---

### 22. Slider (`sofir/slider`)
Slider gambar dengan autoplay.

**Atribut:**
- `slides` - Array data slide
- `autoplay` - Autoplay aktif (true/false)
- `interval` - Interval autoplay (ms)

**Fitur:**
- Navigasi prev/next
- Caption slide
- Responsive

---

### 23. Term Feed (`sofir/term-feed`)
Daftar term dari taksonomi.

**Atribut:**
- `taxonomy` - Nama taksonomi (default: 'category')
- `limit` - Jumlah term (default: 10)

**Tampilan:**
- Nama term
- Jumlah post
- Link ke archive

---

### 24. Timeline (`sofir/timeline`)
Timeline vertikal atau horizontal.

**Atribut:**
- `items` - Array item timeline
- `orientation` - Layout (vertical/horizontal)

**Struktur Item:**
- `date` - Tanggal/tahun
- `title` - Judul event
- `content` - Deskripsi

---

### 25. Timeline Style Kit (`sofir/timeline-style-kit`)
Preset styling untuk timeline.

**Atribut:**
- `stylePreset` - Preset style (modern/classic/minimal)
- `colorScheme` - Skema warna

---

### 26. User Bar (`sofir/user-bar`)
Bar profil user dengan login/logout.

**Tampilan:**
- Avatar user (jika login)
- Nama user
- Link profil
- Link logout
- Tombol login (jika belum login)

---

### 27. Visit Chart (`sofir/visit-chart`)
Grafik bar untuk statistik pengunjung.

**Atribut:**
- `period` - Periode data
- `title` - Judul chart

---

### 28. Work Hours (`sofir/work-hours`)
Jam operasional dengan status buka/tutup.

**Atribut:**
- `postId` - ID post (default: post saat ini)
- `showStatus` - Tampilkan status (true/false)

**Fitur:**
- Tabel jam per hari
- Status "Buka Sekarang" / "Tutup"
- Auto-detect waktu saat ini

**Data Meta:**
Field `sofir_work_hours` berisi:
```php
[
    'monday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
    'tuesday' => ['open' => '09:00', 'close' => '17:00', 'closed' => false],
    // dst...
]
```

---

## Blok Tambahan

### 29. Testimonial Slider (`sofir/testimonial-slider`)
Carousel testimonial dengan rating bintang.

**Atribut:**
- `autoplay` - Autoplay (true/false)
- `interval` - Interval (ms)
- `showRating` - Tampilkan rating (true/false)
- `postType` - Post type (default: 'testimonial')
- `numberOfItems` - Jumlah item

**Meta Field:**
- `sofir_rating` - Rating 1-5 bintang
- `sofir_author` - Nama pemberi testimonial
- `sofir_position` - Jabatan/posisi

---

### 30. Pricing Table (`sofir/pricing-table`)
Tabel perbandingan paket harga.

**Atribut:**
- `columns` - Jumlah kolom (default: 3)
- `showFeatures` - Tampilkan fitur (true/false)
- `highlightBest` - Highlight paket terbaik (true/false)

**Meta Field:**
- `sofir_price` - Harga
- `sofir_period` - Periode (bulan/tahun)
- `sofir_features` - Array fitur
- `sofir_button_text` - Teks tombol
- `sofir_button_url` - URL tombol
- `sofir_featured` - Paket unggulan (true/false)

**Kegunaan:** Halaman pricing, membership

---

### 31. Team Grid (`sofir/team-grid`)
Grid anggota tim dengan foto dan sosial media.

**Atribut:**
- `columns` - Jumlah kolom
- `postType` - Post type (default: 'team_member')
- `numberOfItems` - Jumlah anggota
- `showSocial` - Tampilkan link sosmed (true/false)

**Meta Field:**
- `sofir_position` - Jabatan
- `sofir_twitter` - URL Twitter
- `sofir_linkedin` - URL LinkedIn
- `sofir_email` - Email

---

### 32. FAQ Accordion (`sofir/faq-accordion`)
Accordion untuk FAQ yang bisa expand/collapse.

**Atribut:**
- `postType` - Post type (default: 'faq')
- `numberOfItems` - Jumlah FAQ
- `expandFirst` - Expand item pertama (true/false)

**Struktur Post:**
- Title = Pertanyaan
- Content = Jawaban

**Interaksi:**
- Klik untuk expand/collapse
- Icon + / − 
- Animasi smooth

---

### 33. CTA Banner (`sofir/cta-banner`)
Banner call-to-action dengan gradient.

**Atribut:**
- `title` - Judul banner
- `description` - Deskripsi
- `buttonText` - Teks tombol
- `buttonUrl` - URL tombol
- `backgroundColor` - Warna background
- `textColor` - Warna teks
- `alignment` - Alignment (center/left/right)

**Kegunaan:**
- Halaman landing
- Section CTA
- Banner promosi

---

### 34. Feature Box (`sofir/feature-box`)
Box highlight fitur dengan icon.

**Atribut:**
- `icon` - Icon emoji atau HTML
- `title` - Judul fitur
- `description` - Deskripsi
- `iconPosition` - Posisi icon (top/left)
- `alignment` - Alignment teks

**Contoh:**
```html
<!-- wp:sofir/feature-box {"icon":"🚀","title":"Cepat","description":"Loading super cepat"} /-->
```

---

### 35. Contact Form (`sofir/contact-form`)
Form kontak dengan validasi.

**Atribut:**
- `title` - Judul form
- `showSubject` - Tampilkan field subjek (true/false)
- `showPhone` - Tampilkan field telepon (true/false)
- `submitText` - Teks tombol submit

**Field Form:**
- Nama (wajib)
- Email (wajib)
- Telepon (opsional)
- Subjek (opsional)
- Pesan (wajib)

**Handler AJAX:** `sofir_contact_form`

---

### 36. Social Share (`sofir/social-share`)
Tombol share ke media sosial.

**Atribut:**
- `title` - Judul bagian share
- `platforms` - Array platform ['facebook','twitter', dll]
- `layout` - Layout (horizontal/vertical)

**Platform Didukung:**
- Facebook
- Twitter
- LinkedIn
- WhatsApp
- Pinterest
- Email

---

### 37. Breadcrumb (`sofir/breadcrumb`)
Navigasi breadcrumb trail.

**Atribut:**
- `showHome` - Tampilkan link home (true/false)
- `separator` - Karakter pemisah (default: '/')
- `customClass` - CSS class kustom

---

### 38. Progress Bar (`sofir/progress-bar`)
Progress bar animasi.

**Atribut:**
- `label` - Label progress
- `value` - Nilai progress (0-100)
- `color` - Warna bar
- `showPercentage` - Tampilkan persentase (true/false)

**Kegunaan:**
- Skill bar
- Loading indicator
- Completion status

---

### 39. Appointment Booking (`sofir/appointment-booking`)
Form booking appointment dengan kalender.

**Atribut:**
- `serviceType` - Jenis layanan
- `showCalendar` - Tampilkan kalender (true/false)
- `minDuration` - Durasi minimal (menit)

**Field Form:**
- Nama (wajib)
- Email (wajib)
- Telepon (opsional)
- Tanggal & Waktu (wajib)
- Durasi (wajib)
- Catatan (opsional)

**Handler AJAX:** `sofir_book_appointment`

**Custom Post Type:** `appointment`

**Meta Appointment:**
- `sofir_appointment_datetime` - Tanggal/waktu (ISO 8601)
- `sofir_appointment_duration` - Durasi (menit)
- `sofir_appointment_status` - Status (pending/confirmed/completed/cancelled)
- `sofir_appointment_provider` - ID penyedia layanan
- `sofir_appointment_client` - ID klien

**Hook:**
```php
do_action('sofir/appointment/booked', $appointment_id);
```

---

### 40. Dynamic Data (`sofir/dynamic-data`)
Tampilkan data dinamis dari berbagai sumber.

**Atribut:**
- `source` - Sumber data (default: 'post_meta')
  - `post_meta` - Meta post
  - `post_field` - Field post native (title, content, dll)
  - `user_meta` - Meta user
  - `user_field` - Field user native (display_name, email, dll)
  - `site_option` - Option WordPress
  - `cpt_field` - Field CPT SOFIR
- `key` - Nama field/meta key
- `postId` - ID post (default: post saat ini)
- `userId` - ID user (default: user saat ini)
- `format` - Format output (default: 'text')
  - `text` - Teks biasa
  - `html` - HTML (wp_kses_post)
  - `url` - URL (esc_url)
  - `email` - Link email
  - `phone` - Link telepon
  - `date` - Format tanggal
  - `number` - Format angka
  - `currency` - Format mata uang
  - `image` - Tampilan gambar
  - `array` - Array dipisah koma
  - `json` - JSON encode
- `fallback` - Nilai default jika kosong
- `prefix` - Teks sebelum nilai
- `suffix` - Teks setelah nilai
- `dateFormat` - Format tanggal PHP (default: 'F j, Y')
- `imageSize` - Ukuran gambar WordPress (default: 'medium')

**Contoh Penggunaan:**

Tampilkan harga dari meta:
```html
<!-- wp:sofir/dynamic-data {"source":"post_meta","key":"harga_custom","format":"currency","prefix":"Rp "} /-->
```

Tampilkan email user sebagai link:
```html
<!-- wp:sofir/dynamic-data {"source":"user_field","key":"user_email","format":"email"} /-->
```

Tampilkan tanggal event:
```html
<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"event_date","format":"date","dateFormat":"l, j F Y"} /-->
```

Tampilkan galeri:
```html
<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"gallery","format":"image","imageSize":"large"} /-->
```

Tampilkan info kontak:
```html
<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"contact","format":"json"} /-->
```

**Field CPT yang Didukung:**
- `location` - Alamat, koordinat
- `hours` - Jam operasional
- `rating` - Rating bintang
- `status` - Status operasional
- `price` - Rentang harga
- `contact` - Email, telepon, website
- `gallery` - Array gambar
- `attributes` - Pasangan key-value
- `event_date` - Tanggal/waktu event
- `event_capacity` - Kapasitas maksimal
- `appointment_datetime` - Waktu appointment
- `appointment_duration` - Durasi
- `appointment_status` - Status
- `appointment_provider` - ID penyedia
- `appointment_client` - ID klien

---

## Cara Penggunaan

### Menambahkan Blok di Editor

1. Buka halaman/post di Gutenberg editor
2. Klik tombol "+" untuk menambah blok
3. Cari "SOFIR" di search bar
4. Pilih blok yang diinginkan
5. Konfigurasi atribut di sidebar kanan
6. Publish/Update halaman

### Menggunakan Blok di Template

```php
// Dalam file template WordPress
<?php
echo do_blocks('<!-- wp:sofir/action {"actionLabel":"Klik Sini"} /-->');
?>
```

### Menggunakan Blok di Widget

1. Pergi ke Appearance > Widgets
2. Tambahkan widget "Block"
3. Pilih blok SOFIR
4. Konfigurasi dan simpan

---

## Contoh Implementasi

### 1. Halaman Landing Lengkap

```html
<!-- Navbar -->
<!-- wp:sofir/navbar {"menuId":1} /-->

<!-- Hero Slider -->
<!-- wp:sofir/slider {"slides":[...],"autoplay":true,"interval":5000} /-->

<!-- Section Fitur -->
<!-- wp:heading {"textAlign":"center"} -->
<h2>Fitur Unggulan</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<!-- wp:column -->
<!-- wp:sofir/feature-box {"icon":"🚀","title":"Cepat","description":"Loading super cepat"} /-->
<!-- /wp:column -->

<!-- wp:column -->
<!-- wp:sofir/feature-box {"icon":"🔒","title":"Aman","description":"Keamanan terjamin"} /-->
<!-- /wp:column -->

<!-- wp:column -->
<!-- wp:sofir/feature-box {"icon":"📊","title":"Analytics","description":"Dashboard lengkap"} /-->
<!-- /wp:column -->
<!-- /wp:columns -->

<!-- Pricing Table -->
<!-- wp:heading {"textAlign":"center"} -->
<h2>Paket Harga</h2>
<!-- /wp:heading -->

<!-- wp:sofir/pricing-table {"columns":3,"highlightBest":true} /-->

<!-- Testimonials -->
<!-- wp:heading {"textAlign":"center"} -->
<h2>Testimoni Pelanggan</h2>
<!-- /wp:heading -->

<!-- wp:sofir/testimonial-slider {"numberOfItems":6,"autoplay":true} /-->

<!-- CTA Banner -->
<!-- wp:sofir/cta-banner {"title":"Siap Memulai?","description":"Daftar sekarang dan dapatkan diskon 50%","buttonText":"Daftar Gratis","buttonUrl":"/signup"} /-->

<!-- Contact Form -->
<!-- wp:heading {"textAlign":"center"} -->
<h2>Hubungi Kami</h2>
<!-- /wp:heading -->

<!-- wp:sofir/contact-form {"showPhone":true,"showSubject":true} /-->
```

---

### 2. Halaman Directory / Listing

```html
<!-- Search Bar -->
<!-- wp:sofir/quick-search {"postType":"listing","placeholder":"Cari lokasi, nama, kategori..."} /-->

<!-- Advanced Search -->
<!-- wp:sofir/search-form {"postType":"listing","advancedFilters":true} /-->

<!-- Map View -->
<!-- wp:heading -->
<h2>Peta Lokasi</h2>
<!-- /wp:heading -->

<!-- wp:sofir/map {"postType":"listing","zoom":12,"height":"600px"} /-->

<!-- Listing Grid -->
<!-- wp:heading -->
<h2>Semua Listing</h2>
<!-- /wp:heading -->

<!-- wp:sofir/post-feed {"postType":"listing","postsPerPage":12,"layout":"grid"} /-->

<!-- Categories -->
<!-- wp:heading -->
<h2>Kategori</h2>
<!-- /wp:heading -->

<!-- wp:sofir/term-feed {"taxonomy":"listing_category","limit":20} /-->
```

---

### 3. Halaman Detail Produk/Listing

```html
<!-- Breadcrumb -->
<!-- wp:sofir/breadcrumb {"showHome":true,"separator":"›"} /-->

<!-- Product Gallery -->
<!-- wp:sofir/gallery {"imageIds":[123,456,789,101],"columns":4} /-->

<!-- Price dan Rating -->
<!-- wp:columns -->
<!-- wp:column -->
<!-- wp:sofir/product-price {"showCurrency":true} /-->
<!-- /wp:column -->

<!-- wp:column -->
<!-- wp:sofir/review-stats /-->
<!-- /wp:column -->
<!-- /wp:columns -->

<!-- Dynamic Data Examples -->
<!-- Location -->
<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"location","format":"text","prefix":"📍 "} /-->

<!-- Contact Info -->
<!-- wp:sofir/dynamic-data {"source":"cpt_field","key":"contact","format":"json"} /-->

<!-- Operating Hours -->
<!-- wp:sofir/work-hours {"showStatus":true} /-->

<!-- Appointment Booking -->
<!-- wp:heading -->
<h3>Booking Appointment</h3>
<!-- /wp:heading -->

<!-- wp:sofir/appointment-booking {"serviceType":"consultation","minDuration":60} /-->

<!-- Social Share -->
<!-- wp:sofir/social-share {"platforms":["facebook","twitter","whatsapp","pinterest"]} /-->
```

---

### 4. Dashboard User

```html
<!-- User Bar -->
<!-- wp:sofir/user-bar /-->

<!-- Dashboard Widget -->
<!-- wp:sofir/dashboard {"title":"Dashboard Saya","showStats":true,"showRecent":true} /-->

<!-- Charts -->
<!-- wp:columns -->
<!-- wp:column -->
<!-- wp:sofir/sales-chart {"period":"month","title":"Penjualan Bulan Ini"} /-->
<!-- /wp:column -->

<!-- wp:column -->
<!-- wp:sofir/visit-chart {"period":"week","title":"Pengunjung Minggu Ini"} /-->
<!-- /wp:column -->
<!-- /wp:columns -->

<!-- Create Post Form -->
<!-- wp:heading -->
<h3>Buat Listing Baru</h3>
<!-- /wp:heading -->

<!-- wp:sofir/create-post {"postType":"listing","buttonLabel":"Publikasikan Listing"} /-->

<!-- My Orders -->
<!-- wp:heading -->
<h3>Pesanan Saya</h3>
<!-- /wp:heading -->

<!-- wp:sofir/order /-->
```

---

### 5. Halaman About / Team

```html
<!-- Timeline Sejarah -->
<!-- wp:heading {"textAlign":"center"} -->
<h2>Perjalanan Kami</h2>
<!-- /wp:heading -->

<!-- wp:sofir/timeline {"items":[
    {"date":"2020","title":"Didirikan","content":"Perusahaan didirikan"},
    {"date":"2021","title":"Ekspansi","content":"Buka kantor cabang"},
    {"date":"2022","title":"Award","content":"Menang penghargaan"},
    {"date":"2023","title":"100+ Klien","content":"Capai 100 klien aktif"}
],"orientation":"vertical"} /-->

<!-- Team Grid -->
<!-- wp:heading {"textAlign":"center"} -->
<h2>Tim Kami</h2>
<!-- /wp:heading -->

<!-- wp:sofir/team-grid {"columns":4,"numberOfItems":8,"showSocial":true} /-->

<!-- CTA -->
<!-- wp:sofir/cta-banner {"title":"Tertarik Bergabung?","description":"Kami selalu mencari talenta terbaik","buttonText":"Lihat Lowongan","buttonUrl":"/careers"} /-->
```

---

### 6. Halaman FAQ

```html
<!-- wp:heading {"textAlign":"center"} -->
<h2>Pertanyaan yang Sering Diajukan</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textAlign":"center"} -->
<p>Temukan jawaban untuk pertanyaan umum di bawah ini</p>
<!-- /wp:paragraph -->

<!-- FAQ Accordion -->
<!-- wp:sofir/faq-accordion {"numberOfItems":15,"expandFirst":true} /-->

<!-- CTA untuk Contact -->
<!-- wp:sofir/cta-banner {"title":"Masih Ada Pertanyaan?","description":"Tim support kami siap membantu Anda","buttonText":"Hubungi Kami","buttonUrl":"/contact"} /-->
```

---

## Tips dan Trik

### 1. Kombinasi Blok yang Bagus

**Hero Section:**
```
Slider + CTA Banner
```

**Feature Section:**
```
3 kolom Feature Box + CTA Button (Action block)
```

**Pricing Page:**
```
Pricing Table + FAQ Accordion + CTA Banner
```

**Directory:**
```
Quick Search + Map + Post Feed + Term Feed
```

### 2. Optimasi Performa

- Gunakan lazy load untuk gallery dan slider
- Batasi `postsPerPage` untuk feed yang besar
- Cache data chart dengan transient
- Compress gambar sebelum upload

### 3. Styling Kustom

Tambahkan CSS kustom di theme Anda:

```css
/* Warna brand kustom */
.sofir-action-button {
    background: #your-brand-color;
}

/* Spacing kustom */
.sofir-pricing-table {
    gap: 3em;
}

/* Font kustom */
.sofir-cta-title {
    font-family: 'Your Custom Font', sans-serif;
}
```

### 4. JavaScript Kustom

Extend fungsi blok:

```javascript
// Custom interval slider
jQuery(document).ready(function($) {
    $('.sofir-slider').attr('data-interval', 3000);
});

// Event listener
document.addEventListener('sofir:block:updated', function(e) {
    console.log('Blok diupdate:', e.detail.block);
});
```

---

## Troubleshooting

### Blok Tidak Muncul

1. Cek apakah plugin SOFIR aktif
2. Clear cache WordPress
3. Regenerate block assets
4. Cek console browser untuk error JavaScript

### Styling Tidak Sesuai

1. Cek apakah theme support Gutenberg
2. Enqueue style blok: `wp_enqueue_style('sofir-blocks')`
3. Clear browser cache
4. Cek theme CSS yang mungkin override

### AJAX Tidak Berfungsi

1. Verify nonce: `sofir_blocks`
2. Cek AJAX URL: `sofirBlocks.ajaxUrl`
3. Enable WP_DEBUG untuk lihat error
4. Cek console network tab

### Data Tidak Muncul (Dynamic Data)

1. Pastikan meta key benar
2. Cek apakah post memiliki data di meta field
3. Verify post ID dan user ID
4. Gunakan fallback untuk nilai default

---

## Hook dan Filter untuk Developer

### Action Hooks

```php
// Setelah blok terdaftar
do_action('sofir/blocks/registered');

// Setelah appointment di-booking
do_action('sofir/appointment/booked', $appointment_id);

// Setelah form kontak disubmit
do_action('sofir/contact/submitted', $form_data);
```

### Filter Hooks

```php
// Modifikasi atribut blok
add_filter('sofir/block/attributes', function($attributes, $block_name) {
    // Ubah atribut
    return $attributes;
}, 10, 2);

// Kustomisasi output blok
add_filter('sofir/block/output', function($output, $block_name, $attributes) {
    // Modifikasi HTML output
    return $output;
}, 10, 3);

// Modifikasi data chart
add_filter('sofir/chart/data', function($data, $chart_type) {
    // Custom data
    return $data;
}, 10, 2);
```

---

## Kompatibilitas

### Theme yang Didukung

✅ Twenty Twenty-Four
✅ Astra
✅ GeneratePress
✅ Kadence
✅ Block themes lainnya

### Plugin yang Kompatibel

✅ Yoast SEO
✅ Rank Math
✅ WooCommerce (untuk payment blocks)
✅ Advanced Custom Fields
✅ Polylang / WPML

### Browser yang Didukung

✅ Chrome (latest)
✅ Firefox (latest)
✅ Safari (latest)
✅ Edge (latest)
✅ Mobile browsers

---

## Aksesibilitas

Semua blok mengikuti standar WCAG 2.1 AA:

- ✅ Semantic HTML
- ✅ ARIA labels
- ✅ Keyboard navigation
- ✅ Screen reader friendly
- ✅ Color contrast compliant
- ✅ Focus indicators

---

## Dukungan

Untuk pertanyaan atau issue:
- 📧 Support Email
- 💬 Forum Support
- 📚 Dokumentasi Lengkap
- 🐛 GitHub Issues

---

**Versi:** 1.0.0
**Total Blok:** 40
**Plugin:** SOFIR WordPress Plugin
**Terakhir Diupdate:** 2024
