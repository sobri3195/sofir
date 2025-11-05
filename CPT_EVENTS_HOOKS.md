# CPT Events & Hooks Documentation

Dokumentasi lengkap untuk semua event hooks yang tersedia di SOFIR CPT Manager.

---

## 📚 Daftar Isi

1. [CPT Lifecycle Hooks](#cpt-lifecycle-hooks)
2. [Taxonomy Lifecycle Hooks](#taxonomy-lifecycle-hooks)
3. [Meta Field Update Hooks](#meta-field-update-hooks)
4. [Template Management](#template-management)
5. [Statistics API](#statistics-api)
6. [Contoh Penggunaan](#contoh-penggunaan)

---

## CPT Lifecycle Hooks

### 1. Before Register CPT

Dijalankan sebelum CPT didaftarkan ke WordPress.

```php
do_action( 'sofir/cpt/before_register', string $post_type, array $definition );
```

**Parameters:**
- `$post_type` (string) - Slug CPT yang akan didaftarkan
- `$definition` (array) - Definisi lengkap CPT (args, fields, taxonomies)

**Contoh:**
```php
add_action( 'sofir/cpt/before_register', function( $post_type, $definition ) {
    error_log( "Registering CPT: {$post_type}" );
    
    // Modifikasi definition sebelum registrasi (gunakan reference atau filter)
}, 10, 2 );
```

---

### 2. After Register CPT

Dijalankan setelah CPT berhasil didaftarkan.

```php
do_action( 'sofir/cpt/registered', string $post_type, array $definition, array $args );
do_action( "sofir/cpt/registered_{$post_type}", array $definition, array $args );
```

**Parameters:**
- `$post_type` (string) - Slug CPT yang baru didaftarkan
- `$definition` (array) - Definisi lengkap CPT
- `$args` (array) - Arguments yang digunakan untuk `register_post_type()`

**Contoh:**
```php
// Hook untuk semua CPT
add_action( 'sofir/cpt/registered', function( $post_type, $definition, $args ) {
    error_log( "CPT registered: {$post_type}" );
}, 10, 3 );

// Hook untuk CPT spesifik (contoh: listing)
add_action( 'sofir/cpt/registered_listing', function( $definition, $args ) {
    // Setup tambahan untuk listing
    error_log( "Listing CPT registered with " . count( $definition['fields'] ) . " fields" );
}, 10, 2 );
```

---

### 3. Before Save CPT

Dijalankan sebelum definisi CPT disimpan ke database.

```php
do_action( 'sofir/cpt/before_save', string $slug, array $payload );
```

**Parameters:**
- `$slug` (string) - Slug CPT
- `$payload` (array) - Data form yang akan disimpan

**Contoh:**
```php
add_action( 'sofir/cpt/before_save', function( $slug, $payload ) {
    // Validasi tambahan
    if ( $slug === 'restricted_name' ) {
        wp_die( 'Cannot use this CPT name!' );
    }
}, 10, 2 );
```

---

### 4. After Save CPT

Dijalankan setelah definisi CPT disimpan ke database.

```php
do_action( 'sofir/cpt/saved', string $slug, array $definition );
do_action( "sofir/cpt/saved_{$slug}", array $definition );
```

**Parameters:**
- `$slug` (string) - Slug CPT
- `$definition` (array) - Definisi lengkap yang disimpan

**Contoh:**
```php
// Hook untuk semua CPT
add_action( 'sofir/cpt/saved', function( $slug, $definition ) {
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Send notification
    error_log( "CPT {$slug} has been saved/updated" );
}, 10, 2 );

// Hook untuk CPT spesifik
add_action( 'sofir/cpt/saved_event', function( $definition ) {
    // Setup event-specific configuration
    update_option( 'event_cpt_last_updated', current_time( 'timestamp' ) );
} );
```

---

### 5. Before Delete CPT

Dijalankan sebelum CPT dihapus dari database.

```php
do_action( 'sofir/cpt/before_delete', string $slug, ?array $definition );
```

**Parameters:**
- `$slug` (string) - Slug CPT yang akan dihapus
- `$definition` (array|null) - Definisi CPT (null jika tidak ditemukan)

**Contoh:**
```php
add_action( 'sofir/cpt/before_delete', function( $slug, $definition ) {
    // Backup posts sebelum CPT dihapus
    $posts = get_posts( [
        'post_type'   => $slug,
        'numberposts' => -1,
    ] );
    
    update_option( "backup_posts_{$slug}", $posts );
}, 10, 2 );
```

---

### 6. After Delete CPT

Dijalankan setelah CPT dihapus dari database.

```php
do_action( 'sofir/cpt/deleted', string $slug, ?array $definition );
do_action( "sofir/cpt/deleted_{$slug}", ?array $definition );
```

**Parameters:**
- `$slug` (string) - Slug CPT yang dihapus
- `$definition` (array|null) - Definisi CPT sebelum dihapus

**Contoh:**
```php
add_action( 'sofir/cpt/deleted', function( $slug, $definition ) {
    // Cleanup setelah CPT dihapus
    flush_rewrite_rules();
    delete_option( "custom_config_{$slug}" );
}, 10, 2 );
```

---

## Taxonomy Lifecycle Hooks

### 1. Before Register Taxonomy

```php
do_action( 'sofir/taxonomy/before_register', string $taxonomy, array $definition );
```

**Contoh:**
```php
add_action( 'sofir/taxonomy/before_register', function( $taxonomy, $definition ) {
    error_log( "Registering taxonomy: {$taxonomy}" );
}, 10, 2 );
```

---

### 2. After Register Taxonomy

```php
do_action( 'sofir/taxonomy/registered', string $taxonomy, array $definition, array $args );
do_action( "sofir/taxonomy/registered_{$taxonomy}", array $definition, array $args );
```

**Contoh:**
```php
add_action( 'sofir/taxonomy/registered_listing_category', function( $definition, $args ) {
    // Setup default terms
    if ( ! term_exists( 'restaurant', 'listing_category' ) ) {
        wp_insert_term( 'Restaurant', 'listing_category' );
    }
}, 10, 2 );
```

---

### 3. Before Save Taxonomy

```php
do_action( 'sofir/taxonomy/before_save', string $slug, array $payload );
```

---

### 4. After Save Taxonomy

```php
do_action( 'sofir/taxonomy/saved', string $slug, array $definition );
do_action( "sofir/taxonomy/saved_{$slug}", array $definition );
```

---

### 5. Before Delete Taxonomy

```php
do_action( 'sofir/taxonomy/before_delete', string $slug, ?array $definition );
```

---

### 6. After Delete Taxonomy

```php
do_action( 'sofir/taxonomy/deleted', string $slug, ?array $definition );
do_action( "sofir/taxonomy/deleted_{$slug}", ?array $definition );
```

---

## Meta Field Update Hooks

### 1. Meta Field Updated (All CPTs)

Dijalankan ketika meta field SOFIR diupdate.

```php
do_action( 'sofir/cpt/meta_updated', int $post_id, string $post_type, string $field_name, mixed $meta_value );
```

**Parameters:**
- `$post_id` (int) - ID post yang diupdate
- `$post_type` (string) - Tipe post
- `$field_name` (string) - Nama field yang diupdate (tanpa prefix)
- `$meta_value` (mixed) - Nilai baru dari meta field

**Contoh:**
```php
add_action( 'sofir/cpt/meta_updated', function( $post_id, $post_type, $field_name, $meta_value ) {
    // Log all meta updates
    error_log( "Post {$post_id} ({$post_type}) - Field {$field_name} updated" );
}, 10, 4 );
```

---

### 2. Meta Field Updated (Specific CPT)

```php
do_action( "sofir/cpt/{$post_type}/meta_updated", int $post_id, string $field_name, mixed $meta_value );
```

**Contoh:**
```php
// Hook untuk listing saja
add_action( 'sofir/cpt/listing/meta_updated', function( $post_id, $field_name, $meta_value ) {
    // Update search index ketika listing berubah
    do_action( 'reindex_listing', $post_id );
}, 10, 3 );
```

---

### 3. Meta Field Updated (Specific Field)

```php
do_action( "sofir/cpt/{$post_type}/meta_updated_{$field_name}", int $post_id, mixed $meta_value );
```

**Contoh:**
```php
// Hook ketika rating listing berubah
add_action( 'sofir/cpt/listing/meta_updated_rating', function( $post_id, $meta_value ) {
    // Send notification jika rating tinggi
    if ( $meta_value >= 4.5 ) {
        wp_mail( 
            get_option( 'admin_email' ), 
            'High Rating Alert', 
            "Listing {$post_id} received {$meta_value} rating!" 
        );
    }
}, 10, 2 );

// Hook ketika status event berubah
add_action( 'sofir/cpt/event/meta_updated_status', function( $post_id, $meta_value ) {
    if ( $meta_value === 'featured' ) {
        // Promote event di homepage
        update_option( 'featured_event_id', $post_id );
    }
}, 10, 2 );
```

---

## Template Management

### Get CPT Templates

```php
$manager = \Sofir\Cpt\Manager::instance();
$templates = $manager->get_cpt_templates( 'listing' );
```

**Returns:** Array template Gutenberg untuk CPT

---

### Set CPT Template

```php
$manager = \Sofir\Cpt\Manager::instance();

$template = [
    [ 'core/heading', [ 'level' => 2, 'placeholder' => 'Judul Listing' ] ],
    [ 'core/paragraph', [ 'placeholder' => 'Deskripsi...' ] ],
    [ 'sofir/map', [] ],
];

$manager->set_cpt_template( 'listing', $template, 'all' );
```

**Template Lock Options:**
- `''` (kosong) - Tidak dikunci, user bisa add/remove blocks
- `'all'` - Fully locked, user tidak bisa edit struktur
- `'insert'` - User tidak bisa insert block baru tapi bisa move/remove

**Contoh:**
```php
// Set template untuk event CPT
add_action( 'init', function() {
    $manager = \Sofir\Cpt\Manager::instance();
    
    $event_template = [
        [ 'core/heading', [ 'level' => 1, 'placeholder' => 'Event Title' ] ],
        [ 'sofir/dynamic-data', [
            'dataSource' => 'cpt_field',
            'dataKey'    => 'event_date',
            'format'     => 'date',
        ] ],
        [ 'core/image', [] ],
        [ 'core/paragraph', [ 'placeholder' => 'Event description...' ] ],
        [ 'sofir/map', [] ],
    ];
    
    $manager->set_cpt_template( 'event', $event_template, 'insert' );
} );
```

---

## Statistics API

### Get CPT Statistics

```php
$manager = \Sofir\Cpt\Manager::instance();
$stats = $manager->get_cpt_statistics();
```

**Returns:**
```php
[
    'listing' => [
        'slug'       => 'listing',
        'label'      => 'Listings',
        'singular'   => 'Listing',
        'published'  => 45,
        'draft'      => 3,
        'pending'    => 2,
        'trash'      => 1,
        'total'      => 50,
        'fields'     => [ 'location', 'hours', 'rating', 'status' ],
        'taxonomies' => [ 'listing_category', 'listing_location' ],
    ],
    // ... other CPTs
]
```

---

### Get Taxonomy Statistics

```php
$manager = \Sofir\Cpt\Manager::instance();
$stats = $manager->get_taxonomy_statistics();
```

**Returns:**
```php
[
    'listing_category' => [
        'slug'         => 'listing_category',
        'label'        => 'Listing Categories',
        'singular'     => 'Listing Category',
        'term_count'   => 12,
        'object_types' => [ 'listing' ],
        'hierarchical' => true,
        'filterable'   => true,
    ],
    // ... other taxonomies
]
```

---

## Contoh Penggunaan

### 1. Auto-populate Terms

```php
add_action( 'sofir/taxonomy/registered_listing_category', function( $definition, $args ) {
    $default_terms = [ 'Restaurant', 'Hotel', 'Cafe', 'Spa', 'Gym' ];
    
    foreach ( $default_terms as $term ) {
        if ( ! term_exists( $term, 'listing_category' ) ) {
            wp_insert_term( $term, 'listing_category' );
        }
    }
}, 10, 2 );
```

---

### 2. Send Notification on New Appointment

```php
add_action( 'sofir/cpt/saved_appointment', function( $definition ) {
    // Check if this is a new appointment
    $appointments = get_posts( [
        'post_type'   => 'appointment',
        'post_status' => 'publish',
        'numberposts' => 1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ] );
    
    if ( ! empty( $appointments ) ) {
        $post = $appointments[0];
        
        // Send email notification
        wp_mail(
            get_option( 'admin_email' ),
            'New Appointment Created',
            "New appointment: {$post->post_title}"
        );
    }
} );
```

---

### 3. Sync External API

```php
add_action( 'sofir/cpt/listing/meta_updated_status', function( $post_id, $meta_value ) {
    if ( $meta_value === 'featured' ) {
        // Sync to external directory API
        $post = get_post( $post_id );
        
        wp_remote_post( 'https://api.example.com/featured', [
            'body' => [
                'title'   => $post->post_title,
                'content' => $post->post_content,
                'status'  => $meta_value,
            ],
        ] );
    }
}, 10, 2 );
```

---

### 4. Custom Statistics Dashboard Widget

```php
add_action( 'wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'sofir_cpt_stats',
        'SOFIR Content Statistics',
        function() {
            $manager = \Sofir\Cpt\Manager::instance();
            $stats = $manager->get_cpt_statistics();
            
            echo '<table class="widefat">';
            echo '<thead><tr><th>Post Type</th><th>Published</th><th>Draft</th></tr></thead>';
            echo '<tbody>';
            
            foreach ( $stats as $cpt ) {
                echo '<tr>';
                echo '<td>' . esc_html( $cpt['label'] ) . '</td>';
                echo '<td>' . esc_html( $cpt['published'] ) . '</td>';
                echo '<td>' . esc_html( $cpt['draft'] ) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
        }
    );
} );
```

---

### 5. Auto-assign Default Template

```php
add_action( 'sofir/cpt/registered_event', function( $definition, $args ) {
    $manager = \Sofir\Cpt\Manager::instance();
    
    // Check if template not set
    if ( empty( $manager->get_cpt_templates( 'event' ) ) ) {
        $default_template = [
            [ 'core/heading', [ 'level' => 1 ] ],
            [ 'sofir/dynamic-data', [ 'dataSource' => 'cpt_field', 'dataKey' => 'event_date' ] ],
            [ 'core/paragraph', [] ],
            [ 'sofir/map', [] ],
        ];
        
        $manager->set_cpt_template( 'event', $default_template, 'insert' );
    }
}, 10, 2 );
```

---

### 6. Rating Alert System

```php
add_action( 'sofir/cpt/listing/meta_updated_rating', function( $post_id, $rating ) {
    $post = get_post( $post_id );
    
    if ( $rating >= 4.5 ) {
        // High rating - feature the listing
        update_post_meta( $post_id, 'sofir_listing_status', 'featured' );
        
        // Send congratulation email to author
        $author = get_userdata( $post->post_author );
        wp_mail(
            $author->user_email,
            'Congratulations! High Rating Achieved',
            "Your listing '{$post->post_title}' received a {$rating} star rating!"
        );
    } elseif ( $rating < 2.0 ) {
        // Low rating - send alert to admin
        wp_mail(
            get_option( 'admin_email' ),
            'Low Rating Alert',
            "Listing '{$post->post_title}' received low rating: {$rating}"
        );
    }
}, 10, 2 );
```

---

## Summary Event Hooks

### CPT Hooks
- ✅ `sofir/cpt/before_register` - Sebelum CPT terdaftar
- ✅ `sofir/cpt/registered` - Setelah CPT terdaftar
- ✅ `sofir/cpt/registered_{post_type}` - Setelah CPT spesifik terdaftar
- ✅ `sofir/cpt/before_save` - Sebelum definisi disimpan
- ✅ `sofir/cpt/saved` - Setelah definisi disimpan
- ✅ `sofir/cpt/saved_{post_type}` - Setelah CPT spesifik disimpan
- ✅ `sofir/cpt/before_delete` - Sebelum CPT dihapus
- ✅ `sofir/cpt/deleted` - Setelah CPT dihapus
- ✅ `sofir/cpt/deleted_{post_type}` - Setelah CPT spesifik dihapus

### Taxonomy Hooks
- ✅ `sofir/taxonomy/before_register` - Sebelum taxonomy terdaftar
- ✅ `sofir/taxonomy/registered` - Setelah taxonomy terdaftar
- ✅ `sofir/taxonomy/registered_{taxonomy}` - Setelah taxonomy spesifik terdaftar
- ✅ `sofir/taxonomy/before_save` - Sebelum definisi disimpan
- ✅ `sofir/taxonomy/saved` - Setelah definisi disimpan
- ✅ `sofir/taxonomy/saved_{taxonomy}` - Setelah taxonomy spesifik disimpan
- ✅ `sofir/taxonomy/before_delete` - Sebelum taxonomy dihapus
- ✅ `sofir/taxonomy/deleted` - Setelah taxonomy dihapus
- ✅ `sofir/taxonomy/deleted_{taxonomy}` - Setelah taxonomy spesifik dihapus

### Meta Field Hooks
- ✅ `sofir/cpt/meta_updated` - Meta field updated (semua CPT)
- ✅ `sofir/cpt/{post_type}/meta_updated` - Meta field updated (CPT spesifik)
- ✅ `sofir/cpt/{post_type}/meta_updated_{field}` - Field spesifik updated

---

**Total: 24+ Event Hooks** tersedia untuk customize behavior SOFIR CPT Manager! 🎉
