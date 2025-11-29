# Voxel Integration Settings - Admin UI

## Overview

SOFIR Plugin now includes a comprehensive admin interface for configuring Voxel Theme integration on a per-CPT basis. This allows you to customize how each Custom Post Type works with Voxel's powerful theme features.

## Accessing Voxel Settings

1. Navigate to **SOFIR → Voxel** in your WordPress admin
2. You'll see a list of all registered Custom Post Types
3. Click **Configure** on any CPT to access its Voxel integration settings

## Features

### 1. Enable/Disable Integration

Toggle Voxel integration on or off for each individual CPT. When enabled, the CPT will:
- Auto-map fields to Voxel field types
- Support Voxel templates
- Enable advanced search and filters

### 2. Post Type Settings

Configure core Voxel post type settings:

- **Voxel Post Type Key**: Unique identifier for the post type in Voxel
- **Voxel Icon**: Icon displayed in Voxel interface (e.g., `location-alt`, `calendar-alt`)
- **Enable Search**: Toggle advanced search functionality
- **Enable Map**: Toggle map view for posts with location data

### 3. Field Mapping

Map SOFIR custom fields to Voxel field types for full compatibility:

| SOFIR Field Type | Voxel Field Type | Show in Card | Show in Single | Searchable |
|------------------|------------------|--------------|----------------|------------|
| location         | location         | ✓            | ✓              | ✓          |
| hours            | work-hours       | ✓            | ✓              | ✓          |
| rating           | number           | ✓            | ✓              | ✓          |
| status           | select           | ✓            | ✓              | ✓          |
| price            | number           | ✓            | ✓              | ✓          |
| contact          | email            | ✓            | ✓              | -          |
| gallery          | image            | ✓            | ✓              | -          |
| attributes       | repeater         | -            | ✓              | ✓          |

**Auto-mapping** is enabled by default based on SOFIR field types.

### 4. Elementor Templates

Assign specific Elementor templates for different page types:

- **Archive Page**: Template for post type archive (listing view)
- **Single Page**: Template for individual post view
- **Add New Page**: Template for post submission form
- **Card Design**: Template for post card in grids/lists
- **Login Page**: Custom login template
- **Header**: Custom header template
- **Footer**: Custom footer template
- **Order Page**: Template for order/booking pages (if applicable)
- **Dashboard Page**: Template for user dashboard

Each template can be edited directly with Elementor via the **Edit with Elementor** button.

### 5. Advanced Filters

Enable specific filters in Voxel search forms:

- **Keyword Search**: Text-based search
- **Location Filter**: Geographic location filtering
- **Category Filter**: Taxonomy-based filtering
- **Price Range**: Min/max price filtering
- **Rating Filter**: Star rating filtering
- **Date Range**: Date-based filtering
- **Open Now Filter**: Real-time availability filtering

### 6. Notification Settings

Configure email notifications for users and admins:

#### User Notifications
- **New Post Published**: Notify user when their post goes live
- **Post Status Change**: Notify user of status updates

#### Admin Notifications
- **New Post Submitted**: Notify admin of new submissions

### 7. User Role Settings

Control which user roles can create and edit posts from the frontend:

- Administrator
- Editor
- Author
- Contributor
- Subscriber
- Custom roles

## How It Works

### 1. Installation

The Voxel integration settings are automatically available in SOFIR → Voxel tab. No additional setup required.

### 2. Configuration Process

1. **Navigate** to SOFIR → Voxel
2. **Select** a Custom Post Type
3. **Enable** Voxel Integration
4. **Configure** post type settings
5. **Map** fields to Voxel types
6. **Assign** Elementor templates
7. **Enable** desired filters
8. **Set up** notifications
9. **Configure** user role permissions
10. **Save** settings

### 3. Auto-Detection

When Voxel theme is active, SOFIR automatically:
- Detects Voxel installation
- Shows compatibility notice
- Enables integration features
- Maps fields using intelligent defaults

### 4. Per-CPT Configuration

Each Custom Post Type has independent settings, allowing you to:
- Mix Voxel and non-Voxel post types
- Use different templates per post type
- Configure unique filter combinations
- Set post-type-specific notifications

## CPT Menu Visibility

If your Custom Post Type menus are not appearing in the WordPress admin sidebar, use the **Tools** tab:

1. Navigate to **SOFIR → Tools**
2. Click **Refresh CPT Definitions**
3. This will:
   - Update all CPT visibility settings
   - Enable frontend access
   - Flush rewrite rules
   - Apply CPT Fix v1.0.6

## Compatibility

### Supported Post Types

All SOFIR post types are Voxel-compatible, including:

**Seed CPTs:**
- listing
- profile
- article
- event
- appointment

**Library Template CPTs:**
- hotel (Hotel & Accommodation)
- restaurant_order (Restaurant Orders)
- menu_item (Restaurant Menu)
- vehicle (Car Rental)
- forum_topic (Community Forum)
- doctor (Doctor Appointments)
- course (E-Learning Courses)
- lesson (Course Lessons)
- vendor_store (Marketplace Stores)
- vendor_product (Marketplace Products)

### Voxel Features

Works seamlessly with Voxel's:
- Advanced search and filters
- Map views
- Elementor widgets
- Frontend submission
- User dashboard
- Review system
- Booking system
- Membership integration

## Best Practices

### 1. Template Assignment

- Create dedicated Elementor templates for each post type
- Use Voxel dynamic tags for field display
- Test responsive layouts
- Optimize for mobile devices

### 2. Field Mapping

- Review auto-mapped fields
- Adjust visibility settings per field
- Enable search only for filterable fields
- Use appropriate Voxel field types

### 3. Filter Configuration

- Enable only necessary filters
- Too many filters can overwhelm users
- Test filter combinations
- Consider mobile experience

### 4. Performance

- Limit the number of searchable fields
- Use caching when possible
- Optimize map queries
- Monitor server load

## Troubleshooting

### CPT Menu Not Showing

**Solution:** Navigate to SOFIR → Tools → Refresh CPT Definitions

### Fields Not Displaying in Voxel

**Solution:** Check field mapping in SOFIR → Voxel → [Post Type] → Field Mapping

### Templates Not Working

**Solution:** 
1. Verify template assignment in Voxel settings
2. Check Elementor template status (published)
3. Ensure template type matches usage

### Search Not Working

**Solution:**
1. Enable "Enable Search" in post type settings
2. Mark fields as searchable in field mapping
3. Enable desired filters in Advanced Filters section

## Developer Reference

### Saved Settings Structure

Settings are stored in WordPress options table:

```php
$settings = get_option( 'sofir_voxel_' . $cpt_slug . '_settings', [] );
```

Structure:
```php
[
    'enabled' => bool,
    'post_type_settings' => [
        'key' => string,
        'icon' => string,
        'search_enabled' => bool,
        'map_enabled' => bool,
    ],
    'field_mapping' => [
        'field_key' => [
            'voxel_type' => string,
            'show_in_card' => bool,
            'show_in_single' => bool,
            'searchable' => bool,
        ],
    ],
    'filters' => [
        'keyword' => bool,
        'location' => bool,
        // ... other filters
    ],
    'templates' => [
        'archive' => int,    // Elementor template ID
        'single' => int,
        // ... other templates
    ],
    'notifications' => [
        'user' => [
            'new_post' => bool,
            'status_change' => bool,
        ],
        'admin' => [
            'new_post' => bool,
        ],
    ],
    'user_roles' => array,  // Array of role slugs
]
```

### Hooks & Filters

```php
// Before saving Voxel settings
do_action( 'sofir/voxel/before_save_settings', $cpt_slug, $settings );

// After saving Voxel settings
do_action( 'sofir/voxel/after_save_settings', $cpt_slug, $settings );

// Modify Voxel field types
$types = apply_filters( 'sofir/voxel/field_types', $types );

// Modify Voxel templates list
$templates = apply_filters( 'sofir/voxel/elementor_templates', $templates );
```

### Programmatic Access

```php
use Sofir\Admin\VoxelPanel;

// Get instance
$panel = VoxelPanel::instance();

// Get settings for a CPT
$settings = get_option( 'sofir_voxel_listing_settings' );

// Check if integration is enabled
$enabled = ! empty( $settings['enabled'] );
```

## Version History

### v1.0.0 (Current)
- Initial release
- Full admin UI for Voxel integration
- Per-CPT configuration
- Field mapping system
- Template assignment
- Filter configuration
- Notification settings
- User role management

## Support

For support and questions:
- Check documentation at `/docs/VOXEL_INTEGRATION.md`
- Visit SOFIR → Tools for CPT troubleshooting
- Ensure Voxel theme is active
- Check WordPress and PHP versions meet requirements

---

**Plugin:** SOFIR  
**Feature:** Voxel Integration Settings  
**Author:** SOFIR Team  
**Last Updated:** 2024
