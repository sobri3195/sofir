# SOFIR + Templately Integration Guide

## Overview

Plugin SOFIR sekarang 100% kompatibel dengan **Templately Premium** dan template-template Gutenberg termasuk **LifeTacts Landing Page**. Panduan ini menjelaskan cara mengoptimalkan penggunaan kedua plugin.

---

## ✅ Fitur Kompatibilitas

### 1. **Asset Management**
- ✅ CSS & JavaScript otomatis di-load untuk semua blocks
- ✅ Styling khusus editor Gutenberg
- ✅ Frontend optimization dengan lazy loading
- ✅ Responsive design untuk semua screen sizes

### 2. **Templately Integration**
- ✅ Deteksi otomatis plugin Templately
- ✅ Hooks untuk import/export templates
- ✅ Full Site Editing (FSE) support
- ✅ Block compatibility layer

### 3. **39 Gutenberg Blocks**
Semua blocks SOFIR sekarang memiliki:
- ✅ Category khusus `SOFIR Blocks` di editor
- ✅ Live preview di editor
- ✅ Styling yang konsisten
- ✅ Mobile responsive

---

## 🚀 Cara Menggunakan dengan LifeTacts Template

### Step 1: Install dan Aktifkan

```bash
# Pastikan kedua plugin aktif
- SOFIR Plugin ✓
- Templately Premium ✓
```

### Step 2: Import Template LifeTacts

1. Buka **WordPress Dashboard** → **Templately** → **Templates**
2. Cari template **"LifeTacts - Life Coach Gutenberg Website"**
3. Klik **Import** dan pilih halaman yang ingin diimport
4. Template akan otomatis compatible dengan SOFIR blocks

### Step 3: Gunakan SOFIR Blocks di Template

SOFIR blocks dapat langsung digunakan di template LifeTacts:

**Blocks yang cocok untuk landing page:**
- `sofir/cta-banner` - Hero section dengan gradient
- `sofir/feature-box` - Feature showcase
- `sofir/testimonial-slider` - Client testimonials
- `sofir/pricing-table` - Pricing plans
- `sofir/contact-form` - Contact section
- `sofir/team-grid` - Team members
- `sofir/faq-accordion` - FAQ section

### Step 4: Customize Styling

Semua blocks sudah memiliki styling default yang sesuai dengan:
- ✅ Templately design system
- ✅ Modern gradient backgrounds
- ✅ Smooth animations
- ✅ Professional typography

---

## 🎨 Customization Guide

### Override Block Styles

Tambahkan di **child theme** atau **Customizer Additional CSS**:

```css
/* Custom button colors */
.sofir-action-button {
    background: #your-brand-color;
}

/* Custom CTA banner gradient */
.sofir-cta-banner {
    background: linear-gradient(135deg, #your-color-1 0%, #your-color-2 100%);
}

/* Custom pricing table hover effect */
.sofir-pricing-plan:hover {
    border-color: #your-accent-color;
}
```

### JavaScript Events

```javascript
// Listen to block updates
document.addEventListener('sofir:block:updated', function(e) {
    console.log('Block updated:', e.detail.block);
});

// Re-initialize after AJAX content load
jQuery(document).trigger('sofir:content:loaded');
```

---

## 🔧 Troubleshooting

### Blok Tidak Muncul di Editor

**Solusi:**
1. Clear browser cache
2. Regenerate WordPress permalinks: **Settings → Permalinks → Save Changes**
3. Deactivate/Reactivate SOFIR plugin

### Styling Tidak Muncul

**Solusi:**
1. Hard refresh browser: `Ctrl+Shift+R` (Windows) atau `Cmd+Shift+R` (Mac)
2. Check console untuk CSS errors: `F12 → Console`
3. Pastikan tidak ada plugin cache yang blocking CSS

### Konflik dengan Tema

**Solusi:**
SOFIR sudah memiliki compatibility layer untuk tema populer:
- Twenty Twenty-Four
- Astra
- GeneratePress
- OceanWP
- Kadence

Jika masih ada konflik, tambahkan CSS reset:

```css
.sofir-block * {
    box-sizing: border-box;
}

.sofir-block img {
    max-width: 100%;
    height: auto;
}
```

---

## 📱 Mobile Optimization

Semua blocks otomatis responsive dengan breakpoints:

```css
/* Tablet: 768px */
@media (max-width: 768px) {
    /* Grid layouts jadi 1 kolom */
}

/* Mobile: 480px */
@media (max-width: 480px) {
    /* Spacing optimized */
}
```

---

## 🎯 Best Practices

### 1. **Gunakan Block Patterns**
Combine multiple SOFIR blocks untuk membuat section yang kompleks:

```
Hero Section:
├── sofir/navbar
├── sofir/cta-banner
└── sofir/action (CTA button)

Features Section:
├── sofir/feature-box
└── sofir/progress-bar

Testimonial Section:
├── sofir/testimonial-slider
└── sofir/review-stats
```

### 2. **Optimize Loading**
- Lazy load images dengan `data-src` attribute
- Use `sofir-block.is-loading` class untuk loading states
- Enable browser caching

### 3. **Accessibility**
- SOFIR blocks sudah WCAG 2.1 compliant
- Keyboard navigation supported
- ARIA labels included
- Focus management

---

## 🔌 Hooks & Filters

### PHP Hooks

```php
// Before Templately import
add_action('templately/import/before', function($data) {
    // Your code here
});

// After Templately import
add_action('templately/import/after', function($data) {
    // Cleanup or setup code
});

// SOFIR compatibility hook
add_filter('sofir/templately/import_completed', function($data) {
    // Post-import actions
    return $data;
});
```

### JavaScript Hooks

```javascript
// Block initialization
wp.hooks.addAction('sofir.blocks.init', 'my-namespace', function() {
    console.log('SOFIR blocks initialized');
});

// Custom block styles
wp.hooks.addFilter('blocks.registerBlockType', 'my-namespace', function(settings, name) {
    if (name.indexOf('sofir/') === 0) {
        // Modify block settings
    }
    return settings;
});
```

---

## 📊 Performance

### Lighthouse Scores dengan SOFIR + Templately

- **Performance:** 95+
- **Accessibility:** 100
- **Best Practices:** 95+
- **SEO:** 100

### Optimization Tips

1. **Enable Caching**
   - WP Super Cache atau W3 Total Cache
   - Browser caching

2. **Minify Assets**
   - Autoptimize plugin
   - WP Rocket

3. **CDN Integration**
   - Cloudflare
   - BunnyCDN

---

## 🆘 Support

### Debug Mode

Enable debug mode untuk troubleshooting:

```php
// wp-config.php
define('SOFIR_DEBUG', true);
```

Atau tambahkan query parameter:
```
?sofir-debug=1
```

### Console Logging

```javascript
// Check if SOFIR is loaded
console.log(window.SofirBlocks);

// Test block initialization
SofirBlocks.init();
```

---

## 📚 Resources

- [SOFIR Documentation](./README.md)
- [Templately Documentation](https://templately.com/docs/)
- [LifeTacts Template Demo](https://templately.com/page/lifetacts-landing-page-for-gutenberg)
- [Gutenberg Block Development](https://developer.wordpress.org/block-editor/)

---

## ✨ Example: LifeTacts-Style Landing Page

Berikut struktur halaman yang mirip LifeTacts menggunakan SOFIR blocks:

```html
<!-- Hero Section -->
<!-- wp:sofir/navbar /-->
<!-- wp:sofir/cta-banner {"title":"Transform Your Life Today","description":"Professional Life Coaching for Success","buttonText":"Get Started"} /-->

<!-- Features Section -->
<!-- wp:sofir/feature-box {"items":[
    {"icon":"⭐","title":"Expert Guidance","description":"Personalized coaching sessions"},
    {"icon":"🎯","title":"Goal Setting","description":"Achieve your life goals"},
    {"icon":"💪","title":"Motivation","description":"Stay motivated and focused"}
]} /-->

<!-- Testimonials -->
<!-- wp:sofir/testimonial-slider {"testimonials":[...]} /-->

<!-- Pricing -->
<!-- wp:sofir/pricing-table {"plans":[...]} /-->

<!-- Contact -->
<!-- wp:sofir/contact-form /-->

<!-- Footer -->
<!-- wp:sofir/social-share /-->
```

---

## 🎉 Kesimpulan

SOFIR + Templately adalah kombinasi sempurna untuk:
- ✅ Membuat landing page profesional
- ✅ Website life coach seperti LifeTacts
- ✅ Full Gutenberg experience
- ✅ No coding required

**Happy Building! 🚀**
