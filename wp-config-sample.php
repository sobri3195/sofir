<?php
/**
 * Sample WordPress Configuration File
 * 
 * This is a sample wp-config.php file that demonstrates the correct way to
 * define WordPress debugging constants. Use this as a reference to fix
 * "Constant already defined" errors in your WordPress installation.
 * 
 * IMPORTANT: This file should NOT be placed in your plugin directory.
 * It belongs in your WordPress root directory.
 * 
 * @package SOFIR
 */

/**
 * ============================================================================
 * WordPress Debugging Settings
 * ============================================================================
 * 
 * The following constants should only be defined ONCE in your wp-config.php.
 * If you see "Constant already defined" errors, search your wp-config.php
 * for duplicate definitions and remove them.
 * 
 * Common Mistake:
 * - Having define('WP_DEBUG', true); appear multiple times
 * - Copying debug settings from documentation without checking existing settings
 * 
 * How to Fix:
 * 1. Open your wp-config.php file
 * 2. Search for "WP_DEBUG" (use Ctrl+F or Cmd+F)
 * 3. Keep only ONE set of debug definitions
 * 4. Remove any duplicate definitions
 */

/**
 * For Development/Testing (shows all errors)
 * Uncomment these lines for debugging:
 */
// define( 'WP_DEBUG', true );
// define( 'WP_DEBUG_LOG', true );
// define( 'WP_DEBUG_DISPLAY', true );
// define( 'SCRIPT_DEBUG', true );
// @ini_set( 'display_errors', 1 );

/**
 * For Production (no error display, but log errors)
 * Recommended settings for live sites:
 */
// define( 'WP_DEBUG', false );
// define( 'WP_DEBUG_LOG', false );
// define( 'WP_DEBUG_DISPLAY', false );
// @ini_set( 'display_errors', 0 );

/**
 * Alternative: Conditional debugging based on environment
 * This prevents duplicate definitions and adapts to your environment:
 */
if ( ! defined( 'WP_DEBUG' ) ) {
    // Check if we're in a development environment
    $is_development = ( 
        isset( $_SERVER['HTTP_HOST'] ) && 
        ( 
            strpos( $_SERVER['HTTP_HOST'], 'localhost' ) !== false ||
            strpos( $_SERVER['HTTP_HOST'], '.local' ) !== false ||
            strpos( $_SERVER['HTTP_HOST'], '.dev' ) !== false
        )
    );
    
    define( 'WP_DEBUG', $is_development );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', WP_DEBUG );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false ); // Never display errors on production
}

if ( ! defined( 'SCRIPT_DEBUG' ) ) {
    define( 'SCRIPT_DEBUG', WP_DEBUG );
}

/**
 * ============================================================================
 * Additional Debug Constants
 * ============================================================================
 */

// Save database queries for analysis
if ( ! defined( 'SAVEQUERIES' ) ) {
    define( 'SAVEQUERIES', WP_DEBUG );
}

/**
 * ============================================================================
 * How to Implement This Fix
 * ============================================================================
 * 
 * Step 1: Backup your current wp-config.php
 * 
 * Step 2: Open wp-config.php in a text editor
 * 
 * Step 3: Find the section with debug constants (usually near the bottom,
 *         before "That's all, stop editing!" comment)
 * 
 * Step 4: Replace ALL debug constant definitions with the code above
 *         (copy lines 49-75)
 * 
 * Step 5: Save the file and test your site
 * 
 * ============================================================================
 * Common Locations of Duplicate Definitions
 * ============================================================================
 * 
 * 1. Near the top of the file (added manually)
 * 2. In the middle (added by plugins or during troubleshooting)
 * 3. Near the bottom (default location)
 * 4. After "That's all, stop editing!" (should never be here!)
 * 
 * Search for these patterns and keep only ONE set:
 * - define( 'WP_DEBUG'
 * - define('WP_DEBUG'
 * - define ("WP_DEBUG"
 * 
 * ============================================================================
 * After Fixing
 * ============================================================================
 * 
 * Once you've removed duplicate definitions, your site should no longer show:
 * - "Constant WP_DEBUG already defined" warnings
 * - "Cannot modify header information - headers already sent" errors
 * 
 * If you still see these errors, check:
 * 1. Any whitespace or BOM characters before <?php in wp-config.php
 * 2. Other included files that might define these constants
 * 3. Theme's functions.php
 * 4. Must-use plugins in wp-content/mu-plugins/
 */

/* That's all for debug settings! */
