# 🚀 Voxel + SOFIR Quick Reference Card

**Print this page or bookmark for quick access to common tasks!**

---

## 📍 File Locations

```
📂 modules/voxel/
├── manager.php                           # Main Voxel integration class
├── README-LEARNING-RESOURCES.md          # Documentation index (START HERE)
├── VOXEL-CPT-OPTIMIZATION.md            # CPT menu protection system
├── VOXEL-ASSETS-AND-WORKFLOWS.md        # Assets, snippets, automation
├── VOXEL-CODE-SNIPPETS-LIBRARY.md       # 50+ code snippets
├── VOXEL-TESTING-INTEGRATION-GUIDE.md   # Complete testing guide
└── TEST-VOXEL-INTEGRATION.md            # Automated test suite
```

---

## ⚡ Common Tasks

### 1. Fix CPT Menu Not Showing

```bash
# Option A: Check CPT registration
wp post-type list --format=table

# Option B: Flush rewrite rules
wp rewrite flush

# Option C: Check post type details
wp eval 'var_dump( get_post_type_object( "listing" ) );'

# Option D: Manual fix via code
do_action( 'sofir/voxel/cpt_visibility_restored', 'listing' );
```

**Documentation:** See [VOXEL-CPT-OPTIMIZATION.md](./VOXEL-CPT-OPTIMIZATION.md) - Troubleshooting

---

### 2. Import CPT from Package

```bash
# 1. Go to admin
# SOFIR → Library → Import CPT

# 2. Select package ZIP file

# 3. Configure settings:
#    - Slug: listing
#    - Label: Listings
#    - Voxel compatibility: Enable

# 4. Click Import

# Verify:
wp post-type list
wp posts list --post_type=listing
```

**Documentation:** See [VOXEL-ASSETS-AND-WORKFLOWS.md](./VOXEL-ASSETS-AND-WORKFLOWS.md) - CPT Import Process

---

### 3. Create Custom Filter

**Copy from:** [VOXEL-CODE-SNIPPETS-LIBRARY.md](./VOXEL-CODE-SNIPPETS-LIBRARY.md) - Filter & Search

```php
// Add to functions.php or plugin
add_filter( 'voxel/post-feed/query-args', function( $query_args, $widget ) {
    if ( isset( $_REQUEST['price_min'] ) ) {
        $query_args['meta_query'][] = [
            'key' => 'sofir_price',
            'value' => (float) $_REQUEST['price_min'],
            'compare' => '>=',
            'type' => 'NUMERIC',
        ];
    }
    return $query_args;
}, 10, 2 );
```

---

### 4. Add Location Autocomplete

**Copy from:** [VOXEL-CODE-SNIPPETS-LIBRARY.md](./VOXEL-CODE-SNIPPETS-LIBRARY.md) - Location Field dengan Autocomplete

```php
// PHP: Add AJAX handler
add_action( 'wp_ajax_sofir_location_suggestions', function() {
    // Handle location suggestions
} );

// JavaScript: In assets/js/location-autocomplete.js
jQuery(function($) {
    $('.sofir-location-input').on('keyup', function() {
        // Trigger AJAX suggestions
    });
});
```

---

### 5. Generate Test Data

```bash
# Generate 20 test listings
wp post create --post_type=listing --post_title="Test Listing" \
  --post_content="Test description" --post_status=publish \
  --porcelain | xargs -I {} sh -c '
    wp post meta add {} sofir_location "Jakarta"
    wp post meta add {} sofir_price "1000000"
    wp post meta add {} sofir_rating "4.5"
  '

# Or use snippet from documentation
```

**Documentation:** See [VOXEL-CODE-SNIPPETS-LIBRARY.md](./VOXEL-CODE-SNIPPETS-LIBRARY.md) - Generate Sample Data

---

### 6. Set Up Sure Trigger Automation

```
1. Go to: Sure Trigger → Workflows
2. Create New Workflow
3. Set Trigger: SOFIR listing created
4. Add Action: Create Voxel post type
5. Configure post fields
6. Save & Activate
```

**Documentation:** See [VOXEL-ASSETS-AND-WORKFLOWS.md](./VOXEL-ASSETS-AND-WORKFLOWS.md) - Sure Trigger Integration

---

### 7. Run Integration Tests

```bash
# Start Docker environment
docker-compose up -d

# Wait for WordPress
sleep 30

# Run tests
cd tests
php run-tests.php --module=voxel

# View results
tail -f reports/test-results.log
```

**Documentation:** See [VOXEL-TESTING-INTEGRATION-GUIDE.md](./VOXEL-TESTING-INTEGRATION-GUIDE.md)

---

### 8. Debug Performance Issues

```bash
# Check query performance
wp query-monitor enable

# Check database indexes
wp db query "SHOW INDEXES FROM wp_postmeta"

# Optimize database
wp optimize-db

# Clear caches
wp object-cache flush
wp transient delete-all

# Check slow queries
mysql -u root -p wordpress < /var/log/mysql/slow-query.log
```

**Documentation:** See [VOXEL-TESTING-INTEGRATION-GUIDE.md](./VOXEL-TESTING-INTEGRATION-GUIDE.md) - Performance Testing

---

## 🎯 Code Snippets Quick Access

| Snippet | File | Lines |
|---------|------|-------|
| Rating Field | VOXEL-CODE-SNIPPETS-LIBRARY.md | 1 |
| Work Hours | VOXEL-CODE-SNIPPETS-LIBRARY.md | 2 |
| Location Autocomplete | VOXEL-CODE-SNIPPETS-LIBRARY.md | 3 |
| Advanced Filters | VOXEL-CODE-SNIPPETS-LIBRARY.md | 4 |
| Distance Search | VOXEL-CODE-SNIPPETS-LIBRARY.md | 5 |
| Taxonomy Filters | VOXEL-CODE-SNIPPETS-LIBRARY.md | 6 |
| Single Template | VOXEL-CODE-SNIPPETS-LIBRARY.md | 7 |
| Archive Template | VOXEL-CODE-SNIPPETS-LIBRARY.md | 8 |
| AJAX Filtering | VOXEL-CODE-SNIPPETS-LIBRARY.md | 9 |
| Map Integration | VOXEL-CODE-SNIPPETS-LIBRARY.md | 10 |
| Bulk Import CSV | VOXEL-CODE-SNIPPETS-LIBRARY.md | 11 |
| Generate Data | VOXEL-CODE-SNIPPETS-LIBRARY.md | 12 |
| Query Optimization | VOXEL-CODE-SNIPPETS-LIBRARY.md | 13 |
| Image Optimization | VOXEL-CODE-SNIPPETS-LIBRARY.md | 14 |
| Query Caching | VOXEL-CODE-SNIPPETS-LIBRARY.md | 15 |

---

## 🔗 Voxel Theme Links

| Resource | URL |
|----------|-----|
| Official Website | https://voxel.guide/ |
| Assets Library | https://voxel.guide/assets/?type=asset&switcher=1 |
| Documentation | https://voxel.guide/docs/ |
| Support Forum | https://voxel.guide/support/ |

---

## 📋 Hooks & Filters

### Available SOFIR Hooks for Voxel

```php
// Filter: Customize CPT registration for Voxel
add_filter( 'sofir/cpt/register_args', function( $args, $slug ) {
    // Customize args
    return $args;
}, 10, 2 );

// Filter: Map field types to Voxel
add_filter( 'sofir/field/meta_config', function( $config, $field_key, $post_type ) {
    // Customize field config
    return $config;
}, 10, 3 );

// Action: CPT visibility restored
add_action( 'sofir/voxel/cpt_visibility_restored', function( $post_type ) {
    error_log( "Voxel restored visibility for: {$post_type}" );
}, 10, 1 );

// Filter: Check if should verify visibility
add_filter( 'sofir/voxel/cpt_visibility_check', function( $should_check, $post_type ) {
    // Return false to skip checks for specific CPT
    return $should_check;
}, 10, 2 );
```

---

## 🧪 Testing Checklist

Before deploying to production:

- [ ] CPT menu visible in admin
- [ ] Fields map to Voxel correctly
- [ ] Forms create posts successfully
- [ ] Filters work on archive
- [ ] Single post template displays
- [ ] Map displays correctly
- [ ] Search functions properly
- [ ] No JavaScript errors
- [ ] No PHP errors
- [ ] Responsive on mobile
- [ ] Performance acceptable (< 500ms)
- [ ] XSS prevention works
- [ ] SQL injection prevention works
- [ ] AJAX handlers secured
- [ ] Capabilities checked

---

## 💾 Development Setup Commands

```bash
# Quick Docker setup
docker-compose up -d

# Install WordPress
wp core install \
  --url=http://localhost:8080 \
  --title="Test" \
  --admin_user=admin \
  --admin_password=admin123 \
  --admin_email=admin@test.local

# Install Voxel
wp theme install voxel --activate

# Install SOFIR
wp plugin activate sofir

# Generate test data
wp sofir generate-test-data --count=20

# Access WordPress
# Admin: http://localhost:8080/wp-admin
# User: admin / Password: admin123
```

---

## 🐛 Common Issues & Solutions

| Issue | Solution | Doc |
|-------|----------|-----|
| CPT menu missing | Flush rewrite rules, check capabilities | VOXEL-CPT-OPTIMIZATION.md |
| Fields not mapping | Clear Voxel cache, re-register fields | VOXEL-CODE-SNIPPETS-LIBRARY.md |
| Slow performance | Enable query caching, optimize images | VOXEL-CODE-SNIPPETS-LIBRARY.md |
| Filters not working | Check AJAX handler, verify meta keys | VOXEL-CODE-SNIPPETS-LIBRARY.md |
| Template not displaying | Verify Voxel template exists, check syntax | VOXEL-TESTING-INTEGRATION-GUIDE.md |
| XSS vulnerability | Always escape output with esc_html() | VOXEL-CODE-SNIPPETS-LIBRARY.md |
| SQL injection | Use $wpdb->prepare() for queries | VOXEL-CODE-SNIPPETS-LIBRARY.md |

---

## 📊 Performance Targets

| Metric | Target | Acceptable |
|--------|--------|-----------|
| Page Load Time | < 500ms | < 1s |
| Database Queries | < 50 | < 100 |
| Memory Usage | < 256MB | < 512MB |
| Page Size | < 1MB | < 2MB |
| Lighthouse Score | > 90 | > 70 |

---

## 🚀 Deployment Checklist

- [ ] Run all tests
- [ ] Performance benchmarks passed
- [ ] Security scan passed
- [ ] Browser compatibility verified
- [ ] Backup database before deploy
- [ ] Test on staging environment
- [ ] Document any custom code
- [ ] Update documentation
- [ ] Notify team of changes
- [ ] Monitor error logs post-deploy

---

## 📞 Quick Support

| Issue | Action |
|-------|--------|
| **Can't find answer** | Check README-LEARNING-RESOURCES.md |
| **CPT problem** | Go to VOXEL-CPT-OPTIMIZATION.md |
| **Need code example** | Search VOXEL-CODE-SNIPPETS-LIBRARY.md |
| **Testing question** | See VOXEL-TESTING-INTEGRATION-GUIDE.md |
| **Setup help** | Read VOXEL-ASSETS-AND-WORKFLOWS.md |

---

## 🎓 Learning Resources

**30-Minute Quick Start:**
1. Read README-LEARNING-RESOURCES.md (5 min)
2. Browse https://voxel.guide/assets/ (10 min)
3. Skim VOXEL-CODE-SNIPPETS-LIBRARY.md (10 min)
4. Try one snippet (5 min)

**2-Hour Hands-On:**
1. Setup Docker environment (20 min)
2. Run test scenario 1 (40 min)
3. Implement a snippet (30 min)
4. Test in browser (30 min)

**Full Mastery:**
Follow the complete learning paths in README-LEARNING-RESOURCES.md (4-6 hours)

---

## 📝 File Menu

- 📄 README-LEARNING-RESOURCES.md - **START HERE** - Documentation index
- 🎯 VOXEL-CPT-OPTIMIZATION.md - CPT menu protection system
- 🌐 VOXEL-ASSETS-AND-WORKFLOWS.md - Assets, automation, imports
- 💻 VOXEL-CODE-SNIPPETS-LIBRARY.md - 50+ code snippets
- 🧪 VOXEL-TESTING-INTEGRATION-GUIDE.md - Testing framework
- ⚡ QUICK-REFERENCE.md - This file!

---

**Last Updated:** 2025  
**Version:** 1.0  
**Status:** Complete

---

**🎉 Ready to integrate? Start with README-LEARNING-RESOURCES.md!**

