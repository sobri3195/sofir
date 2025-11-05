# SOFIR Mobile Support Documentation

## Overview

SOFIR Directory includes comprehensive mobile support with a responsive mobile menu and bottom navigation bar optimized for touch devices. This feature enhances the mobile user experience for directory-based websites.

## Features

### 1. Mobile Menu

A slide-in navigation panel that appears from the right side of the screen:

- **Hamburger Toggle Button**: Fixed position toggle button (top-right)
- **Slide-in Panel**: 80% width (max 320px) with smooth transitions
- **Menu Integration**: Supports WordPress menus via menu ID or theme location
- **User Section**: 
  - **Logged In**: Shows avatar, display name, and logout link
  - **Logged Out**: Shows Login and Register buttons
- **Overlay**: Semi-transparent background overlay
- **Keyboard Support**: ESC key to close menu
- **Accessibility**: ARIA labels and semantic HTML

### 2. Bottom Navigation Bar

A fixed bottom navbar with customizable navigation items:

- **Fixed Position**: Always visible at bottom of viewport
- **Auto-Hide**: Hides on scroll down, shows on scroll up
- **Default Items**:
  - 🏠 **Home**: Link to homepage
  - 🔍 **Search**: Link to search page
  - ➕ **Add**: Create new post (logged-in users only)
  - 💬 **Messages**: Messages page (logged-in users only)
  - 👤 **Profile**: User profile or login link
- **Extensible**: Custom items via `sofir/mobile/bottom_nav_item` action hook
- **Responsive**: Automatically adds 70px bottom padding to body

### 3. Settings Panel

Configure mobile features from WordPress admin:

- **Enable/Disable**: Toggle mobile support on/off
- **Menu Selection**: Choose which WordPress menu to display
- **Bottom Nav Toggle**: Show/hide bottom navigation bar
- **Breakpoint**: Set mobile breakpoint (default: 768px)

## Usage

### Automatic Integration

Mobile features are automatically enabled on mobile devices when the settings are enabled:

```php
// Automatically loads on mobile devices
// No additional code needed
```

### Shortcodes

#### Mobile Menu Shortcode

```php
// Use default settings
[sofir_mobile_menu]

// Specify custom menu ID
[sofir_mobile_menu menu_id="123"]
```

#### Bottom Navbar Shortcode

```php
// Use default items (home,search,add,messages,profile)
[sofir_bottom_navbar]

// Custom items
[sofir_bottom_navbar items="home,search,profile"]

// Only home and profile
[sofir_bottom_navbar items="home,profile"]
```

### PHP Integration

```php
// Render mobile menu programmatically
echo \Sofir\Directory\Mobile::instance()->render_mobile_menu_shortcode();

// Render bottom navbar with custom items
echo \Sofir\Directory\Mobile::instance()->render_bottom_navbar_shortcode([
    'items' => 'home,search,messages,profile'
]);

// Get current settings
$settings = \Sofir\Directory\Mobile::instance()->get_settings();
```

## Customization

### Adding Custom Bottom Nav Items

Use the `sofir/mobile/bottom_nav_item` action hook:

```php
add_action('sofir/mobile/bottom_nav_item', function($item) {
    if ($item === 'custom') {
        echo '<a href="/custom-page" class="sofir-bottom-nav-item">';
        echo '<span class="sofir-nav-icon">⭐</span>';
        echo '<span class="sofir-nav-label">Custom</span>';
        echo '</a>';
    }
}, 10, 1);

// Then use: [sofir_bottom_navbar items="home,custom,profile"]
```

### CSS Customization

Override default styles in your theme:

```css
/* Customize mobile menu colors */
.sofir-mobile-menu-panel {
    background: #1a1a1a;
    color: #fff;
}

/* Customize bottom navbar */
.sofir-bottom-navbar {
    background: linear-gradient(to right, #667eea, #764ba2);
}

.sofir-bottom-nav-item {
    color: #fff;
}

/* Customize primary button */
.sofir-bottom-nav-item.sofir-nav-primary {
    background: #ff6b6b;
}

/* Adjust mobile breakpoint */
@media (max-width: 992px) {
    .sofir-mobile-menu-toggle {
        display: block;
    }
}
```

### JavaScript Events

Listen to mobile menu events:

```javascript
// Mobile menu opened
jQuery(document).on('sofir:mobile-menu:open', function() {
    console.log('Mobile menu opened');
});

// Mobile menu closed
jQuery(document).on('sofir:mobile-menu:close', function() {
    console.log('Mobile menu closed');
});

// Bottom nav hidden
jQuery(document).on('sofir:bottom-nav:hide', function() {
    console.log('Bottom nav hidden');
});

// Bottom nav shown
jQuery(document).on('sofir:bottom-nav:show', function() {
    console.log('Bottom nav shown');
});
```

## Settings

### Admin Configuration

1. Navigate to **SOFIR > Dashboard > Content**
2. Scroll to **Mobile Settings** section
3. Configure options:
   - ✅ **Enable Mobile Support**: Turn on/off mobile features
   - 📱 **Mobile Menu**: Select which WordPress menu to display
   - 📍 **Show Bottom Nav**: Toggle bottom navigation bar
   - 📐 **Mobile Breakpoint**: Set responsive breakpoint (default: 768px)
4. Click **Save Mobile Settings**

### Programmatic Settings

```php
// Get mobile settings
$mobile = \Sofir\Directory\Mobile::instance();
$settings = $mobile->get_settings();

// Settings array structure:
[
    'enabled' => true,
    'menu_id' => 123,
    'show_bottom_nav' => true,
    'breakpoint' => 768,
]
```

## Technical Details

### File Structure

```
modules/directory/
├── mobile.php              # Mobile class implementation
└── MOBILE_SUPPORT.md      # This documentation

assets/
├── css/
│   └── mobile.css         # Mobile styles
└── js/
    └── mobile.js          # Mobile JavaScript
```

### Class Reference

**Namespace**: `Sofir\Directory\Mobile`

**Methods**:

```php
// Get singleton instance
Mobile::instance(): Mobile

// Initialize hooks and actions
boot(): void

// Get current settings
get_settings(): array

// Enqueue mobile assets
enqueue_mobile_assets(): void

// Render mobile menu HTML
render_mobile_menu(): void
render_mobile_menu_shortcode(array $atts = []): string

// Render bottom navbar HTML
render_bottom_navbar(): void
render_bottom_navbar_shortcode(array $atts = []): string

// Handle settings save
handle_save_settings(): void
```

### Hooks & Filters

**Actions**:
- `sofir/mobile/bottom_nav_item` - Render custom bottom nav items

**JavaScript Events**:
- `sofir:mobile-menu:open` - Triggered when mobile menu opens
- `sofir:mobile-menu:close` - Triggered when mobile menu closes
- `sofir:bottom-nav:hide` - Triggered when bottom nav hides
- `sofir:bottom-nav:show` - Triggered when bottom nav shows

### CSS Classes

**Mobile Menu**:
- `.sofir-mobile-menu` - Main container
- `.sofir-mobile-menu.is-active` - Active state
- `.sofir-mobile-menu-overlay` - Background overlay
- `.sofir-mobile-menu-panel` - Slide-in panel
- `.sofir-mobile-menu-toggle` - Hamburger button
- `.sofir-mobile-menu-close` - Close button
- `.sofir-mobile-nav` - Navigation container
- `.sofir-mobile-user-info` - User section
- `.sofir-mobile-auth-buttons` - Login/Register buttons

**Bottom Navbar**:
- `.sofir-bottom-navbar` - Main navbar container
- `.sofir-bottom-navbar.is-hidden` - Hidden state
- `.sofir-bottom-nav-item` - Individual nav item
- `.sofir-bottom-nav-item.sofir-nav-primary` - Primary action item
- `.sofir-nav-icon` - Icon element
- `.sofir-nav-label` - Label element

### JavaScript API

```javascript
// Access mobile data
var mobileData = SOFIR_MOBILE_DATA;
console.log(mobileData.breakpoint); // 768
console.log(mobileData.isMobile);   // true/false

// jQuery selectors
var mobileMenu = jQuery('#sofir-mobile-menu');
var bottomNav = jQuery('.sofir-bottom-navbar');

// Check if menu is active
if (mobileMenu.hasClass('is-active')) {
    // Menu is open
}

// Check if bottom nav is hidden
if (bottomNav.hasClass('is-hidden')) {
    // Nav is hidden
}
```

## Browser Support

- ✅ iOS Safari 12+
- ✅ Chrome Mobile 80+
- ✅ Firefox Mobile 68+
- ✅ Samsung Internet 10+
- ✅ UC Browser
- ✅ Opera Mobile

## Performance

- **Lightweight**: ~4KB CSS + ~2KB JavaScript (unminified)
- **Conditional Loading**: Only loads on mobile devices
- **Hardware Acceleration**: CSS transforms for smooth animations
- **Throttled Scroll**: Optimized scroll listener for bottom nav

## Best Practices

1. **Menu Selection**: Choose a mobile-optimized menu with fewer items
2. **Bottom Nav Items**: Limit to 4-5 items for best UX
3. **Icons**: Use emoji or icon fonts for better visual hierarchy
4. **Touch Targets**: Keep nav items at least 44x44px (automatically handled)
5. **Testing**: Test on real devices, not just browser DevTools
6. **Accessibility**: Ensure keyboard navigation works properly

## Examples

### Directory Homepage with Mobile Support

```php
// In your theme's functions.php
add_action('after_setup_theme', function() {
    // Register mobile menu
    register_nav_menu('mobile', __('Mobile Menu', 'theme'));
});

// In header.php (will auto-render on mobile)
// Mobile menu and bottom nav automatically appear on mobile devices
```

### Custom Directory Page

```php
<?php
/**
 * Template Name: Directory Mobile
 */

get_header();
?>

<div class="directory-page">
    <!-- Directory map -->
    <?php echo do_shortcode('[sofir_directory_map post_type="listing"]'); ?>
    
    <!-- Directory filters -->
    <?php echo do_shortcode('[sofir_directory_filters post_type="listing"]'); ?>
    
    <!-- Directory listings -->
    <?php echo do_shortcode('[sofir_post_feed post_type="listing" columns="2"]'); ?>
</div>

<!-- Mobile menu and bottom nav automatically appear -->

<?php get_footer(); ?>
```

### Custom Bottom Nav for Directory

```php
// Add custom directory-specific nav items
add_action('sofir/mobile/bottom_nav_item', function($item) {
    switch ($item) {
        case 'directory':
            echo '<a href="/directory" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">📍</span>';
            echo '<span class="sofir-nav-label">Directory</span>';
            echo '</a>';
            break;
            
        case 'map':
            echo '<a href="/map" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">🗺️</span>';
            echo '<span class="sofir-nav-label">Map</span>';
            echo '</a>';
            break;
            
        case 'favorites':
            echo '<a href="/favorites" class="sofir-bottom-nav-item">';
            echo '<span class="sofir-nav-icon">❤️</span>';
            echo '<span class="sofir-nav-label">Favorites</span>';
            echo '</a>';
            break;
    }
}, 10, 1);

// Use in template or shortcode
// [sofir_bottom_navbar items="directory,map,search,favorites,profile"]
```

## Troubleshooting

### Mobile menu not showing

1. Check if mobile support is enabled in settings
2. Verify you're viewing on a mobile device or viewport < 768px
3. Check browser console for JavaScript errors
4. Ensure jQuery is loaded

### Bottom nav overlapping content

The plugin automatically adds 70px bottom padding to body on mobile. If content still overlaps:

```css
@media (max-width: 768px) {
    body {
        padding-bottom: 80px !important;
    }
}
```

### Menu not closing on link click

Add custom JavaScript:

```javascript
jQuery(document).on('click', '.sofir-mobile-nav a', function() {
    jQuery('#sofir-mobile-menu').removeClass('is-active');
    jQuery('body').removeClass('sofir-mobile-menu-open');
});
```

### Custom icons not displaying

Replace emoji with icon fonts:

```css
.sofir-nav-icon {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
}

.sofir-nav-icon::before {
    content: "\f015"; /* Home icon */
}
```

## Integration with Other Modules

### Web Directory Dashboard Template

The mobile support integrates seamlessly with the web directory dashboard template:

```php
// Template: web-directory-dashboard
// Automatically includes mobile menu and bottom nav
// Responsive grid layout optimizes for mobile screens
// Map and filters adjust for touch devices
```

### Directory Blocks

All directory blocks are mobile-optimized:

- `sofir/map` - Touch-friendly zoom controls
- `sofir/search-form` - Mobile-friendly input fields
- `sofir/post-feed` - Responsive grid columns
- `sofir/review-stats` - Compact mobile layout

## License

Part of SOFIR WordPress Plugin - Mobile Support Module

---

**Last Updated**: 2024
**Module Version**: 1.0.0
**Compatibility**: SOFIR 1.0.0+
