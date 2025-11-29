# Widget Gallery SOFIR untuk Elementor

Solusi gallery profesional lengkap terinspirasi dari tema Moment dan Imagely dengan 20+ gaya tampilan yang memukau.

## 📸 Ikhtisar Widget Gallery

SOFIR menyediakan 4 widget Elementor yang powerful untuk membuat gallery foto, album, slideshow, dan carousel yang menakjubkan:

### 1. Widget Gallery (`sofir-gallery`)
Gallery multi-layout profesional dengan 7 gaya tampilan berbeda.

### 2. Widget Slideshow (`sofir-slideshow`)
Presentasi slideshow klasik dengan berbagai efek transisi.

### 3. Widget Filmstrip Gallery (`sofir-filmstrip-gallery`)
Carousel sinematik dengan gaya filmstrip dan side-scroll.

### 4. Widget Album (`sofir-album`)
Organisasi beberapa gallery menjadi koleksi album yang indah.

---

## 🎨 Widget 1: Gallery

**Lokasi:** Kategori SOFIR Elements  
**Nama Widget:** `sofir-gallery`  
**Ikon:** Gallery Grid

### Fitur

#### 7 Layout Gallery:
1. **Masonry Gallery** - Layout ala Pinterest yang mempertahankan proporsi asli
2. **Mosaic Gallery** - Kolase mulus tanpa celah
3. **Tiled Gallery** - Layout grid terstruktur
4. **Thumbnail Grid** - Grid gambar yang sangat dapat disesuaikan
5. **Film Gallery** - Bingkai elegan di sekitar setiap gambar
6. **Blog Style** - Layout vertikal satu kolom
7. **Image Browser** - Satu gambar besar pada satu waktu

### Pengaturan Widget

#### Tab Content:
- **Add Images** - Pilih beberapa gambar dari perpustakaan media
- **Layout** - Pilih dari 7 opsi layout
- **Columns** - 1-6 kolom (responsif)
- **Gap** - Jarak antar gambar (0-100px)
- **Image Size** - Thumbnail/Medium/Medium Large/Large/Full
- **Enable Lightbox** - Lightbox canggih dengan tampilan fullscreen
- **Show Caption** - Tampilkan caption gambar
- **Show Title** - Tampilkan judul gambar
- **Lazy Load** - Tingkatkan performa dengan lazy loading

#### Pengaturan Lightbox:
- **Enable Social Sharing** - Bagikan gambar di media sosial
- **Enable Fullscreen** - Mode tampilan fullscreen
- **Enable Zoom** - Fitur zoom in/out
- **Enable Autoplay** - Otomatis melewati gambar
- **Autoplay Speed** - Waktu antar slide (ms)
- **Show Image Counter** - Tampilkan posisi saat ini (misal "3/10")
- **Enable Download** - Izinkan download gambar

#### Tab Style:
- **Image Style:**
  - Border Radius - Sudut membulat
  - Border - Border gambar
  - Box Shadow - Efek bayangan
  - Hover Effect - Zoom In/Zoom Out/Grayscale/Blur

### Contoh Penggunaan

```php
// Gunakan di template
echo do_shortcode('[elementor-template id="123"]');
```

### Kelas CSS

```css
.sofir-gallery                    /* Wrapper utama */
.sofir-gallery-masonry           /* Layout masonry */
.sofir-gallery-mosaic            /* Layout mosaic */
.sofir-gallery-tiled             /* Layout tiled */
.sofir-gallery-thumbnail         /* Grid thumbnail */
.sofir-gallery-film              /* Gallery film */
.sofir-gallery-blog              /* Gaya blog */
.sofir-gallery-imagebrowser      /* Image browser */
.sofir-gallery-item              /* Item individual */
.sofir-gallery-overlay           /* Overlay hover */
.sofir-gallery-hover-zoom        /* Efek zoom hover */
```

---

## 🎬 Widget 2: Slideshow

**Lokasi:** Kategori SOFIR Elements  
**Nama Widget:** `sofir-slideshow`  
**Ikon:** Slideshow

### Fitur

- **4 Efek Transisi:** Fade, Slide, Zoom, Flip
- **3 Tipe Pagination:** Dots, Thumbnails, Numbers
- **Autoplay** dengan kecepatan yang dapat dikonfigurasi
- **Navigation Arrows** dengan styling custom
- **Navigasi Keyboard** (tombol Arrow)
- **Touch Swipe** support untuk mobile
- **Pause on Hover** option
- **Loop** pemutaran berkelanjutan
- **Captions** dengan styling custom

### Pengaturan Widget

#### Tab Content:
- **Add Images** - Pemilih gallery
- **Image Size** - Medium/Medium Large/Large/Full

#### Pengaturan Slideshow:
- **Autoplay** - Otomatis maju slide
- **Autoplay Speed** - 1000-10000ms
- **Transition Speed** - 100-2000ms
- **Transition Effect** - Fade/Slide/Zoom/Flip
- **Show Navigation Arrows** - Tombol Previous/Next
- **Show Pagination Dots** - Indikator bawah
- **Pagination Type** - Dots/Thumbnails/Numbers
- **Show Captions** - Tampilkan caption gambar
- **Pause on Hover** - Hentikan autoplay saat hover
- **Loop** - Pemutaran berkelanjutan
- **Keyboard Navigation** - Dukungan tombol arrow
- **Touch Swipe** - Dukungan touch mobile

#### Tab Style:
- **Slideshow Style:**
  - Height - 200-1000px atau 20-100vh
  - Navigation Color - Warna panah
  - Navigation Background - Background panah
  - Pagination Color - Warna dot
  - Pagination Active Color - Warna dot aktif

- **Caption Style:**
  - Text Color - Warna teks
  - Background Color - Warna background
  - Typography - Tipografi
  - Padding - Padding

### Kelas CSS

```css
.sofir-slideshow                 /* Wrapper utama */
.sofir-slideshow-container       /* Kontainer slide */
.sofir-slideshow-item            /* Slide individual */
.sofir-slideshow-nav             /* Tombol navigasi */
.sofir-slideshow-pagination      /* Wrapper pagination */
.sofir-slideshow-caption         /* Overlay caption */
.sofir-slideshow-effect-fade     /* Transisi fade */
.sofir-slideshow-effect-slide    /* Transisi slide */
.sofir-slideshow-effect-zoom     /* Transisi zoom */
.sofir-slideshow-effect-flip     /* Transisi flip */
```

---

## 🎞️ Widget 3: Filmstrip Gallery

**Lokasi:** Kategori SOFIR Elements  
**Nama Widget:** `sofir-filmstrip-gallery`  
**Ikon:** Carousel

### Fitur

- **2 Gaya:** Filmstrip (dengan perforasi), Side Scroll
- **Efek Filmstrip Sinematik** - Border perforasi film asli
- **Horizontal Scrolling** - Navigasi carousel yang mulus
- **Responsif** - Jumlah item berbeda per perangkat
- **Autoplay** dengan pause saat hover
- **Loop** scrolling berkelanjutan
- **Integrasi Lightbox** - Klik untuk memperbesar
- **Captions** tampilan opsional

### Pengaturan Widget

#### Tab Content:
- **Add Images** - Pemilih gallery
- **Style** - Filmstrip/Side Scroll
- **Image Size** - Medium/Medium Large/Large/Full

#### Pengaturan Carousel:
- **Items to Show** - 1-10 item terlihat
- **Items to Scroll** - 1-10 item per navigasi
- **Autoplay** - Auto-scroll diaktifkan
- **Autoplay Speed** - Interval scroll (ms)
- **Scroll Speed** - Durasi animasi (100-2000ms)
- **Show Navigation** - Panah Previous/Next
- **Loop** - Scrolling tak terbatas
- **Pause on Hover** - Berhenti saat mouse hover
- **Enable Lightbox** - Klik untuk lihat fullscreen
- **Show Captions** - Tampilkan caption

#### Responsif:
- **Tablet Items** - Item di tablet (1-8)
- **Mobile Items** - Item di mobile (1-4)

#### Tab Style:
- **Filmstrip Style:**
  - Item Height - 100-800px
  - Item Gap - Jarak 0-50px
  - Border Radius - Sudut membulat
  - Border - Border
  - Filmstrip Effect - Toggle efek perforasi
  - Navigation Position - Top/Center/Bottom
  - Navigation Color - Warna navigasi
  - Navigation Background - Background navigasi

### Kelas CSS

```css
.sofir-filmstrip-gallery         /* Wrapper utama */
.sofir-filmstrip-container       /* Kontainer */
.sofir-filmstrip-track           /* Track scrolling */
.sofir-filmstrip-item            /* Item individual */
.sofir-filmstrip-effect          /* Perforasi filmstrip */
.sofir-filmstrip-nav             /* Tombol navigasi */
.sofir-filmstrip-caption         /* Overlay caption */
```

---

## 📚 Widget 4: Album

**Lokasi:** Kategori SOFIR Elements  
**Nama Widget:** `sofir-album`  
**Ikon:** Photo Library

### Fitur

- **3 Layout:** Grid Album, List Album, Masonry
- **Beberapa Album** - Organisir gallery menjadi album
- **Sub-Album** - Dukungan album bersarang
- **Cover Images** - Pemilihan cover custom atau otomatis
- **Jumlah Gambar** - Tampilkan jumlah foto
- **Deskripsi** - Deskripsi album
- **Integrasi Lightbox** - Lihat semua foto album
- **Efek Hover** - Animasi Lift/Zoom/Fade

### Pengaturan Widget

#### Tab Content:
- **Albums** - Field repeater:
  - Album Title - Nama album
  - Description - Deskripsi album
  - Images - Gallery foto
  - Cover Image - Cover custom (opsional)

- **Layout** - Grid Album/List Album/Masonry
- **Columns** - 1-4 kolom
- **Gap** - Jarak antar album
- **Cover Image Size** - Medium/Medium Large/Large/Full
- **Show Image Count** - Tampilkan badge jumlah foto
- **Show Description** - Tampilkan deskripsi
- **Enable Lightbox** - Klik untuk lihat foto
- **Enable Sub-Albums** - Izinkan album bersarang

#### Tab Style:
- **Album Style:**
  - Background Color - Warna background
  - Border - Border
  - Border Radius - Sudut membulat
  - Box Shadow - Bayangan
  - Padding - Padding
  - Hover Effect - Lift/Zoom/Fade

- **Title Style:**
  - Color - Warna
  - Typography - Tipografi
  - Spacing - Jarak

- **Description Style:**
  - Color - Warna
  - Typography - Tipografi

- **Image Count Style:**
  - Color - Warna
  - Background Color - Warna background
  - Typography - Tipografi

### Kelas CSS

```css
.sofir-album                     /* Wrapper utama */
.sofir-album-grid                /* Layout grid */
.sofir-album-list                /* Layout list */
.sofir-album-masonry             /* Layout masonry */
.sofir-album-item                /* Card album */
.sofir-album-cover               /* Gambar cover */
.sofir-album-count               /* Badge jumlah gambar */
.sofir-album-overlay             /* Overlay hover */
.sofir-album-content             /* Konten teks */
.sofir-album-title               /* Judul album */
.sofir-album-description         /* Deskripsi */
.sofir-album-hover-lift          /* Efek hover lift */
.sofir-album-hover-zoom          /* Efek hover zoom */
```

---

## 🎯 Advanced Lightbox

Semua widget gallery menyertakan lightbox canggih dengan fitur berikut:

### Fitur Lightbox:
- ✅ **Fullscreen View** - Tampilan foto yang imersif
- ✅ **Navigation** - Panah Previous/Next
- ✅ **Kontrol Keyboard** - Tombol arrow, ESC untuk tutup
- ✅ **Image Counter** - Indikator posisi "3/10"
- ✅ **Captions** - Tampilkan caption gambar
- ✅ **Zoom** - Pinch to zoom di mobile
- ✅ **Social Sharing** - Bagikan gambar
- ✅ **Autoplay Slideshow** - Otomatis maju gambar
- ✅ **Download** - Opsi download gambar
- ✅ **Deep Linking** - URL langsung ke gambar
- ✅ **Responsif** - Bekerja di semua perangkat

### Shortcut Keyboard Lightbox:
- `←` Panah Kiri - Gambar sebelumnya
- `→` Panah Kanan - Gambar berikutnya
- `ESC` - Tutup lightbox

### Kelas CSS

```css
.sofir-lightbox                  /* Overlay lightbox */
.sofir-lightbox-content          /* Wrapper konten */
.sofir-lightbox-image            /* Gambar utama */
.sofir-lightbox-nav              /* Panah navigasi */
.sofir-lightbox-close            /* Tombol tutup */
.sofir-lightbox-caption          /* Teks caption */
.sofir-lightbox-counter          /* Counter gambar */
```

---

## 📱 Desain Responsif

Semua widget gallery sepenuhnya responsif:

### Breakpoint:
- **Desktop:** Kolom penuh (1024px+)
- **Tablet:** Kolom dikurangi (768px-1023px)
- **Mobile:** 1-2 kolom (di bawah 768px)

### Fitur Mobile:
- Navigasi touch swipe
- Ukuran gambar dioptimalkan
- Kontrol navigasi responsif
- Layout full-width di layar kecil

---

## 🎨 Kustomisasi

### CSS Custom:

```css
/* Efek hover gallery custom */
.sofir-gallery-item:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

/* Tinggi slideshow custom */
.sofir-slideshow {
    height: 80vh;
}

/* Ukuran item filmstrip custom */
.sofir-filmstrip-item {
    width: 400px;
    height: 300px;
}

/* Gaya card album custom */
.sofir-album-item {
    border: 2px solid #0073aa;
    border-radius: 15px;
}
```

### Hook JavaScript:

```javascript
// Inisialisasi gallery dengan pengaturan custom
document.addEventListener('DOMContentLoaded', function() {
    const gallery = document.querySelector('.sofir-gallery');
    if (gallery) {
        // Inisialisasi custom
    }
});
```

---

## 🚀 Performa

### Fitur Optimasi:
- ✅ **Lazy Loading** - Muat gambar sesuai kebutuhan
- ✅ **Optimasi Gambar** - Ukuran gambar WordPress
- ✅ **CSS Grid/Flexbox** - Layout modern
- ✅ **JavaScript Minimal** - Vanilla JS, tanpa dependensi
- ✅ **Animasi Efisien** - Transform yang dipercepat hardware

### Best Practice:
1. Gunakan ukuran gambar yang sesuai (jangan gunakan "Full" kecuali diperlukan)
2. Aktifkan lazy loading untuk gallery besar
3. Optimalkan gambar sebelum upload
4. Gunakan format WebP jika memungkinkan
5. Batasi kecepatan autoplay ke interval yang wajar

---

## 🛠️ Referensi Developer

### Registrasi Widget:

```php
// widgets/gallery.php
namespace Sofir\Elementor\Widgets;

class Gallery extends BaseWidget {
    public function get_name() {
        return 'sofir-gallery';
    }
    
    public function get_categories() {
        return [ 'sofir' ];
    }
}
```

### Enqueue Asset:

```php
// modules/elementor/manager.php
public function enqueue_frontend_styles(): void {
    wp_enqueue_style(
        'sofir-gallery',
        SOFIR_PLUGIN_URL . 'assets/css/gallery.css',
        [],
        SOFIR_VERSION
    );
}

public function enqueue_frontend_scripts(): void {
    wp_enqueue_script(
        'sofir-gallery',
        SOFIR_PLUGIN_URL . 'assets/js/gallery.js',
        [],
        SOFIR_VERSION,
        true
    );
}
```

### File Widget:
- `/modules/elementor/widgets/gallery.php` - Widget gallery
- `/modules/elementor/widgets/slideshow.php` - Widget slideshow
- `/modules/elementor/widgets/filmstrip-gallery.php` - Widget filmstrip
- `/modules/elementor/widgets/album.php` - Widget album
- `/modules/elementor/base-widget.php` - Kelas widget base
- `/modules/elementor/manager.php` - Registrasi widget

### File Asset:
- `/assets/css/gallery.css` - Style gallery (920 baris)
- `/assets/js/gallery.js` - Fungsi gallery (590 baris)

---

## 📖 Contoh Penggunaan

### Contoh 1: Portfolio Fotografi

```
1. Tambahkan widget Gallery ke halaman
2. Pilih layout "Masonry Gallery"
3. Upload 20-30 gambar
4. Set kolom ke 3
5. Aktifkan lightbox dengan social sharing
6. Pilih efek hover "Zoom In"
```

### Contoh 2: Showcase Produk

```
1. Tambahkan widget Slideshow
2. Upload gambar produk
3. Set efek transisi "Fade"
4. Aktifkan thumbnail pagination
5. Set autoplay ke 4000ms
6. Tampilkan caption dengan nama produk
```

### Contoh 3: Album Foto Pernikahan

```
1. Tambahkan widget Album
2. Buat album: "Ceremony", "Reception", "Portraits"
3. Upload foto ke setiap album
4. Set layout "Grid Album" dengan 3 kolom
5. Aktifkan lightbox
6. Tambahkan deskripsi ke setiap album
```

### Contoh 4: Gallery Event

```
1. Tambahkan widget Filmstrip Gallery
2. Pilih gaya "Filmstrip"
3. Upload foto event
4. Set items to show: 4
5. Aktifkan filmstrip effect
6. Aktifkan autoplay dengan pause on hover
```

---

## 🔧 Troubleshooting

### Gambar Tidak Muncul:
- Cek permission file
- Verifikasi URL gambar benar
- Pastikan gambar diupload ke media library

### Lightbox Tidak Bekerja:
- Cek konflik JavaScript
- Verifikasi gallery.js ter-enqueue
- Cek browser console untuk error

### Masalah Layout:
- Bersihkan cache Elementor
- Regenerate CSS
- Cek konflik CSS tema

### Masalah Performa:
- Aktifkan lazy loading
- Optimalkan ukuran gambar
- Kurangi kecepatan autoplay
- Batasi jumlah gambar per halaman

---

## 📊 Perbandingan Widget

| Fitur | Gallery | Slideshow | Filmstrip | Album |
|-------|---------|-----------|-----------|-------|
| Beberapa Layout | 7 | 4 efek | 2 gaya | 3 layout |
| Lightbox | ✅ | ❌ | ✅ | ✅ |
| Autoplay | ❌ | ✅ | ✅ | ❌ |
| Caption | ✅ | ✅ | ✅ | ✅ |
| Navigation | ❌ | ✅ | ✅ | ❌ |
| Responsif | ✅ | ✅ | ✅ | ✅ |
| Efek Hover | ✅ | ❌ | ❌ | ✅ |
| Sub-Album | ❌ | ❌ | ❌ | ✅ |
| Touch Swipe | ❌ | ✅ | ❌ | ❌ |

---

## 🎓 Sumber Inspirasi

Widget ini terinspirasi dari solusi gallery terkemuka di industri:

### Tema Moment (Priority Vision)
- Layout fotografi profesional
- Transisi elegan
- Desain siap portfolio

### Imagely
- Fitur lightbox canggih
- 20+ layout gallery
- Fokus fotografi profesional
- Performa dioptimalkan

### Fitur Utama yang Diimplementasikan:
- ✅ Masonry Gallery
- ✅ Mosaic Gallery
- ✅ Tiled Gallery
- ✅ Thumbnail Grid
- ✅ Film Gallery
- ✅ Filmstrip Gallery
- ✅ Side Scroll Gallery
- ✅ Slideshow Gallery
- ✅ Blog Style Gallery
- ✅ Image Browser Gallery
- ✅ Grid Album
- ✅ List Album
- ✅ Advanced Lightbox

---

## 📝 Changelog

### Versi 1.0.0 (Saat Ini)
- ✅ Rilis awal
- ✅ 4 widget gallery
- ✅ 7 layout gallery
- ✅ Advanced lightbox
- ✅ Desain responsif penuh
- ✅ Dukungan touch/swipe
- ✅ Navigasi keyboard
- ✅ Performa dioptimalkan

---

## 🤝 Dukungan

Untuk masalah atau pertanyaan tentang widget gallery:
1. Cek bagian troubleshooting
2. Tinjau contoh penggunaan
3. Cek browser console untuk error
4. Hubungi dukungan SOFIR

---

**Dibuat dengan ❤️ oleh Plugin SOFIR**  
Solusi Gallery Profesional untuk WordPress & Elementor
