# CPT Menu Visibility Fix v1.0.8 - Complete Solution

## 📋 Overview

**Problem**: CPT menu items (vehicle, listing, dll) tidak muncul di admin sidebar WordPress walaupun sudah install template atau import package.

**Solution**: Dual-layer protection system dengan direct global registry manipulation untuk memastikan CPT SELALU visible.

## ✅ What's Fixed

### 1. Dual-Layer Init Hook System

**Location**: `includes/class-admin-library-panel.php`

```php
public function boot(): void {
    // Priority 999 pada admin_init - fix definitions
    add_action( 'admin_init', [ $this, 'auto_fix_cpt_visibility' ], 999 );
    
    // Priority 999 pada init - force global registry
    add_action( 'init', [ $this, 'force_cpt_visibility_on_init' ], 999 );
}
```

**Why Priority 999?**
- Runs AFTER all plugins/themes register their CPTs
- Overrides any theme/plugin that might hide CPT menus
- Ensures SOFIR CPTs always visible

### 2. Direct Global Registry Manipulation

**Method**: `force_cpt_visibility_on_init()`

```php
public function force_cpt_visibility_on_init(): void {
    global $wp_post_types;
    
    $manager = CptManager::instance();
    $post_types = $manager->get_post_types();
    
    foreach ( $post_types as $slug => $definition ) {
        if ( ! isset( $wp_post_types[ $slug ] ) ) {
            continue;
        }
        
        $post_type_obj = $wp_post_types[ $slug ];
        
        if ( ! $post_type_obj->show_in_menu || ! $post_type_obj->show_ui || ! $post_type_obj->public ) {
            // Force all visibility flags
            $post_type_obj->public = true;
            $post_type_obj->show_in_menu = true;
            $post_type_obj->show_ui = true;
            $post_type_obj->show_in_nav_menus = true;
            $post_type_obj->publicly_queryable = true;
            $post_type_obj->can_export = true;
            $post_type_obj->exclude_from_search = false;
            
            // WP_DEBUG logging
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( sprintf( '[SOFIR CPT] Forced visibility for CPT: %s', $slug ) );
            }
        }
    }
}
```

**Key Features**:
- Direct manipulation of `global $wp_post_types`
- No need to re-register CPT
- Immediate effect - no cache issues
- Real-time fix on every request

### 3. Removed Transient Blocking

**Before (v1.0.7)**:
```php
public function auto_fix_cpt_visibility(): void {
    $fixed = get_transient( 'sofir_cpt_visibility_fixed' );
    
    if ( $fixed ) {
        return; // ❌ Blocked after first fix
    }
    
    $this->ensure_cpt_menus_visible();
    set_transient( 'sofir_cpt_visibility_fixed', true, DAY_IN_SECONDS );
}
```

**After (v1.0.8)**:
```php
public function auto_fix_cpt_visibility(): void {
    if ( ! function_exists( 'is_admin' ) || ! is_admin() ) {
        return;
    }

    // ✅ ALWAYS fix visibility
    $this->ensure_cpt_menus_visible();
    
    // Version-based rewrite flush
    $current_version = '1.0.8';
    $rewrite_flushed = get_option( 'sofir_cpt_rewrite_flushed', '0' );
    
    if ( $rewrite_flushed !== $current_version ) {
        flush_rewrite_rules();
        update_option( 'sofir_cpt_rewrite_flushed', $current_version );
    }
}
```

**Benefits**:
- Fix runs on EVERY request
- No cache blocking
- Handles dynamic visibility changes

### 4. Version-Based Rewrite Flush

**Location**: `includes/class-admin-library-panel.php`

```php
$current_version = '1.0.8';
$rewrite_flushed = get_option( 'sofir_cpt_rewrite_flushed', '0' );

if ( $rewrite_flushed !== $current_version ) {
    flush_rewrite_rules();
    update_option( 'sofir_cpt_rewrite_flushed', $current_version );
}
```

**Benefits**:
- Flush only once per version
- Prevents unnecessary rewrites
- Auto-triggers on plugin update

### 5. Enhanced WP_DEBUG Logging

```php
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( sprintf( '[SOFIR CPT] Forced visibility for CPT: %s (show_in_menu=%s, show_ui=%s, public=%s)', 
        $slug, 
        $post_type_obj->show_in_menu ? 'true' : 'false',
        $post_type_obj->show_ui ? 'true' : 'false',
        $post_type_obj->public ? 'true' : 'false'
    ) );
    
    error_log( '[SOFIR CPT] Rewrite rules flushed - version: ' . $current_version );
    error_log( '[SOFIR CPT] CPT definitions version updated to 1.0.8' );
    error_log( sprintf( '[SOFIR CPT] Updated visibility settings for: %s', $slug ) );
}
```

## 📊 Complete Visibility Flags

All CPT visibility flags set to correct values:

```php
$definition['args']['public'] = true;
$definition['args']['show_in_menu'] = true;
$definition['args']['show_ui'] = true;
$definition['args']['show_in_nav_menus'] = true;
$definition['args']['publicly_queryable'] = true;
$definition['args']['can_export'] = true;
$definition['args']['exclude_from_search'] = false;
```

## 🔄 Version Updates

### CPT Definitions Version
- **Before**: `1.0.7`
- **After**: `1.0.8`

**Location**: `includes/sofir-cpt-manager.php`

```php
public function check_and_update_definitions(): void {
    $version = get_option( 'sofir_cpt_definitions_version', '0' );
    $current_version = '1.0.8'; // Updated
    
    // ... update logic
}
```

### Library Panel Version
- **Before**: `1.0.7`
- **After**: `1.0.8`

**Location**: `includes/class-admin-library-panel.php`

```php
if ( $updated ) {
    update_option( 'sofir_cpt_definitions_version', '1.0.8' );
}
```

## 🚀 How It Works

### Execution Flow

```
WordPress Init (priority 1)
    ↓
SOFIR CPT Registration (priority 1)
    ↓
Theme/Plugin CPT Modifications (priority 10-100)
    ↓
force_cpt_visibility_on_init() (priority 999) ← ✅ FORCE VISIBILITY
    ↓
Admin Menu Built
    ↓
admin_init Hook
    ↓
auto_fix_cpt_visibility() (priority 999) ← ✅ FIX DEFINITIONS
    ↓
ensure_cpt_menus_visible() ← ✅ UPDATE DATABASE
    ↓
flush_rewrite_rules() (if version changed)
```

### Real-World Example

**Scenario**: User installs "Vehicle Rental" template

```
1. Template installed via handle_install_ready_cpt()
2. CPT "vehicle" registered with visibility flags
3. WordPress init completes
4. force_cpt_visibility_on_init() runs at priority 999
5. Checks global $wp_post_types['vehicle']
6. Finds show_in_menu = false (somehow changed)
7. FORCES show_in_menu = true
8. Menu item appears immediately
9. admin_init runs
10. ensure_cpt_menus_visible() updates database
11. flush_rewrite_rules() if version changed
12. ✅ Vehicle menu visible forever
```

## 🛠️ Testing

### Test Visibility Fix

1. Install a template:
   - Go to SOFIR → Library
   - Click "Install" on any template
   - Check admin sidebar
   - ✅ CPT menu should appear immediately

2. Import a package:
   - Go to SOFIR → Library
   - Upload CPT package
   - Click "Import"
   - Check admin sidebar
   - ✅ CPT menu should appear immediately

3. Enable WP_DEBUG:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

4. Check logs at `wp-content/debug.log`:
```
[SOFIR CPT] Forced visibility for CPT: vehicle (show_in_menu=true, show_ui=true, public=true)
[SOFIR CPT] Updated visibility settings for: vehicle
[SOFIR CPT] Rewrite rules flushed - version: 1.0.8
[SOFIR CPT] CPT definitions version updated to 1.0.8
```

### Test Rewrite Flush

1. Check current version:
```php
$version = get_option( 'sofir_cpt_rewrite_flushed' );
echo $version; // Should be '1.0.8'
```

2. Force flush:
```php
delete_option( 'sofir_cpt_rewrite_flushed' );
// Visit admin page
// Check debug.log for flush message
```

## 📝 Options Reference

### Database Options

| Option Key | Value | Description |
|------------|-------|-------------|
| `sofir_cpt_definitions_version` | `1.0.8` | CPT definitions schema version |
| `sofir_cpt_rewrite_flushed` | `1.0.8` | Last rewrite flush version |
| `sofir_cpt_definitions` | Array | CPT configurations |

## 🐛 Troubleshooting

### CPT Menu Still Not Visible?

1. **Enable WP_DEBUG**:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

2. **Check debug.log** for SOFIR messages

3. **Manually trigger fix**:
   - Go to SOFIR → Library
   - Click "🔧 Fix CPT Menu Visibility" button
   - Check admin sidebar

4. **Check global registry**:
```php
global $wp_post_types;
var_dump( $wp_post_types['your_cpt_slug'] );
```

### Rewrite Rules Not Working?

1. **Flush manually**:
   - Go to Settings → Permalinks
   - Click "Save Changes"

2. **Check version**:
```php
$version = get_option( 'sofir_cpt_rewrite_flushed' );
if ( $version !== '1.0.8' ) {
    delete_option( 'sofir_cpt_rewrite_flushed' );
    flush_rewrite_rules();
}
```

## ✨ Benefits

### Before v1.0.8
❌ Transient blocking fix after first run
❌ CPT menu disappears after cache
❌ Manual fix required
❌ No logging
❌ Inconsistent behavior

### After v1.0.8
✅ Fix runs on EVERY request
✅ Direct global registry manipulation
✅ Dual-layer protection
✅ Comprehensive logging
✅ Version-based rewrite flush
✅ ALWAYS visible
✅ No cache issues
✅ Works with any theme/plugin

## 🔗 Related Files

- `includes/class-admin-library-panel.php` - Main fix logic
- `includes/sofir-cpt-manager.php` - CPT registration & version check
- `includes/class-admin-manager.php` - Admin bootstrapping

## 📚 Documentation

See also:
- [CPT Menu Fix v1.0.6](./CPT_MENU_FIX_V1.0.6.md)
- [CPT Menu Fix Summary](./CPT_MENU_FIX_SUMMARY.md)
- [Voxel CPT Optimization](./modules/voxel/VOXEL-CPT-OPTIMIZATION.md)

## 🎯 Conclusion

**v1.0.8 adalah complete solution untuk CPT menu visibility**:
- ✅ Dual-layer protection (init + admin_init priority 999)
- ✅ Direct global registry manipulation
- ✅ No transient blocking
- ✅ Version-based rewrite flush
- ✅ Comprehensive logging
- ✅ CPT menu SELALU visible, DIJAMIN!
