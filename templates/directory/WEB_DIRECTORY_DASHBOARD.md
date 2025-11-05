# Web Directory Dashboard Template

## Overview

Template **Web Directory Dashboard** adalah dashboard komprehensif yang dirancang khusus untuk mengelola dan menampilkan web directory. Template ini menggabungkan fitur-fitur directory dengan dashboard analytics yang powerful.

## Features

### 📊 Overview Statistics
- **Total Listings**: Menampilkan jumlah total listing dengan persentase pertumbuhan
- **Reviews Count**: Total review dengan rating rata-rata
- **Active Users**: Jumlah pengguna aktif dengan trend

### 🗺️ Interactive Directory Map
- Integrasi dengan Mapbox/Google Maps
- Real-time listing locations
- Clustering support untuk performa optimal
- Zoom dan pan interaktif

### 📈 Directory Analytics
- **Visit Chart**: Grafik kunjungan mingguan
- **Ring Chart**: Distribusi listing berdasarkan status (Featured, Standard, Pending)
- Performance metrics untuk setiap kategori

### 🎯 Quick Actions Sidebar
Aksi cepat yang dapat diakses langsung:
- ➕ Add New Listing
- 📊 View All Stats
- ⚙️ Directory Settings
- 💬 Manage Reviews

### 🔍 Advanced Filters
Filter berdasarkan:
- **Rating**: 5 stars, 4+ stars, 3+ stars
- **Status**: Active, Featured, Pending
- **Search**: Full-text search
- **Categories**: Top categories dengan jumlah listing

### 💬 Recent Reviews Section
- Menampilkan review terbaru
- Rating visualization dengan bintang
- Nama reviewer dan komentar
- Link ke halaman semua review

### 📑 Recent Listings Feed
Grid view dari listing terbaru dengan:
- Thumbnail image
- Title dan excerpt
- Meta information
- Action buttons

## SOFIR Blocks Used

Template ini menggunakan blocks berikut:

1. **sofir/map** - Interactive map dengan clustering
2. **sofir/review-stats** - Review statistics dengan rating
3. **sofir/visit-chart** - Visitor analytics chart
4. **sofir/ring-chart** - Donut chart untuk distribusi data
5. **sofir/post-feed** - Grid listing terbaru
6. **sofir/search-form** - Advanced search dengan filters
7. **sofir/term-feed** - Category listing dengan counts

## Customization

### Mengubah Warna Gradient Header
```html
"gradient":"vivid-cyan-blue-to-vivid-purple"
```
Ganti dengan gradient WordPress lainnya atau custom CSS.

### Menyesuaikan Kolom Layout
Layout menggunakan columns 66.66% / 33.33%:
```html
<!-- wp:column {"width":"66.66%"} -->
<!-- wp:column {"width":"33.33%"} -->
```

### Mengubah Post Type
Default menggunakan `listing`:
```html
<!-- wp:sofir/post-feed {"postType":"listing",...} /-->
```
Ubah ke custom post type lain sesuai kebutuhan.

### Konfigurasi Map
```html
<!-- wp:sofir/map {"postType":"listing","zoom":12,"height":"500px"} /-->
```
- `postType`: CPT yang memiliki location field
- `zoom`: Level zoom (1-20)
- `height`: Tinggi map

## Integration dengan Directory Module

Template ini terintegrasi penuh dengan:

### 1. Directory Manager
- Mapbox/Google Maps provider
- Clustering configuration
- Filter query vars
- Schedule filtering (open now)

### 2. CPT Manager
- Location metadata
- Rating system
- Hours of operation
- Status filtering (active, featured, pending)

### 3. Review System
- Comment meta untuk rating
- Average rating calculation
- Review submission form
- Rating stars visualization

## Use Cases

### 1. Business Directory
Untuk daftar bisnis lokal dengan:
- Lokasi pada map
- Rating dan review
- Jam operasional
- Kategori bisnis

### 2. Restaurant Directory
Menampilkan restoran dengan:
- Menu highlights
- Price range
- Booking integration
- Customer reviews

### 3. Hotel/Property Listing
Directory properti dengan:
- Location maps
- Price filters
- Availability status
- Photo galleries

### 4. Service Provider Directory
Direktori penyedia layanan:
- Professional profiles
- Service categories
- Rating dan testimonials
- Contact information

## Technical Details

### Required CPT Fields
Template mengharapkan CPT dengan fields:
- `sofir_location` - Address dan koordinat
- `sofir_rating` - Average rating (0-5)
- `sofir_status` - Status (active, featured, pending, closed)
- `sofir_hours` - Operating hours
- `sofir_contact` - Contact information

### Responsive Design
- Desktop: 2-column layout (66% + 33%)
- Tablet: Single column stacked
- Mobile: Optimized touch interfaces

### Performance
- Lazy loading untuk images
- Map clustering untuk banyak markers
- Pagination untuk listings
- Caching untuk statistics

## Examples

### Membuat Custom Quick Action
```html
<!-- wp:button {"backgroundColor":"base","textColor":"primary","width":100} -->
<div class="wp-block-button">
  <a class="wp-block-button__link" href="/custom-action">
    🎨 Custom Action
  </a>
</div>
<!-- /wp:button -->
```

### Menambahkan Filter Custom
```html
<!-- wp:buttons {"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-buttons">
  <!-- wp:button {"className":"is-style-outline","width":100} -->
  <div class="wp-block-button is-style-outline">
    <a class="wp-block-button__link" data-filter="custom">
      🏷️ Custom Filter
    </a>
  </div>
  <!-- /wp:button -->
</div>
<!-- /wp:buttons -->
```

### Kustomisasi Stat Card
```html
<!-- wp:group {"backgroundColor":"base-2","style":{"border":{"radius":"12px"}}} -->
<div class="wp-block-group">
  <!-- wp:heading {"level":3} -->
  <h3>Custom Metric</h3>
  <!-- /wp:heading -->
  
  <!-- wp:paragraph {"style":{"typography":{"fontSize":"48px"}}} -->
  <p style="font-size:48px">999</p>
  <!-- /wp:paragraph -->
  
  <!-- wp:paragraph {"style":{"color":{"text":"#10b981"}}} -->
  <p style="color:#10b981">↑ 25% increase</p>
  <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
```

## Troubleshooting

### Map Tidak Muncul
1. Cek Directory Settings di SOFIR admin
2. Pastikan Mapbox token atau Google API key sudah diisi
3. Verify CPT memiliki field `location` dengan koordinat valid

### Statistics Tidak Akurat
1. Pastikan CPT fields sudah populated
2. Check meta queries di REST API
3. Clear object cache jika menggunakan caching plugin

### Filter Tidak Bekerja
1. Verify CPT memiliki fields yang di-filter
2. Check filter query vars registration
3. Ensure REST API filterable is enabled

## Best Practices

1. **SEO Optimization**
   - Gunakan semantic HTML headings
   - Add schema markup untuk LocalBusiness
   - Optimize images dengan alt text

2. **Performance**
   - Enable map clustering untuk 100+ listings
   - Use pagination untuk large datasets
   - Lazy load images dan heavy blocks

3. **User Experience**
   - Provide clear filter feedback
   - Show loading states
   - Mobile-first design approach

4. **Accessibility**
   - Proper heading hierarchy
   - ARIA labels untuk interactive elements
   - Keyboard navigation support

## Related Documentation

- [Directory Module Documentation](/modules/directory/README.md)
- [CPT Manager Guide](/modules/cpt/README.md)
- [Blocks Documentation](/modules/blocks/BLOCKS_DOCUMENTATION.md)
- [Map Block Usage](/modules/blocks/BLOCKS_DOCUMENTATION.md#map-block)

## Version History

- **v1.0** - Initial release dengan core features
  - Overview statistics
  - Interactive map
  - Analytics charts
  - Quick actions
  - Advanced filters
  - Recent reviews

## Support

Untuk bantuan lebih lanjut:
- Lihat dokumentasi lengkap di `/modules/blocks/`
- Check existing templates untuk inspirasi
- Review SOFIR memory untuk best practices
