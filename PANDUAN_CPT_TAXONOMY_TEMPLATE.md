# Panduan Lengkap: CPT, Taxonomy & Template SOFIR

Dokumentasi komprehensif untuk membuat Custom Post Type (CPT), taxonomy, dan menggunakan template di SOFIR WordPress Plugin.

---

## 📚 Daftar Isi

1. [Custom Post Type (CPT)](#1-custom-post-type-cpt)
2. [Taxonomy](#2-taxonomy)
3. [Field Catalog](#3-field-catalog)
4. [Template System](#4-template-system)
5. [REST API Filtering](#5-rest-api-filtering)
6. [Contoh Praktis](#6-contoh-praktis)

---

## 1. Custom Post Type (CPT)

### 1.1 Pengertian CPT

Custom Post Type adalah jenis konten khusus di WordPress selain post dan page. SOFIR menyediakan CPT Manager yang powerful untuk membuat dan mengelola CPT dengan mudah.

### 1.2 CPT Bawaan SOFIR

SOFIR sudah dilengkapi dengan 5 CPT siap pakai:

| CPT | Slug | Fungsi | Icon |
|-----|------|--------|------|
| **Listing** | `listing` | Directory bisnis dengan lokasi, jam operasional, rating | 📍 location-alt |
| **Profile** | `profile` | Profil pengguna atau organisasi | 🆔 id |
| **Article** | `article` | Artikel atau blog post | 📄 media-document |
| **Event** | `event` | Event atau acara dengan tanggal dan kapasitas | 📅 calendar |
| **Appointment** | `appointment` | Sistem booking janji temu | 🕐 clock |

### 1.3 Struktur CPT

Setiap CPT memiliki struktur:

```php
[
    'args' => [
        'labels'    => [...],        // Label tampilan
        'menu_icon' => 'dashicons-*', // Icon di admin menu
        'rewrite'   => ['slug' => 'slug'], // URL slug
        'supports'  => [...],        // Fitur yang didukung
    ],
    'fields' => [...],               // Field dari catalog
    'taxonomies' => [...],           // Taxonomy yang terkait
]
```

### 1.4 Membuat CPT Baru (Programmatically)

#### Metode 1: Melalui Filter Hook

```php
add_filter('sofir/cpt/definitions', function($post_types) {
    $post_types['produk'] = [
        'args' => [
            'labels' => [
                'name'          => __('Produk', 'textdomain'),
                'singular_name' => __('Produk', 'textdomain'),
                'add_new'       => __('Tambah Produk', 'textdomain'),
                'add_new_item'  => __('Tambah Produk Baru', 'textdomain'),
                'edit_item'     => __('Edit Produk', 'textdomain'),
                'all_items'     => __('Semua Produk', 'textdomain'),
            ],
            'public'       => true,
            'has_archive'  => true,
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-cart',
            'rewrite'      => ['slug' => 'produk'],
            'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
        ],
        'fields' => [
            'price' => [
                'label'       => 'Harga',
                'description' => 'Harga produk',
                'meta'        => [
                    'type'          => 'string',
                    'single'        => true,
                    'show_in_rest'  => true,
                ],
                'filterable'  => true,
                'filter'      => [
                    'mode'      => 'meta_exact',
                    'query_var' => 'price',
                    'compare'   => '=',
                ],
            ],
            'rating' => [
                'label'       => 'Rating',
                'description' => 'Rating produk 0-5',
                'meta'        => [
                    'type'          => 'number',
                    'single'        => true,
                    'show_in_rest'  => true,
                ],
                'filterable'  => true,
                'filter'      => [
                    'mode'      => 'meta_numeric',
                    'query_var' => 'rating_min',
                    'compare'   => '>=',
                    'type'      => 'NUMERIC',
                ],
            ],
        ],
        'taxonomies' => ['produk_category', 'produk_tag'],
    ];
    
    return $post_types;
});
```

#### Metode 2: Menggunakan Field Catalog

```php
add_filter('sofir/cpt/definitions', function($post_types) {
    $manager = \Sofir\Cpt\Manager::instance();
    $catalog = $manager->get_field_catalog();
    
    // Gunakan field yang sudah ada di catalog
    $post_types['resto'] = [
        'args' => [
            'labels' => [
                'name'          => 'Restoran',
                'singular_name' => 'Restoran',
            ],
            'menu_icon' => 'dashicons-food',
            'rewrite'   => ['slug' => 'restoran'],
            'supports'  => ['title', 'editor', 'thumbnail'],
        ],
        'fields' => [
            'location' => $catalog['location'],  // Field lokasi siap pakai
            'hours'    => $catalog['hours'],     // Field jam operasional
            'rating'   => $catalog['rating'],    // Field rating
            'contact'  => $catalog['contact'],   // Field kontak
        ],
    ];
    
    return $post_types;
});
```

### 1.5 Menggunakan CPT di Template

#### Query CPT

```php
// Query listing dengan filter
$args = [
    'post_type' => 'listing',
    'posts_per_page' => 10,
    'meta_query' => [
        [
            'key'     => 'sofir_listing_rating',
            'value'   => 4,
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ],
    ],
];

$query = new WP_Query($args);

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();
        
        // Ambil meta data
        $rating = get_post_meta(get_the_ID(), 'sofir_listing_rating', true);
        $location = get_post_meta(get_the_ID(), 'sofir_listing_location', true);
        
        // Tampilkan
        echo '<h2>' . get_the_title() . '</h2>';
        echo '<p>Rating: ' . $rating . '</p>';
        echo '<p>Lokasi: ' . $location['city'] . '</p>';
    }
    wp_reset_postdata();
}
```

#### Shortcode untuk CPT

```php
// Buat shortcode untuk listing
add_shortcode('listing_grid', function($atts) {
    $atts = shortcode_atts([
        'count' => 6,
        'category' => '',
    ], $atts);
    
    $args = [
        'post_type' => 'listing',
        'posts_per_page' => (int) $atts['count'],
    ];
    
    if (!empty($atts['category'])) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'listing_category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ],
        ];
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        echo '<div class="listing-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            echo '<div class="listing-item">';
            the_post_thumbnail('medium');
            echo '<h3>' . get_the_title() . '</h3>';
            echo '</div>';
        }
        echo '</div>';
        wp_reset_postdata();
    }
    
    return ob_get_clean();
});

// Penggunaan: [listing_grid count="6" category="restaurant"]
```

---

## 2. Taxonomy

### 2.1 Pengertian Taxonomy

Taxonomy adalah cara mengelompokkan konten di WordPress. Seperti Categories dan Tags, tapi bisa dibuat custom untuk CPT tertentu.

### 2.2 Taxonomy Bawaan SOFIR

SOFIR sudah menyediakan 6 taxonomy siap pakai:

| Taxonomy | Untuk CPT | Hierarchical | Fungsi |
|----------|-----------|--------------|--------|
| `listing_category` | listing | ✅ Yes | Kategori listing (Restaurant, Hotel, dll) |
| `listing_location` | listing | ❌ No | Tag lokasi listing |
| `profile_category` | profile | ❌ No | Kategori profil |
| `event_category` | event | ✅ Yes | Kategori event |
| `event_tag` | event | ❌ No | Tag event |
| `appointment_service` | appointment | ✅ Yes | Jenis layanan appointment |

### 2.3 Membuat Taxonomy Baru

#### Metode 1: Filter Hook

```php
add_filter('sofir/cpt/taxonomy_definitions', function($taxonomies) {
    $taxonomies['produk_brand'] = [
        'args' => [
            'labels' => [
                'name'          => 'Brand',
                'singular_name' => 'Brand',
                'add_new_item'  => 'Tambah Brand Baru',
                'search_items'  => 'Cari Brand',
            ],
            'hierarchical'      => false,  // false = seperti tags
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'brand'],
        ],
        'object_type' => ['produk'],  // CPT yang menggunakan taxonomy ini
        'filterable'  => true,        // Bisa difilter di REST API
    ];
    
    return $taxonomies;
});
```

#### Metode 2: WordPress Native (tanpa filter)

```php
add_action('init', function() {
    register_taxonomy('produk_warna', ['produk'], [
        'labels' => [
            'name'          => 'Warna',
            'singular_name' => 'Warna',
        ],
        'hierarchical'      => true,  // true = seperti categories
        'show_in_rest'      => true,
        'show_admin_column' => true,
    ]);
});
```

### 2.4 Struktur Taxonomy

```php
[
    'args' => [
        'labels'            => [...],     // Label tampilan
        'hierarchical'      => true/false, // Category-style atau Tag-style
        'show_in_rest'      => true,      // Gutenberg & REST API support
        'show_admin_column' => true,      // Tampil di kolom admin
        'rewrite'           => ['slug' => 'slug'], // URL slug
    ],
    'object_type' => ['cpt1', 'cpt2'],   // CPT yang menggunakan
    'filterable'  => true,                // REST API filter support
]
```

### 2.5 Menggunakan Taxonomy

#### Assign Terms ke Post

```php
// Set single term (category-style)
wp_set_object_terms($post_id, 'restaurant', 'listing_category');

// Set multiple terms (tag-style)
wp_set_object_terms($post_id, ['jakarta', 'bandung'], 'listing_location');

// Append (tidak hapus existing)
wp_set_object_terms($post_id, 'cafe', 'listing_category', true);
```

#### Query by Taxonomy

```php
$args = [
    'post_type' => 'listing',
    'tax_query' => [
        'relation' => 'AND',
        [
            'taxonomy' => 'listing_category',
            'field'    => 'slug',
            'terms'    => 'restaurant',
        ],
        [
            'taxonomy' => 'listing_location',
            'field'    => 'slug',
            'terms'    => ['jakarta', 'bandung'],
            'operator' => 'IN',
        ],
    ],
];

$query = new WP_Query($args);
```

#### Display Terms

```php
// Get terms for a post
$categories = get_the_terms($post_id, 'listing_category');
if ($categories && !is_wp_error($categories)) {
    foreach ($categories as $cat) {
        echo '<span>' . esc_html($cat->name) . '</span>';
    }
}

// Get all terms
$all_categories = get_terms([
    'taxonomy'   => 'listing_category',
    'hide_empty' => false,
]);
```

---

## 3. Field Catalog

### 3.1 15 Field Types Bawaan

SOFIR menyediakan 15 field types siap pakai:

| Field | Type | Fungsi | Filter Mode |
|-------|------|--------|-------------|
| **location** | object | Address, city, lat/lng | meta_like |
| **hours** | object | Jam operasional per hari | schedule |
| **rating** | number | Rating 0-5 | meta_numeric |
| **status** | string | Status (active, closed, featured) | meta_exact |
| **price** | string | Price range ($, $$, Premium) | meta_exact |
| **contact** | object | Email, phone, website | - |
| **gallery** | array | Array of attachment IDs | - |
| **attributes** | object | Key-value pairs | meta_like |
| **event_date** | object | Start & end datetime | date_range |
| **event_capacity** | number | Max attendees | meta_numeric |
| **appointment_datetime** | string | Appointment datetime | date_range |
| **appointment_duration** | number | Duration in minutes | meta_numeric |
| **appointment_status** | string | Status appointment | meta_exact |
| **appointment_provider** | number | Provider user ID | meta_exact |
| **appointment_client** | number | Client user ID | meta_exact |

### 3.2 Struktur Field

```php
'field_name' => [
    'label'       => 'Label Field',
    'description' => 'Deskripsi field',
    'meta'        => [
        'type'              => 'string|number|object|array',
        'single'            => true,
        'show_in_rest'      => true,
        'default'           => ...,
        'sanitize_callback' => 'callback_function',
        'auth_callback'     => 'callback_function',
    ],
    'filterable'  => true,  // Apakah bisa difilter?
    'filter'      => [
        'mode'      => 'meta_like|meta_exact|meta_numeric|schedule|date_range',
        'query_var' => 'query_param_name',
        'compare'   => 'LIKE|=|>=|<=',
        'type'      => 'NUMERIC|DATE',
    ],
]
```

### 3.3 Filter Modes

#### 1. meta_like
Untuk text search (LIKE query).

```php
'filter' => [
    'mode'      => 'meta_like',
    'query_var' => 'search_name',
    'compare'   => 'LIKE',
]

// REST API: /wp-json/wp/v2/listing?sofir_listing_search_name=jakarta
```

#### 2. meta_exact
Untuk exact match.

```php
'filter' => [
    'mode'      => 'meta_exact',
    'query_var' => 'status',
    'compare'   => '=',
]

// REST API: /wp-json/wp/v2/listing?sofir_listing_status=active
```

#### 3. meta_numeric
Untuk filter numeric (>=, <=, =).

```php
'filter' => [
    'mode'      => 'meta_numeric',
    'query_var' => 'rating_min',
    'compare'   => '>=',
    'type'      => 'NUMERIC',
]

// REST API: /wp-json/wp/v2/listing?sofir_listing_rating_min=4
```

#### 4. schedule
Untuk "open now" filter (jam operasional).

```php
'filter' => [
    'mode'      => 'schedule',
    'query_var' => 'open_now',
]

// REST API: /wp-json/wp/v2/listing?sofir_listing_open_now=1
```

#### 5. date_range
Untuk filter tanggal/waktu.

```php
'filter' => [
    'mode'      => 'date_range',
    'query_var' => 'event_after',
    'compare'   => '>=',
]

// REST API: /wp-json/wp/v2/event?sofir_event_event_after=2024-12-01
```

### 3.4 Membuat Custom Field

```php
add_filter('sofir/cpt/field_catalog', function($catalog) {
    $catalog['video_url'] = [
        'label'       => 'Video URL',
        'description' => 'YouTube or Vimeo URL',
        'meta'        => [
            'type'              => 'string',
            'single'            => true,
            'show_in_rest'      => true,
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ],
        'filterable'  => false,
    ];
    
    $catalog['stock'] = [
        'label'       => 'Stock',
        'description' => 'Product stock quantity',
        'meta'        => [
            'type'              => 'number',
            'single'            => true,
            'show_in_rest'      => true,
            'default'           => 0,
            'sanitize_callback' => 'absint',
        ],
        'filterable'  => true,
        'filter'      => [
            'mode'      => 'meta_numeric',
            'query_var' => 'min_stock',
            'compare'   => '>=',
            'type'      => 'NUMERIC',
        ],
    ];
    
    return $catalog;
});
```

### 3.5 Menggunakan Field di Frontend

```php
// Get location field
$location = get_post_meta($post_id, 'sofir_listing_location', true);
echo $location['city'] . ', ' . $location['country'];

// Get rating
$rating = get_post_meta($post_id, 'sofir_listing_rating', true);
echo 'Rating: ' . $rating . '/5';

// Get contact
$contact = get_post_meta($post_id, 'sofir_listing_contact', true);
echo 'Email: ' . $contact['email'];
echo 'Phone: ' . $contact['phone'];

// Get gallery
$gallery = get_post_meta($post_id, 'sofir_listing_gallery', true);
if (is_array($gallery)) {
    foreach ($gallery as $attachment_id) {
        echo wp_get_attachment_image($attachment_id, 'medium');
    }
}

// Get event date
$event_date = get_post_meta($post_id, 'sofir_event_event_date', true);
echo 'Start: ' . date('F j, Y', strtotime($event_date['start']));
echo 'End: ' . date('F j, Y', strtotime($event_date['end']));
```

---

## 4. Template System

### 4.1 34 Template Siap Pakai

SOFIR menyediakan 34 page templates dalam 8 kategori:

#### Landing Pages (7)
- startup-launch
- agency-spotlight
- restaurant-menu
- real-estate-property
- event-registration
- course-landing
- saas-product

#### Directory (6)
- city-directory
- healthcare-network
- fitness-studio-directory
- hotel-booking
- job-board
- lawyer-directory

#### Blog (5)
- modern-magazine
- tech-news-portal
- personal-blog
- recipe-blog
- travel-blog

#### Profile (5)
- business-profile
- freelancer-portfolio
- creative-agency
- personal-resume
- photography-portfolio

#### Ecommerce (2)
- product-catalog
- checkout-page

#### Membership (2)
- member-dashboard
- pricing-plans

#### Header (4)
- modern-header
- minimal-header
- business-header
- centered-header

#### Footer (4)
- multi-column-footer
- simple-footer
- business-footer
- newsletter-footer

### 4.2 Menggunakan Template

#### Via Admin Panel

1. Login ke WordPress Admin
2. Menu **SOFIR** → **Templates**
3. Browse kategori template
4. Klik **Preview** untuk melihat tampilan
5. Klik **Import Template** untuk membuat page baru
6. Edit dan customize sesuai kebutuhan

#### Via Shortcode

```php
// Import dan gunakan template via shortcode
[sofir_template slug="startup-launch" title="Our Startup"]
```

#### Programmatically

```php
// Import template via code
$template_slug = 'restaurant-menu';
$templates = get_option('sofir_templates', []);

if (isset($templates[$template_slug])) {
    $template = $templates[$template_slug];
    
    $post_id = wp_insert_post([
        'post_title'   => 'Menu Restoran',
        'post_content' => $template['content'],
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ]);
}
```

### 4.3 Header & Footer Templates

#### Copy Pattern (One-Click)

1. Buka **SOFIR** → **Templates**
2. Scroll ke **Header Designs** atau **Footer Designs**
3. Klik tombol **Copy Pattern**
4. Pattern otomatis tersalin ke clipboard
5. Buka Gutenberg Editor
6. Paste (Ctrl+V / Cmd+V)
7. Customize!

#### Clickable Preview

- Klik **gambar preview** untuk melihat live template
- Preview muncul dalam modal iframe
- Tekan **ESC** untuk menutup
- Support keyboard navigation

#### Menggunakan di FSE (Full Site Editing)

```
1. Appearance → Editor
2. Template Parts → Header
3. Click "+" add new
4. Paste SOFIR header pattern
5. Save
6. Repeat untuk Footer
```

### 4.4 Membuat Template Custom

#### Struktur Template

Template menggunakan Gutenberg block syntax:

```html
<!-- wp:group {"align":"full","backgroundColor":"primary"} -->
<div class="wp-block-group alignfull has-primary-background-color">
    <!-- wp:heading {"level":1} -->
    <h1>Welcome to Our Website</h1>
    <!-- /wp:heading -->
    
    <!-- wp:sofir/action {"text":"Get Started","url":"#"} /-->
</div>
<!-- /wp:group -->
```

#### Register Template Baru

```php
add_filter('sofir/templates/definitions', function($templates) {
    $templates['my-custom-page'] = [
        'category'     => 'landing',
        'name'         => 'My Custom Landing',
        'description'  => 'Custom landing page template',
        'preview'      => 'path/to/preview.svg',
        'content'      => '<!-- wp:blocks here -->',
        'context'      => 'page', // 'page' | 'template' | 'pattern'
        'requirements' => [
            'theme_support' => ['align-wide'],
        ],
    ];
    
    return $templates;
});
```

---

## 5. REST API Filtering

### 5.1 Endpoint CPT

Semua CPT otomatis tersedia di REST API:

```
GET /wp-json/wp/v2/listing
GET /wp-json/wp/v2/profile
GET /wp-json/wp/v2/article
GET /wp-json/wp/v2/event
GET /wp-json/wp/v2/appointment
```

### 5.2 Filter Parameters

#### Meta Filters

```bash
# Filter by rating (minimum 4)
GET /wp-json/wp/v2/listing?sofir_listing_rating_min=4

# Filter by status
GET /wp-json/wp/v2/listing?sofir_listing_status=active

# Filter by price range
GET /wp-json/wp/v2/listing?sofir_listing_price=$$

# Search by location
GET /wp-json/wp/v2/listing?sofir_listing_location=jakarta

# Open now
GET /wp-json/wp/v2/listing?sofir_listing_open_now=1

# Event after date
GET /wp-json/wp/v2/event?sofir_event_event_after=2024-12-01

# Appointment status
GET /wp-json/wp/v2/appointment?sofir_appointment_appointment_status=confirmed
```

#### Taxonomy Filters

```bash
# Filter by taxonomy
GET /wp-json/wp/v2/listing?sofir_tax_listing_category=restaurant

# Multiple terms (comma-separated)
GET /wp-json/wp/v2/listing?sofir_tax_listing_location=jakarta,bandung

# Combined filters
GET /wp-json/wp/v2/listing?sofir_listing_rating_min=4&sofir_tax_listing_category=restaurant
```

#### Standard WordPress Filters

```bash
# Pagination
GET /wp-json/wp/v2/listing?per_page=10&page=2

# Search
GET /wp-json/wp/v2/listing?search=cafe

# Order
GET /wp-json/wp/v2/listing?orderby=date&order=desc
GET /wp-json/wp/v2/listing?orderby=title&order=asc

# Author
GET /wp-json/wp/v2/listing?author=1

# Date
GET /wp-json/wp/v2/listing?after=2024-01-01T00:00:00
GET /wp-json/wp/v2/listing?before=2024-12-31T23:59:59
```

### 5.3 Response Example

```json
{
  "id": 123,
  "title": {
    "rendered": "Amazing Restaurant"
  },
  "content": {
    "rendered": "<p>Best food in town</p>"
  },
  "meta": {
    "sofir_listing_location": {
      "address": "Jl. Sudirman No. 1",
      "city": "Jakarta",
      "country": "Indonesia",
      "lat": -6.2088,
      "lng": 106.8456
    },
    "sofir_listing_rating": 4.5,
    "sofir_listing_status": "active",
    "sofir_listing_contact": {
      "email": "info@resto.com",
      "phone": "+62812345678",
      "website": "https://resto.com"
    }
  }
}
```

### 5.4 JavaScript Fetch Example

```javascript
// Fetch listings dengan rating >= 4
fetch('/wp-json/wp/v2/listing?sofir_listing_rating_min=4')
  .then(response => response.json())
  .then(data => {
    data.forEach(listing => {
      console.log(listing.title.rendered);
      console.log(listing.meta.sofir_listing_rating);
    });
  });

// Fetch dengan multiple filters
const params = new URLSearchParams({
  sofir_listing_rating_min: 4,
  sofir_tax_listing_category: 'restaurant',
  sofir_listing_open_now: 1,
  per_page: 10
});

fetch(`/wp-json/wp/v2/listing?${params}`)
  .then(response => response.json())
  .then(data => {
    // Process data
  });
```

---

## 6. Contoh Praktis

### 6.1 Membuat Directory Real Estate

#### Step 1: Define CPT

```php
add_filter('sofir/cpt/definitions', function($post_types) {
    $manager = \Sofir\Cpt\Manager::instance();
    $catalog = $manager->get_field_catalog();
    
    $post_types['property'] = [
        'args' => [
            'labels' => [
                'name'          => 'Properti',
                'singular_name' => 'Properti',
                'add_new_item'  => 'Tambah Properti',
            ],
            'menu_icon' => 'dashicons-admin-home',
            'rewrite'   => ['slug' => 'property'],
            'supports'  => ['title', 'editor', 'thumbnail', 'excerpt'],
        ],
        'fields' => [
            'location' => $catalog['location'],
            'price'    => $catalog['price'],
            'gallery'  => $catalog['gallery'],
            'contact'  => $catalog['contact'],
            'status'   => $catalog['status'],
        ],
        'taxonomies' => ['property_type', 'property_location'],
    ];
    
    return $post_types;
});
```

#### Step 2: Define Taxonomies

```php
add_filter('sofir/cpt/taxonomy_definitions', function($taxonomies) {
    $taxonomies['property_type'] = [
        'args' => [
            'labels' => [
                'name'          => 'Tipe Properti',
                'singular_name' => 'Tipe',
            ],
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
        ],
        'object_type' => ['property'],
        'filterable'  => true,
    ];
    
    $taxonomies['property_location'] = [
        'args' => [
            'labels' => [
                'name'          => 'Lokasi',
                'singular_name' => 'Lokasi',
            ],
            'hierarchical'      => false,
            'show_in_rest'      => true,
        ],
        'object_type' => ['property'],
        'filterable'  => true,
    ];
    
    return $taxonomies;
});
```

#### Step 3: Add Custom Fields

```php
add_filter('sofir/cpt/field_catalog', function($catalog) {
    $catalog['bedrooms'] = [
        'label' => 'Kamar Tidur',
        'meta'  => [
            'type'          => 'number',
            'single'        => true,
            'show_in_rest'  => true,
            'default'       => 0,
        ],
        'filterable' => true,
        'filter' => [
            'mode'      => 'meta_numeric',
            'query_var' => 'bedrooms_min',
            'compare'   => '>=',
            'type'      => 'NUMERIC',
        ],
    ];
    
    $catalog['bathrooms'] = [
        'label' => 'Kamar Mandi',
        'meta'  => [
            'type'          => 'number',
            'single'        => true,
            'show_in_rest'  => true,
            'default'       => 0,
        ],
        'filterable' => true,
        'filter' => [
            'mode'      => 'meta_numeric',
            'query_var' => 'bathrooms_min',
            'compare'   => '>=',
            'type'      => 'NUMERIC',
        ],
    ];
    
    return $catalog;
});

// Update property definition untuk include custom fields
add_filter('sofir/cpt/definitions', function($post_types) {
    if (isset($post_types['property'])) {
        $manager = \Sofir\Cpt\Manager::instance();
        $catalog = $manager->get_field_catalog();
        
        $post_types['property']['fields']['bedrooms'] = $catalog['bedrooms'];
        $post_types['property']['fields']['bathrooms'] = $catalog['bathrooms'];
    }
    
    return $post_types;
});
```

#### Step 4: Create Archive Page

```php
// templates/archive-property.php
<?php get_header(); ?>

<div class="property-archive">
    <h1>Daftar Properti</h1>
    
    <!-- Filter Form -->
    <form id="property-filter" method="get">
        <select name="property_type">
            <option value="">Semua Tipe</option>
            <?php
            $types = get_terms(['taxonomy' => 'property_type']);
            foreach ($types as $type) {
                echo '<option value="' . $type->slug . '">' . $type->name . '</option>';
            }
            ?>
        </select>
        
        <input type="number" name="bedrooms_min" placeholder="Min. Kamar Tidur">
        <input type="number" name="bathrooms_min" placeholder="Min. Kamar Mandi">
        
        <button type="submit">Filter</button>
    </form>
    
    <!-- Property Grid -->
    <div class="property-grid">
        <?php
        $args = [
            'post_type' => 'property',
            'posts_per_page' => 12,
        ];
        
        // Apply filters from GET
        if (!empty($_GET['property_type'])) {
            $args['tax_query'] = [[
                'taxonomy' => 'property_type',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['property_type']),
            ]];
        }
        
        if (!empty($_GET['bedrooms_min'])) {
            $args['meta_query'][] = [
                'key'     => 'sofir_property_bedrooms',
                'value'   => absint($_GET['bedrooms_min']),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ];
        }
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                $bedrooms = get_post_meta(get_the_ID(), 'sofir_property_bedrooms', true);
                $bathrooms = get_post_meta(get_the_ID(), 'sofir_property_bathrooms', true);
                $location = get_post_meta(get_the_ID(), 'sofir_property_location', true);
                ?>
                <div class="property-card">
                    <?php the_post_thumbnail('medium'); ?>
                    <h3><?php the_title(); ?></h3>
                    <p><?php echo esc_html($location['city']); ?></p>
                    <p>
                        🛏️ <?php echo $bedrooms; ?> Kamar |
                        🚿 <?php echo $bathrooms; ?> Kamar Mandi
                    </p>
                    <a href="<?php the_permalink(); ?>">Lihat Detail</a>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
</div>

<?php get_footer(); ?>
```

#### Step 5: REST API Endpoint

```javascript
// Frontend JavaScript untuk filter dengan AJAX
document.getElementById('property-filter').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const params = new URLSearchParams();
    
    // Build query params
    if (formData.get('property_type')) {
        params.append('sofir_tax_property_type', formData.get('property_type'));
    }
    if (formData.get('bedrooms_min')) {
        params.append('sofir_property_bedrooms_min', formData.get('bedrooms_min'));
    }
    if (formData.get('bathrooms_min')) {
        params.append('sofir_property_bathrooms_min', formData.get('bathrooms_min'));
    }
    
    // Fetch dari REST API
    const response = await fetch(`/wp-json/wp/v2/property?${params}`);
    const properties = await response.json();
    
    // Render results
    const grid = document.querySelector('.property-grid');
    grid.innerHTML = properties.map(prop => `
        <div class="property-card">
            <img src="${prop.featured_media_url}" alt="${prop.title.rendered}">
            <h3>${prop.title.rendered}</h3>
            <p>${prop.meta.sofir_property_location.city}</p>
            <p>
                🛏️ ${prop.meta.sofir_property_bedrooms} Kamar |
                🚿 ${prop.meta.sofir_property_bathrooms} Kamar Mandi
            </p>
            <a href="${prop.link}">Lihat Detail</a>
        </div>
    `).join('');
});
```

### 6.2 Event Management System

```php
// Sudah tersedia di SOFIR!
// CPT: 'event'
// Fields: event_date, event_capacity, location, contact, gallery

// Query upcoming events
$args = [
    'post_type' => 'event',
    'meta_query' => [
        [
            'key'     => 'sofir_event_event_date',
            'value'   => gmdate('Y-m-d'),
            'compare' => '>=',
            'type'    => 'DATE',
        ],
    ],
    'orderby' => 'meta_value',
    'meta_key' => 'sofir_event_event_date',
    'order' => 'ASC',
];

$events = new WP_Query($args);

// REST API
// GET /wp-json/wp/v2/event?sofir_event_event_after=2024-12-01
```

### 6.3 Appointment Booking

```php
// Sudah tersedia di SOFIR!
// CPT: 'appointment'
// Fields: appointment_datetime, appointment_duration, appointment_status, 
//         appointment_provider, appointment_client

// Query available appointments
$args = [
    'post_type' => 'appointment',
    'meta_query' => [
        'relation' => 'AND',
        [
            'key'     => 'sofir_appointment_appointment_status',
            'value'   => 'pending',
            'compare' => '=',
        ],
        [
            'key'     => 'sofir_appointment_appointment_datetime',
            'value'   => gmdate('Y-m-d H:i:s'),
            'compare' => '>=',
            'type'    => 'DATETIME',
        ],
    ],
];

$appointments = new WP_Query($args);

// REST API
// GET /wp-json/wp/v2/appointment?sofir_appointment_appointment_status=pending
```

---

## 🎯 Best Practices

### CPT Development

1. **Gunakan Field Catalog** - Manfaatkan 15 field types yang sudah ada
2. **Enable REST API** - Set `show_in_rest => true` untuk Gutenberg & API support
3. **Hierarchical Taxonomy** - Gunakan untuk kategori berlevel
4. **Flat Taxonomy** - Gunakan untuk tags atau labels
5. **Filterable Fields** - Enable filtering untuk field yang sering diquery

### Performance

1. **Index Meta Keys** - Untuk field yang sering di-query, pertimbangkan indexing
2. **Pagination** - Selalu gunakan pagination untuk listing
3. **Lazy Load Images** - Untuk gallery dan featured images
4. **Cache Queries** - Cache hasil query yang sering diakses

### Security

1. **Sanitize Input** - Gunakan sanitize callbacks
2. **Authorization** - Gunakan auth_callback untuk protect meta
3. **Validate Data** - Validate sebelum save
4. **Escape Output** - Selalu escape data saat output

### SEO

1. **Custom Titles** - Buat custom page titles untuk CPT archive
2. **Meta Description** - Add meta description untuk CPT single pages
3. **Structured Data** - Gunakan schema markup untuk rich snippets
4. **XML Sitemap** - Include CPT di sitemap

---

## 🆘 Troubleshooting

### CPT tidak muncul

```php
// Flush rewrite rules
flush_rewrite_rules();

// Atau via code
add_action('after_switch_theme', 'flush_rewrite_rules');
```

### Meta tidak tersimpan

```php
// Pastikan authorize_meta return true
public static function authorize_meta(): bool {
    return current_user_can('edit_posts');
}

// Atau buat custom capability check
return current_user_can('edit_' . $post_type);
```

### REST API filter tidak bekerja

```php
// Pastikan 'filterable' => true
// Pastikan 'show_in_rest' => true
// Check query_var naming: sofir_{post_type}_{field}
```

### Template tidak import

```php
// Check user capability
current_user_can('edit_pages');

// Check nonce
wp_verify_nonce($_POST['nonce'], 'sofir_import_template');

// Check template exists
isset($templates[$slug]);
```

---

## 📚 Resources

### Dokumentasi Terkait

- [Header & Footer Templates](./templates/HEADER_FOOTER_TEMPLATES.md)
- [Quick Start Guide](./templates/QUICK_START.md)
- [Usage Examples](./templates/USAGE_EXAMPLES.md)
- [Main README](./README.md)

### WordPress Codex

- [register_post_type()](https://developer.wordpress.org/reference/functions/register_post_type/)
- [register_taxonomy()](https://developer.wordpress.org/reference/functions/register_taxonomy/)
- [register_post_meta()](https://developer.wordpress.org/reference/functions/register_post_meta/)
- [WP_Query](https://developer.wordpress.org/reference/classes/wp_query/)
- [REST API Handbook](https://developer.wordpress.org/rest-api/)

### Support

**Questions?** Open an issue di repository atau contact support.

---

**Version:** 1.0.0  
**Last Updated:** 2024  
**Author:** SOFIR Team (Sobri + Firman)

---

## Quick Reference

```
┌─────────────────────────────────────────────────────────┐
│  SOFIR CPT Manager - Quick Commands                    │
├─────────────────────────────────────────────────────────┤
│  📝 Create CPT       → Filter: sofir/cpt/definitions   │
│  🏷️  Create Taxonomy  → Filter: sofir/cpt/taxonomy_definitions │
│  🔧 Custom Field     → Filter: sofir/cpt/field_catalog │
│  🎨 Template         → SOFIR → Templates menu          │
│  🔌 REST API         → /wp-json/wp/v2/{post_type}      │
│  🗂️  Query Meta       → get_post_meta($id, $key, true) │
└─────────────────────────────────────────────────────────┘
```
