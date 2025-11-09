# Ready-to-Use CPT Library - Complete Guide

## 📚 Overview

**Ready-to-Use CPT Library** is a revolutionary feature in SOFIR that allows you to create various professional websites with just **one click**. No need for complex manual configuration - just choose a template, click install, and your website is ready!

## 🎯 Why Ready-to-Use CPT Library?

### Problems Solved
- ❌ Manual CPT configuration is time-consuming
- ❌ Need to reconfigure fields and filters for every project
- ❌ Difficult to duplicate CPT structure across websites
- ❌ Clients need quick websites with complete features

### Solutions
- ✅ Install complete templates in 1 click
- ✅ CPTs pre-configured with optimal fields and filters
- ✅ Ready-to-use taxonomies with hierarchical structure
- ✅ Easily duplicate structure to multiple websites

## 🚀 5 Ready-to-Use Templates

### 1. 🏢 Business Directory
**Perfect for**: Business directories, company listings, yellow pages

**Complete Features**:
- 📍 **Location & Map**: Google Maps/Mapbox integration to display business location
- ⭐ **Rating & Review**: 5-star rating system with user reviews
- ⏰ **Operating Hours**: Dedicated field for opening/closing hours per day
- 🔍 **Search Filters**: Filter by location, rating, status, price, and attributes
- 💰 **Pricing**: Price range for categorization (cheap/medium/expensive)
- 📞 **Contact**: Phone, email, website, social media
- 🖼️ **Photo Gallery**: Multiple images to showcase business

**Use Cases**:
- Restaurant and culinary directory
- Hotel and accommodation listings
- Local yellow pages
- Service and professional directory

**CPT & Taxonomies**:
- CPT: `listing` with 8 custom fields
- Taxonomy: `listing_category` (hierarchical), `listing_location` (flat)

---

### 2. 🏨 Hotel & Accommodation
**Perfect for**: Hotel websites, villas, homestays, accommodation booking

**Complete Features**:
- 💵 **Price per Night**: Price field with currency format
- 📸 **Photo Gallery**: Showcase rooms, facilities, and hotel areas
- ⭐ **Rating & Review**: Reviews from previous guests
- 📍 **Location**: Hotel location map
- 🔍 **Filters**: Filter by location, rating, price, and facilities
- 🏷️ **Attributes**: WiFi, AC, Pool, Parking, Breakfast, etc.

**Use Cases**:
- Hotel chain websites
- Villa booking platform
- Homestay and guesthouse listings
- Local accommodation aggregator

**CPT & Taxonomies**:
- CPT: `listing` (customized for properties)
- Taxonomy: `listing_category` (property type), `listing_location` (area)

---

### 3. 📰 News & Blog
**Perfect for**: News portals, blogs, online media, digital magazines

**Complete Features**:
- 📝 **Full Articles**: Full text editor with media support
- 🖼️ **Featured Image**: Thumbnail image for each article
- 💬 **Comments**: Built-in WordPress comment system
- 👤 **Author**: Attribution for writers
- 🏷️ **Categories**: Content organization with categories
- 📅 **Archive**: Auto archive by date

**Use Cases**:
- Local/national news portals
- Corporate blogs
- Online magazines
- Content marketing platforms

**CPT & Taxonomies**:
- CPT: `article` with minimal fields
- Taxonomy: Built-in categories and tags

---

### 4. 📅 Events & Calendar
**Perfect for**: Event websites, seminars, conferences, workshops

**Complete Features**:
- 📆 **Date & Time**: Date/time picker for event schedule
- 👥 **Participant Capacity**: Track maximum number of participants
- 📍 **Event Location**: Address and location map
- 📞 **Organizer Contact**: Contact info for registration
- 🖼️ **Gallery**: Venue photos and documentation
- 🔄 **Status**: Draft, scheduled, ongoing, completed, cancelled
- 🔍 **Filters**: Filter by date, location, capacity, status

**Use Cases**:
- Event organizer websites
- Seminar and workshop calendar
- Conferences and exhibitions
- Community meetups

**CPT & Taxonomies**:
- CPT: `event` with 7 custom fields
- Taxonomy: `event_category`, `event_tag`

---

### 5. ⏰ Appointments & Booking
**Perfect for**: Appointment booking systems, salons, clinics, consultations

**Complete Features**:
- 📅 **Date & Time**: Datetime picker for appointments
- ⏱️ **Duration**: Appointment duration in minutes
- 📊 **Booking Status**: Pending, confirmed, cancelled, completed, no-show
- 👨‍⚕️ **Provider**: Doctor, stylist, consultant, etc.
- 👤 **Client**: Client booking data
- 📞 **Contact**: Phone and email for confirmation
- 🔍 **Filters**: Filter by date, status, provider, client

**Use Cases**:
- Salon and barbershop booking
- Clinic/doctor appointments
- Online consultations
- Service reservations

**CPT & Taxonomies**:
- CPT: `appointment` with 7 custom fields
- Taxonomy: `appointment_service` (service types)

---

## 💡 How to Use

### Step 1: Access Library Tab
1. Login to WordPress Admin
2. Click **SOFIR** menu in sidebar
3. Select **Library** tab
4. Scroll to **🎁 Ready-to-Use CPT Library** section

### Step 2: Choose Template
View 5 available template cards. Each card displays:
- **Icon & Badge**: Visual identifier and status (Popular/New/Simple/Pro)
- **Template Name**: Type of website that can be created
- **Description**: Brief explanation of main features
- **Features List**: Complete list of included features

### Step 3: Install Template
1. Click **+ Install Template** button on chosen card
2. System will automatically:
   - Register Custom Post Type
   - Setup all custom fields
   - Register taxonomies
   - Activate filters
   - Flush rewrite rules
3. Page will refresh with success message
4. Template is ready to use!

### Step 4: Verify Installation
1. Check WordPress Admin menu - new CPT will appear in sidebar
2. Click CPT menu to start adding content
3. Test on frontend to see results

### Step 5: Refresh Permalink (Important!)
1. Go to **Settings → Permalinks**
2. Click **Save Changes** (without changing anything)
3. This ensures rewrite rules work correctly

---

## 🔄 Multi-Site Support

### Clone Websites Easily
CPT Library allows you to clone website structure to multiple sites:

**Scenario 1: Development → Staging → Production**
```
1. Setup CPT on development site
2. Export to JSON via Library tab
3. Import JSON to staging site
4. Test and validate
5. Import JSON to production site
```

**Scenario 2: Multi-Branch Business**
```
1. Setup template on master site (HQ)
2. Export configuration
3. Import to each branch site
4. Each branch has same structure
```

**Scenario 3: Client Projects**
```
1. Create template for specific niche (e.g., Restaurant Directory)
2. Export as package
3. Install to all similar client projects
4. Customize content per client
```

### Export & Import
Besides ready templates, you can also:

**Export Your Own CPT**:
1. Library tab → Export CPT Package
2. Select CPT to export
3. Preview data before download
4. Download JSON file

**Import CPT from File**:
1. Library tab → Import CPT Package
2. Upload JSON file
3. System auto-registers CPT
4. Refresh permalink

---

## 📦 What Gets Installed Automatically

### For Each Template

**1. Custom Post Type**
- Slug and labels
- Menu icon in admin
- Support features (title, editor, thumbnail, etc.)
- Archive and single page support
- REST API endpoint

**2. Custom Fields**
Special fields according to template needs:
- `location` → Address field with map picker
- `rating` → Rating 1-5 stars
- `hours` → Operating hours per day
- `price` → Price range or price field
- `contact` → Phone, email, website, social
- `gallery` → Multiple image upload
- `status` → Status dropdown options
- `attributes` → Key-value pair attributes
- `event_date` → Date/time picker
- `event_capacity` → Number field
- `appointment_datetime` → Datetime picker
- `appointment_duration` → Duration in minutes
- `appointment_status` → Status dropdown
- `appointment_provider` → Provider selection
- `appointment_client` → Client information

**3. Taxonomies**
Categories and tags for content organization:
- Hierarchical (like categories)
- Flat (like tags)
- Filterable in REST API

**4. Filters**
Active REST API filters for:
- Meta query (exact, like, numeric, range)
- Taxonomy query
- Date range query
- Schedule query (open_now)
- Custom attribute filters

**5. Rewrite Rules**
- SEO-friendly URLs
- Archive pages
- Single post pages
- Taxonomy archives

---

## 🎨 Customization After Install

### 1. Change Labels
```php
// Access via SOFIR → Content → Edit CPT
// Change Singular Name, Plural Name, Menu Icon
```

### 2. Add/Remove Fields
```php
// Access via SOFIR → Content → Edit CPT → Fields
// Choose from 15 available field types
```

### 3. Configure Filters
```php
// Access via SOFIR → Content → Edit CPT → Filters
// Enable/disable filters as needed
```

### 4. Custom Template Files
```php
// Create template files in theme:
// - single-{post_type}.php
// - archive-{post_type}.php
// - taxonomy-{taxonomy}.php
```

---

## 🔧 Advanced: Programmatic Access

### Get Template Definitions
```php
$library_panel = \Sofir\Admin\LibraryPanel::instance();
$templates = $library_panel->get_ready_templates();

foreach ( $templates as $key => $template ) {
    echo $template['name'];
    print_r( $template['cpts'] );
    print_r( $template['taxonomies'] );
}
```

### Install Template Programmatically
```php
// Not recommended - use admin UI
// But can be done via:
$manager = \Sofir\Cpt\Manager::instance();

foreach ( $template['cpts'] as $cpt_slug => $cpt_config ) {
    $manager->save_post_type( $cpt_config );
}

flush_rewrite_rules();
```

### Check if Template Installed
```php
$manager = \Sofir\Cpt\Manager::instance();
$existing_cpts = array_keys( $manager->get_post_types() );

$is_installed = in_array( 'listing', $existing_cpts, true );
```

---

## 🎯 Best Practices

### 1. Planning
- **Identify needs**: What type of website will be created?
- **Choose template**: Which template is most suitable?
- **Evaluate fields**: Need additional custom fields?

### 2. Installation
- **Install in development**: Don't go straight to production
- **Test thoroughly**: Check all features work
- **Backup first**: Always backup before install

### 3. Customization
- **Minimal changes**: Don't change too much from template
- **Document changes**: Record customizations made
- **Test after changes**: Validate every change

### 4. Deployment
- **Export to JSON**: Save configuration for backup
- **Import to production**: Use tested JSON file
- **Refresh permalink**: Don't forget to flush rewrite rules

### 5. Maintenance
- **Regular backup**: Export CPT configuration regularly
- **Version control**: Store JSON files in git
- **Documentation**: Update docs if structure changes

---

## 🐛 Troubleshooting

### Template Doesn't Appear in Menu
**Problem**: After install, CPT doesn't appear in admin menu

**Solution**:
1. Hard refresh browser (Ctrl+F5)
2. Logout and login again
3. Check user capabilities (must have `manage_options`)

### 404 Error on Single/Archive Page
**Problem**: CPT pages show 404 Not Found

**Solution**:
1. Go to **Settings → Permalinks**
2. Click **Save Changes**
3. Refresh rewrite rules
4. Clear browser cache

### Fields Don't Appear in Edit Post
**Problem**: Custom fields not visible in post editor

**Solution**:
1. Check Screen Options (top right corner)
2. Ensure custom fields are checked
3. Reload page
4. Clear WordPress object cache

### Import Failed
**Problem**: Error when importing JSON file

**Solution**:
1. Validate JSON syntax (use jsonlint.com)
2. Check file size limit in php.ini
3. Check file permissions
4. Check WordPress error log

### Template Already Installed But Want to Re-install
**Problem**: Want to reinstall template

**Solution**:
1. Go to SOFIR → Content
2. Delete existing CPT
3. Return to Library tab
4. Reinstall template

---

## 📊 Template Comparison

| Feature | Business | Hotel | News | Events | Appointments |
|---------|----------|-------|------|--------|--------------|
| Location & Map | ✅ | ✅ | ❌ | ✅ | ❌ |
| Rating | ✅ | ✅ | ❌ | ❌ | ❌ |
| Operating Hours | ✅ | ❌ | ❌ | ❌ | ❌ |
| Pricing | ✅ | ✅ | ❌ | ❌ | ❌ |
| Photo Gallery | ✅ | ✅ | ✅ | ✅ | ❌ |
| Contact | ✅ | ✅ | ❌ | ✅ | ✅ |
| Date/Time | ❌ | ❌ | ❌ | ✅ | ✅ |
| Capacity | ❌ | ❌ | ❌ | ✅ | ❌ |
| Status | ✅ | ❌ | ❌ | ✅ | ✅ |
| Comments | ✅ | ✅ | ✅ | ✅ | ❌ |
| Author | ❌ | ❌ | ✅ | ✅ | ✅ |
| Categories | ✅ | ✅ | ✅ | ✅ | ✅ |

---

## 🚀 Future Enhancements

Features to be added in upcoming versions:

### 1. More Templates
- 🛍️ E-commerce Product Catalog
- 🎓 Course & Learning Management
- 🏥 Medical & Healthcare
- 🍽️ Restaurant & Menu
- 🏋️ Fitness & Gym
- 🎬 Video Gallery
- 📚 Library & Books
- 🚗 Car Dealership

### 2. Template Variations
- Variations for each template (basic, pro, premium)
- Customizable field combinations
- Pre-filled demo content

### 3. Cloud Library
- Download templates from cloud marketplace
- Share templates with community
- Rate and review templates

### 4. Template Builder
- Visual builder to create custom templates
- Drag-and-drop field configuration
- Export to marketplace

### 5. Import Options
- Choose components to import
- Merge strategies for existing CPTs
- Preview before import
- Include/exclude demo content

---

## 📞 Support & Feedback

### Need Help?
- 📧 Email: support@sofir.io
- 💬 Forum: https://sofir.io/community
- 📚 Docs: https://docs.sofir.io

### Feature Request
Have ideas for new templates? Submit via:
- GitHub Issues: https://github.com/sofir/plugin
- Community Forum: https://sofir.io/feature-requests

### Bug Report
Found a bug? Report with details:
1. Template used
2. WordPress & PHP version
3. Error message
4. Steps to reproduce

---

## 📝 Changelog

### Version 1.0.0 (2024)
- ✅ 5 ready-to-use templates
- ✅ One-click installation
- ✅ Export/Import functionality
- ✅ Full REST API support
- ✅ Multi-site compatibility

---

**🎉 Congratulations! You can now create various professional websites with SOFIR CPT Library!**
