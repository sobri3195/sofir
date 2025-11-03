# Quick Fix: WP_DEBUG Duplicate Constants

## 🚨 Problem
```
Warning: Constant WP_DEBUG already defined in wp-config.php on line 104
Warning: Cannot modify header information - headers already sent
```

## ✅ Solution (5 Minutes)

### Step 1: Backup
```bash
cp wp-config.php wp-config.php.backup
```

### Step 2: Find Duplicates
Open `wp-config.php` and search for:
- `WP_DEBUG`
- `WP_DEBUG_LOG`
- `WP_DEBUG_DISPLAY`

### Step 3: Remove All Definitions
Delete ALL instances of these constants.

### Step 4: Add Safe Definitions
Add this code **ONCE** in wp-config.php (before "That's all, stop editing!"):

```php
// Debug settings - safe from duplicates
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', false );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', false );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false );
}
```

### Step 5: Save & Test
Save the file and refresh your site. Errors should be gone!

---

## 📋 Development Mode (Show Errors)
For local/staging sites:
```php
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', true );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', true );
}
```

## 🔒 Production Mode (Hide Errors)
For live sites:
```php
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', false );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
    define( 'WP_DEBUG_LOG', false );
}

if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
    define( 'WP_DEBUG_DISPLAY', false );
}
```

---

## ⚠️ Common Mistakes to Avoid

### ❌ DON'T
```php
define( 'WP_DEBUG', true );
// ... more code ...
define( 'WP_DEBUG', false ); // ERROR: Duplicate!
```

### ✅ DO
```php
if ( ! defined( 'WP_DEBUG' ) ) {
    define( 'WP_DEBUG', true );
}
```

---

## 🔍 Still Not Working?

1. **Check file encoding**: Save as UTF-8 without BOM
2. **Check for whitespace**: No spaces/characters before `<?php`
3. **No closing tag**: Remove `?>` at end of wp-config.php
4. **Clear cache**: Browser cache + WordPress cache
5. **Check other files**: Look in theme's functions.php

---

## 📚 More Help
- See `WP_CONFIG_FIX_GUIDE.md` for detailed instructions
- Check `wp-config-sample.php` for examples
- Visit: https://wordpress.org/support/article/debugging-in-wordpress/

---

**Quick Checklist:**
- [ ] Backed up wp-config.php
- [ ] Found all WP_DEBUG definitions
- [ ] Removed duplicates
- [ ] Added safe conditional definitions
- [ ] Verified no whitespace before `<?php`
- [ ] Saved as UTF-8 without BOM
- [ ] Tested site - errors gone ✓
