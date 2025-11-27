# SOFIR Voxel Theme Integration

Complete integration guide for using SOFIR CPT Library with Voxel Theme and Elementor.

## Overview

SOFIR now provides **full compatibility** with Voxel Theme, allowing you to use SOFIR's powerful CPT Library templates with Voxel's advanced directory features and Elementor page builder.

## Features

### ✅ Auto Field Mapping
- SOFIR fields automatically map to Voxel field types
- Location, hours, rating, price, gallery, and more
- Searchable and filterable fields preserved
- Native Voxel field behavior

### ✅ Native Voxel Template Support
- SOFIR CPTs work with Voxel templates
- Single, archive, and card templates
- Voxel styling and layouts
- Full theme customization

### ✅ Elementor Widgets (40 Total)
- **2 New Voxel-Specific Widgets**:
  - Voxel Listings (Grid/List/Map layouts)
  - Voxel Search Form (Advanced filtering)
- **12 SOFIR Elements** - Compatible with Voxel
- **7 Booking & Events** - Compatible with Voxel
- **16 E-Commerce** - WC/EDD/NC/Vendor support
- **3 E-Learning** - Course platform

### ✅ Advanced Search & Filters
- AJAX-powered filtering
- Location autocomplete
- Price range slider
- Rating filters
- Date range picker
- Open now filter
- Category filters

### ✅ 11 Ready-to-Use Templates
All SOFIR CPT Library templates are Voxel-compatible:
1. Business Directory
2. Hotel & Accommodation
3. News & Blog
4. Events & Calendar
5. Appointments
6. E-Commerce
7. Restaurant Orders
8. Car Rental
9. Community Forum
10. Doctor Appointments
11. E-Learning Courses

## Installation

### Requirements
- WordPress 6.0+
- PHP 8.0+
- SOFIR Plugin (latest version)
- Voxel Theme (any version)
- Elementor (free or pro)

### Setup
1. Install and activate Voxel Theme
2. Install and activate SOFIR Plugin
3. Voxel integration is **automatically enabled**
4. Go to **SOFIR → Library** to install CPT templates

## Field Mapping

SOFIR fields automatically map to Voxel field types:

| SOFIR Field | Voxel Type | Features |
|-------------|------------|----------|
| `location` | location | Address, lat/lng, map |
| `hours` | work-hours | Days, hours, timezone |
| `rating` | number | Min, max, searchable |
| `status` | select | Choices, multiple |
| `price` | number | Min, max, currency |
| `contact` | email | Email, phone, website |
| `gallery` | image | Multiple images |
| `attributes` | repeater | Custom fields |
| `event_date` | date | Format, range |
| `event_capacity` | number | Min, max |
| `appointment_datetime` | date | Date + time |
| `appointment_status` | select | Status choices |

## Elementor Widgets

### Voxel Listings Widget

Display CPT listings with Voxel theme styling.

**Features:**
- Grid, List, Masonry, Map layouts
- AJAX filtering
- Search & sorting
- Pagination
- Voxel card templates
- Responsive columns

**Usage:**
1. Add "Voxel Listings" widget to page
2. Select post type
3. Choose layout (grid/list/map)
4. Enable filters and search
5. Customize styling

**Settings:**
- Post Type: Select CPT
- Posts Per Page: 12 (default)
- Order By: Date, Title, Modified, Random
- Order: ASC/DESC
- Layout: Grid/List/Masonry/Map
- Columns: 1-6 columns
- Show Filters: Yes/No
- Show Search: Yes/No
- Use Voxel Templates: Yes/No
- Enable AJAX: Yes/No

### Voxel Search Form Widget

Advanced search form with filters.

**Features:**
- Keyword search
- Location autocomplete
- Category filters
- Price range
- Rating filter
- Date range
- Open now filter
- Horizontal/Vertical/Inline layouts

**Usage:**
1. Add "Voxel Search Form" widget
2. Select post type
3. Enable desired fields
4. Choose layout
5. Customize button

**Settings:**
- Post Type: Select CPT
- Redirect To: Custom URL or archive
- Show Keyword: Yes/No
- Show Location: Yes/No
- Show Categories: Yes/No
- Show Price Range: Yes/No
- Show Rating: Yes/No
- Show Date Range: Yes/No
- Show Open Now: Yes/No
- Form Layout: Horizontal/Vertical/Inline

## CPT Library Templates

### Business Directory
Perfect for directory websites with Voxel.

**Includes:**
- Listing CPT with location, rating, hours
- Category and location taxonomies
- Advanced search filters
- Map integration
- Voxel card templates

**Installation:**
1. Go to **SOFIR → Library**
2. Click **Install** on Business Directory
3. Go to **Elementor** → Add Voxel widgets
4. Done! Your directory is ready

### Events & Calendar
Event listings with Voxel calendar views.

**Includes:**
- Event CPT with date, capacity, location
- Category taxonomy
- Calendar views
- Registration forms
- RSVP tracking

### Hotel & Accommodation
Booking system with Voxel listings.

**Includes:**
- Hotel CPT with price, rating, gallery
- Amenities taxonomy
- Booking forms
- Location maps
- Review system

### Restaurant Orders
Restaurant menu and ordering with Voxel.

**Includes:**
- Menu CPT with price, gallery
- Category taxonomy
- Order forms
- Table booking
- Delivery tracking

### E-Learning Courses
Online course platform with Voxel.

**Includes:**
- Course CPT with pricing, lessons
- Category taxonomy
- Enrollment system
- Progress tracking
- Certificate generation

## AJAX Filtering

SOFIR provides AJAX-powered filtering without page reload.

### JavaScript API

```javascript
// Listen to filter updates
jQuery('.sofir-voxel-listings').on('sofir:listings:updated', function(e, data) {
    console.log('Total results:', data.total);
    console.log('HTML:', data.html);
});

// Access Voxel integration
window.SofirVoxel.init();
```

### PHP Filters

```php
// Customize field mapping
add_filter('sofir/field/meta_config', function($config, $field_key, $post_type) {
    if ($field_key === 'location') {
        $config['voxel_type'] = 'location';
        $config['voxel_searchable'] = true;
    }
    return $config;
}, 10, 3);

// Customize CPT args for Voxel
add_filter('sofir/cpt/register_args', function($args, $slug) {
    $args['voxel_enabled'] = true;
    return $args;
}, 10, 2);
```

## Styling

### CSS Variables

```css
/* Voxel Card Styling */
.voxel-enabled .sofir-listing-card {
    --voxel-card-bg: #fff;
    --voxel-card-border: #e5e5e5;
    --voxel-card-hover: #f9f9f9;
}

/* Dark Mode Support */
.voxel-enabled.dark-mode .sofir-listing-card {
    background: #1e1e1e;
    color: #e0e0e0;
}
```

### Custom Styling

```css
/* Override card styles */
.sofir-listing-card {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Custom search form */
.sofir-voxel-search-form {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 40px;
}
```

## Best Practices

### Performance
- Enable AJAX filtering for better UX
- Use pagination for large datasets
- Optimize images for cards
- Cache Voxel templates

### SEO
- Use SOFIR SEO module
- Add schema markup
- Optimize meta fields
- Use semantic HTML

### User Experience
- Enable search on archive pages
- Add filters for easy navigation
- Use map view for location-based
- Mobile-responsive layouts

### Accessibility
- Use ARIA labels
- Keyboard navigation
- Screen reader support
- High contrast mode

## Troubleshooting

### CPT Menu Not Showing
1. Go to **SOFIR → Tools**
2. Click **Refresh CPT Definitions**
3. Flush permalinks at **Settings → Permalinks**

### Fields Not Mapping
1. Check field types in SOFIR
2. Verify Voxel field config
3. Clear cache
4. Re-save post type

### AJAX Not Working
1. Check browser console for errors
2. Verify nonce security
3. Check AJAX URL
4. Test with browser dev tools

### Voxel Templates Not Loading
1. Verify Voxel theme is active
2. Check template assignments
3. Clear Elementor cache
4. Re-save Elementor templates

## Compatibility

### Voxel Theme
- ✅ All Voxel versions supported
- ✅ Voxel templates compatible
- ✅ Voxel filters integrated
- ✅ Voxel search compatible

### Elementor
- ✅ Elementor Free
- ✅ Elementor Pro
- ✅ Theme Builder
- ✅ Popup Builder

### E-Commerce
- ✅ WooCommerce
- ✅ Easy Digital Downloads
- ✅ North Commerce
- ✅ Multi-Vendor

## Advanced Usage

### Custom Post Types

```php
// Register custom CPT with Voxel support
$cpt_manager = \Sofir\Cpt\Manager::instance();
$cpt_manager->save_post_type([
    'slug' => 'property',
    'singular' => 'Property',
    'plural' => 'Properties',
    'fields' => ['location', 'price', 'gallery'],
    'filters' => ['location', 'price'],
    'taxonomies' => ['property_type'],
]);

// Auto-compatible with Voxel!
```

### Custom Widgets

```php
// Create custom Elementor widget for Voxel
class Custom_Voxel_Widget extends \Sofir\Elementor\Widgets\Voxel_Listings {
    public function get_name() {
        return 'custom-voxel-widget';
    }
    
    // Override methods as needed
}
```

### REST API

```php
// Get Voxel-compatible CPTs
GET /wp-json/sofir/v1/cpt/voxel

// Response:
{
    "listing": {
        "voxel_enabled": true,
        "voxel_templates": ["single", "archive", "card"],
        "fields": [...]
    }
}
```

## Support

### Documentation
- **English**: `/docs/voxel-integration.md`
- **Indonesian**: `/docs/integrasi-voxel.md`
- **Developer Guide**: `/docs/voxel-api.md`

### Resources
- Demo: https://demo.sofir.id/voxel
- Video Tutorial: Coming soon
- Community Forum: https://forum.sofir.id

### Get Help
1. Check documentation first
2. Search community forum
3. Submit support ticket
4. Email: support@sofir.id

## Changelog

### Version 1.0.0 (2024-01-15)
- ✅ Initial Voxel integration
- ✅ 2 new Elementor widgets
- ✅ Auto field mapping system
- ✅ AJAX filtering
- ✅ Location autocomplete
- ✅ Voxel template support
- ✅ 11 CPT templates compatible
- ✅ Full documentation

## License

SOFIR Voxel Integration is part of the SOFIR Plugin.
Licensed under GPL v3 or later.

---

**Made with ❤️ by SOFIR Team**

For more information, visit: https://sofir.id
