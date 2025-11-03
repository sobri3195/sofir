# Fixing "Constant WP_DEBUG already defined" Error

## Problem Description

You're seeing these errors in your WordPress site:

```
Warning: Constant WP_DEBUG already defined in /path/to/wp-config.php on line 104
Warning: Constant WP_DEBUG_LOG already defined in /path/to/wp-config.php on line 105
Warning: Cannot modify header information - headers already sent...
```

## Root Cause

The WordPress configuration file (`wp-config.php`) has **duplicate definitions** of debugging constants. PHP only allows a constant to be defined once, and when you try to define it again, it generates a warning. These warnings cause output to be sent to the browser, which prevents WordPress from setting HTTP headers properly (resulting in the "headers already sent" error).

## Quick Fix (5 minutes)

### Step 1: Backup Your wp-config.php

```bash
# Connect to your server via SSH or FTP
# Make a backup copy
cp wp-config.php wp-config.php.backup
```

### Step 2: Edit wp-config.php

Open your `wp-config.php` file located in your WordPress root directory.

### Step 3: Find ALL Debug Constant Definitions

Search for these terms (use Ctrl+F or Cmd+F):
- `WP_DEBUG`
- `WP_DEBUG_LOG`
- `WP_DEBUG_DISPLAY`

You'll likely find these constants defined **multiple times** in your file, for example:

```php
// First definition (line 50)
define( 'WP_DEBUG', true );

// ... other code ...

// Duplicate definition (line 104)
define( 'WP_DEBUG', false );  // ← This causes the error!
```

### Step 4: Remove Duplicate Definitions

**Keep only ONE set of debug constant definitions.** Delete all others.

#### Recommended Approach - Use Conditional Definitions

Replace all debug constant definitions with this safe code:

```php
// Safe approach - only defines if not already defined
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', false ); // Set to true for debugging
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', false );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false );
}

if ( ! defined( 'SCRIPT_DEBUG' ) ) {
    define( 'SCRIPT_DEBUG', false );
}
```

### Step 5: Check for Hidden Issues

Before saving, check for:

1. **No whitespace before `<?php`** at the very start of the file
2. **No closing `?>` tag** at the end of the file (not needed and can cause issues)
3. **No BOM (Byte Order Mark)** - save as UTF-8 without BOM

### Step 6: Save and Test

1. Save the file
2. Refresh your WordPress site
3. The warnings should be gone

## Detailed Solution

### For Development Sites

If you're working on a development/staging site and want detailed debugging:

```php
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', true ); // Log to wp-content/debug.log
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', true ); // Show errors on screen
}

if ( ! defined( 'SCRIPT_DEBUG' ) ) {
    define( 'SCRIPT_DEBUG', true ); // Use non-minified scripts
}

if ( ! defined( 'SAVEQUERIES' ) ) {
    define( 'SAVEQUERIES', true ); // Log database queries
}

@ini_set( 'display_errors', 1 );
```

### For Production Sites

If you're working on a live site, use these secure settings:

```php
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', false );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', false );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false ); // NEVER show errors on production
}

@ini_set( 'display_errors', 0 );
```

### Environment-Aware Configuration (Advanced)

Automatically enable debugging based on your domain:

```php
if ( ! defined( 'WP_DEBUG' ) ) {
    $is_development = (
        isset( $_SERVER['HTTP_HOST'] ) &&
        (
            strpos( $_SERVER['HTTP_HOST'], 'localhost' ) !== false ||
            strpos( $_SERVER['HTTP_HOST'], '.local' ) !== false ||
            strpos( $_SERVER['HTTP_HOST'], '.dev' ) !== false ||
            strpos( $_SERVER['HTTP_HOST'], 'staging' ) !== false
        )
    );
    
    define( 'WP_DEBUG', $is_development );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', WP_DEBUG );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false ); // Always false for security
}
```

## Common Mistakes

### ❌ Wrong - Multiple Definitions

```php
// Top of file
define( 'WP_DEBUG', true );

// ... 50 lines later ...

// Duplicate - causes error!
define( 'WP_DEBUG', false );
```

### ✅ Correct - Single Definition

```php
// Only defined once
define( 'WP_DEBUG', true );
```

### ✅ Even Better - Conditional Definition

```php
// Won't cause errors even if defined elsewhere
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
}
```

## Still Having Issues?

If you've removed duplicates but still see errors:

### 1. Check Other Files

The constants might be defined in:
- `wp-content/mu-plugins/*.php` (must-use plugins)
- Your theme's `functions.php`
- Other plugin files (though this is rare and bad practice)

Search these locations:

```bash
# SSH into your server and run:
grep -r "define.*WP_DEBUG" wp-content/mu-plugins/
grep "define.*WP_DEBUG" wp-content/themes/*/functions.php
```

### 2. Check File Encoding

Ensure your `wp-config.php` is saved as **UTF-8 without BOM**.

In most text editors:
- Notepad++: Encoding → UTF-8 without BOM
- VS Code: Click encoding in status bar → Save with Encoding → UTF-8
- Sublime Text: File → Save with Encoding → UTF-8

### 3. Check for Hidden Characters

Open `wp-config.php` in a hex editor or use:

```bash
# Check first few bytes
od -c wp-config.php | head
```

The file should start with `<?php` with no spaces or characters before it.

### 4. Verify Syntax

Make sure all opening braces have closing braces:

```php
// ✅ Correct
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
}

// ❌ Wrong - missing closing brace
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
// Missing }
```

## Prevention Tips

### For Future Edits

1. **Always search first** - Before adding debug constants, search for existing ones
2. **Use conditional checks** - Always wrap defines in `if ( ! defined() )` checks
3. **Comment your changes** - Note when/why you added debug settings
4. **Keep it organized** - Put all debug settings in one section
5. **Use version control** - Track changes to wp-config.php

### Sample Organized Structure

```php
/**
 * ===========================================================================
 * DEBUG SETTINGS
 * ===========================================================================
 * Last modified: 2024-01-15
 * All debug constants are defined in this section only
 */
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', false );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', false );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false );
}
/* End debug settings */
```

## Understanding the Error

### Why "Headers Already Sent"?

1. PHP constants generate **warning messages**
2. Warning messages are **output** to the browser
3. Once ANY output is sent, HTTP headers can't be modified
4. WordPress needs to set headers for cookies, redirects, content-type, etc.
5. Since output was already sent (the warning), header modification fails

### The Chain Reaction

```
Duplicate define() 
  ↓
PHP Warning displayed
  ↓
Output sent to browser
  ↓
Headers already sent
  ↓
WordPress can't set headers
  ↓
More errors cascade
```

## Testing Your Fix

After fixing wp-config.php:

1. **Clear all caches**
   - Browser cache (Ctrl+Shift+R or Cmd+Shift+R)
   - WordPress cache (if using a caching plugin)
   - Server cache (Varnish, Redis, etc.)

2. **Check error logs**
   ```bash
   tail -f wp-content/debug.log
   ```

3. **Test key pages**
   - Homepage
   - Admin dashboard
   - Login page
   - Any custom post types

4. **Verify headers**
   - Use browser dev tools (F12 → Network tab)
   - Check response headers are being set correctly

## Need More Help?

See our sample configuration file: `wp-config-sample.php`

This file contains:
- Safe, conditional definitions
- Comments explaining each setting
- Different configurations for dev vs. production
- Best practices for WordPress configuration

## Summary Checklist

- [ ] Backed up wp-config.php
- [ ] Searched for all instances of WP_DEBUG constants
- [ ] Removed duplicate definitions
- [ ] Used conditional `if ( ! defined() )` checks
- [ ] Verified no whitespace before `<?php`
- [ ] Verified no closing `?>` tag at end
- [ ] Saved as UTF-8 without BOM
- [ ] Tested site - warnings gone
- [ ] Checked error logs - clean
- [ ] Documented the fix for your team

---

**Related Files:**
- `wp-config-sample.php` - Sample configuration with safe practices
- WordPress Codex: https://wordpress.org/support/article/debugging-in-wordpress/
