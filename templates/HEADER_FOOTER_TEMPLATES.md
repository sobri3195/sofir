# Header & Footer Templates - Gutenberg Ready

SOFIR menyediakan koleksi lengkap template header dan footer yang siap pakai untuk Gutenberg Block Editor dan Full Site Editing (FSE). Template-template ini dirancang profesional, responsive, dan dapat langsung di-copy sebagai block pattern.

---

## 🎯 Fitur Utama

### ✨ Clickable Preview
- **Klik gambar preview** untuk melihat tampilan template secara langsung
- Preview ditampilkan dalam modal iframe dengan theme styling
- Visual feedback dengan ikon mata (👁) saat hover
- Keyboard support: tekan Enter atau Space pada preview image

### 📋 Copy Pattern  
- **One-click copy** pattern code ke clipboard
- Langsung paste ke Gutenberg editor
- Compatible dengan semua WordPress themes
- Fallback manual copy untuk browser yang tidak support clipboard API

### 🔄 Live Preview
- Preview template dengan theme style aktif
- Modal full-screen yang responsive
- Close dengan ESC key atau klik tombol close
- Smooth animations dan transitions

---

## 📦 Template Header (4 Designs)

### 1. **Modern Header**
**File:** `templates/components/modern-header.html`  
**Style:** Modern, clean, horizontal layout  
**Features:**
- Logo + site title di kiri
- Navigasi horizontal di tengah
- CTA button dengan border radius di kanan
- Flex layout yang responsive

**Use Cases:**
- Startup websites
- SaaS products
- Modern business sites
- Tech companies

---

### 2. **Minimal Header**
**File:** `templates/components/minimal-header.html`  
**Style:** Minimalis, three-column layout  
**Features:**
- Logo di kiri
- Navigasi di tengah (center-aligned)
- Login link di kanan
- Clean dan sederhana

**Use Cases:**
- Portfolio websites
- Personal blogs
- Minimalist brands
- Creative agencies

---

### 3. **Business Header**
**File:** `templates/components/business-header.html`  
**Style:** Professional, two-tier layout  
**Features:**
- Top bar dengan info kontak (email, phone, social)
- Main navigation bar dengan logo
- Professional color scheme
- Multi-level navigation ready

**Use Cases:**
- Corporate websites
- Professional services (lawyer, doctor, consultant)
- B2B companies
- Enterprise sites

---

### 4. **Centered Header**
**File:** `templates/components/centered-header.html`  
**Style:** Center-aligned, vertical layout  
**Features:**
- Logo dan title di tengah atas
- Navigasi horizontal di bawah logo
- Symmetric design
- Elegant spacing

**Use Cases:**
- Fashion websites
- Luxury brands
- Photography portfolios
- Magazine-style sites

---

## 📦 Template Footer (4 Designs)

### 1. **Multi Column Footer**
**File:** `templates/components/multi-column-footer.html`  
**Style:** Comprehensive, 4-column layout  
**Features:**
- Column 1: Company info + logo + tagline + social links (40% width)
- Column 2: Company links (About, Careers, Blog, Contact) (20% width)
- Column 3: Services links (20% width)
- Column 4: Legal links (Privacy, Terms, Cookie, Disclaimer) (20% width)
- Separator line + copyright di bottom

**Use Cases:**
- Large corporate sites
- E-commerce websites
- Directory platforms
- Content-rich websites

---

### 2. **Simple Footer**
**File:** `templates/components/simple-footer.html`  
**Style:** Clean, three-column layout  
**Features:**
- Column 1: Copyright text (33%)
- Column 2: Navigation links center-aligned (33%)
- Column 3: Social media icons right-aligned (33%)
- Dark background dengan white text
- Minimalist dan efficient

**Use Cases:**
- Small business websites
- Landing pages
- Portfolio sites
- Simple blogs

---

### 3. **Business Footer**
**File:** `templates/components/business-footer.html`  
**Style:** Professional, comprehensive layout  
**Features:**
- Company information section
- Newsletter signup form
- Contact details (address, phone, email)
- Social media links
- Professional dark theme

**Use Cases:**
- Business websites
- Professional services
- Corporate sites
- B2B platforms

---

### 4. **Newsletter Footer**
**File:** `templates/components/newsletter-footer.html`  
**Style:** Modern, subscription-focused  
**Features:**
- Prominent newsletter signup section
- Gradient background
- Email form with submit button
- Social media integration
- Modern, eye-catching design

**Use Cases:**
- Content marketing sites
- Newsletter-focused websites
- Blog sites with email list
- Community platforms

---

## 🚀 Cara Menggunakan

### Metode 1: Copy Pattern (Recommended)

1. Buka **SOFIR → Templates** di admin WordPress
2. Scroll ke section **Header Designs** atau **Footer Designs**
3. **Klik preview image** untuk melihat template
4. Klik tombol **"Copy Pattern"**
5. Buka Gutenberg editor
6. Paste (Ctrl+V / Cmd+V) di editor
7. Customize sesuai kebutuhan

### Metode 2: Import ke FSE (Block Theme Only)

1. Pastikan menggunakan Block Theme (Twenty Twenty-Four, dll)
2. Buka **Appearance → Editor**
3. Pilih template part (Header atau Footer)
4. Paste pattern code yang sudah di-copy
5. Save template

### Metode 3: Manual Copy dari File

1. Buka file template di folder `templates/components/`
2. Copy semua HTML block syntax
3. Paste ke Gutenberg editor
4. Customize sesuai kebutuhan

---

## 🎨 Customization Tips

### Mengubah Warna
```html
<!-- Original -->
"backgroundColor":"black"

<!-- Custom Color -->
"backgroundColor":"primary"
```

### Mengubah Spacing
```html
<!-- Original -->
"padding":{"top":"3rem","bottom":"3rem"}

<!-- Custom -->
"padding":{"top":"2rem","bottom":"2rem"}
```

### Mengubah Font
```html
<!-- Original -->
"typography":{"fontWeight":"700","fontSize":"1.5rem"}

<!-- Custom -->
"typography":{"fontWeight":"600","fontSize":"2rem"}
```

### Menambahkan Custom Class
```html
<!-- Tambahkan setelah wp:group -->
"className":"my-custom-class"
```

---

## 🔧 Technical Details

### Block Pattern Registration
Semua header dan footer templates otomatis terdaftar sebagai Gutenberg block patterns dengan:
- **Category:** `sofir-header` atau `sofir-footer`
- **Context:** `pattern` (dapat di-copy)
- **Pattern Slug:** `sofir/modern-header`, `sofir/simple-footer`, dll

### WordPress Blocks Used
Templates menggunakan native WordPress blocks:
- `wp:group` - Container dan layout
- `wp:columns` - Multi-column layout
- `wp:site-logo` - Dynamic site logo
- `wp:site-title` - Dynamic site title
- `wp:navigation` - Navigation menu
- `wp:button` - CTA buttons
- `wp:social-links` - Social media icons
- `wp:paragraph` - Text content
- `wp:heading` - Section headings
- `wp:list` - Link lists
- `wp:separator` - Divider lines

### Responsive Behavior
Semua templates menggunakan:
- Flexbox dan CSS Grid untuk layout
- Responsive column widths
- Mobile-friendly spacing
- Breakpoint-aware designs

---

## 📱 Mobile Compatibility

Semua header dan footer templates fully responsive dengan:
- **Columns** otomatis stack di mobile
- **Navigation** collapse ke mobile menu
- **Social icons** tetap visible
- **Spacing** adjusted untuk layar kecil
- **Touch-friendly** button sizes

---

## 🎯 Best Practices

### Header Selection
- **Modern Header** → Websites dengan strong CTA
- **Minimal Header** → Clean, content-focused sites
- **Business Header** → Professional, info-heavy sites
- **Centered Header** → Elegant, symmetric designs

### Footer Selection
- **Multi Column** → Comprehensive site information
- **Simple Footer** → Clean, minimal approach
- **Business Footer** → Professional presence
- **Newsletter Footer** → Email list building

### Combination Tips
- **Modern Header + Simple Footer** → Clean startup site
- **Business Header + Business Footer** → Professional corporate
- **Minimal Header + Newsletter Footer** → Content marketing
- **Centered Header + Multi Column Footer** → Magazine style

---

## 🔍 SEO Considerations

Templates sudah optimized untuk SEO:
- Semantic HTML structure
- Proper heading hierarchy
- Accessible navigation
- Mobile-friendly layout
- Fast loading (native blocks)

---

## 🐛 Troubleshooting

### Pattern tidak muncul di editor
**Solusi:** Pastikan plugin SOFIR sudah aktif dan refresh editor (Ctrl+F5)

### Style tidak sesuai
**Solusi:** Cek apakah theme mendukung theme.json dan full site editing

### Copy tidak berfungsi
**Solusi:** Browser mungkin block clipboard access. Gunakan manual copy dari modal.

### Layout broken di mobile
**Solusi:** Pastikan theme memiliki responsive CSS untuk block columns

---

## 📞 Support

Untuk pertanyaan atau custom template request:
- Check dokumentasi lengkap di README.md
- Visit SOFIR admin panel untuk preview langsung
- Customize template sesuai brand guideline Anda

---

**Template Version:** 1.0.0  
**Last Updated:** 2024  
**Compatible With:** WordPress 6.3+, Gutenberg, FSE
