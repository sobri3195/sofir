# SOFIR CPT Manager - Quick Reference

Quick reference guide untuk developer yang ingin cepat implement fitur CPT Manager.

---

## 🚀 Quick Start

### Create Custom Post Type

```php
add_filter('sofir/cpt/definitions', function($post_types) {
    $post_types['product'] = [
        'args' => [
            'labels'    => [ 'name' => 'Products', 'singular_name' => 'Product' ],
            'menu_icon' => 'dashicons-cart',
        ],
        'fields' => [
            'price'  => [ /* from field catalog */ ],
            'rating' => [ /* from field catalog */ ],
        ],
        'taxonomies' => [ 'product_category' ],
    ];
    
    return $post_types;
});
```

### Create Taxonomy

```php
add_filter('sofir/taxonomy/definitions', function($taxonomies) {
    $taxonomies['product_category'] = [
        'args' => [
            'labels'       => [ 'name' => 'Categories', 'singular_name' => 'Category' ],
            'hierarchical' => true,
        ],
        'object_type' => [ 'product' ],
        'filterable'  => true,
    ];
    
    return $taxonomies;
});
```

---

## 📝 Field Catalog (Quick Copy)

```php
$manager = \Sofir\Cpt\Manager::instance();
$catalog = $manager->get_field_catalog();

// Available fields:
'location'             // Address + coordinates
'hours'                // Weekly schedule
'rating'               // 0-5 stars
'status'               // active, closed, featured, pending
'price'                // Price range tag
'contact'              // Email/phone/website
'gallery'              // Attachment IDs array
'attributes'           // Key-value pairs
'event_date'           // Start/end datetime
'event_capacity'       // Max attendees
'appointment_datetime' // Appointment time
'appointment_duration' // Duration in minutes
'appointment_status'   // Booking status
'appointment_provider' // Provider user ID
'appointment_client'   // Client user ID

// Use in CPT definition:
'fields' => [
    'location' => $catalog['location'],
    'rating'   => $catalog['rating'],
]
```

---

## 🎣 Event Hooks (Copy & Paste)

### CPT Registered Hook

```php
add_action('sofir/cpt/registered_listing', function($definition, $args) {
    // Your code here
    error_log('Listing CPT registered!');
}, 10, 2);
```

### Meta Field Updated Hook

```php
add_action('sofir/cpt/listing/meta_updated_rating', function($post_id, $rating) {
    if ($rating >= 4.5) {
        update_post_meta($post_id, 'sofir_listing_status', 'featured');
    }
}, 10, 2);
```

### Taxonomy Saved Hook

```php
add_action('sofir/taxonomy/saved_listing_category', function($definition) {
    // Auto-populate default terms
    $terms = [ 'Restaurant', 'Hotel', 'Cafe' ];
    foreach ($terms as $term) {
        if (!term_exists($term, 'listing_category')) {
            wp_insert_term($term, 'listing_category');
        }
    }
});
```

---

## 🔍 Query Examples

### Basic Query

```php
$query = new WP_Query([
    'post_type' => 'listing',
    'posts_per_page' => 10,
]);
```

### With Meta Filter

```php
$query = new WP_Query([
    'post_type' => 'listing',
    'meta_query' => [
        [
            'key'     => 'sofir_listing_rating',
            'value'   => 4,
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ],
    ],
]);
```

### With Taxonomy Filter

```php
$query = new WP_Query([
    'post_type' => 'listing',
    'tax_query' => [
        [
            'taxonomy' => 'listing_category',
            'field'    => 'slug',
            'terms'    => 'restaurant',
        ],
    ],
]);
```

### Multiple Filters

```php
$query = new WP_Query([
    'post_type' => 'listing',
    'meta_query' => [
        [
            'key'     => 'sofir_listing_rating',
            'value'   => 4,
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ],
        [
            'key'     => 'sofir_listing_status',
            'value'   => 'active',
            'compare' => '=',
        ],
    ],
    'tax_query' => [
        [
            'taxonomy' => 'listing_category',
            'field'    => 'slug',
            'terms'    => [ 'restaurant', 'cafe' ],
            'operator' => 'IN',
        ],
    ],
]);
```

---

## 🌐 REST API URLs

### Basic Request

```
GET /wp-json/wp/v2/listing
```

### With Filters

```
GET /wp-json/wp/v2/listing?sofir_listing_rating_min=4
GET /wp-json/wp/v2/listing?sofir_listing_status=active
GET /wp-json/wp/v2/listing?sofir_listing_open_now=1
GET /wp-json/wp/v2/event?sofir_event_event_after=2024-12-01
GET /wp-json/wp/v2/listing?sofir_tax_listing_category=restaurant,cafe
```

### Multiple Filters

```
GET /wp-json/wp/v2/listing?sofir_listing_rating_min=4&sofir_listing_status=active&sofir_tax_listing_category=restaurant
```

---

## 📊 Statistics API

### Get CPT Stats

```php
$manager = \Sofir\Cpt\Manager::instance();
$stats = $manager->get_cpt_statistics();

echo $stats['listing']['published'];  // Number of published listings
echo $stats['listing']['total'];      // Total listings (published + draft + pending)
echo count($stats['listing']['fields']); // Number of fields
```

### Get Taxonomy Stats

```php
$stats = $manager->get_taxonomy_statistics();

echo $stats['listing_category']['term_count'];  // Number of terms
echo $stats['listing_category']['hierarchical']; // true/false
```

---

## 🎨 Template Management

### Get Template

```php
$manager = \Sofir\Cpt\Manager::instance();
$templates = $manager->get_cpt_templates('event');
```

### Set Template

```php
$template = [
    [ 'core/heading', [ 'level' => 1 ] ],
    [ 'sofir/dynamic-data', [
        'dataSource' => 'cpt_field',
        'dataKey'    => 'event_date',
        'format'     => 'date',
    ] ],
    [ 'core/paragraph', [] ],
];

$manager->set_cpt_template('event', $template, 'insert');
```

**Template Lock Options:**
- `''` - No lock (users can add/remove blocks)
- `'all'` - Fully locked (no changes)
- `'insert'` - Can't insert new blocks (can move/remove)

---

## 🔧 Get/Set Meta Values

### Get Meta

```php
// Location
$location = get_post_meta($post_id, 'sofir_listing_location', true);
echo $location['city'];
echo $location['lat'];

// Rating
$rating = get_post_meta($post_id, 'sofir_listing_rating', true);

// Hours
$hours = get_post_meta($post_id, 'sofir_listing_hours', true);
$monday_hours = $hours['monday'] ?? [];

// Gallery
$gallery = get_post_meta($post_id, 'sofir_listing_gallery', true);
foreach ($gallery as $attachment_id) {
    echo wp_get_attachment_image($attachment_id, 'medium');
}

// Attributes
$attributes = get_post_meta($post_id, 'sofir_listing_attributes', true);
echo $attributes['wifi'] ?? 'N/A';
```

### Update Meta

```php
// Location
update_post_meta($post_id, 'sofir_listing_location', [
    'address' => '123 Main St',
    'city'    => 'Jakarta',
    'lat'     => -6.2088,
    'lng'     => 106.8456,
]);

// Rating
update_post_meta($post_id, 'sofir_listing_rating', 4.5);

// Status
update_post_meta($post_id, 'sofir_listing_status', 'featured');

// Gallery
update_post_meta($post_id, 'sofir_listing_gallery', [ 123, 456, 789 ]);

// Attributes
update_post_meta($post_id, 'sofir_listing_attributes', [
    'wifi'    => 'yes',
    'parking' => 'available',
    'price'   => '$$',
]);
```

---

## 🔨 Utility Functions

### Get Post Type Fields

```php
$manager = \Sofir\Cpt\Manager::instance();
$fields = $manager->get_post_type_fields('listing');

foreach ($fields as $field_key => $field_config) {
    echo $field_config['label'];
}
```

### Get Filter Query Vars

```php
$query_vars = $manager->get_filter_query_vars('listing');
// [ 'sofir_listing_location', 'sofir_listing_rating_min', ... ]
```

### Get Taxonomy Query Vars

```php
$query_vars = $manager->get_taxonomy_query_vars('listing');
// [ 'sofir_tax_listing_category', 'sofir_tax_listing_location', ... ]
```

---

## 🎯 Common Patterns

### Create Shortcode for CPT

```php
add_shortcode('latest_listings', function($atts) {
    $atts = shortcode_atts([
        'count'    => 6,
        'category' => '',
    ], $atts);
    
    $args = [
        'post_type'      => 'listing',
        'posts_per_page' => (int) $atts['count'],
    ];
    
    if ($atts['category']) {
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
        echo '<div class="listings-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            ?>
            <div class="listing-item">
                <?php the_post_thumbnail('medium'); ?>
                <h3><?php the_title(); ?></h3>
                <div class="rating">
                    <?php echo get_post_meta(get_the_ID(), 'sofir_listing_rating', true); ?> ⭐
                </div>
            </div>
            <?php
        }
        echo '</div>';
        wp_reset_postdata();
    }
    
    return ob_get_clean();
});

// Usage: [latest_listings count="6" category="restaurant"]
```

### Add Custom Admin Column

```php
add_filter('manage_listing_posts_columns', function($columns) {
    $columns['rating'] = 'Rating';
    return $columns;
});

add_action('manage_listing_posts_custom_column', function($column, $post_id) {
    if ($column === 'rating') {
        $rating = get_post_meta($post_id, 'sofir_listing_rating', true);
        echo $rating ? $rating . ' ⭐' : '—';
    }
}, 10, 2);
```

### Auto-feature High-rated Content

```php
add_action('sofir/cpt/listing/meta_updated_rating', function($post_id, $rating) {
    if ($rating >= 4.5) {
        update_post_meta($post_id, 'sofir_listing_status', 'featured');
        
        // Optional: Send notification
        $post = get_post($post_id);
        wp_mail(
            get_option('admin_email'),
            'High Rating Achieved',
            "{$post->post_title} achieved {$rating} stars!"
        );
    }
}, 10, 2);
```

---

## 📋 Naming Conventions

### Meta Keys
```
sofir_{post_type}_{field_name}

Examples:
sofir_listing_location
sofir_listing_rating
sofir_event_event_date
```

### Query Vars
```
sofir_{post_type}_{filter_name}

Examples:
sofir_listing_rating_min
sofir_listing_status
sofir_listing_open_now
```

### Taxonomy Filters
```
sofir_tax_{taxonomy}

Examples:
sofir_tax_listing_category
sofir_tax_event_tag
```

### Hook Names
```
sofir/cpt/{action}
sofir/cpt/{post_type}/{action}
sofir/taxonomy/{action}

Examples:
sofir/cpt/registered
sofir/cpt/registered_listing
sofir/cpt/listing/meta_updated
sofir/cpt/listing/meta_updated_rating
sofir/taxonomy/saved
```

---

## ⚡ Performance Tips

1. **Cache queries**: Use transients untuk expensive queries
2. **Limit meta queries**: Use indexed meta keys
3. **Use pagination**: Don't query all posts at once
4. **Lazy load**: Load heavy fields only when needed
5. **Use REST API**: For frontend filtering instead of AJAX

---

## 🆘 Common Issues

### Meta not saving?
```php
// Check authorization callback
current_user_can('edit_post', $post_id);
```

### Filters not working?
```php
// Ensure filterable = true
// Check query var naming: sofir_{post_type}_{field}
// Verify show_in_rest = true
```

### 404 errors?
```php
// Flush rewrite rules
flush_rewrite_rules();
```

---

## 📚 Documentation Links

- **Complete Guide**: [PANDUAN_CPT_TAXONOMY_TEMPLATE.md](./PANDUAN_CPT_TAXONOMY_TEMPLATE.md)
- **Event Hooks**: [CPT_EVENTS_HOOKS.md](./CPT_EVENTS_HOOKS.md)
- **Feature Summary**: [CPT_FEATURES_SUMMARY.md](./CPT_FEATURES_SUMMARY.md)

---

**Quick, Simple, Powerful!** 🚀
