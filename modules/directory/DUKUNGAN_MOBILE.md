# Dokumentasi Dukungan Mobile SOFIR

## Ringkasan

SOFIR Directory menyertakan dukungan mobile lengkap dengan menu mobile responsif dan bottom navigation bar yang dioptimalkan untuk perangkat touch. Fitur ini meningkatkan pengalaman pengguna mobile untuk website berbasis direktori.

## Fitur Utama

### 1. Menu Mobile

Panel navigasi slide-in yang muncul dari sisi kanan layar:

- **Tombol Toggle Hamburger**: Tombol toggle posisi fixed (kanan-atas)
- **Panel Slide-in**: Lebar 80% (maksimal 320px) dengan transisi smooth
- **Integrasi Menu**: Mendukung menu WordPress via ID menu atau theme location
- **Bagian User**: 
  - **Sudah Login**: Menampilkan avatar, nama, dan link logout
  - **Belum Login**: Menampilkan tombol Login dan Register
- **Overlay**: Background overlay semi-transparan
- **Dukungan Keyboard**: Tekan ESC untuk menutup menu
- **Aksesibilitas**: Label ARIA dan HTML semantik

### 2. Bottom Navigation Bar

Navbar fixed di bagian bawah dengan item navigasi yang bisa dikustomisasi:

- **Posisi Fixed**: Selalu terlihat di bagian bawah viewport
- **Auto-Hide**: Sembunyi saat scroll ke bawah, muncul saat scroll ke atas
- **Item Default**:
  - 🏠 **Home**: Link ke homepage
  - 🔍 **Search**: Link ke halaman pencarian
  - ➕ **Add**: Buat posting baru (hanya user yang sudah login)
  - 💬 **Messages**: Halaman pesan (hanya user yang sudah login)
  - 👤 **Profile**: Profil user atau link login
- **Dapat Dikembangkan**: Item custom via action hook `sofir/mobile/bottom_nav_item`
- **Responsif**: Otomatis menambah padding 70px di bagian bawah body
- **Active State**: Highlight otomatis untuk item yang sedang aktif

### 3. Panel Pengaturan

Konfigurasi fitur mobile dari WordPress admin:

- **Enable/Disable**: Toggle dukungan mobile on/off
- **Pilihan Menu**: Pilih menu WordPress mana yang akan ditampilkan
- **Toggle Bottom Nav**: Tampilkan/sembunyikan bottom navigation bar
- **Breakpoint**: Set breakpoint mobile (default: 768px)

## Cara Penggunaan

### Integrasi Otomatis

Fitur mobile otomatis aktif di perangkat mobile ketika pengaturan diaktifkan:

```php
// Otomatis dimuat di perangkat mobile
// Tidak perlu kode tambahan
```

### Shortcode

#### Shortcode Menu Mobile

```php
// Gunakan pengaturan default
[sofir_mobile_menu]

// Tentukan ID menu custom
[sofir_mobile_menu menu_id="123"]
```

#### Shortcode Bottom Navbar

```php
// Gunakan item default (home,search,add,messages,profile)
[sofir_bottom_navbar]

// Item custom
[sofir_bottom_navbar items="home,search,profile"]

// Hanya home dan profile
[sofir_bottom_navbar items="home,profile"]
```

### Integrasi PHP

```php
// Render menu mobile secara programmatik
echo \Sofir\Directory\Mobile::instance()->render_mobile_menu_shortcode();

// Render bottom navbar dengan item custom
echo \Sofir\Directory\Mobile::instance()->render_bottom_navbar_shortcode([
    'items' => 'home,search,messages,profile'
]);

// Dapatkan pengaturan saat ini
$settings = \Sofir\Directory\Mobile::instance()->get_settings();
```

## Kustomisasi

### Menambah Item Bottom Nav Custom

Gunakan action hook `sofir/mobile/bottom_nav_item`:

```php
add_action('sofir/mobile/bottom_nav_item', function($item) {
    if ($item === 'direktori') {
        echo '<a href="/direktori" class="sofir-bottom-nav-item">';
        echo '<span class="sofir-nav-icon">📍</span>';
        echo '<span class="sofir-nav-label">Direktori</span>';
        echo '</a>';
    }
    
    if ($item === 'peta') {
        echo '<a href="/peta" class="sofir-bottom-nav-item">';
        echo '<span class="sofir-nav-icon">🗺️</span>';
        echo '<span class="sofir-nav-label">Peta</span>';
        echo '</a>';
    }
    
    if ($item === 'favorit') {
        echo '<a href="/favorit" class="sofir-bottom-nav-item">';
        echo '<span class="sofir-nav-icon">❤️</span>';
        echo '<span class="sofir-nav-label">Favorit</span>';
        echo '</a>';
    }
}, 10, 1);

// Lalu gunakan: [sofir_bottom_navbar items="direktori,peta,search,favorit,profile"]
```

### Kustomisasi CSS

Override style default di theme Anda:

```css
/* Kustomisasi warna menu mobile */
.sofir-mobile-menu-panel {
    background: #1a1a1a;
    color: #fff;
}

/* Kustomisasi bottom navbar */
.sofir-bottom-navbar {
    background: linear-gradient(to right, #667eea, #764ba2);
}

.sofir-bottom-nav-item {
    color: #fff;
}

/* Kustomisasi tombol primary */
.sofir-bottom-nav-item.sofir-nav-primary {
    background: #ff6b6b;
}

/* Kustomisasi active state */
.sofir-bottom-nav-item.is-current {
    color: #ff6b6b;
}

/* Sesuaikan breakpoint mobile */
@media (max-width: 992px) {
    .sofir-mobile-menu-toggle {
        display: block;
    }
}
```

### Event JavaScript

Listen ke event menu mobile:

```javascript
// Menu mobile dibuka
jQuery(document).on('sofir:mobile-menu:open', function() {
    console.log('Menu mobile dibuka');
});

// Menu mobile ditutup
jQuery(document).on('sofir:mobile-menu:close', function() {
    console.log('Menu mobile ditutup');
});

// Bottom nav disembunyikan
jQuery(document).on('sofir:bottom-nav:hide', function() {
    console.log('Bottom nav disembunyikan');
});

// Bottom nav ditampilkan
jQuery(document).on('sofir:bottom-nav:show', function() {
    console.log('Bottom nav ditampilkan');
});
```

## Pengaturan

### Konfigurasi Admin

1. Navigasi ke **SOFIR > Dashboard > Content**
2. Scroll ke bagian **Mobile Settings**
3. Konfigurasi opsi:
   - ✅ **Enable Mobile Support**: Aktifkan/nonaktifkan fitur mobile
   - 📱 **Mobile Menu**: Pilih menu WordPress yang akan ditampilkan
   - 📍 **Show Bottom Nav**: Toggle bottom navigation bar
   - 📐 **Mobile Breakpoint**: Set breakpoint responsif (default: 768px)
4. Klik **Save Mobile Settings**

### Pengaturan Programmatik

```php
// Dapatkan pengaturan mobile
$mobile = \Sofir\Directory\Mobile::instance();
$settings = $mobile->get_settings();

// Struktur array settings:
[
    'enabled' => true,
    'menu_id' => 123,
    'show_bottom_nav' => true,
    'breakpoint' => 768,
]
```

## Detail Teknis

### Struktur File

```
modules/directory/
├── mobile.php              # Implementasi class Mobile
├── MOBILE_SUPPORT.md      # Dokumentasi bahasa Inggris
└── DUKUNGAN_MOBILE.md     # Dokumentasi bahasa Indonesia (file ini)

assets/
├── css/
│   └── mobile.css         # Style mobile
└── js/
    └── mobile.js          # JavaScript mobile
```

### Referensi Class

**Namespace**: `Sofir\Directory\Mobile`

**Method**:

```php
// Dapatkan instance singleton
Mobile::instance(): Mobile

// Inisialisasi hooks dan actions
boot(): void

// Dapatkan pengaturan saat ini
get_settings(): array

// Enqueue asset mobile
enqueue_mobile_assets(): void

// Render HTML menu mobile
render_mobile_menu(): void
render_mobile_menu_shortcode(array $atts = []): string

// Render HTML bottom navbar
render_bottom_navbar(): void
render_bottom_navbar_shortcode(array $atts = []): string

// Handle simpan pengaturan
handle_save_settings(): void
```

### Hooks & Filter

**Actions**:
- `sofir/mobile/bottom_nav_item` - Render item bottom nav custom

**Event JavaScript**:
- `sofir:mobile-menu:open` - Dipicu saat menu mobile dibuka
- `sofir:mobile-menu:close` - Dipicu saat menu mobile ditutup
- `sofir:bottom-nav:hide` - Dipicu saat bottom nav disembunyikan
- `sofir:bottom-nav:show` - Dipicu saat bottom nav ditampilkan

### CSS Classes

**Menu Mobile**:
- `.sofir-mobile-menu` - Container utama
- `.sofir-mobile-menu.is-active` - State aktif
- `.sofir-mobile-menu-overlay` - Background overlay
- `.sofir-mobile-menu-panel` - Panel slide-in
- `.sofir-mobile-menu-toggle` - Tombol hamburger
- `.sofir-mobile-menu-close` - Tombol tutup
- `.sofir-mobile-nav` - Container navigasi
- `.sofir-mobile-user-info` - Bagian user
- `.sofir-mobile-auth-buttons` - Tombol Login/Register

**Bottom Navbar**:
- `.sofir-bottom-navbar` - Container navbar utama
- `.sofir-bottom-navbar.is-hidden` - State tersembunyi
- `.sofir-bottom-nav-item` - Item nav individual
- `.sofir-bottom-nav-item.is-current` - Item yang sedang aktif
- `.sofir-bottom-nav-item.sofir-nav-primary` - Item aksi primary
- `.sofir-nav-icon` - Elemen ikon
- `.sofir-nav-label` - Elemen label

### API JavaScript

```javascript
// Akses data mobile
var mobileData = SOFIR_MOBILE_DATA;
console.log(mobileData.breakpoint); // 768
console.log(mobileData.isMobile);   // true/false

// Selektor jQuery
var mobileMenu = jQuery('#sofir-mobile-menu');
var bottomNav = jQuery('.sofir-bottom-navbar');

// Cek apakah menu aktif
if (mobileMenu.hasClass('is-active')) {
    // Menu sedang terbuka
}

// Cek apakah bottom nav tersembunyi
if (bottomNav.hasClass('is-hidden')) {
    // Nav tersembunyi
}

// Cek item yang sedang aktif
jQuery('.sofir-bottom-nav-item.is-current').each(function() {
    console.log('Item aktif:', jQuery(this).find('.sofir-nav-label').text());
});
```

## Dukungan Browser

- ✅ iOS Safari 12+
- ✅ Chrome Mobile 80+
- ✅ Firefox Mobile 68+
- ✅ Samsung Internet 10+
- ✅ UC Browser
- ✅ Opera Mobile

## Performa

- **Ringan**: ~4KB CSS + ~2KB JavaScript (unminified)
- **Conditional Loading**: Hanya dimuat di perangkat mobile
- **Hardware Acceleration**: CSS transforms untuk animasi smooth
- **Throttled Scroll**: Scroll listener yang dioptimalkan untuk bottom nav
- **Event Optimization**: Event hanya dipicu saat state berubah

## Best Practice

1. **Pilihan Menu**: Pilih menu yang dioptimalkan untuk mobile dengan item lebih sedikit
2. **Item Bottom Nav**: Batasi hingga 4-5 item untuk UX terbaik
3. **Ikon**: Gunakan emoji atau icon fonts untuk hierarki visual yang lebih baik
4. **Touch Target**: Jaga agar item nav minimal 44x44px (sudah ditangani otomatis)
5. **Testing**: Test di perangkat asli, bukan hanya browser DevTools
6. **Aksesibilitas**: Pastikan navigasi keyboard berfungsi dengan baik
7. **Active State**: Manfaatkan highlight otomatis untuk feedback visual

## Contoh Penggunaan

### Homepage Direktori dengan Dukungan Mobile

```php
// Di functions.php theme Anda
add_action('after_setup_theme', function() {
    // Register menu mobile
    register_nav_menu('mobile', __('Menu Mobile', 'theme'));
});

// Di header.php (akan auto-render di mobile)
// Menu mobile dan bottom nav otomatis muncul di perangkat mobile
```

### Halaman Direktori Custom

```php
<?php
/**
 * Template Name: Direktori Mobile
 */

get_header();
?>

<div class="directory-page">
    <!-- Peta direktori -->
    <?php echo do_shortcode('[sofir_directory_map post_type="listing"]'); ?>
    
    <!-- Filter direktori -->
    <?php echo do_shortcode('[sofir_directory_filters post_type="listing"]'); ?>
    
    <!-- Listing direktori -->
    <?php echo do_shortcode('[sofir_post_feed post_type="listing" columns="2"]'); ?>
</div>

<!-- Menu mobile dan bottom nav otomatis muncul -->

<?php get_footer(); ?>
```

### Bottom Nav Custom untuk Direktori

```php
// Tambah item nav khusus direktori
add_action('sofir/mobile/bottom_nav_item', function($item) {
    switch ($item) {
        case 'direktori':
            echo '<a href="/direktori" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">📍</span>';
            echo '<span class="sofir-nav-label">Direktori</span>';
            echo '</a>';
            break;
            
        case 'peta':
            echo '<a href="/peta" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">🗺️</span>';
            echo '<span class="sofir-nav-label">Peta</span>';
            echo '</a>';
            break;
            
        case 'favorit':
            if (is_user_logged_in()) {
                echo '<a href="/favorit" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">❤️</span>';
                echo '<span class="sofir-nav-label">Favorit</span>';
                echo '</a>';
            }
            break;
            
        case 'notifikasi':
            if (is_user_logged_in()) {
                $count = get_user_meta(get_current_user_id(), 'unread_notifications', true);
                echo '<a href="/notifikasi" class="sofir-bottom-nav-item">';
                echo '<span class="sofir-nav-icon">🔔</span>';
                if ($count > 0) {
                    echo '<span class="sofir-nav-badge">' . esc_html($count) . '</span>';
                }
                echo '<span class="sofir-nav-label">Notifikasi</span>';
                echo '</a>';
            }
            break;
    }
}, 10, 1);

// Gunakan di template atau shortcode
// [sofir_bottom_navbar items="direktori,peta,search,notifikasi,profile"]
```

### Menambahkan Badge Notifikasi

```php
// Tambah CSS untuk badge
add_action('wp_head', function() {
    ?>
    <style>
    .sofir-nav-badge {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: #e74c3c;
        color: #fff;
        font-size: 0.625rem;
        padding: 0.125rem 0.375rem;
        border-radius: 10px;
        font-weight: 600;
    }
    </style>
    <?php
});
```

## Troubleshooting

### Menu mobile tidak muncul

1. Cek apakah dukungan mobile diaktifkan di pengaturan
2. Verifikasi Anda melihat di perangkat mobile atau viewport < 768px
3. Cek browser console untuk error JavaScript
4. Pastikan jQuery sudah dimuat

### Bottom nav overlap dengan konten

Plugin otomatis menambah padding 70px di bagian bawah body pada mobile. Jika konten masih overlap:

```css
@media (max-width: 768px) {
    body {
        padding-bottom: 80px !important;
    }
}
```

### Menu tidak tertutup saat link diklik

Plugin sudah menangani ini secara otomatis. Jika masih bermasalah, periksa apakah ada JavaScript lain yang mengganggu.

### Ikon custom tidak muncul

Ganti emoji dengan icon fonts:

```css
.sofir-nav-icon {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
}

.sofir-nav-icon::before {
    content: "\f015"; /* Ikon home */
}
```

### Active state tidak terdeteksi

Active state menggunakan URL matching. Untuk halaman dengan parameter query:

```javascript
jQuery(document).ready(function() {
    var currentPath = window.location.pathname;
    jQuery('.sofir-bottom-nav-item').each(function() {
        var itemPath = new URL(jQuery(this).attr('href'), window.location.origin).pathname;
        if (currentPath === itemPath) {
            jQuery(this).addClass('is-current');
        }
    });
});
```

## Integrasi dengan Modul Lain

### Template Web Directory Dashboard

Dukungan mobile terintegrasi seamless dengan template web directory dashboard:

```php
// Template: web-directory-dashboard
// Otomatis menyertakan menu mobile dan bottom nav
// Layout grid responsif optimize untuk layar mobile
// Map dan filter menyesuaikan untuk perangkat touch
```

### Blok Direktori

Semua blok direktori dioptimalkan untuk mobile:

- `sofir/map` - Kontrol zoom yang touch-friendly
- `sofir/search-form` - Input field yang mobile-friendly
- `sofir/post-feed` - Kolom grid responsif
- `sofir/review-stats` - Layout mobile compact

### Integrasi dengan Membership

```php
// Bottom nav untuk member area
add_action('sofir/mobile/bottom_nav_item', function($item) {
    if ($item === 'dashboard' && is_user_logged_in()) {
        echo '<a href="/member-dashboard" class="sofir-bottom-nav-item">';
        echo '<span class="sofir-nav-icon">📊</span>';
        echo '<span class="sofir-nav-label">Dashboard</span>';
        echo '</a>';
    }
}, 10, 1);
```

## Tips & Trik

### Animasi Custom untuk Nav Items

```javascript
jQuery(document).on('click', '.sofir-bottom-nav-item', function() {
    jQuery(this).find('.sofir-nav-icon').addClass('bounce');
    setTimeout(function() {
        jQuery('.sofir-nav-icon').removeClass('bounce');
    }, 300);
});
```

```css
@keyframes bounce {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.sofir-nav-icon.bounce {
    animation: bounce 0.3s ease;
}
```

### Dark Mode Support

```css
@media (prefers-color-scheme: dark) {
    .sofir-mobile-menu-panel {
        background: #1a1a1a;
        color: #fff;
    }
    
    .sofir-mobile-nav a {
        color: #fff;
        border-bottom-color: #333;
    }
    
    .sofir-bottom-navbar {
        background: #1a1a1a;
        border-top: 1px solid #333;
    }
    
    .sofir-bottom-nav-item {
        color: #ccc;
    }
    
    .sofir-bottom-nav-item.is-current {
        color: #4a9eff;
    }
}
```

### Vibrate Feedback untuk Touch

```javascript
jQuery(document).on('click', '.sofir-bottom-nav-item', function() {
    if ('vibrate' in navigator) {
        navigator.vibrate(50);
    }
});
```

## Lisensi

Bagian dari SOFIR WordPress Plugin - Modul Mobile Support

---

**Terakhir Diperbarui**: 2024
**Versi Modul**: 1.0.0
**Kompatibilitas**: SOFIR 1.0.0+
