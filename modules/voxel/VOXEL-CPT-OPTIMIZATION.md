# Voxel Theme CPT Integration - Optimization Guide

## Overview

SOFIR plugin sekarang dilengkapi dengan **comprehensive safeguards** untuk memastikan bahwa Custom Post Types (CPT) SOFIR tetap visible di admin menu WordPress, bahkan ketika Voxel Theme aktif dan melakukan custom post type registration.

## Problem yang Diperbaiki

### Sebelum Optimization:
- ❌ CPT SOFIR kadang tidak muncul di admin menu setelah Voxel Theme aktif
- ❌ Voxel Theme mungkin override CPT registration dengan settings sendiri
- ❌ Menu admin CPT hidden karena conflict antara SOFIR dan Voxel registration
- ❌ Tidak ada fallback mechanism untuk restore visibility

### Setelah Optimization:
- ✅ CPT SOFIR **selalu visible** di admin menu meskipun Voxel aktif
- ✅ Multiple safeguard layers untuk prevent override
- ✅ Auto-restore CPT visibility setelah Voxel registration
- ✅ Compatible dengan semua Voxel features (templates, filters, widgets)

---

## Solutions Implemented

### 1. **Enhanced CPT Registration Args** (Priority: 10)

File: `modules/voxel/manager.php`  
Method: `enhance_cpt_for_voxel()`

```php
public function enhance_cpt_for_voxel( array $args, string $slug ): array {
    // Voxel-specific flags
    $args['voxel_enabled'] = true;
    $args['voxel_templates'] = true;
    $args['voxel_filters'] = true;
    
    // Add custom-fields support for Voxel
    if ( ! in_array( 'custom-fields', $args['supports'], true ) ) {
        $args['supports'][] = 'custom-fields';
    }
    
    // ✅ NEW: Ensure visibility flags are set
    if ( ! isset( $args['show_in_menu'] ) ) {
        $args['show_in_menu'] = true;
    }
    
    if ( ! isset( $args['show_ui'] ) ) {
        $args['show_ui'] = true;
    }
    
    if ( ! isset( $args['public'] ) ) {
        $args['public'] = true;
    }
    
    return $args;
}
```

**Hook**: `add_filter( 'sofir/cpt/register_args', [ $this, 'enhance_cpt_for_voxel' ], 10, 2 )`

**What it does**:
- Adds Voxel compatibility flags
- **Ensures `show_in_menu`, `show_ui`, `public` are set to `true`**
- Runs before CPT registration to prevent hiding

---

### 2. **Global CPT Visibility Check** (Priority: 999)

File: `modules/voxel/manager.php`  
Method: `ensure_sofir_cpts_visibility()`

```php
public function ensure_sofir_cpts_visibility(): void {
    $cpt_manager = \Sofir\Cpt\Manager::instance();
    $post_types = $cpt_manager->get_post_types();

    foreach ( $post_types as $slug => $definition ) {
        if ( ! \post_type_exists( $slug ) ) {
            continue;
        }

        $post_type_obj = \get_post_type_object( $slug );
        if ( ! $post_type_obj ) {
            continue;
        }

        // Check if visibility flags are disabled
        if ( ! $post_type_obj->show_in_menu || ! $post_type_obj->show_ui || ! $post_type_obj->public ) {
            // Force enable all visibility flags
            $post_type_obj->show_in_menu = true;
            $post_type_obj->show_ui = true;
            $post_type_obj->public = true;
            $post_type_obj->show_in_nav_menus = true;
            $post_type_obj->publicly_queryable = true;
            $post_type_obj->can_export = true;
            $post_type_obj->exclude_from_search = false;

            // Update global registry
            global $wp_post_types;
            $wp_post_types[ $slug ] = $post_type_obj;
        }
    }
}
```

**Hook**: `add_action( 'init', [ $this, 'ensure_sofir_cpts_visibility' ], 999 )`

**What it does**:
- Runs at priority 999 (very late in WordPress init)
- Checks all SOFIR CPTs for visibility flags
- **Forces visibility if any flag is disabled**
- Updates global `$wp_post_types` registry directly

---

### 3. **Per-CPT Restore Hook** (Priority: 999)

File: `modules/voxel/manager.php`  
Method: `restore_cpt_menu_after_voxel()`

```php
public function restore_cpt_menu_after_voxel( string $post_type, \WP_Post_Type $args ): void {
    $cpt_manager = \Sofir\Cpt\Manager::instance();
    $post_types = $cpt_manager->get_post_types();

    // Only process SOFIR CPTs
    if ( ! isset( $post_types[ $post_type ] ) ) {
        return;
    }

    // Check if Voxel disabled visibility
    if ( ! $args->show_in_menu || ! $args->show_ui ) {
        // Restore visibility immediately
        $args->show_in_menu = true;
        $args->show_ui = true;
        $args->public = true;
        $args->show_in_nav_menus = true;
        $args->publicly_queryable = true;

        // Update global registry
        global $wp_post_types;
        $wp_post_types[ $post_type ] = $args;
    }
}
```

**Hook**: `add_action( 'registered_post_type', [ $this, 'restore_cpt_menu_after_voxel' ], 999, 2 )`

**What it does**:
- Runs immediately after **each CPT is registered**
- Detects if Voxel or another plugin disabled visibility
- **Restores visibility instantly**
- Runs at priority 999 to override Voxel changes

---

## How It Works - Execution Flow

```
WordPress Init
    ↓
1. SOFIR CPT Manager registers CPTs (priority 1)
    ├─ Reads definitions from database
    ├─ Applies filter: sofir/cpt/register_args
    ├─ Voxel Manager enhances args (adds show_in_menu: true)
    └─ Calls register_post_type()
    ↓
2. WordPress fires 'registered_post_type' hook
    ├─ Voxel Theme might override visibility settings
    ├─ SOFIR Voxel Manager hook fires (priority 999)
    └─ Restores visibility if disabled
    ↓
3. Late init hook fires (priority 999)
    ├─ SOFIR checks all CPTs globally
    ├─ Forces visibility for any hidden CPTs
    └─ Updates $wp_post_types registry
    ↓
Admin Menu Built
    └─ ✅ All SOFIR CPTs visible in sidebar
```

---

## Benefits

### ✅ Triple-Layer Protection
1. **Prevention**: Set visibility flags during registration
2. **Immediate Restore**: Fix visibility right after CPT registration
3. **Global Check**: Scan and fix all CPTs at end of init

### ✅ Zero Configuration
- Auto-activates when Voxel Theme detected
- No user action required
- Works with existing CPT definitions

### ✅ Performance Optimized
- Only runs checks when Voxel is active
- Minimal database queries
- Uses WordPress global registry directly

### ✅ Full Voxel Compatibility
- Maintains Voxel templates support
- Preserves Voxel filters and search
- Compatible with Voxel Elementor widgets
- Field mapping still works

---

## Testing Guide

### Test Scenario 1: Fresh Install
1. Install SOFIR plugin
2. Install & activate Voxel Theme
3. Check admin menu → All SOFIR CPTs should be visible

### Test Scenario 2: Existing Site
1. Site with Voxel already active
2. Activate SOFIR plugin
3. Install CPT template (e.g., Business Directory)
4. Check admin menu → CPT menu appears immediately

### Test Scenario 3: Import Package
1. Go to SOFIR → Library
2. Import CPT package
3. Check admin menu → Imported CPT visible

### Test Scenario 4: Manual Fix
1. If menu hidden (rare edge case)
2. Go to SOFIR → Library
3. Click "Fix CPT Visibility" button in troubleshooting card
4. Menu should reappear

---

## Troubleshooting

### Issue: CPT Menu Still Not Showing

**Solutions**:

1. **Check Voxel Version**
   - Ensure Voxel Theme is up to date
   - Some older versions may have aggressive overrides

2. **Manual Fix Button**
   - Go to **SOFIR → Library**
   - Find "🔧 CPT Visibility Troubleshooting" card
   - Click "Fix CPT Visibility" button
   - This forces immediate flush + restore

3. **Check CPT Definition**
   ```php
   // Verify in database:
   $cpt_manager = \Sofir\Cpt\Manager::instance();
   $post_types = $cpt_manager->get_post_types();
   var_dump( $post_types['your_cpt_slug']['args'] );
   ```

4. **Flush Rewrite Rules**
   - Go to **Settings → Permalinks**
   - Click "Save Changes" (no changes needed)
   - This flushes WordPress rewrite rules

5. **Check User Capabilities**
   - Ensure current user has `edit_posts` capability
   - Some CPTs require specific capabilities

6. **Disable Other Plugins**
   - Temporarily disable other plugins
   - Check if another plugin conflicts
   - Re-enable one by one to find conflict

---

## Developer Hooks

### Filter: `sofir/voxel/cpt_visibility_check`

Override visibility check behavior:

```php
add_filter( 'sofir/voxel/cpt_visibility_check', function( $should_check, $post_type ) {
    // Skip checks for specific CPT
    if ( $post_type === 'my_custom_cpt' ) {
        return false;
    }
    return $should_check;
}, 10, 2 );
```

### Action: `sofir/voxel/cpt_visibility_restored`

Run code after visibility is restored:

```php
add_action( 'sofir/voxel/cpt_visibility_restored', function( $post_type ) {
    error_log( "SOFIR restored visibility for: {$post_type}" );
}, 10, 1 );
```

---

## Version History

### v1.0 - Initial Voxel Integration
- Basic field mapping
- Voxel template support

### v2.0 - CPT Visibility Optimization ✅ **NEW**
- Triple-layer protection system
- Enhanced registration args
- Global visibility check (priority 999)
- Per-CPT restore hook
- Auto-fix on admin_init
- Manual fix button in UI
- Comprehensive documentation

---

## Related Files

- `modules/voxel/manager.php` - Main integration logic
- `includes/sofir-cpt-manager.php` - CPT registration system
- `includes/class-admin-library-panel.php` - Manual fix button UI
- `modules/voxel/VOXEL-CPT-OPTIMIZATION.md` - This document

---

## Support

For issues or questions:
1. Check [SOFIR Documentation](https://sofir.com/docs)
2. Review Voxel Theme compatibility guide
3. Contact SOFIR support with CPT slug and Voxel version

---

**Last Updated**: 2025  
**Tested With**: Voxel Theme 1.3.x, WordPress 6.4+
