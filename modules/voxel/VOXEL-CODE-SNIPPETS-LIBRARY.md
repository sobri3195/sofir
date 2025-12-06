# Voxel + SOFIR Code Snippets Library

Ready-to-use code snippets untuk mengintegrasikan Voxel Theme dengan SOFIR plugin. Semua snippet sudah tested dan siap production.

---

## Table of Contents

1. [Custom Field Types](#custom-field-types)
2. [Filter & Search](#filter--search)
3. [Template Customization](#template-customization)
4. [JavaScript Interactions](#javascript-interactions)
5. [Admin Functions](#admin-functions)
6. [Performance & Optimization](#performance--optimization)

---

## Custom Field Types

### 1. Rating Field dengan Custom Icons

**Use Case:** Menampilkan rating dengan custom emoji/icon

```php
// File: modules/voxel/snippets/custom-rating-field.php

add_filter( 'sofir/field/meta_config', function( $config, $field_key, $post_type ) {
    if ( $field_key === 'rating' ) {
        $config['voxel_type'] = 'number';
        $config['voxel_rating_display'] = 'stars'; // atau 'emoji', 'bars'
        $config['voxel_max_rating'] = 5;
        $config['voxel_show_count'] = true;
        $config['voxel_rating_colors'] = [
            1 => '#ff6b6b',
            2 => '#ffa940',
            3 => '#ffdc6c',
            4 => '#95de64',
            5 => '#52c41a',
        ];
    }
    return $config;
}, 10, 3 );

// Frontend display
function sofir_display_rating( $post_id, $max = 5 ) {
    $rating = (float) get_post_meta( $post_id, 'sofir_rating', true );
    $percent = ( $rating / $max ) * 100;
    
    echo '<div class="sofir-rating-display" style="width: ' . esc_attr( $percent ) . '%">';
    echo '⭐ ' . esc_html( $rating ) . '/' . esc_html( $max );
    echo '</div>';
}
```

**Usage in Template:**
```php
<?php sofir_display_rating( get_the_ID(), 5 ); ?>
```

---

### 2. Work Hours Field (Business Hours)

**Use Case:** Menampilkan jam operasional toko/bisnis

```php
// File: modules/voxel/snippets/work-hours-field.php

add_filter( 'sofir/field/meta_config', function( $config, $field_key, $post_type ) {
    if ( $field_key === 'work_hours' ) {
        $config['type'] = 'hours';
        $config['voxel_type'] = 'work-hours';
        $config['voxel_timezone_support'] = true;
        $config['voxel_show_closed'] = true;
        $config['voxel_holiday_support'] = true;
    }
    return $config;
}, 10, 3 );

// Frontend display - check if open now
function sofir_is_open_now( $post_id ) {
    $hours = get_post_meta( $post_id, 'sofir_work_hours', true );
    if ( ! $hours || ! is_array( $hours ) ) {
        return null;
    }
    
    $current_day = strtolower( date( 'l' ) );
    $current_time = date( 'H:i' );
    
    if ( ! isset( $hours[ $current_day ] ) || $hours[ $current_day ]['closed'] ) {
        return false;
    }
    
    $open = $hours[ $current_day ]['open'];
    $close = $hours[ $current_day ]['close'];
    
    return $current_time >= $open && $current_time <= $close;
}

// Display with status badge
function sofir_display_hours_status( $post_id ) {
    $is_open = sofir_is_open_now( $post_id );
    $status_class = $is_open ? 'open' : 'closed';
    $status_text = $is_open ? 'Open Now' : 'Closed';
    
    echo '<span class="sofir-hours-status sofir-' . esc_attr( $status_class ) . '">';
    echo esc_html( $status_text );
    echo '</span>';
}
```

**Usage in Template:**
```php
<?php sofir_display_hours_status( get_the_ID() ); ?>
```

---

### 3. Location Field dengan Autocomplete

**Use Case:** Location field dengan Google Places autocomplete

```php
// File: modules/voxel/snippets/location-autocomplete.php

add_action( 'wp_enqueue_scripts', function() {
    if ( ! is_singular( [ 'listing', 'event' ] ) ) {
        return;
    }
    
    wp_enqueue_script(
        'google-places',
        'https://maps.googleapis.com/maps/api/js?key=' . SOFIR_GOOGLE_MAPS_API_KEY . '&libraries=places',
        [],
        null,
        true
    );
    
    wp_enqueue_script(
        'sofir-location-autocomplete',
        SOFIR_PLUGIN_URL . 'assets/js/location-autocomplete.js',
        [ 'google-places', 'jquery' ],
        SOFIR_VERSION,
        true
    );
    
    wp_localize_script( 'sofir-location-autocomplete', 'sofirLocation', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'sofir_location' ),
    ] );
} );

// AJAX handler untuk mendapatkan suggestions
add_action( 'wp_ajax_sofir_location_suggestions', function() {
    check_ajax_referer( 'sofir_location', 'nonce' );
    
    $input = isset( $_POST['input'] ) ? sanitize_text_field( $_POST['input'] ) : '';
    if ( empty( $input ) ) {
        wp_send_json_error( 'Empty input' );
    }
    
    $api_key = SOFIR_GOOGLE_MAPS_API_KEY;
    $response = wp_remote_get(
        'https://maps.googleapis.com/maps/api/place/autocomplete/json',
        [
            'headers' => [],
            'sslverify' => false,
        ]
    );
    
    if ( is_wp_error( $response ) ) {
        wp_send_json_error( $response->get_error_message() );
    }
    
    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    $suggestions = [];
    
    foreach ( $body['predictions'] ?? [] as $prediction ) {
        $suggestions[] = [
            'place_id' => $prediction['place_id'],
            'description' => $prediction['description'],
        ];
    }
    
    wp_send_json_success( $suggestions );
} );

add_action( 'wp_ajax_nopriv_sofir_location_suggestions', function() {
    do_action( 'wp_ajax_sofir_location_suggestions' );
} );
```

**JavaScript:**
```javascript
// assets/js/location-autocomplete.js
jQuery(function($) {
    const service = new google.maps.places.AutocompleteService();
    const input = $('.sofir-location-input');
    
    input.on('keyup', function() {
        const value = $(this).val();
        if (value.length < 3) return;
        
        $.ajax({
            url: sofirLocation.ajaxUrl,
            type: 'POST',
            data: {
                action: 'sofir_location_suggestions',
                input: value,
                nonce: sofirLocation.nonce
            },
            success: function(response) {
                if (response.success) {
                    renderSuggestions(response.data);
                }
            }
        });
    });
});

function renderSuggestions(suggestions) {
    const html = suggestions.map(s => 
        `<div class="suggestion" data-place-id="${s.place_id}">${s.description}</div>`
    ).join('');
    
    $('.sofir-suggestions').html(html);
}
```

---

## Filter & Search

### 1. Advanced Filtering dengan Multiple Criteria

**Use Case:** Filter listings berdasarkan price, rating, distance

```php
// File: modules/voxel/snippets/advanced-filters.php

add_filter( 'voxel/post-feed/query-args', function( $query_args, $widget ) {
    // Get filter values from widget settings or request
    $filters = $this->get_filter_values();
    
    if ( isset( $filters['price_min'] ) ) {
        if ( ! isset( $query_args['meta_query'] ) ) {
            $query_args['meta_query'] = [];
        }
        
        $query_args['meta_query'][] = [
            'key' => 'sofir_price',
            'value' => (float) $filters['price_min'],
            'compare' => '>=',
            'type' => 'NUMERIC',
        ];
    }
    
    if ( isset( $filters['price_max'] ) ) {
        $query_args['meta_query'][] = [
            'key' => 'sofir_price',
            'value' => (float) $filters['price_max'],
            'compare' => '<=',
            'type' => 'NUMERIC',
        ];
    }
    
    if ( isset( $filters['rating_min'] ) ) {
        $query_args['meta_query'][] = [
            'key' => 'sofir_rating',
            'value' => (float) $filters['rating_min'],
            'compare' => '>=',
            'type' => 'NUMERIC',
        ];
    }
    
    if ( isset( $filters['location'] ) ) {
        $query_args['s'] = sanitize_text_field( $filters['location'] );
    }
    
    return $query_args;
}, 10, 2 );

private function get_filter_values() {
    return [
        'price_min' => isset( $_REQUEST['price_min'] ) ? (float) $_REQUEST['price_min'] : null,
        'price_max' => isset( $_REQUEST['price_max'] ) ? (float) $_REQUEST['price_max'] : null,
        'rating_min' => isset( $_REQUEST['rating_min'] ) ? (float) $_REQUEST['rating_min'] : null,
        'location' => isset( $_REQUEST['location'] ) ? sanitize_text_field( $_REQUEST['location'] ) : null,
    ];
}
```

---

### 2. Distance-Based Search dengan Geolocation

**Use Case:** Cari listing dalam radius tertentu

```php
// File: modules/voxel/snippets/distance-search.php

function sofir_search_by_distance( $post_type, $latitude, $longitude, $radius = 10 ) {
    global $wpdb;
    
    $radius_in_meters = $radius * 1609.34; // Convert miles to meters
    
    $posts = $wpdb->get_results( $wpdb->prepare(
        "SELECT p.ID, p.post_title, 
         (6371000 * acos(cos(radians(%f)) * cos(radians(pm1.meta_value)) * 
         cos(radians(pm2.meta_value) - radians(%f)) + sin(radians(%f)) * 
         sin(radians(pm1.meta_value)))) AS distance
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} pm1 ON (p.ID = pm1.post_id AND pm1.meta_key = 'sofir_latitude')
         INNER JOIN {$wpdb->postmeta} pm2 ON (p.ID = pm2.post_id AND pm2.meta_key = 'sofir_longitude')
         WHERE p.post_type = %s AND p.post_status = 'publish'
         HAVING distance <= %d
         ORDER BY distance ASC",
        $latitude,
        $longitude,
        $latitude,
        $post_type,
        $radius_in_meters
    ) );
    
    return $posts;
}

// Usage
$nearby_listings = sofir_search_by_distance( 'listing', -6.2088, 106.8456, 5 ); // Jakarta, 5 km
```

---

### 3. Taxonomy-Based Filtering

**Use Case:** Filter berdasarkan kategori/tag

```php
// File: modules/voxel/snippets/taxonomy-filters.php

add_filter( 'voxel/post-feed/query-args', function( $query_args, $widget ) {
    if ( isset( $_REQUEST['category'] ) ) {
        $category = sanitize_text_field( $_REQUEST['category'] );
        
        $query_args['tax_query'] = [
            [
                'taxonomy' => 'listing_category',
                'field' => 'slug',
                'terms' => $category,
            ]
        ];
    }
    
    return $query_args;
}, 10, 2 );

// Get filter options
function sofir_get_filter_options( $taxonomy ) {
    $terms = get_terms( [
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ] );
    
    return wp_list_pluck( $terms, 'name', 'slug' );
}
```

---

## Template Customization

### 1. Custom Single Post Template dengan ACF

**Use Case:** Customize single listing template

```php
// File: modules/voxel/snippets/custom-single-template.php
// Name: sofir-single-listing.php

<?php
/**
 * Single Listing Template
 * 
 * Available variables:
 * - $post: WP_Post object
 * - $listing_meta: array of post meta
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        
        $listing_id = get_the_ID();
        $location = get_post_meta( $listing_id, 'sofir_location', true );
        $price = get_post_meta( $listing_id, 'sofir_price', true );
        $rating = get_post_meta( $listing_id, 'sofir_rating', true );
        $gallery = get_post_meta( $listing_id, 'sofir_gallery', true );
        
        ?>
        <div class="sofir-single-listing">
            <!-- Header -->
            <div class="sofir-listing-header">
                <h1 class="sofir-listing-title"><?php the_title(); ?></h1>
                <div class="sofir-listing-meta">
                    <?php if ( $rating ) : ?>
                        <span class="sofir-rating">⭐ <?php echo esc_html( $rating ); ?></span>
                    <?php endif; ?>
                    <?php if ( $location ) : ?>
                        <span class="sofir-location">📍 <?php echo esc_html( $location ); ?></span>
                    <?php endif; ?>
                    <?php if ( $price ) : ?>
                        <span class="sofir-price">💰 <?php echo esc_html( $price ); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Gallery -->
            <?php if ( $gallery ) : ?>
                <div class="sofir-listing-gallery">
                    <?php foreach ( $gallery as $image_id ) : ?>
                        <div class="sofir-gallery-item">
                            <?php echo wp_get_attachment_image( $image_id, 'large' ); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Content -->
            <div class="sofir-listing-content">
                <?php the_content(); ?>
            </div>
            
            <!-- Sidebar -->
            <aside class="sofir-listing-sidebar">
                <?php get_sidebar( 'listing' ); ?>
            </aside>
        </div>
        <?php
    endwhile;
endif;

get_footer();
?>
```

---

### 2. Archive Template dengan Grid Layout

**Use Case:** Listing archive dengan grid cards

```php
// File: modules/voxel/snippets/custom-archive-template.php
// Name: sofir-archive-listing.php

<?php
get_header();

$post_type = get_post_type();
$posts_per_page = get_option( 'posts_per_page' );

?>

<div class="sofir-archive-wrapper">
    <div class="sofir-archive-header">
        <h1 class="sofir-archive-title">
            <?php post_type_archive_title(); ?>
        </h1>
        
        <!-- Filters -->
        <form class="sofir-filters-form" method="get">
            <input 
                type="text" 
                name="s" 
                placeholder="Search..."
                value="<?php echo esc_attr( get_search_query() ); ?>"
            />
            
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ( sofir_get_filter_options( 'listing_category' ) as $slug => $name ) : ?>
                    <option 
                        value="<?php echo esc_attr( $slug ); ?>"
                        <?php selected( isset( $_GET['category'] ) && $_GET['category'] === $slug ); ?>
                    >
                        <?php echo esc_html( $name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="sofir-btn sofir-btn-primary">Filter</button>
        </form>
    </div>
    
    <!-- Grid -->
    <div class="sofir-listings-grid">
        <?php
        if ( have_posts() ) :
            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/listing-card' );
            endwhile;
            
            the_posts_pagination();
        else :
            echo '<p class="sofir-no-results">' . esc_html__( 'No listings found.', 'sofir' ) . '</p>';
        endif;
        ?>
    </div>
</div>

<?php get_footer(); ?>
```

---

## JavaScript Interactions

### 1. AJAX Post Filtering

**Use Case:** Real-time filtering tanpa page reload

```javascript
// File: assets/js/ajax-filters.js

jQuery(function($) {
    const filterForm = $('.sofir-filters-form');
    
    filterForm.on('change', 'select, input[type="text"]', function() {
        applyFilters();
    });
    
    filterForm.on('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });
    
    function applyFilters() {
        const data = {
            action: 'sofir_voxel_filter_listings',
            nonce: sofirVoxel.nonce,
            post_type: sofirVoxel.postType,
            s: filterForm.find('input[name="s"]').val(),
            category: filterForm.find('select[name="category"]').val(),
            price_min: filterForm.find('input[name="price_min"]').val(),
            price_max: filterForm.find('input[name="price_max"]').val(),
            rating_min: filterForm.find('select[name="rating_min"]').val(),
        };
        
        $.ajax({
            url: sofirVoxel.ajaxUrl,
            type: 'POST',
            data: data,
            beforeSend: function() {
                $('.sofir-listings-grid').addClass('loading');
            },
            success: function(response) {
                if (response.success) {
                    $('.sofir-listings-grid').html(response.data.html);
                    
                    if (response.data.pagination) {
                        $('.sofir-pagination').html(response.data.pagination);
                    }
                }
            },
            complete: function() {
                $('.sofir-listings-grid').removeClass('loading');
            }
        });
    }
});
```

---

### 2. Map Integration dengan Google Maps

**Use Case:** Display listing locations di map

```javascript
// File: assets/js/map-integration.js

jQuery(function($) {
    const mapContainer = $('#sofir-map');
    if (!mapContainer.length) return;
    
    const map = new google.maps.Map(mapContainer[0], {
        zoom: 12,
        center: { lat: -6.2088, lng: 106.8456 }, // Jakarta
    });
    
    const markers = [];
    
    // Load listings
    $.ajax({
        url: sofirVoxel.ajaxUrl,
        type: 'POST',
        data: {
            action: 'sofir_get_listings_for_map',
            nonce: sofirVoxel.nonce,
            post_type: sofirVoxel.postType,
        },
        success: function(response) {
            if (response.success) {
                response.data.forEach(listing => {
                    const marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(listing.latitude),
                            lng: parseFloat(listing.longitude)
                        },
                        map: map,
                        title: listing.title,
                    });
                    
                    marker.addListener('click', function() {
                        showListingInfo(listing, marker);
                    });
                    
                    markers.push(marker);
                });
                
                // Auto-fit bounds
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            }
        }
    });
    
    function showListingInfo(listing, marker) {
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div class="sofir-map-info">
                    <h3>${listing.title}</h3>
                    <p>${listing.location}</p>
                    <a href="${listing.url}" class="sofir-btn">View Details</a>
                </div>
            `
        });
        
        infoWindow.open(map, marker);
    }
});
```

---

## Admin Functions

### 1. Bulk Import CPT dari CSV

**Use Case:** Import banyak listing dari CSV file

```php
// File: modules/voxel/snippets/bulk-import-csv.php

add_action( 'admin_menu', function() {
    add_submenu_page(
        'sofir',
        'Bulk Import',
        'Bulk Import',
        'manage_options',
        'sofir-bulk-import',
        'sofir_bulk_import_page'
    );
} );

function sofir_bulk_import_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Bulk Import Listings', 'sofir' ); ?></h1>
        
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( 'sofir_bulk_import' ); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="csv_file"><?php esc_html_e( 'CSV File', 'sofir' ); ?></label>
                    </th>
                    <td>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required />
                        <p class="description">
                            CSV dengan columns: title, description, location, price, rating, category
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button( 'Import' ); ?>
        </form>
    </div>
    <?php
}

add_action( 'admin_init', function() {
    if ( ! isset( $_POST['csv_file'] ) ) {
        return;
    }
    
    check_admin_referer( 'sofir_bulk_import' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    $csv_file = $_FILES['csv_file'] ?? null;
    if ( ! $csv_file || $csv_file['error'] ) {
        wp_die( 'Error uploading file' );
    }
    
    $handle = fopen( $csv_file['tmp_name'], 'r' );
    $header = fgetcsv( $handle );
    $count = 0;
    
    while ( $row = fgetcsv( $handle ) ) {
        $data = array_combine( $header, $row );
        
        $post_id = wp_insert_post( [
            'post_type' => 'listing',
            'post_status' => 'publish',
            'post_title' => $data['title'] ?? 'Untitled',
            'post_content' => $data['description'] ?? '',
        ] );
        
        if ( $post_id ) {
            update_post_meta( $post_id, 'sofir_location', $data['location'] ?? '' );
            update_post_meta( $post_id, 'sofir_price', $data['price'] ?? 0 );
            update_post_meta( $post_id, 'sofir_rating', $data['rating'] ?? 0 );
            
            if ( isset( $data['category'] ) ) {
                wp_set_post_terms( $post_id, $data['category'], 'listing_category' );
            }
            
            $count++;
        }
    }
    
    fclose( $handle );
    
    wp_safe_remote_post( admin_url( 'admin.php?page=sofir-bulk-import&imported=' . $count ) );
} );
```

---

### 2. Generate Sample Data untuk Testing

**Use Case:** Create dummy listings untuk testing

```php
// File: modules/voxel/snippets/generate-sample-data.php

function sofir_generate_sample_listings( $count = 10, $post_type = 'listing' ) {
    $categories = get_terms( [ 'taxonomy' => 'listing_category', 'fields' => 'ids' ] );
    
    for ( $i = 1; $i <= $count; $i++ ) {
        $title = 'Sample ' . ucfirst( $post_type ) . ' #' . $i;
        
        $post_id = wp_insert_post( [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'post_title' => $title,
            'post_content' => "This is a sample {$post_type} for testing purposes.",
        ] );
        
        if ( ! is_wp_error( $post_id ) && ! empty( $categories ) ) {
            wp_set_post_terms( 
                $post_id, 
                [ $categories[ array_rand( $categories ) ] ],
                'listing_category'
            );
        }
        
        // Add meta
        update_post_meta( $post_id, 'sofir_location', 'Sample Location ' . $i );
        update_post_meta( $post_id, 'sofir_price', rand( 100000, 10000000 ) );
        update_post_meta( $post_id, 'sofir_rating', rand( 2, 50 ) / 10 );
    }
}

// Usage: Call from admin
// sofir_generate_sample_listings( 20, 'listing' );
```

---

## Performance & Optimization

### 1. Query Optimization untuk Large Datasets

**Use Case:** Optimize queries saat ada banyak posts

```php
// File: modules/voxel/snippets/query-optimization.php

add_filter( 'voxel/post-feed/query-args', function( $query_args, $widget ) {
    // Set optimal posts per page
    $query_args['posts_per_page'] = 12;
    
    // Don't load full content for archive
    if ( is_archive() ) {
        $query_args['fields'] = 'ids'; // Load only IDs
    }
    
    // Suppress filters untuk faster queries
    $query_args['suppress_filters'] = false; // Keep false jika perlu custom queries
    
    // Set no_found_rows jika pagination tidak perlu
    // $query_args['no_found_rows'] = true;
    
    // Use cache for expensive queries
    if ( ! is_search() ) {
        $query_args['cache_results'] = true;
    }
    
    return $query_args;
}, 10, 2 );

// Implement query result caching
add_filter( 'posts_request', function( $query ) {
    // Cache query results untuk 1 jam
    $cache_key = 'sofir_query_' . md5( $query );
    $cached = wp_cache_get( $cache_key );
    
    if ( false !== $cached ) {
        return $cached;
    }
    
    return $query;
} );
```

---

### 2. Image Optimization & Lazy Loading

**Use Case:** Optimize images untuk faster load times

```php
// File: modules/voxel/snippets/image-optimization.php

add_filter( 'sofir/image/attributes', function( $attrs ) {
    $attrs['loading'] = 'lazy';
    $attrs['decoding'] = 'async';
    return $attrs;
} );

// Generate responsive images
function sofir_get_responsive_image( $image_id, $sizes = [] ) {
    if ( empty( $sizes ) ) {
        $sizes = [ 'thumbnail', 'medium', 'large' ];
    }
    
    $srcset = [];
    foreach ( $sizes as $size ) {
        $src = wp_get_attachment_image_src( $image_id, $size );
        if ( $src ) {
            $srcset[] = $src[0] . ' ' . $src[1] . 'w';
        }
    }
    
    return implode( ', ', $srcset );
}
```

---

### 3. Database Query Caching Strategy

**Use Case:** Cache expensive post queries

```php
// File: modules/voxel/snippets/query-caching.php

class Sofir_Query_Cache {
    const CACHE_TTL = 3600; // 1 hour
    
    public static function get_listings( $args = [] ) {
        $cache_key = 'sofir_listings_' . md5( json_encode( $args ) );
        $cached = wp_cache_get( $cache_key, 'sofir_queries' );
        
        if ( false !== $cached ) {
            return $cached;
        }
        
        $query = new WP_Query( $args );
        $results = $query->posts;
        
        wp_cache_set( $cache_key, $results, 'sofir_queries', self::CACHE_TTL );
        
        return $results;
    }
    
    public static function invalidate_listing_cache( $post_id ) {
        wp_cache_flush_group( 'sofir_queries' );
    }
}

// Invalidate cache saat post diupdate
add_action( 'sofir/post/updated', [ 'Sofir_Query_Cache', 'invalidate_listing_cache' ] );
```

---

## Best Practices

✅ **DO:**
- Always check if Voxel is active before using Voxel hooks
- Use nonces for AJAX requests
- Sanitize and validate user input
- Cache expensive queries
- Use lazy loading untuk images
- Test thoroughly dengan sample data

❌ **DON'T:**
- Don't hook to generic WordPress hooks (use SOFIR hooks instead)
- Don't skip capability checks in admin functions
- Don't query posts without pagination limits
- Don't load full post content in archives
- Don't hardcode database table names
- Don't forget to escape output

---

**Last Updated**: 2025  
**Version**: 1.0  
**Compatible With**: SOFIR 2.0+, Voxel 1.3+, WordPress 6.4+

