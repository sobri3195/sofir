# SOFIR Mobile Support - Quick Start Guide

## 📱 Fitur Mobile / Mobile Features

SOFIR Directory menyertakan dukungan mobile lengkap untuk meningkatkan pengalaman pengguna di perangkat mobile.

SOFIR Directory includes comprehensive mobile support to enhance user experience on mobile devices.

## ✨ Fitur Utama / Key Features

### 1. 📱 Mobile Menu (Menu Slide-in)
- Hamburger toggle button di kanan-atas / Top-right hamburger toggle
- Panel slide-in dari kanan / Slide-in panel from right
- Support menu WordPress / WordPress menu support
- User info & auth buttons / User section with login/logout
- Auto-close saat link diklik / Auto-close on link click
- Keyboard support (ESC) / ESC key support

### 2. 📍 Bottom Navigation Bar
- Fixed position di bawah layar / Fixed bottom position
- Auto-hide saat scroll / Auto-hide on scroll
- 5 item default: Home, Search, Add, Messages, Profile
- Active state highlighting / Highlight item aktif otomatis
- Extensible via hooks / Dapat dikembangkan dengan hooks
- Badge support untuk notifikasi / Notification badge support

### 3. ⚙️ Settings Panel
- Enable/disable mobile support
- Menu selection / Pilihan menu
- Bottom nav toggle
- Breakpoint customization / Kustomisasi breakpoint

## 🚀 Quick Start

### Shortcode Basic

```php
// Mobile menu dengan pengaturan default
[sofir_mobile_menu]

// Bottom navbar dengan item default
[sofir_bottom_navbar]

// Bottom navbar dengan item custom
[sofir_bottom_navbar items="home,search,profile"]
```

### PHP Integration

```php
// Render mobile menu
echo \Sofir\Directory\Mobile::instance()->render_mobile_menu_shortcode();

// Render bottom navbar
echo \Sofir\Directory\Mobile::instance()->render_bottom_navbar_shortcode([
    'items' => 'home,search,messages,profile'
]);
```

### Menambah Item Custom / Add Custom Items

```php
add_action('sofir/mobile/bottom_nav_item', function($item) {
    if ($item === 'direktori') {
        echo '<a href="/direktori" class="sofir-bottom-nav-item">';
        echo '<span class="sofir-nav-icon">📍</span>';
        echo '<span class="sofir-nav-label">Direktori</span>';
        echo '</a>';
    }
}, 10, 1);

// Gunakan: [sofir_bottom_navbar items="home,direktori,search,profile"]
```

## 📚 Dokumentasi Lengkap / Full Documentation

### 📖 English Documentation
- **[MOBILE_SUPPORT.md](./MOBILE_SUPPORT.md)** - Complete documentation with API reference, hooks, customization guide
- **[MOBILE_EXAMPLES.md](./MOBILE_EXAMPLES.md)** - Practical implementation examples for various use cases

### 📖 Dokumentasi Bahasa Indonesia
- **[DUKUNGAN_MOBILE.md](./DUKUNGAN_MOBILE.md)** - Dokumentasi lengkap dengan referensi API, hooks, panduan kustomisasi

## 🎨 Kustomisasi CSS / CSS Customization

```css
/* Warna menu mobile / Mobile menu colors */
.sofir-mobile-menu-panel {
    background: #1a1a1a;
    color: #fff;
}

/* Warna bottom navbar / Bottom navbar colors */
.sofir-bottom-navbar {
    background: linear-gradient(to right, #667eea, #764ba2);
}

/* Item yang sedang aktif / Active item */
.sofir-bottom-nav-item.is-current {
    color: #0073aa;
    font-weight: 600;
}
```

## 🔔 JavaScript Events

```javascript
// Listen ke events
jQuery(document).on('sofir:mobile-menu:open', function() {
    console.log('Menu dibuka / Menu opened');
});

jQuery(document).on('sofir:mobile-menu:close', function() {
    console.log('Menu ditutup / Menu closed');
});

jQuery(document).on('sofir:bottom-nav:hide', function() {
    console.log('Bottom nav disembunyikan / Bottom nav hidden');
});

jQuery(document).on('sofir:bottom-nav:show', function() {
    console.log('Bottom nav ditampilkan / Bottom nav shown');
});
```

## 📱 Use Cases / Kasus Penggunaan

### Restaurant Directory / Direktori Restoran
```php
[sofir_bottom_navbar items="restaurants,map,search,favorites,profile"]
```

### Business Directory / Direktori Bisnis
```php
[sofir_bottom_navbar items="directory,categories,add-business,messages,profile"]
```

### Job Board / Portal Lowongan
```php
[sofir_bottom_navbar items="jobs,applications,saved-jobs,alerts,profile"]
```

### Real Estate / Properti
```php
[sofir_bottom_navbar items="properties,map-search,filter,favorites,profile"]
```

### Event Directory / Direktori Event
```php
[sofir_bottom_navbar items="events,calendar,my-tickets,notifications,profile"]
```

## 🛠️ Technical Stack

- **PHP Class**: `Sofir\Directory\Mobile`
- **CSS**: `/assets/css/mobile.css` (~4KB)
- **JavaScript**: `/assets/js/mobile.js` (~2KB)
- **jQuery**: Required dependency
- **WordPress**: 5.0+

## 🎯 Features Checklist

- ✅ Responsive mobile menu
- ✅ Bottom navigation bar
- ✅ Auto-hide on scroll
- ✅ Active state detection
- ✅ Keyboard support (ESC)
- ✅ Touch-friendly targets (44x44px)
- ✅ Custom events API
- ✅ Extensible via hooks
- ✅ Badge support
- ✅ User authentication states
- ✅ Conditional loading (mobile only)
- ✅ Smooth animations
- ✅ Admin settings panel

## 🔧 Settings / Pengaturan

Navigate to: **SOFIR > Dashboard > Content > Mobile Settings**

Options:
- ✅ Enable Mobile Support
- 📱 Select Mobile Menu
- 📍 Show Bottom Navigation
- 📐 Mobile Breakpoint (default: 768px)

## 🌐 Browser Support

- ✅ iOS Safari 12+
- ✅ Chrome Mobile 80+
- ✅ Firefox Mobile 68+
- ✅ Samsung Internet 10+
- ✅ UC Browser
- ✅ Opera Mobile

## 📊 Performance

- Lightweight: ~6KB total (CSS + JS)
- Conditional loading: Only on mobile
- Hardware accelerated animations
- Optimized scroll listeners
- Event debouncing

## 🐛 Troubleshooting / Pemecahan Masalah

### Menu tidak muncul / Menu not showing
1. Cek pengaturan di admin / Check admin settings
2. Verifikasi breakpoint / Verify breakpoint
3. Cek browser console / Check browser console

### Bottom nav overlap konten / Bottom nav overlaps content
```css
@media (max-width: 768px) {
    body {
        padding-bottom: 80px !important;
    }
}
```

### Active state tidak terdeteksi / Active state not detected
Fitur ini menggunakan URL matching. Pastikan URL item nav cocok dengan URL halaman saat ini.

This feature uses URL matching. Ensure nav item URLs match current page URLs.

## 📞 Support

- 📖 Full Docs: [MOBILE_SUPPORT.md](./MOBILE_SUPPORT.md)
- 📖 Dokumentasi: [DUKUNGAN_MOBILE.md](./DUKUNGAN_MOBILE.md)
- 💡 Examples: [MOBILE_EXAMPLES.md](./MOBILE_EXAMPLES.md)

## 📝 Version

- **Module Version**: 1.0.0
- **SOFIR Compatibility**: 1.0.0+
- **Last Updated**: 2024

---

**🎉 Ready to use! / Siap digunakan!**

Fitur mobile otomatis aktif di perangkat mobile. Tidak perlu konfigurasi tambahan.

Mobile features automatically activate on mobile devices. No additional configuration needed.
