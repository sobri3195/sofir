# AI24 Assistant Integrator - Voxel Theme Integration Guide

## Overview

This document details how to integrate AI24 Assistant Integrator with the Voxel theme within SOFIR. This creates powerful AI-assisted content creation for Voxel-based directories, marketplaces, and community platforms.

## Voxel Theme Integration Points

### 1. Voxel CPT Support

Voxel creates custom post types for various content types:

```php
// Common Voxel CPTs
- property          // Real estate listings
- agent             // Real estate agents
- listing           // General marketplace listings
- vendor            // Vendor stores
- service           // Service providers
- professional      // Professional profiles
- company           // Business profiles
```

### 2. Voxel Custom Fields

Voxel uses a custom field system:

```php
// Field Types
Text, Email, Phone, Website, Image, File
Location, Number, Checkbox, Select, Radio
Repeater, Relationship, Color, Date, Time

// Common Use Cases
- Property: title, description, price, features, location
- Agent: name, bio, license_number, photo, services
- Vendor: store_name, description, logo, contact_info
```

### 3. Voxel Filters & Search

Voxel includes advanced filtering:

```php
- Taxonomy filters
- Custom field filters
- Location-based filters
- Price range filters
- Multi-select filters

// Filter Integration Points
- Filter results display
- Search bar enhancement
- Filter count display
- Reset filters action
```

## AI24-Powered Features for Voxel

### Feature 1: Auto-Generated Descriptions

**Use Case**: Generate professional descriptions for property listings

```
Input:
- Property type: Apartment
- Bedrooms: 3
- Bathrooms: 2
- Price: $500,000
- Location: Downtown

AI24 Output:
"Beautiful 3-bedroom, 2-bathroom apartment in the heart of downtown.
This stunning property features modern finishes, high ceilings, and 
an open floor plan perfect for contemporary living. Located in a 
vibrant neighborhood with excellent amenities..."

Implementation:
1. Detect when new property is created
2. Extract property data from Voxel fields
3. Send to AI24 for description generation
4. Auto-fill property description field
5. Allow user to edit before saving
```

### Feature 2: Enhanced Profile Bios

**Use Case**: Create professional bios for agents, vendors, professionals

```
Input:
- Agent name: John Smith
- Years of experience: 15
- Specialties: Residential, Commercial
- License: RE123456

AI24 Output:
"John Smith is a highly experienced real estate professional with
15 years of expertise in residential and commercial properties.
Licensed since [year], John has successfully completed over [count]
transactions and is committed to providing exceptional service..."

Implementation:
1. Metabox for AI bio generation
2. Extract agent/vendor info
3. Generate with AI24
4. Preview before save
5. Allow customization
```

### Feature 3: SEO Optimization Suggestions

**Use Case**: Improve SEO for Voxel listings

```
Analysis Includes:
- Keyword suggestions
- Meta description
- URL optimization
- Content structure
- Internal linking
- Schema markup

Implementation:
1. Analyze listing content
2. Check current SEO score
3. Provide suggestions
4. Auto-implement basics
5. Show improvement report
```

### Feature 4: Bulk Content Generation

**Use Case**: Create content for multiple Voxel posts

```
Process:
1. Select multiple posts/listings
2. Configure generation parameters
3. Run bulk generation
4. Review generated content
5. Approve and publish

Implementation:
- WP-CLI command: wp sofir ai24 bulk-generate
- Admin UI for bulk operations
- Progress tracking
- Rollback capability
```

### Feature 5: Content Translation

**Use Case**: Translate Voxel content to multiple languages

```
Supported Languages:
- English
- Spanish
- French
- German
- Chinese
- Japanese
- Arabic
- Indonesian
- And more...

Implementation:
1. Select language
2. Translate content
3. Update fields
4. Maintain formatting
5. Preserve links
```

## Implementation Architecture

### Voxel Integration Flow

```
┌─────────────────────────────────────┐
│  Voxel Post Save                    │
└──────────────┬──────────────────────┘
               │
               ▼
       Is AI24 enabled?
       ├─ NO  └─ Exit
       └─ YES
          │
          ▼
       Is Voxel CPT?
       ├─ NO  └─ Exit
       └─ YES
          │
          ▼
    Extract Voxel Fields
    ├─ Title
    ├─ Description
    ├─ Custom fields
    └─ Metadata
          │
          ▼
    Generate using AI24
    ├─ Enhanced description
    ├─ SEO suggestions
    ├─ Keywords
    └─ Schema markup
          │
          ▼
    Map to Voxel Fields
    ├─ Update description
    ├─ Update keywords
    ├─ Update meta
    └─ Store suggestions
          │
          ▼
    Save to Post Meta
    └─ sofir_ai24_suggestions
          │
          ▼
    Trigger Hooks
    ├─ sofir/ai24/voxel_updated
    └─ sofir/voxel/ai24_suggestions_ready
          │
          ▼
    Update Admin Notice
    └─ Show AI24 suggestions available
```

### Metabox Implementation

```php
// Voxel Post Edit Screen
┌──────────────────────────────────────┐
│ AI24 Suggestions Metabox             │
├──────────────────────────────────────┤
│                                      │
│ Status: Generated ✓                  │
│ Suggestions Available: 3              │
│                                      │
│ [+ Add Description]                  │
│ [+ Add Keywords]                     │
│ [+ Add Meta Description]             │
│                                      │
│ Suggested Description:               │
│ ┌──────────────────────────────────┐ │
│ │ Beautiful 3-bedroom apartment... │ │
│ │                                  │ │
│ │ [Use] [Edit] [Discard]          │ │
│ └──────────────────────────────────┘ │
│                                      │
│ Suggested Keywords:                  │
│ ┌──────────────────────────────────┐ │
│ │ apartment, downtown, modern      │ │
│ │ 3-bedroom, luxury                │ │
│ │                                  │
│ │ [Use] [Discard]                 │ │
│ └──────────────────────────────────┘ │
│                                      │
│ [Regenerate] [Settings]              │
│                                      │
└──────────────────────────────────────┘
```

## Voxel Field Mapping

### Property CPT Mapping

```php
'property' => [
    'title' => [
        'ai24_field' => 'title_suggestion',
        'field_key' => 'post_title',
        'type' => 'text'
    ],
    'description' => [
        'ai24_field' => 'description',
        'field_key' => 'post_content',
        'type' => 'textarea'
    ],
    'property_type' => [
        'ai24_field' => 'property_type',
        'field_key' => 'voxel_property_type',  // Voxel custom field
        'type' => 'select'
    ],
    'bedrooms' => [
        'ai24_field' => 'bedrooms',
        'field_key' => 'voxel_bedrooms',
        'type' => 'number'
    ],
    'bathrooms' => [
        'ai24_field' => 'bathrooms',
        'field_key' => 'voxel_bathrooms',
        'type' => 'number'
    ],
    'price' => [
        'ai24_field' => 'price',
        'field_key' => 'voxel_price',
        'type' => 'number'
    ],
    'location' => [
        'ai24_field' => 'location',
        'field_key' => 'voxel_location',
        'type' => 'location'
    ],
    'features' => [
        'ai24_field' => 'features',
        'field_key' => 'voxel_features',
        'type' => 'repeater'
    ],
    'seo_meta_description' => [
        'ai24_field' => 'meta_description',
        'field_key' => '_yoast_wpseo_metadesc',
        'type' => 'text'
    ],
    'seo_keywords' => [
        'ai24_field' => 'keywords',
        'field_key' => 'sofir_ai24_keywords',
        'type' => 'hidden'
    ]
]
```

### Agent CPT Mapping

```php
'agent' => [
    'name' => [
        'ai24_field' => 'agent_name',
        'field_key' => 'voxel_agent_name',
        'type' => 'text'
    ],
    'bio' => [
        'ai24_field' => 'professional_bio',
        'field_key' => 'post_content',
        'type' => 'textarea'
    ],
    'specialties' => [
        'ai24_field' => 'specialties',
        'field_key' => 'voxel_specialties',
        'type' => 'multiselect'
    ],
    'experience_years' => [
        'ai24_field' => 'years_experience',
        'field_key' => 'voxel_experience',
        'type' => 'number'
    ],
    'license' => [
        'ai24_field' => 'license_number',
        'field_key' => 'voxel_license',
        'type' => 'text'
    ]
]
```

## AJAX Integration Points

### Trigger AI24 Generation

```javascript
// JavaScript - AJAX request
jQuery.post(
    '/wp-json/sofir/v1/ai24/generate',
    {
        post_id: postId,
        context: 'voxel_property',
        cpt: 'property',
        _wpnonce: nonce
    },
    function(response) {
        if (response.success) {
            // Update metabox with suggestions
            updateMetabox(response.data);
        } else {
            // Show error message
            showError(response.data.message);
        }
    }
);
```

### Apply AI24 Suggestion

```javascript
// Apply suggested description
jQuery('.ai24-apply-description').on('click', function() {
    var description = jQuery('.ai24-suggestion-description').val();
    
    // Update Voxel field
    jQuery('[data-voxel-field="post_content"]').val(description);
    
    // Save field
    jQuery('#publish').trigger('click');
});
```

### Metabox JavaScript

```php
// assets/js/voxel-ai24-metabox.js
(function($) {
    'use strict';
    
    $(document).ready(function() {
        initAI24Metabox();
        bindEvents();
    });
    
    function initAI24Metabox() {
        // Load initial suggestions
        // Display loading state
        // Fetch AI24 suggestions
        // Render suggestions in metabox
    }
    
    function bindEvents() {
        // Regenerate button
        $('.ai24-regenerate').on('click', regenerateSuggestions);
        
        // Apply buttons
        $('.ai24-apply').on('click', applySuggestion);
        
        // Discard buttons
        $('.ai24-discard').on('click', discardSuggestion);
        
        // Settings button
        $('.ai24-settings').on('click', openSettings);
    }
    
    function regenerateSuggestions() {
        // Trigger AI24 generation again
    }
    
    function applySuggestion(e) {
        var field = $(e.target).data('field');
        var value = $(e.target).data('value');
        
        // Apply to Voxel field
        updateVoxelField(field, value);
        
        // Track usage
        trackUsage('suggestion_applied');
    }
    
    function discardSuggestion(e) {
        var field = $(e.target).data('field');
        // Remove suggestion from display
    }
    
    function updateVoxelField(field, value) {
        // Update appropriate Voxel field
    }
    
    function openSettings() {
        // Show settings modal
    }
})( jQuery );
```

## Voxel Field Types Support

### Text Fields
```php
Property Title, Agent Name, Business Name
→ AI24: Title suggestions, name optimization
```

### Textarea Fields
```php
Property Description, Agent Bio, Service Description
→ AI24: Enhanced descriptions, professional writing
```

### Select Fields
```php
Property Type, Service Category, Business Type
→ AI24: Auto-fill based on content
```

### Number Fields
```php
Bedrooms, Bathrooms, Price, Years of Experience
→ AI24: Auto-detect from description/content
```

### Location Fields
```php
Property Location, Office Address
→ AI24: Location suggestions, neighborhood insights
```

### Repeater Fields
```php
Property Features, Service Packages, Portfolio Items
→ AI24: Generate feature lists, complete repeater items
```

### Checkbox/Multi-Select
```php
Amenities, Specialties, Skills
→ AI24: Suggest relevant options
```

## Hooks & Filters

### Filters

```php
// Modify field mapping
apply_filters(
    'sofir/ai24/voxel/field_mapping',
    $default_mapping,
    $post_type
);

// Modify AI24 request
apply_filters(
    'sofir/ai24/voxel/generation_request',
    $request_data,
    $post_id,
    $post
);

// Modify AI24 response
apply_filters(
    'sofir/ai24/voxel/generation_response',
    $response_data,
    $post_id,
    $post
);

// Modify suggestions before display
apply_filters(
    'sofir/ai24/voxel/suggestions',
    $suggestions,
    $post_id
);

// Should auto-apply suggestion?
apply_filters(
    'sofir/ai24/voxel/auto_apply',
    false,  // default
    $field,
    $post_id,
    $suggestion
);
```

### Actions

```php
// After Voxel post updated by AI24
do_action(
    'sofir/ai24/voxel_updated',
    $post_id,
    $suggestions,
    $applied_fields
);

// After suggestions generated
do_action(
    'sofir/ai24/voxel/suggestions_generated',
    $post_id,
    $suggestions
);

// When user applies suggestion
do_action(
    'sofir/ai24/voxel/suggestion_applied',
    $post_id,
    $field,
    $value
);

// When user discards suggestion
do_action(
    'sofir/ai24/voxel/suggestion_discarded',
    $post_id,
    $field
);
```

## Settings for Voxel Integration

### Admin Settings UI

```
Voxel Integration Tab
├─ Enable AI24 for Voxel: [✓]
├─ Auto-Generate on Post Save: [✓]
├─ Auto-Apply Suggestions: [ ]
├─ Show Metabox: [✓]
├─ Suggest Keywords: [✓]
├─ Suggest Meta Description: [✓]
│
├─ Per-CPT Settings:
│  ├─ Property
│  │  ├─ Enable: [✓]
│  │  ├─ Generate Description: [✓]
│  │  ├─ Generate Keywords: [✓]
│  │  └─ Generate Features List: [✓]
│  │
│  ├─ Agent
│  │  ├─ Enable: [✓]
│  │  ├─ Generate Bio: [✓]
│  │  └─ Suggest Specialties: [✓]
│  │
│  └─ [Add More CPTs]
│
└─ [Save Settings]
```

### Options Structure

```php
sofir_ai24_voxel_enabled         // bool
sofir_ai24_voxel_auto_generate   // bool
sofir_ai24_voxel_auto_apply      // bool
sofir_ai24_voxel_show_metabox    // bool
sofir_ai24_voxel_suggest_keywords // bool
sofir_ai24_voxel_suggest_meta    // bool
sofir_ai24_voxel_cpt_settings    // array (serialized)
```

## Performance Considerations

### Optimization Strategies

1. **Lazy Loading**
   ```php
   // Only load Voxel integration when needed
   if ('property' === get_post_type()) {
       load_voxel_integration();
   }
   ```

2. **Batch Processing**
   ```php
   // Process multiple posts with WP-Cron
   wp_schedule_event(time(), 'hourly', 'sofir_ai24_batch_process');
   ```

3. **Caching**
   ```php
   // Cache suggestions per post
   $suggestions = get_transient('sofir_ai24_suggestions_' . $post_id);
   ```

4. **Asynchronous Processing**
   ```php
   // Use AJAX to avoid blocking post save
   wp_remote_post(admin_url('admin-ajax.php'), [
       'async' => true,
       'action' => 'sofir_ai24_generate'
   ]);
   ```

## Real-World Use Cases

### Use Case 1: Real Estate Marketplace

```
Scenario:
- Property agent creates new listing
- AI24 auto-generates professional description
- Generates 5-10 feature suggestions
- Suggests SEO meta description
- Recommends property keywords

Result:
- Professional, consistent listings
- Better SEO ranking
- Faster listing creation
- Improved buyer engagement
```

### Use Case 2: Service Directory

```
Scenario:
- Service provider creates profile
- AI24 generates professional bio
- Suggests relevant services/specialties
- Creates service packages
- Generates service descriptions

Result:
- Professional service profiles
- Better discoverability
- Consistent quality
- Time-saving content creation
```

### Use Case 3: Multi-Vendor Marketplace

```
Scenario:
- Vendor creates product listing
- AI24 generates product description
- Suggests features and benefits
- Creates product comparison content
- Generates category suggestions

Result:
- Attractive product listings
- Better conversion rates
- Faster product uploads
- Improved SEO
```

## Testing Checklist

- [ ] AI24 Metabox displays on Voxel posts
- [ ] Suggestions generated correctly
- [ ] Field mapping works for all CPTs
- [ ] Apply/Discard buttons functional
- [ ] Suggestions saved to post meta
- [ ] Admin notices display correctly
- [ ] Performance acceptable (< 1s metabox load)
- [ ] No conflicts with Voxel functionality
- [ ] Mobile admin interface works
- [ ] Error messages user-friendly
- [ ] Hooks fire correctly
- [ ] Filters work as expected

## Troubleshooting

### Issue: Metabox not showing
**Solution**: Check if Voxel integration enabled in settings

### Issue: Suggestions not generating
**Solution**: Verify AI24 API key and connection

### Issue: Slow post save
**Solution**: Disable auto-generate, use manual button instead

### Issue: Fields not updating
**Solution**: Check field mapping configuration

### Issue: Cache not clearing
**Solution**: Use "Clear Cache" button in admin

## Future Enhancements

- [ ] Multi-language support
- [ ] Custom field types
- [ ] Advanced filtering integration
- [ ] Bulk content generation
- [ ] Content translation
- [ ] Image alt text generation
- [ ] Thumbnail generation
- [ ] Category suggestions
- [ ] Competitor analysis
- [ ] Price optimization

---

**Document Version**: 1.0
**Last Updated**: 2025-01-XX
**Status**: DRAFT - Ready for Review
**Related Documents**: AI24_INTEGRATION_PLAN.md, AI24_TECHNICAL_SPECIFICATION.md
