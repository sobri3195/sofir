# Voxel Theme CPT Integration - Test Suite

## Quick Test Commands

### Test 1: Check if Voxel is Detected
```php
// Run in WordPress console or add to functions.php temporarily
$voxel_manager = \Sofir\Voxel\Manager::instance();
var_dump( $voxel_manager->is_voxel_active() );
// Expected: true (if Voxel Theme installed)
```

### Test 2: Check CPT Visibility Flags
```php
// Check specific CPT visibility
$post_type = 'vehicle'; // Replace with your CPT slug
$cpt_obj = get_post_type_object( $post_type );

if ( $cpt_obj ) {
    echo "CPT: {$post_type}\n";
    echo "show_in_menu: " . ( $cpt_obj->show_in_menu ? 'YES' : 'NO' ) . "\n";
    echo "show_ui: " . ( $cpt_obj->show_ui ? 'YES' : 'NO' ) . "\n";
    echo "public: " . ( $cpt_obj->public ? 'YES' : 'NO' ) . "\n";
    echo "show_in_nav_menus: " . ( $cpt_obj->show_in_nav_menus ? 'YES' : 'NO' ) . "\n";
    echo "publicly_queryable: " . ( $cpt_obj->publicly_queryable ? 'YES' : 'NO' ) . "\n";
} else {
    echo "CPT not found!\n";
}
// Expected: All flags should be YES
```

### Test 3: Check All SOFIR CPTs
```php
// List all SOFIR CPTs and their visibility
$cpt_manager = \Sofir\Cpt\Manager::instance();
$post_types = $cpt_manager->get_post_types();

foreach ( $post_types as $slug => $definition ) {
    if ( post_type_exists( $slug ) ) {
        $cpt_obj = get_post_type_object( $slug );
        $visible = $cpt_obj->show_in_menu && $cpt_obj->show_ui && $cpt_obj->public;
        echo sprintf( "%s: %s\n", $slug, $visible ? '✅ VISIBLE' : '❌ HIDDEN' );
    } else {
        echo sprintf( "%s: ⚠️ NOT REGISTERED\n", $slug );
    }
}
// Expected: All CPTs should show ✅ VISIBLE
```

### Test 4: Trigger Manual Fix
```php
// Manually trigger visibility fix
$voxel_manager = \Sofir\Voxel\Manager::instance();
$voxel_manager->ensure_sofir_cpts_visibility();
echo "CPT visibility fix triggered!\n";
flush_rewrite_rules();
echo "Rewrite rules flushed!\n";
// Expected: No errors, check admin menu after
```

---

## WordPress Admin Tests

### Test 5: Admin Menu Check
1. Login to WordPress admin
2. Look at sidebar menu
3. Check if SOFIR CPTs appear:
   - **Vehicles** (if installed)
   - **Listings** (if installed)
   - **Events** (if installed)
   - **Appointments** (if installed)
   - etc.
4. **Expected**: All SOFIR CPTs should be visible in admin menu

### Test 6: Install New Template with Voxel Active
1. Go to **SOFIR → Library**
2. Find a ready template (e.g., "Business Directory")
3. Click **Install**
4. Wait for success message
5. Check admin menu immediately
6. **Expected**: New CPT menu appears automatically

### Test 7: Import Package with Voxel Active
1. Go to **SOFIR → Library**
2. Upload a CPT package JSON file
3. Click **Import**
4. Wait for success message
5. Check admin menu
6. **Expected**: Imported CPT menu appears automatically

### Test 8: Manual Fix Button
1. Go to **SOFIR → Library**
2. Scroll to "🔧 CPT Visibility Troubleshooting" card
3. Click **Fix CPT Visibility** button
4. Wait for success notice
5. Check admin menu
6. **Expected**: All CPT menus restored

---

## Debug Mode Tests

### Test 9: Enable WP_DEBUG and Check Logs
1. Edit `wp-config.php`:
   ```php
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_LOG', true );
   define( 'WP_DEBUG_DISPLAY', false );
   ```
2. Reload WordPress admin
3. Check `/wp-content/debug.log` for entries like:
   ```
   [SOFIR Voxel] Restored visibility for CPT: vehicle
   [SOFIR Voxel] Restored visibility after registration for CPT: listing
   ```
4. **Expected**: Log entries confirm visibility restoration

### Test 10: Check Hook Execution
```php
// Add to functions.php temporarily
add_action( 'sofir/voxel/cpt_visibility_restored', function( $post_type ) {
    error_log( "Visibility restored hook fired for: {$post_type}" );
}, 10, 1 );

// Check debug.log after page load
// Expected: Hook fires for CPTs that needed restoration
```

---

## Performance Tests

### Test 11: Check Hook Priorities
```php
// Verify Voxel hooks are registered with correct priority
global $wp_filter;

echo "sofir/cpt/register_args filters:\n";
if ( isset( $wp_filter['sofir/cpt/register_args'] ) ) {
    foreach ( $wp_filter['sofir/cpt/register_args']->callbacks as $priority => $callbacks ) {
        echo "Priority {$priority}: " . count( $callbacks ) . " callbacks\n";
    }
}

echo "\ninit actions (priority 999):\n";
if ( isset( $wp_filter['init'] ) && isset( $wp_filter['init']->callbacks[999] ) ) {
    foreach ( $wp_filter['init']->callbacks[999] as $callback ) {
        var_dump( $callback['function'] );
    }
}
// Expected: ensure_sofir_cpts_visibility at priority 999
```

### Test 12: Database Query Count
```php
// Before optimization
define( 'SAVEQUERIES', true );
// Load page
// Check $wpdb->num_queries

// Should be minimal increase (< 5 queries)
global $wpdb;
echo "Total queries: " . $wpdb->num_queries . "\n";
// Expected: No significant increase in query count
```

---

## Edge Case Tests

### Test 13: Voxel Not Installed
1. Deactivate Voxel Theme
2. Check admin menu
3. **Expected**: SOFIR CPTs still visible (optimization only runs if Voxel active)

### Test 14: Multiple CPTs with Same Name
1. Create CPT with slug `listing` in SOFIR
2. Install Voxel (which also has `listing` CPT)
3. Check admin menu
4. **Expected**: SOFIR CPT takes precedence, menu visible

### Test 15: Switch Between Themes
1. Active theme: Voxel
2. Check SOFIR CPT menu → Should be visible
3. Switch to Twenty Twenty-Four theme
4. Check SOFIR CPT menu → Should still be visible
5. Switch back to Voxel
6. Check SOFIR CPT menu → Should still be visible
7. **Expected**: CPT menus remain visible regardless of theme

---

## Automated Test Script

```php
<?php
/**
 * Run this in WordPress admin or via WP-CLI
 * wp eval-file voxel-cpt-test.php
 */

echo "=== SOFIR Voxel Integration Test Suite ===\n\n";

// Test 1: Voxel Detection
$voxel_manager = \Sofir\Voxel\Manager::instance();
$voxel_active = $voxel_manager->is_voxel_active();
echo "1. Voxel Theme Active: " . ( $voxel_active ? '✅ YES' : '⚠️ NO' ) . "\n";

// Test 2: Get SOFIR CPTs
$cpt_manager = \Sofir\Cpt\Manager::instance();
$post_types = $cpt_manager->get_post_types();
echo "2. Total SOFIR CPTs: " . count( $post_types ) . "\n";

// Test 3: Check Visibility
$visible_count = 0;
$hidden_count = 0;
$not_registered = 0;

foreach ( $post_types as $slug => $definition ) {
    if ( post_type_exists( $slug ) ) {
        $cpt_obj = get_post_type_object( $slug );
        $visible = $cpt_obj->show_in_menu && $cpt_obj->show_ui && $cpt_obj->public;
        
        if ( $visible ) {
            $visible_count++;
        } else {
            $hidden_count++;
            echo "   ❌ HIDDEN: {$slug}\n";
        }
    } else {
        $not_registered++;
        echo "   ⚠️ NOT REGISTERED: {$slug}\n";
    }
}

echo "3. Visibility Status:\n";
echo "   ✅ Visible: {$visible_count}\n";
echo "   ❌ Hidden: {$hidden_count}\n";
echo "   ⚠️ Not Registered: {$not_registered}\n";

// Test 4: Check Hooks
echo "4. Hook Registration:\n";
echo "   - enhance_cpt_for_voxel: " . ( has_filter( 'sofir/cpt/register_args', [ $voxel_manager, 'enhance_cpt_for_voxel' ] ) !== false ? '✅' : '❌' ) . "\n";
echo "   - ensure_sofir_cpts_visibility: " . ( has_action( 'init', [ $voxel_manager, 'ensure_sofir_cpts_visibility' ] ) !== false ? '✅' : '❌' ) . "\n";
echo "   - restore_cpt_menu_after_voxel: " . ( has_action( 'registered_post_type', [ $voxel_manager, 'restore_cpt_menu_after_voxel' ] ) !== false ? '✅' : '❌' ) . "\n";

// Test 5: Final Result
echo "\n=== FINAL RESULT ===\n";
if ( $hidden_count === 0 && $not_registered === 0 ) {
    echo "✅ ALL TESTS PASSED!\n";
    echo "All SOFIR CPTs are visible in admin menu.\n";
} else {
    echo "⚠️ ISSUES DETECTED!\n";
    echo "Some CPTs are hidden or not registered.\n";
    echo "Try manual fix: Go to SOFIR → Library → Fix CPT Visibility\n";
}

echo "\n";
```

---

## Expected Results Summary

| Test | Expected Outcome |
|------|------------------|
| Voxel Detection | Should return `true` if Voxel installed |
| CPT Visibility Flags | All flags should be `YES` |
| All SOFIR CPTs | All should show `✅ VISIBLE` |
| Manual Fix | No errors, menus restored |
| Admin Menu Check | All CPTs visible in sidebar |
| Install Template | New CPT menu appears automatically |
| Import Package | Imported CPT menu appears automatically |
| Manual Fix Button | Success notice, menus restored |
| Debug Logs | Restoration logs appear |
| Hook Execution | `sofir/voxel/cpt_visibility_restored` fires |
| Hook Priorities | Hooks at priority 999 registered |
| Database Queries | < 5 additional queries |
| Voxel Not Installed | CPTs still visible |
| Multiple CPTs | SOFIR CPT takes precedence |
| Switch Themes | CPTs remain visible |

---

## Troubleshooting Failed Tests

### If CPT Still Hidden After All Tests:

1. **Check User Capabilities**
   ```php
   current_user_can( 'edit_posts' );
   // Should return true
   ```

2. **Check Global Registry**
   ```php
   global $wp_post_types;
   var_dump( $wp_post_types['your_cpt_slug'] );
   // Check all visibility flags
   ```

3. **Force Re-registration**
   ```php
   $cpt_manager = \Sofir\Cpt\Manager::instance();
   $cpt_manager->register_dynamic_post_types();
   flush_rewrite_rules();
   ```

4. **Clear All Caches**
   - Object cache (Redis, Memcached)
   - Page cache (WP Super Cache, W3 Total Cache)
   - OpCache (`opcache_reset()`)
   - Transients (`delete_transient( 'sofir_cpt_visibility_fixed' )`)

5. **Check for Plugin Conflicts**
   - Deactivate all plugins except SOFIR
   - Check if CPT appears
   - Re-activate plugins one by one

---

## Support Contact

If all tests fail, please provide:
1. WordPress version
2. Voxel Theme version
3. SOFIR plugin version
4. Active plugins list
5. Debug log output
6. Test script output

Contact: SOFIR Support Team
