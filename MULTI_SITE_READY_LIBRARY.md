# Multi-Site Ready Library - Overview

## 🌐 Multi-Site Development dengan Library CPT

Library CPT SOFIR dirancang dengan **multi-site compatibility** penuh, memungkinkan Anda:

### ✅ Fitur Multi-Site

1. **Clone Website Structure**
   - Export CPT dari site A
   - Import ke site B, C, D, dst
   - Struktur identik di semua site

2. **Development Workflow**
   - Setup di local/development
   - Export configuration
   - Deploy ke staging/production

3. **Franchise/Branch Management**
   - Template master di HQ site
   - Duplicate ke setiap branch
   - Consistency across locations

4. **Client Project Reusability**
   - Buat template untuk niche tertentu
   - Reuse untuk multiple clients
   - Save development time

## 📦 5 Template Siap Pakai

### 🏢 Business Directory
**Use Case Multi-Site**:
- Yellow pages untuk multiple cities
- Franchise directory chain
- Regional business listings

### 🏨 Hotel & Accommodation
**Use Case Multi-Site**:
- Hotel chain dengan multiple locations
- Multi-city accommodation platform
- Property management multiple sites

### 📰 News & Blog
**Use Case Multi-Site**:
- News network (regional editions)
- Multi-brand blog platform
- Content syndication network

### 📅 Events & Calendar
**Use Case Multi-Site**:
- Multi-city event platform
- Regional event calendars
- Conference series across locations

### ⏰ Appointments & Booking
**Use Case Multi-Site**:
- Clinic chain booking system
- Multi-location salon booking
- Franchise appointment system

## 🚀 Quick Start Multi-Site

### Scenario 1: Restaurant Chain

```bash
# Site 1: Master Template Setup
1. Install "Business Directory" template
2. Customize fields untuk restaurant
3. Export to JSON

# Site 2-10: Branch Sites
1. Import JSON file
2. Add branch-specific content
3. Same structure, different data
```

### Scenario 2: Hotel Chain

```bash
# HQ Site
1. Install "Hotel & Accommodation" template
2. Configure fields (price, amenities, etc)
3. Export configuration

# Each Hotel Site
1. Import configuration
2. Add hotel photos
3. Set local pricing
4. Publish rooms
```

### Scenario 3: Event Organizer

```bash
# Main Site
1. Install "Events & Calendar" template
2. Setup event categories
3. Configure booking fields
4. Export package

# Regional Sites
1. Import event structure
2. Add regional events
3. Local event management
```

## 📋 Export/Import Workflow

### Export dari Master Site

```php
// Via Admin UI
SOFIR → Library → Export CPT Package
↓
[✓] Select CPT
↓
👁 Preview Data
↓
⬇ Download JSON
```

### Import ke Target Sites

```php
// Via Admin UI
SOFIR → Library → Import CPT Package
↓
📥 Upload JSON File
↓
⬆ Import Package
↓
✅ CPT Registered
↓
🔄 Refresh Permalink
```

## 🎯 Best Practices Multi-Site

### 1. Consistency
- Use same template across all sites
- Same field configuration
- Unified taxonomy structure

### 2. Version Control
- Store JSON files in git
- Tag versions for each release
- Track changes over time

### 3. Documentation
- Document customizations
- Note any site-specific changes
- Maintain update log

### 4. Testing
- Test in development first
- Validate on staging
- Deploy to production last

### 5. Maintenance
- Update master template
- Re-export configuration
- Update all sites systematically

## 🔧 Programmatic Multi-Site

### WP Multisite Network

```php
// Install template to all sites in network
if ( is_multisite() ) {
    $sites = get_sites();
    
    foreach ( $sites as $site ) {
        switch_to_blog( $site->blog_id );
        
        // Import template
        $library = \Sofir\Admin\LibraryPanel::instance();
        $library->handle_install_ready_cpt();
        
        restore_current_blog();
    }
}
```

### Batch Import via WP-CLI

```bash
# Create custom WP-CLI command
wp sofir import-template --template=business_directory --sites=all

# Or site-specific
wp sofir import-template --template=hotel --site-id=2,3,4
```

## 📊 Real-World Examples

### Example 1: Indonesia Yellow Pages
```
Setup:
- Master: Jakarta HQ site
- Branches: 34 provincial sites
- Template: Business Directory
- Data: Local businesses per province
```

### Example 2: Hotel Network
```
Setup:
- Master: Corporate site
- Branches: 50 hotel locations
- Template: Hotel & Accommodation
- Data: Rooms, pricing, bookings per hotel
```

### Example 3: News Network
```
Setup:
- Master: National news site
- Branches: Regional news sites
- Template: News & Blog
- Data: Local news per region
```

## 🛠️ Advanced Configuration

### Custom Template for Multi-Site

```php
// Create custom template
$custom_template = [
    'name' => 'Custom Multi-Site Template',
    'cpts' => [
        'custom_cpt' => [
            'slug' => 'custom_cpt',
            // ... configuration
        ]
    ],
    'taxonomies' => [
        // ... taxonomies
    ]
];

// Export
$exporter = new \Sofir\Admin\CptExporter();
$exporter->export_package( ['custom_cpt'], 'multi-site-template' );

// Import to multiple sites
// ... import logic
```

### Site-Specific Customization

```php
// After import, customize per site
add_filter( 'sofir/cpt/listing/args', function( $args ) {
    // Site-specific modifications
    if ( get_current_blog_id() === 2 ) {
        $args['menu_icon'] = 'dashicons-building';
    }
    
    return $args;
} );
```

## 🎓 Training Resources

### Documentation
- [CPT Ready Library Guide (ID)](CPT_READY_LIBRARY_GUIDE_ID.md)
- [CPT Ready Library Guide (EN)](CPT_READY_LIBRARY_GUIDE_EN.md)
- [CPT Library Tab Guide](CPT_LIBRARY_TAB_GUIDE.md)

### Video Tutorials
- How to Export/Import CPT
- Multi-Site Setup Walkthrough
- Template Customization Guide

### Support
- Forum: https://sofir.io/community
- Email: support@sofir.io
- Docs: https://docs.sofir.io

## 🚀 Getting Started

### Quick Start (5 minutes)

1. **Choose Template**
   ```
   SOFIR → Library → Ready Templates
   ```

2. **Install**
   ```
   Click "Install Template" button
   ```

3. **Export**
   ```
   SOFIR → Library → Export CPT Package
   ```

4. **Import to Other Sites**
   ```
   Upload JSON to target sites
   ```

5. **Done!**
   ```
   All sites have same structure
   ```

## 📈 Benefits

### Time Savings
- Setup once, deploy many times
- No repetitive configuration
- Instant site cloning

### Consistency
- Uniform structure across sites
- Same fields and filters
- Standardized workflow

### Scalability
- Add new sites easily
- Update all sites systematically
- Centralized template management

### Flexibility
- Customize per site if needed
- Different content, same structure
- Easy maintenance

---

## 🎉 Conclusion

Library CPT SOFIR adalah solusi lengkap untuk **multi-site development**, memungkinkan Anda:

✅ Membuat berbagai jenis website (directory, hotel, news, events, appointments)  
✅ Clone struktur ke unlimited sites  
✅ Export/Import dengan mudah  
✅ Maintain consistency across network  
✅ Scale business dengan cepat  

**Ready to go multi-site? Start with Library CPT!** 🚀
