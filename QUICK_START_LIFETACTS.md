# 🚀 Quick Start: Membuat Landing Page ala LifeTacts

Panduan cepat untuk membuat landing page profesional seperti LifeTacts menggunakan SOFIR + Templately.

---

## ⚡ Setup (5 Menit)

### 1. Aktivasi Plugin

```
✅ SOFIR Plugin - Active
✅ Templately Premium - Active
```

### 2. Verifikasi Blocks

Masuk ke **Post Editor** → Klik **+** → Cari **"SOFIR"**

Anda harus melihat kategori **SOFIR Blocks** dengan 39 blocks.

---

## 🎨 Membuat Landing Page LifeTacts-Style

### Struktur Halaman Lengkap

```
1. Hero Section
   └── CTA Banner + Action Button

2. Features Section
   └── Feature Box (3 kolom)

3. About Section
   └── Image + Text

4. Services Section
   └── Pricing Table

5. Testimonials
   └── Testimonial Slider

6. FAQ
   └── FAQ Accordion

7. Contact
   └── Contact Form

8. Footer
   └── Social Share + Breadcrumb
```

---

## 📝 Step-by-Step Implementation

### Step 1: Create New Page

1. **Pages** → **Add New**
2. Title: "Home" atau "Landing"
3. Pilih template: **Blank** atau **Full Width**

### Step 2: Add Hero Section

```
Block: sofir/cta-banner

Settings:
- Title: "Transform Your Life Today"
- Description: "Professional Life Coaching for Success and Happiness"
- Button Text: "Book Free Consultation"
- Background: Gradient (Purple to Blue)
```

**Preview:**
```html
<!-- wp:sofir/cta-banner {
  "title":"Transform Your Life Today",
  "description":"Professional Life Coaching for Success",
  "buttonText":"Book Consultation",
  "buttonUrl":"#contact"
} /-->
```

### Step 3: Add Features

```
Block: sofir/feature-box

Add 3 Features:
1. Icon: ⭐ | Title: "Expert Guidance" | Description: "20+ years experience"
2. Icon: 🎯 | Title: "Goal Achievement" | Description: "Proven success strategies"
3. Icon: 💪 | Title: "Life Transformation" | Description: "Real lasting change"
```

### Step 4: Add Pricing

```
Block: sofir/pricing-table

Plans:
1. Basic - $99/month
   - Weekly 1-on-1 sessions
   - Email support
   - Goal tracking

2. Premium - $199/month (Featured)
   - Daily coaching access
   - Priority support
   - Custom action plans
   
3. Elite - $399/month
   - 24/7 availability
   - Personal strategy
   - Lifetime access
```

### Step 5: Add Testimonials

```
Block: sofir/testimonial-slider

Testimonials:
1. "Life-changing experience! Highly recommended."
   - Sarah Johnson, CEO

2. "Best investment I've ever made in myself."
   - Michael Brown, Entrepreneur

3. "Achieved goals I thought were impossible."
   - Lisa Chen, Designer
```

### Step 6: Add FAQ

```
Block: sofir/faq-accordion

Questions:
1. How long is each session?
   → Each session is 60 minutes.

2. Do you offer refunds?
   → Yes, 30-day money-back guarantee.

3. Can I cancel anytime?
   → Yes, no long-term contracts.
```

### Step 7: Add Contact Form

```
Block: sofir/contact-form

Fields:
- Name
- Email
- Phone (optional)
- Message
- Submit Button: "Send Message"
```

---

## 🎨 Styling Tips

### Color Scheme LifeTacts-Style

```css
/* Primary Colors */
--primary: #667eea;      /* Purple */
--secondary: #764ba2;    /* Dark Purple */
--accent: #f093fb;       /* Pink */

/* Gradients */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
```

### Custom CSS (Optional)

```css
/* Hero Enhancement */
.sofir-cta-banner {
    min-height: 600px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Feature Box Hover */
.sofir-feature-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
}

/* Pricing Featured */
.sofir-pricing-plan.featured {
    transform: scale(1.05);
    z-index: 1;
}
```

---

## 📱 Mobile Optimization

Semua blocks sudah responsive, tapi untuk hasil terbaik:

### Checklist Mobile:

```
✅ Test di mobile view editor (click mobile icon di atas)
✅ Adjust font sizes kalau terlalu besar
✅ Reduce padding di sections
✅ Stack pricing tables vertically (otomatis)
```

### Mobile Settings:

```css
@media (max-width: 768px) {
    .sofir-cta-title {
        font-size: 2em !important;
    }
    
    .sofir-cta-description {
        font-size: 1em !important;
    }
}
```

---

## 🔧 Advanced Customization

### Add Smooth Scroll

```javascript
// Add to theme functions.php
wp_enqueue_script('smooth-scroll', get_template_directory_uri() . '/js/smooth-scroll.js', ['jquery'], '1.0', true);
```

```javascript
// smooth-scroll.js
jQuery(document).ready(function($) {
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if(target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });
});
```

### Add Animations on Scroll

```html
<!-- Add to <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
  AOS.init({
    duration: 1000,
    once: true
  });
</script>
```

Tambahkan attribute ke blocks:
```html
data-aos="fade-up"
data-aos="zoom-in"
data-aos="slide-left"
```

---

## 🚀 Performance Optimization

### 1. Image Optimization

```
✅ Use WebP format
✅ Compress with TinyPNG/ImageOptim
✅ Lazy load (already enabled in SOFIR)
✅ Max size: 1920px width
```

### 2. Caching

Install plugin:
- **WP Rocket** (Premium) atau
- **W3 Total Cache** (Free)

### 3. Minify Assets

Enable di **WP Rocket** atau **Autoptimize**:
```
✅ Minify HTML
✅ Minify CSS
✅ Minify JavaScript
✅ Combine files
```

---

## 📊 Tracking & Analytics

### Google Analytics Setup

```html
<!-- Add to theme header.php or use plugin -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

### Track Button Clicks

```javascript
jQuery('.sofir-action-button').on('click', function() {
    gtag('event', 'button_click', {
        'event_category': 'CTA',
        'event_label': $(this).text()
    });
});
```

---

## ✅ Pre-Launch Checklist

```
□ Test all links (buttons, navbar, footer)
□ Verify forms submit correctly
□ Check mobile responsiveness
□ Test page load speed (GTmetrix)
□ Verify SEO meta tags
□ Test on different browsers
□ Check accessibility (WAVE tool)
□ Setup SSL certificate (HTTPS)
□ Configure caching
□ Setup Google Analytics
```

---

## 🐛 Common Issues & Fixes

### Issue 1: Blocks tidak muncul di editor

**Fix:**
```
1. Clear browser cache (Ctrl+Shift+Delete)
2. Go to Settings → Permalinks → Save Changes
3. Deactivate/Reactivate SOFIR plugin
```

### Issue 2: Styling tidak apply

**Fix:**
```
1. Hard refresh: Ctrl+Shift+R (Windows) / Cmd+Shift+R (Mac)
2. Clear server cache
3. Check browser console for CSS errors (F12)
```

### Issue 3: Konflik dengan tema

**Fix:**
```css
/* Add to Customizer → Additional CSS */
.sofir-block {
    all: revert;
}

.sofir-block * {
    box-sizing: border-box !important;
}
```

### Issue 4: Mobile menu tidak berfungsi

**Fix:**
```
1. Ensure jQuery is loaded
2. Check console for JavaScript errors
3. Add mobile toggle button manually
```

---

## 🎓 Learning Resources

### Video Tutorials (Search YouTube):
- "Gutenberg Block Editor Tutorial"
- "WordPress Landing Page Design"
- "Life Coach Website Tutorial"

### Helpful Links:
- [WordPress Codex](https://codex.wordpress.org/)
- [Gutenberg Handbook](https://developer.wordpress.org/block-editor/)
- [CSS-Tricks](https://css-tricks.com/)

---

## 💡 Pro Tips

1. **Start with wireframe** - Sketch your layout first
2. **Keep it simple** - Less is more
3. **Use whitespace** - Don't cram everything
4. **Test often** - Check mobile after every section
5. **Get feedback** - Show to friends/colleagues
6. **Iterate** - Your first version won't be perfect

---

## 🎉 You're Ready!

Dengan panduan ini, Anda bisa membuat landing page profesional dalam:

⏱️ **Setup:** 5 minutes
⏱️ **Build:** 30-60 minutes
⏱️ **Polish:** 15-30 minutes

**Total: 1-2 hours untuk website profesional!**

---

## 📞 Need Help?

- Check: `TEMPLATELY_INTEGRATION_GUIDE.md`
- Debug mode: Add `?sofir-debug=1` to URL
- Browser console: Press F12

**Happy Building! 🚀**
