# Custom Post Type Export/Import - Technical Documentation

## Overview

The CPT Export/Import feature provides a complete solution for exporting and importing Custom Post Types with all related data including content, taxonomies, terms, and metadata. This feature enables easy content migration, demo content distribution, and development-to-production workflows.

---

## Architecture

### Core Components

1. **CPT Manager Methods**
   - `export_cpt_package( array $post_types ): array`
   - `import_cpt_package( array $package ): array`
   - `get_export_preview( array $post_types ): array`

2. **Admin Panel Handlers**
   - `handle_export_cpt()`
   - `handle_import_cpt()`
   - `handle_export_preview_ajax()`

3. **UI Components**
   - Export form with checkboxes
   - Preview panel with AJAX loading
   - Import form with file uploader

---

## Export Package Structure

### Package Format

```json
{
  "version": "1.0.0",
  "plugin": "SOFIR",
  "timestamp": "2024-01-15 10:30:00",
  "post_types": {
    "listing": {
      "args": {...},
      "fields": {...},
      "taxonomies": ["listing_category", "listing_location"]
    }
  },
  "taxonomies": {
    "listing_category": {
      "args": {...},
      "object_type": ["listing"],
      "filterable": true
    }
  },
  "posts": [
    {
      "ID": 123,
      "post_title": "Sample Listing",
      "post_content": "...",
      "post_excerpt": "...",
      "post_status": "publish",
      "post_type": "listing",
      "post_author": 1,
      "post_date": "2024-01-15 10:00:00",
      "post_name": "sample-listing",
      "menu_order": 0,
      "thumbnail": 456,
      "taxonomies": {
        "listing_category": ["restaurant", "cafe"]
      }
    }
  ],
  "terms": [
    {
      "taxonomy": "listing_category",
      "name": "Restaurant",
      "slug": "restaurant",
      "description": "",
      "parent": 0
    }
  ],
  "meta": [
    {
      "post_id": 123,
      "meta_key": "sofir_listing_location",
      "meta_value": "{\"address\":\"...\",\"lat\":0,\"lng\":0}"
    }
  ]
}
```

---

## Export Process Flow

### 1. User Selection
```javascript
// User selects post types via checkboxes
$('.sofir-export-checkbox').on('change', function() {
    // Enable/disable buttons based on selection
});
```

### 2. Preview Data (Optional)
```javascript
// AJAX call to get preview
$.post(ajaxurl, {
    action: 'sofir_get_export_preview',
    nonce: nonce,
    post_types: selected
});
```

### 3. Export Execution
```php
// Backend processing
$manager = CptManager::instance();
$package = $manager->export_cpt_package( $post_types );

// Package includes:
// - Post type definitions
// - Taxonomy definitions  
// - All posts with metadata
// - All terms
// - Taxonomy-post relationships
```

### 4. File Generation
```php
$json = wp_json_encode( $package, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );

header( 'Content-Type: application/json' );
header( 'Content-Disposition: attachment; filename="' . $filename . '.json"' );
echo $json;
```

---

## Import Process Flow

### 1. File Upload
```php
// Accept .json or .zip files
$file_ext = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

if ( $file_ext === 'zip' ) {
    // Extract JSON from ZIP
    $zip = new ZipArchive();
    $json_content = $zip->getFromName( 'package.json' );
}
```

### 2. Validation
```php
$package = json_decode( $json_content, true );

if ( empty( $package['version'] ) || empty( $package['plugin'] ) ) {
    return ['errors' => ['Invalid package format']];
}
```

### 3. Import Post Types & Taxonomies
```php
// Register CPT definitions
foreach ( $package['post_types'] as $slug => $definition ) {
    $this->post_types[ $slug ] = $definition;
}
update_option( 'sofir_cpt_definitions', $this->post_types );

// Register taxonomy definitions
foreach ( $package['taxonomies'] as $slug => $definition ) {
    $this->taxonomies[ $slug ] = $definition;
}
update_option( 'sofir_taxonomy_definitions', $this->taxonomies );

// Trigger registration
$this->register_dynamic_post_types();
$this->register_dynamic_taxonomies();
```

### 4. Import Terms
```php
foreach ( $package['terms'] as $term_data ) {
    // Skip if term already exists
    $term_exists = term_exists( $term_data['slug'], $term_data['taxonomy'] );
    if ( $term_exists ) continue;
    
    // Create term
    wp_insert_term( $term_data['name'], $term_data['taxonomy'], [...] );
}
```

### 5. Import Posts with ID Mapping
```php
$post_id_map = []; // Map old IDs to new IDs

foreach ( $package['posts'] as $post_data ) {
    $old_id = $post_data['ID'];
    
    // Check for existing post
    $existing = get_posts([
        'post_type' => $post_data['post_type'],
        'name' => $post_data['post_name'],
        'posts_per_page' => 1
    ]);
    
    if ( !empty( $existing ) ) {
        // Map to existing post ID
        $post_id_map[ $old_id ] = $existing[0]->ID;
        continue;
    }
    
    // Create new post
    $new_id = wp_insert_post( $post_data );
    $post_id_map[ $old_id ] = $new_id;
}
```

### 6. Import Metadata
```php
foreach ( $package['meta'] as $meta_data ) {
    $old_post_id = $meta_data['post_id'];
    $new_post_id = $post_id_map[ $old_post_id ] ?? 0;
    
    if ( $new_post_id > 0 ) {
        update_post_meta( 
            $new_post_id, 
            $meta_data['meta_key'], 
            maybe_unserialize( $meta_data['meta_value'] ) 
        );
    }
}
```

### 7. Set Taxonomy Relationships
```php
foreach ( $package['posts'] as $post_data ) {
    $new_id = $post_id_map[ $post_data['ID'] ] ?? 0;
    
    if ( $new_id > 0 && !empty( $post_data['taxonomies'] ) ) {
        foreach ( $post_data['taxonomies'] as $taxonomy => $term_slugs ) {
            wp_set_post_terms( $new_id, $term_slugs, $taxonomy );
        }
    }
}
```

### 8. Finalization
```php
flush_rewrite_rules();
```

---

## Preview Feature

### Purpose
Allow users to see what data will be exported without actually exporting.

### Implementation
```php
public function get_export_preview( array $post_types ): array {
    $preview = [];
    
    foreach ( $post_types as $post_type ) {
        $post_count = wp_count_posts( $post_type );
        
        $taxonomies_info = [];
        foreach ( $related_taxonomies as $taxonomy ) {
            $terms = get_terms([...]);
            $taxonomies_info[ $taxonomy ] = [
                'label' => ...,
                'count' => count( $terms ),
                'terms' => array_map( fn($t) => $t->name, $terms )
            ];
        }
        
        $preview[ $post_type ] = [
            'label' => ...,
            'post_count' => $total,
            'fields' => array_keys( $fields ),
            'taxonomies' => $taxonomies_info
        ];
    }
    
    return $preview;
}
```

### AJAX Handler
```php
public function handle_export_preview_ajax(): void {
    check_ajax_referer( 'sofir-admin-nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( ['message' => 'Unauthorized'] );
    }
    
    $post_types = $_POST['post_types'] ?? [];
    $preview = $manager->get_export_preview( $post_types );
    
    wp_send_json_success( ['preview' => $preview] );
}
```

---

## JavaScript Integration

### Export Form Handler
```javascript
jQuery(document).ready(function($) {
    const checkboxes = $('.sofir-export-checkbox');
    const previewBtn = $('#sofir-preview-export');
    const downloadBtn = $('#sofir-download-export');
    const previewDiv = $('#sofir-export-preview');

    // Enable buttons when at least one checkbox checked
    checkboxes.on('change', function() {
        const checked = checkboxes.filter(':checked').length;
        previewBtn.prop('disabled', checked === 0);
        downloadBtn.prop('disabled', checked === 0);
    });

    // Preview button handler
    previewBtn.on('click', function() {
        const selected = [];
        checkboxes.filter(':checked').each(function() {
            selected.push($(this).val());
        });

        // AJAX call to get preview data
        $.post(ajaxurl, {
            action: 'sofir_get_export_preview',
            nonce: nonce,
            post_types: selected
        }, function(response) {
            if (response.success) {
                // Render preview HTML
                renderPreview(response.data.preview);
            }
        });
    });
});
```

---

## Security Considerations

### 1. Capability Check
```php
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Unauthorized' );
}
```

### 2. Nonce Verification
```php
$this->verify_request( 'sofir_export_cpt' );
check_ajax_referer( 'sofir-admin-nonce', 'nonce' );
```

### 3. File Type Validation
```php
if ( ! in_array( $file_ext, ['json', 'zip'], true ) ) {
    wp_die( 'Invalid file format' );
}
```

### 4. Sanitization
```php
$post_types = array_map( 'sanitize_key', $_POST['export_post_types'] );
$filename = sanitize_file_name( $_POST['export_filename'] );
```

---

## Error Handling

### Export Errors
- No post types selected → User-friendly error message
- PHP memory limit exceeded → Graceful failure with error log

### Import Errors
- Invalid JSON → "Format JSON tidak valid"
- Missing file → "File tidak ditemukan"
- Corrupted package → "Invalid package format"
- ZIP extraction failed → "ZipArchive tidak tersedia"

### Error Response Format
```php
$results = [
    'post_types_imported' => 0,
    'taxonomies_imported' => 0,
    'terms_imported' => 0,
    'posts_imported' => 0,
    'errors' => ['Error message 1', 'Error message 2']
];
```

---

## Performance Optimization

### 1. Batch Processing
For large datasets, consider implementing batch processing:
```php
// Process posts in chunks of 100
$chunks = array_chunk( $posts, 100 );
foreach ( $chunks as $chunk ) {
    // Process chunk
}
```

### 2. Memory Management
```php
// Increase memory limit for large exports
ini_set( 'memory_limit', '512M' );
```

### 3. Time Limit
```php
// Increase execution time for large imports
set_time_limit( 300 ); // 5 minutes
```

---

## Hooks & Filters

### Available Hooks
```php
// Before/after export
do_action( 'sofir/cpt/before_export', $post_types );
do_action( 'sofir/cpt/after_export', $package );

// Before/after import
do_action( 'sofir/cpt/before_import', $package );
do_action( 'sofir/cpt/after_import', $results );

// Filter package before export
$package = apply_filters( 'sofir/cpt/export_package', $package, $post_types );

// Filter package before import
$package = apply_filters( 'sofir/cpt/import_package', $package );
```

---

## Testing Guidelines

### Unit Tests
```php
// Test export package structure
public function test_export_package_structure() {
    $package = $this->manager->export_cpt_package(['listing']);
    
    $this->assertArrayHasKey('version', $package);
    $this->assertArrayHasKey('plugin', $package);
    $this->assertArrayHasKey('post_types', $package);
}

// Test import with valid package
public function test_import_valid_package() {
    $results = $this->manager->import_cpt_package($package);
    
    $this->assertEquals(1, $results['post_types_imported']);
    $this->assertEmpty($results['errors']);
}
```

### Integration Tests
- Export → Import → Verify data integrity
- Export with multiple post types
- Import with existing data (duplicate handling)
- Import with invalid package

---

## Future Enhancements

### Planned Features
1. **ZIP Export with Images**: Include media files in ZIP
2. **Selective Import**: Choose which items to import
3. **Import Preview**: Show what will be imported before executing
4. **REST API Endpoints**: Enable programmatic export/import
5. **Incremental Import**: Update existing posts instead of skipping
6. **Import Mapping**: Map old taxonomies/fields to new ones
7. **Batch Processing UI**: Progress bar for large imports
8. **Scheduled Exports**: Automatic periodic backups

---

## API Reference

### Export Function
```php
/**
 * Export CPT package
 * 
 * @param array $post_types Array of post type slugs to export
 * @return array Complete package data
 */
public function export_cpt_package( array $post_types ): array
```

### Import Function
```php
/**
 * Import CPT package
 * 
 * @param array $package Package data from export
 * @return array Results with counts and errors
 */
public function import_cpt_package( array $package ): array
```

### Preview Function
```php
/**
 * Get export preview without actual export
 * 
 * @param array $post_types Array of post type slugs
 * @return array Preview data with counts and metadata
 */
public function get_export_preview( array $post_types ): array
```

---

## Changelog

### Version 1.0.0 (2024-01-15)
- Initial release
- Export multiple post types with all data
- Import with duplicate detection
- Preview feature with AJAX
- Support for JSON and ZIP files

---

## Credits

Developed by SOFIR Team
Licensed under GPL v2 or later

---

## Support

For technical support or feature requests:
- GitHub: https://github.com/sofir/plugin
- Documentation: https://docs.sofir.dev
- Email: dev@sofir.dev
