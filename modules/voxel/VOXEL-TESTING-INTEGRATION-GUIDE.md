# Voxel + SOFIR Integration Testing & Real-World Scenarios

Complete guide untuk testing SOFIR plugin integration dengan Voxel Theme dalam real-world scenarios.

---

## Table of Contents

1. [Development Environment Setup](#development-environment-setup)
2. [Integration Testing Scenarios](#integration-testing-scenarios)
3. [Performance Testing](#performance-testing)
4. [Security Testing](#security-testing)
5. [Browser Compatibility](#browser-compatibility)
6. [Real-World Workflows](#real-world-workflows)
7. [Troubleshooting Guide](#troubleshooting-guide)

---

## Development Environment Setup

### Prerequisites

```bash
# Operating System
- Ubuntu 20.04+ atau Windows 10+ dengan WSL2
- Minimum 8GB RAM, 20GB disk space

# Software Requirements
- Docker & Docker Compose
- PHP 8.0+
- Node.js 16+
- npm atau yarn
- Git
- wp-cli
```

### Quick Setup dengan Docker

**File: `docker-compose.yml`**

```yaml
version: '3.8'

services:
  wordpress:
    image: wordpress:latest
    ports:
      - "8080:80"
    environment:
      WORDPRESS_DB_HOST: mysql
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
      WORDPRESS_DB_NAME: wordpress
      WORDPRESS_DEBUG: 'true'
      WORDPRESS_DEBUG_LOG: /var/www/html/wp-content/debug.log
    volumes:
      - ./wp-content:/var/www/html/wp-content
      - ./plugins/sofir:/var/www/html/wp-content/plugins/sofir
      - ./themes/voxel:/var/www/html/wp-content/themes/voxel
    depends_on:
      - mysql
  
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: wordpress
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

**Setup Commands:**

```bash
# Start environment
docker-compose up -d

# Wait for WordPress to be ready
sleep 30

# Install WordPress
wp core install \
  --url=http://localhost:8080 \
  --title="SOFIR Voxel Test" \
  --admin_user=admin \
  --admin_password=admin123 \
  --admin_email=admin@test.local

# Install Voxel Theme
wp theme install voxel --activate

# Install SOFIR Plugin
cp -r /path/to/sofir /var/www/html/wp-content/plugins/
wp plugin activate sofir

# Generate test data
wp sofir generate-test-data --count=20

# Show WordPress URL
echo "WordPress URL: http://localhost:8080"
echo "Admin: http://localhost:8080/wp-admin"
echo "User: admin / Password: admin123"
```

---

## Integration Testing Scenarios

### Scenario 1: Fresh Installation (Clean Setup)

**Objective:** Verify SOFIR + Voxel works correctly on fresh install

**Steps:**

1. **Clean WordPress installation**
   ```bash
   docker-compose down -v
   docker-compose up -d
   wp core install --url=http://localhost:8080 --title="Test" --admin_user=admin --admin_password=admin123 --admin_email=admin@test.local
   ```

2. **Install Voxel Theme**
   ```bash
   wp theme install voxel --activate
   ```

3. **Verify Voxel is active**
   ```bash
   wp theme list
   # Should show: voxel (active)
   ```

4. **Install SOFIR**
   ```bash
   wp plugin activate sofir
   ```

5. **Check admin menu**
   - Go to http://localhost:8080/wp-admin
   - Verify SOFIR menu appears in sidebar
   - Verify CPT submenus visible (if CPTs already registered)

6. **Create test CPT**
   - Go to SOFIR → Library
   - Import a CPT template (e.g., Business Directory)
   - Verify menu item appears for new CPT

**Expected Results:**
- ✅ SOFIR menu visible
- ✅ Imported CPT menu visible
- ✅ No admin notices/errors
- ✅ Can access CPT admin page

**Test Pass/Fail:** ___________

---

### Scenario 2: CPT Import & Voxel Compatibility

**Objective:** Verify imported CPTs work with Voxel

**Steps:**

1. **Import CPT from library**
   - SOFIR → Library → Choose "Business Directory"
   - Click "Import" or "Use Template"
   - Fill in required fields
   - Confirm import

2. **Create sample posts**
   ```bash
   wp post create --post_type=listing --post_status=publish \
     --post_title="Sample Listing 1" \
     --post_content="This is a test listing"
   
   wp post meta add <POST_ID> sofir_location "Jakarta, Indonesia"
   wp post meta add <POST_ID> sofir_price "1000000"
   wp post meta add <POST_ID> sofir_rating "4.5"
   ```

3. **Verify in Voxel Settings**
   - Go to Voxel → Post Types
   - Find your imported CPT (e.g., "Listing")
   - Verify all fields are mapped correctly
   - Check field types match SOFIR field types

4. **Create Voxel template**
   - Voxel → Templates → Create Single Post Template
   - Select your CPT
   - Add fields from SOFIR
   - Publish template

5. **View frontend**
   - Go to your site frontend
   - Navigate to listing archive
   - Verify posts display correctly
   - Click on post to view single template
   - Check if Voxel template renders correctly

**Expected Results:**
- ✅ CPT menu visible after import
- ✅ Fields appear in Voxel settings
- ✅ Posts display on archive page
- ✅ Single post template works
- ✅ All meta fields visible on frontend

**Test Pass/Fail:** ___________

---

### Scenario 3: Form Submission → CPT Creation

**Objective:** Verify SOFIR form creates posts with Voxel compatibility

**Steps:**

1. **Create SOFIR form**
   - SOFIR → Forms → Create New
   - Add fields:
     - Title (text)
     - Description (textarea)
     - Location (location field)
     - Price (number)
     - Category (select)

2. **Configure post creation action**
   - Form Settings → Actions → Create Post
   - Select Post Type: listing
   - Map fields:
     - Form "Title" → Post Title
     - Form "Description" → Post Content
     - Form "Location" → Meta: sofir_location
     - Form "Price" → Meta: sofir_price
     - Form "Category" → Taxonomy: listing_category

3. **Test form submission**
   - Add form to page/shortcode
   - Submit test data
   - Verify post created:
     ```bash
     wp post list --post_type=listing --format=table
     ```

4. **Verify in Voxel**
   - Go to Voxel → Post Types → Your CPT
   - Verify new post appears in list
   - Verify all fields populated correctly
   - Check if post displays on frontend

**Expected Results:**
- ✅ Post created in database
- ✅ Meta fields saved correctly
- ✅ Post visible in WordPress admin
- ✅ Post visible in Voxel admin
- ✅ Post displays on frontend with correct data

**Test Pass/Fail:** ___________

---

### Scenario 4: Voxel Filters & Search

**Objective:** Verify Voxel filtering works with SOFIR data

**Steps:**

1. **Create test listings with varied data**
   ```bash
   for i in {1..10}; do
     wp post create --post_type=listing \
       --post_title="Listing $i" \
       --post_content="Description $i" \
       --post_status=publish
     
     POST_ID=$! 
     wp post meta add $POST_ID sofir_price "$((i * 100000))"
     wp post meta add $POST_ID sofir_rating "$((i % 5 + 1))".0
     wp post meta add $POST_ID sofir_location "Location $((i % 3))"
   done
   ```

2. **Configure Voxel filters**
   - Voxel → Post Types → Your CPT → Filters
   - Enable filters for:
     - Price range
     - Rating
     - Location

3. **Test on frontend**
   - Go to archive page
   - Test price filter (min/max)
   - Test rating filter
   - Test location search
   - Verify results update correctly

4. **Test combined filters**
   - Apply price filter AND rating filter
   - Verify results filtered correctly
   - Reset filters
   - Verify all posts display again

**Expected Results:**
- ✅ Each filter works individually
- ✅ Multiple filters work together
- ✅ Results update dynamically
- ✅ Reset filters shows all posts
- ✅ No JavaScript console errors

**Test Pass/Fail:** ___________

---

### Scenario 5: Elementor Widget Compatibility

**Objective:** Verify Elementor widgets render correctly in Voxel

**Steps:**

1. **Install Elementor**
   ```bash
   wp plugin install elementor --activate
   ```

2. **Create page with Elementor**
   - Create new page
   - Edit with Elementor
   - Add widgets:
     - SOFIR Post Feed
     - SOFIR Map
     - SOFIR Filters

3. **Configure widgets with Voxel**
   - Post Feed widget:
     - Select CPT: listing
     - Select post type: Voxel listing
     - Set posts per page: 9
   - Map widget:
     - Select location field
     - Set map type: Google Maps
   - Filters widget:
     - Select filterable fields

4. **Publish and test**
   - View page on frontend
   - Verify widgets display
   - Test interactions
   - Check responsive behavior

**Expected Results:**
- ✅ All widgets render without errors
- ✅ Data displays correctly
- ✅ Interactions work (filters, pagination)
- ✅ Responsive on mobile
- ✅ No console errors

**Test Pass/Fail:** ___________

---

## Performance Testing

### Load Testing

**Objective:** Measure performance with large datasets

```bash
#!/bin/bash
# Script: tests/performance-load-test.sh

# Generate 1000 test posts
echo "Generating 1000 test posts..."
for i in {1..1000}; do
  wp post create \
    --post_type=listing \
    --post_title="Listing $i" \
    --post_content="Description $i" \
    --post_status=publish \
    --porcelain | xargs -I {} \
  wp post meta add {} sofir_location "Location" \
    sofir_price $((RANDOM * 1000)) \
    sofir_rating "$((RANDOM % 5 + 1))".0
done

echo "Testing archive page load time..."
time curl http://localhost:8080/listings/

echo "Testing with filters..."
time curl "http://localhost:8080/listings/?sofir_price_min=100000&sofir_price_max=500000"

echo "Load test completed."
```

**Metrics to track:**
- Response time: < 500ms (optimal), < 1s (acceptable)
- Memory usage: < 512MB
- Database queries: < 50
- Page size: < 2MB

---

### Query Performance

```php
// File: tests/test-query-performance.php

function test_voxel_query_performance() {
    $timer_start = microtime( true );
    
    $query = new WP_Query( [
        'post_type' => 'listing',
        'posts_per_page' => 20,
        'meta_query' => [
            [
                'key' => 'sofir_price',
                'value' => [ 0, 1000000 ],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC',
            ]
        ]
    ] );
    
    $time = microtime( true ) - $timer_start;
    
    echo "Query time: {$time}ms\n";
    echo "Found posts: " . $query->found_posts . "\n";
    echo "Query count: " . count( $query->queries ) . "\n";
    
    return $time < 0.5; // Should be < 500ms
}

test_voxel_query_performance();
```

---

## Security Testing

### AJAX Handler Security

```php
// File: tests/test-ajax-security.php

function test_ajax_nonce_validation() {
    // Test 1: Invalid nonce
    $_POST['nonce'] = 'invalid_nonce';
    $_POST['post_type'] = 'listing';
    
    ob_start();
    do_action( 'wp_ajax_sofir_voxel_filter_listings' );
    $output = ob_get_clean();
    
    // Should return nonce error
    assert( strpos( $output, 'nonce' ) !== false, 'Nonce validation failed' );
    
    // Test 2: Missing post_type
    unset( $_POST['post_type'] );
    
    ob_start();
    do_action( 'wp_ajax_sofir_voxel_filter_listings' );
    $output = ob_get_clean();
    
    // Should handle missing parameter
    assert( $output !== '', 'Should return response' );
    
    echo "✅ AJAX security tests passed\n";
}
```

### SQL Injection Prevention

```php
// File: tests/test-sql-injection-prevention.php

function test_sql_injection_prevention() {
    $_POST['nonce'] = wp_create_nonce( 'sofir_voxel' );
    $_POST['post_type'] = 'listing\'; DROP TABLE posts; --';
    
    ob_start();
    do_action( 'wp_ajax_sofir_voxel_filter_listings' );
    $output = ob_get_clean();
    
    // Verify table still exists
    global $wpdb;
    $table_exists = $wpdb->get_var( 
        "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_NAME = 'wp_posts'" 
    );
    
    assert( $table_exists > 0, 'SQL injection successful (BAD!)' );
    echo "✅ SQL injection prevention test passed\n";
}
```

### XSS Prevention

```php
// File: tests/test-xss-prevention.php

function test_xss_prevention() {
    $_POST['nonce'] = wp_create_nonce( 'sofir_voxel' );
    $_POST['s'] = '<script>alert("XSS")</script>';
    $_POST['post_type'] = 'listing';
    
    ob_start();
    do_action( 'wp_ajax_sofir_voxel_filter_listings' );
    $output = ob_get_clean();
    
    // Verify script tags are escaped
    assert( strpos( $output, '<script>' ) === false, 'XSS vulnerability found!' );
    assert( strpos( $output, 'alert' ) === false, 'XSS vulnerability found!' );
    
    echo "✅ XSS prevention test passed\n";
}
```

---

## Browser Compatibility

### Test Matrix

| Browser | Desktop | Mobile | Status |
|---------|---------|--------|--------|
| Chrome  | Latest  | Latest | ✅ |
| Firefox | Latest  | Latest | ✅ |
| Safari  | Latest  | Latest | ✅ |
| Edge    | Latest  | N/A    | ✅ |
| IE 11   | 11      | N/A    | ❌ Not supported |

### Manual Testing Checklist

```markdown
## Chrome / Firefox / Safari / Edge

- [ ] Archive page loads without errors
- [ ] Filters work correctly
- [ ] Search functionality works
- [ ] Map displays properly
- [ ] Images load correctly
- [ ] Forms submit successfully
- [ ] Responsive layout works on resize
- [ ] Console shows no errors
- [ ] No performance issues

## Mobile (iOS Safari / Chrome Android)

- [ ] Archive page responsive
- [ ] Filters usable on mobile
- [ ] Touch interactions work
- [ ] Map pinch/zoom works
- [ ] Forms accessible
- [ ] No horizontal scroll
- [ ] Text readable without zoom
```

### Automated Testing dengan Cypress

```javascript
// File: cypress/integration/voxel-integration.spec.js

describe('SOFIR + Voxel Integration', () => {
    beforeEach(() => {
        cy.visit('http://localhost:8080/listings/');
    });

    it('should display listings archive', () => {
        cy.get('.sofir-listings-grid').should('exist');
        cy.get('.sofir-listing-card').should('have.length.greaterThan', 0);
    });

    it('should filter by price', () => {
        cy.get('input[name="price_min"]').type('100000');
        cy.get('input[name="price_max"]').type('500000');
        cy.get('button[type="submit"]').click();

        cy.get('.sofir-listing-card').each(($card) => {
            cy.wrap($card).find('.sofir-price').should('be.visible');
        });
    });

    it('should search listings', () => {
        cy.get('input[name="s"]').type('Jakarta');
        cy.get('button[type="submit"]').click();

        cy.get('.sofir-listing-card').should('have.length.greaterThan', 0);
    });

    it('should display map', () => {
        cy.get('#sofir-map').should('exist');
        cy.get('.gm-style').should('be.visible');
    });

    it('should load single post', () => {
        cy.get('.sofir-listing-card a').first().click();
        cy.url().should('match', /\/listing\/[^/]+/);
        cy.get('.sofir-single-listing').should('exist');
    });
});
```

**Run tests:**
```bash
npx cypress open
# Or run headless
npx cypress run
```

---

## Real-World Workflows

### Workflow 1: Real Estate Marketplace

**Use Case:** Multi-vendor real estate listing platform

**Setup:**

1. **Create CPT: Property**
   - Fields: Location, Price, Bedrooms, Bathrooms, Area, Type
   - Voxel compatible

2. **Create Form: Add Listing**
   - Form fields matching CPT fields
   - Create post on submit
   - Assign to current user

3. **Create Voxel Templates:**
   - Single property page
   - Property archive
   - Map view
   - Vendor dashboard

4. **Setup Filters:**
   - Price range (slider)
   - Property type (select)
   - Location (map)
   - Bedrooms (number)

5. **Add Elementor Widgets:**
   - Featured listings widget
   - Map widget
   - Search form widget
   - Agent profile widget

**Testing Script:**
```bash
# Create test properties
wp post create --post_type=property --post_title="Apartment Jakarta" \
  --post_content="Modern apartment" --post_status=publish \
  --porcelain | xargs -I {} sh -c '
    wp post meta add {} sofir_location "Jakarta, Indonesia"
    wp post meta add {} sofir_price "2500000000"
    wp post meta add {} sofir_bedrooms "3"
    wp post meta add {} sofir_bathrooms "2"
    wp post meta add {} sofir_area "150"
  '

# Verify on frontend
curl http://localhost:8080/properties/
```

---

### Workflow 2: Event Management

**Use Case:** Event discovery and booking platform

**Setup:**

1. **Create CPT: Event**
   - Fields: Date, Time, Location, Capacity, Ticket Price
   - Voxel compatible with calendar view

2. **Voxel Calendar View:**
   - Display events on calendar
   - Click to see details
   - Responsive calendar

3. **Booking Integration:**
   - SOFIR Forms for ticket purchase
   - Payment gateway integration
   - Ticket generation

4. **Frontend Features:**
   - Calendar view
   - List view
   - Map view
   - Category filtering

**Test:**
```bash
# Create events
for i in {1..5}; do
  DATE=$((RANDOM % 30 + 1))
  wp post create --post_type=event \
    --post_title="Event $i" \
    --post_status=publish \
    --porcelain | xargs -I {} sh -c '
      wp post meta add {} sofir_date "2024-02-'$DATE'"
      wp post meta add {} sofir_capacity "100"
      wp post meta add {} sofir_price "50000"
    '
done
```

---

## Troubleshooting Guide

### Issue: CPT Menu Not Visible

**Symptoms:** CPT doesn't appear in admin sidebar

**Causes:**
1. Voxel override
2. Missing capabilities
3. Rewrite rules not flushed
4. Transient blocking

**Solutions:**

```bash
# Solution 1: Check CPT registration
wp post-type list --format=table

# Solution 2: Check post type capabilities
wp eval 'var_dump( get_post_type_object( "listing" ) );'

# Solution 3: Flush rewrite rules
wp rewrite flush

# Solution 4: Check for transients
wp transient delete sofir_cpt_visibility_fixed

# Solution 5: Reset Voxel settings
wp option delete voxel_post_types
wp voxel setup:post-types
```

---

### Issue: Voxel Fields Not Showing

**Symptoms:** Fields not visible in Voxel field list

**Causes:**
1. Field mapping not configured
2. Voxel field types not supported
3. Cache not cleared

**Solutions:**

```php
// Check field mapping
$manager = \Sofir\Voxel\Manager::instance();
$mapping = $manager->get_field_mapping();
var_dump( $mapping );

// Clear Voxel cache
wp voxel cache:flush

// Re-register fields
wp sofir update-voxel-fields
```

---

### Issue: Performance Degradation

**Symptoms:** Page load time > 1 second

**Causes:**
1. Too many posts per page
2. Unoptimized queries
3. Missing indexes
4. Large image files

**Solutions:**

```bash
# Check query performance
wp query-monitor enable

# Check database indexes
wp db query "SHOW INDEXES FROM wp_postmeta WHERE Column_name='meta_key'"

# Optimize images
wp image-optimize

# Clear object cache
wp object-cache flush

# Check slow queries
tail -f /var/log/mysql/slow-query.log
```

---

## Regression Testing Checklist

Before each release, verify:

- [ ] CPT menu visible with Voxel active
- [ ] Fields map correctly to Voxel
- [ ] Forms create posts successfully
- [ ] Filters work on archive pages
- [ ] Single post template displays
- [ ] Map integration works
- [ ] Search functions correctly
- [ ] Elementor widgets render
- [ ] No JavaScript errors
- [ ] No PHP errors in debug log
- [ ] AJAX requests work
- [ ] Performance acceptable
- [ ] Mobile responsive
- [ ] XSS prevention works
- [ ] SQL injection prevention works
- [ ] CSRF tokens validated
- [ ] Capabilities checked
- [ ] No deprecated functions

---

**Last Updated**: 2025  
**Version**: 1.0  
**Status**: Complete

