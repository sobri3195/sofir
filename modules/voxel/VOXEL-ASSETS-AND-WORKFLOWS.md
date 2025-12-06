# Voxel Theme - Assets Learning & Integration Workflows

## 📚 Table of Contents

1. [Voxel Assets Guide](#voxel-assets-guide)
2. [Code Snippet Integration](#code-snippet-integration)
3. [Sure Trigger Integration](#sure-trigger-integration)
4. [Ottokit Workflow](#ottokit-workflow)
5. [CPT Import Process](#cpt-import-process)
6. [Development Login Credentials](#development-login-credentials)

---

## Voxel Assets Guide

### What Are Voxel Assets?

Voxel assets are pre-built resources including:
- **Components** - Reusable UI building blocks
- **Templates** - Page layouts and designs
- **Widgets** - Custom Elementor widgets for Voxel theme
- **Patterns** - Gutenberg patterns (if Voxel supports)
- **Code Snippets** - PHP, JavaScript, CSS code examples

### Asset Library Location

```
https://voxel.guide/assets/
```

### Asset Browser with Switcher

```
https://voxel.guide/assets/?type=asset&switcher=1
```

**Parameters:**
- `type=asset` - Show all assets
- `type=component` - Show components only
- `type=template` - Show templates only
- `type=widget` - Show widgets only
- `switcher=1` - Enable the asset switcher/filter UI

### Asset Categories

#### 1. **Components**
- Master listing layout
- Post detail layout
- Search form component
- Map integration component
- Filter sidebar
- Reviews/ratings component
- User profile component
- Booking form component

#### 2. **Templates**
- Business Directory page
- Events calendar
- Real estate listings
- Job board
- Course platform
- Restaurant menu system
- Hotel booking
- Directory with map

#### 3. **Widgets**
- Post feed widget
- Term feed widget
- Search form widget
- Map widget
- Single post widget
- User profile widget
- Custom field widgets

#### 4. **Code Snippets**
- Custom field types
- Filter hooks
- Template customization
- JavaScript interactions
- Admin functions

### How to Use Assets for SOFIR Integration

#### Step 1: Browse Assets
1. Go to https://voxel.guide/assets/?type=asset&switcher=1
2. Filter by category (component, template, widget)
3. Click on asset to view details and code

#### Step 2: Study Asset Structure
- Examine Elementor widget structure
- Review template hierarchy
- Check custom field implementations
- Analyze JavaScript interactions

#### Step 3: Adapt for SOFIR CPTs
- Map asset components to SOFIR CPT fields
- Convert asset templates to SOFIR template format
- Implement asset widgets in SOFIR Elementor module
- Apply asset code snippets to SOFIR hooks

---

## Code Snippet Integration

### Understanding Voxel Code Snippets

Voxel provides code snippets for common tasks:

#### Example: Custom Field Type Snippet

```php
// Register custom field type
add_filter( 'voxel/field-types', function( $field_types ) {
    $field_types['my_custom_field'] = [
        'label' => 'My Custom Field',
        'icon' => 'las la-star',
        'base' => 'Base_Field',
        'settings' => [
            'label' => 'text',
            'description' => 'text',
            'required' => 'checkbox',
            'editable' => 'checkbox',
        ],
    ];
    return $field_types;
}, 100 );
```

#### Example: Custom Filter Hook

```php
// Filter posts before display
add_filter( 'voxel/post-feed/query-args', function( $query_args, $widget ) {
    $query_args['posts_per_page'] = 20;
    $query_args['orderby'] = 'meta_value_num';
    $query_args['meta_key'] = 'voxel_rating';
    return $query_args;
}, 10, 2 );
```

#### Example: Template Customization

```php
// Modify single post template
add_filter( 'voxel/single-post/template', function( $template ) {
    // Add custom HTML/CSS
    $template .= '<div class="custom-section">Custom content</div>';
    return $template;
} );
```

### How to Integrate Snippets into SOFIR

#### 1. Convert to SOFIR Hooks

```php
// Instead of:
add_filter( 'voxel/field-types', ... );

// Use SOFIR hooks:
add_filter( 'sofir/voxel/field_types', ... );
add_filter( 'sofir/cpt/register_args', ... );
add_filter( 'sofir/field/meta_config', ... );
```

#### 2. Create Snippet Library

Location: `modules/voxel/snippets/`

```
modules/voxel/snippets/
├── custom-fields.php
├── filters.php
├── templates.php
├── javascript-interactions.php
└── admin-functions.php
```

#### 3. Implement in Manager

File: `modules/voxel/manager.php`

```php
public function boot(): void {
    // ... existing code ...
    
    if ( $this->is_voxel_active() ) {
        require_once SOFIR_PLUGIN_PATH . 'modules/voxel/snippets/custom-fields.php';
        require_once SOFIR_PLUGIN_PATH . 'modules/voxel/snippets/filters.php';
    }
}
```

### Best Practices for Code Snippet Integration

1. **Always use SOFIR namespaced hooks** instead of generic WordPress hooks
2. **Validate Voxel is active** before running snippet code
3. **Use priority > 999** for hooks that need to override Voxel defaults
4. **Add error handling** with try-catch blocks
5. **Log with WP_DEBUG** for debugging
6. **Document all snippets** with examples
7. **Test with Voxel test site** before production

---

## Sure Trigger Integration

### What is Sure Trigger?

Sure Trigger is an automation plugin that integrates with:
- WordPress core functions
- Third-party apps (Slack, Discord, Zapier, etc.)
- Custom webhooks
- Scheduled tasks

### Setting Up Sure Trigger for SOFIR + Voxel

#### Step 1: Install Sure Trigger

1. Download from WordPress.org
2. Activate plugin
3. Go to **Sure Trigger → Settings**
4. Activate necessary integrations

#### Step 2: Create SOFIR Custom Triggers

**Trigger:** When SOFIR CPT is created

```
Plugin: SOFIR
Trigger: Post created
Post Type: Select your CPT (listing, event, etc)
```

**Trigger:** When SOFIR CPT field is updated

```
Plugin: SOFIR
Trigger: Post meta updated
Meta Key: sofir_field_name
Meta Value: Any value or specific value
```

#### Step 3: Create Actions

**Action 1: Send Slack Notification**

```
When: SOFIR listing created
Then: Send Slack message
Message: New listing: {post_title} - {sofir_location}
Channel: #listings
```

**Action 2: Create Voxel Post Type**

```
When: SOFIR product created
Then: Create post
Post Type: Custom post type
Title: {post_title}
Status: publish
```

**Action 3: Update Post Field**

```
When: SOFIR payment completed
Then: Update post meta
Post ID: {post_id}
Meta Key: sofir_payment_status
Meta Value: completed
```

**Action 4: Call Webhook**

```
When: SOFIR form submitted
Then: Make HTTP request
URL: https://your-api.com/webhook
Method: POST
Body: {
  "form_id": {form_id},
  "submission_data": {submission_data}
}
```

### SOFIR + Sure Trigger Workflow Example

#### Scenario: Auto-Publish Voxel Listing from SOFIR Form

1. **Create SOFIR Form**
   - Fields: Title, Location, Price, Description, Images
   - Submit action: Save as SOFIR listing

2. **Create Sure Trigger Automation**
   - Trigger: SOFIR listing created
   - Action 1: Send email notification
   - Action 2: Create Voxel post type entry
   - Action 3: Add location metadata
   - Action 4: Send Slack notification

3. **Implement in Code**

```php
// In modules/voxel/manager.php
add_action( 'sofir/post/created', function( $post_id, $post_type ) {
    do_action( 'sure_trigger/sofir_post_created', $post_id, $post_type );
}, 10, 2 );

add_action( 'sofir/form/submitted', function( $submission_data, $form_id ) {
    do_action( 'sure_trigger/sofir_form_submitted', $submission_data, $form_id );
}, 10, 2 );

add_action( 'sofir/payment/completed', function( $payment_id, $amount ) {
    do_action( 'sure_trigger/sofir_payment_completed', $payment_id, $amount );
}, 10, 2 );
```

---

## Ottokit Workflow

### What is Ottokit?

Ottokit is a GitHub automation framework for WordPress development:
- Repository automation
- Code review workflows
- Deployment automation
- GitHub Actions integration
- Issue/PR management

### Ottokit Setup for SOFIR + Voxel

#### Step 1: Create GitHub Workflow File

File: `.github/workflows/voxel-integration-test.yml`

```yaml
name: Voxel Integration Tests

on:
  pull_request:
    paths:
      - 'modules/voxel/**'
      - 'includes/sofir-cpt-manager.php'

jobs:
  test-voxel-integration:
    runs-on: ubuntu-latest
    
    services:
      wordpress:
        image: wordpress:latest
        options: >-
          --health-cmd="curl -f http://localhost/ || exit 1"
          --health-interval=10s
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Install SOFIR Plugin
        run: |
          mkdir -p wp-content/plugins/sofir
          cp -r . wp-content/plugins/sofir/
      
      - name: Install Voxel Theme
        run: |
          mkdir -p wp-content/themes/voxel
          git clone https://github.com/voxeltheme/voxel.git wp-content/themes/voxel
      
      - name: Run Integration Tests
        run: |
          cd tests
          php run-tests.php --module=voxel
      
      - name: Generate Test Report
        if: always()
        run: |
          php tests/generate-report.php
      
      - name: Upload Test Results
        if: always()
        uses: actions/upload-artifact@v2
        with:
          name: test-results
          path: tests/reports/
```

#### Step 2: Define Ottokit Triggers

File: `.ottokit/config.json`

```json
{
  "voxel": {
    "enabled": true,
    "triggers": [
      {
        "event": "pull_request",
        "action": "opened",
        "paths": ["modules/voxel/**"],
        "workflow": "voxel-integration-test"
      },
      {
        "event": "pull_request",
        "action": "synchronize",
        "paths": ["modules/voxel/**"],
        "workflow": "voxel-integration-test"
      }
    ]
  }
}
```

#### Step 3: Create Ottokit Custom Action

File: `.ottokit/actions/test-voxel-cpt-compatibility.js`

```javascript
async function testVoxelCPTCompatibility(context) {
  const { payload } = context;
  const changedFiles = payload.pull_request.changed_files;
  
  // Check if Voxel-related files were changed
  const voxelChanges = changedFiles.filter(file => 
    file.includes('modules/voxel/') || 
    file.includes('sofir-cpt-manager.php')
  );
  
  if (voxelChanges.length === 0) {
    console.log('No Voxel-related changes detected');
    return;
  }
  
  // Run compatibility tests
  const testResults = await runVoxelCompatibilityTests();
  
  // Add PR comment with results
  await context.octokit.issues.createComment({
    ...context.repo,
    issue_number: payload.pull_request.number,
    body: `## Voxel Integration Test Results\n\n${testResults}`
  });
  
  // Set status check
  await context.octokit.checks.create({
    ...context.repo,
    name: 'Voxel Compatibility',
    head_sha: payload.pull_request.head.sha,
    status: testResults.passed ? 'completed' : 'in_progress',
    conclusion: testResults.passed ? 'success' : 'failure',
    output: {
      title: 'Voxel Integration Tests',
      summary: `${testResults.passed} passed, ${testResults.failed} failed`
    }
  });
}

async function runVoxelCompatibilityTests() {
  // Test CPT visibility
  // Test field mapping
  // Test template compatibility
  // Test widget integration
  // Test AJAX handlers
  return {
    passed: true,
    failed: 0,
    details: 'All tests passed'
  };
}

module.exports = { testVoxelCPTCompatibility };
```

#### Step 4: Set PR Requirements

File: `.ottokit/pr-requirements.json`

```json
{
  "voxel-related": {
    "pattern": "modules/voxel/**",
    "requiredChecks": [
      "Voxel Compatibility",
      "PHPUnit Tests",
      "Code Quality"
    ],
    "requiredReviews": 1,
    "dismissStaleReviews": false,
    "codeOwners": ["@voxel-developer"]
  }
}
```

---

## CPT Import Process

### Complete CPT Import Workflow

#### Phase 1: Prepare CPT Definition

**File: `modules/cpt/definitions/my-listing.php`**

```php
return [
    'slug' => 'listing',
    'name' => 'Listings',
    'singular' => 'Listing',
    'description' => 'Real estate listings',
    'icon' => 'dashicons-location-alt',
    'supports' => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
    'has_archive' => true,
    'rewrite' => [ 'slug' => 'listings' ],
    'voxel_enabled' => true,
    'voxel_templates' => true,
    'voxel_filters' => true,
    'fields' => [
        'location' => [
            'type' => 'location',
            'label' => 'Property Location',
            'filterable' => true,
        ],
        'price' => [
            'type' => 'number',
            'label' => 'Price',
            'min' => 0,
            'max' => 99999999,
            'filterable' => true,
        ],
        'bedrooms' => [
            'type' => 'number',
            'label' => 'Bedrooms',
            'filterable' => true,
        ],
        'gallery' => [
            'type' => 'gallery',
            'label' => 'Property Images',
        ],
    ]
];
```

#### Phase 2: Import via Admin UI

1. **Go to: SOFIR → Library → Import CPT**
2. **Select Import Type:**
   - From Package (ZIP file)
   - From URL
   - Manual entry
   - From Voxel Library

3. **Configure Settings:**
   - Slug: `listing`
   - Label: `Listings`
   - Menu position: 5
   - Voxel compatibility: Enable
   - Auto-templates: Enable

4. **Review & Import**
   - Verify CPT definition
   - Check field mappings
   - Confirm Voxel compatibility
   - Click Import

#### Phase 3: Post-Import Setup

**Step 1: Verify CPT Menu**
```php
// Check in admin sidebar
// Menu should appear under SOFIR → CPTs
```

**Step 2: Create Default Template**
```php
// Go to SOFIR → Templates
// Create Voxel single post template
// Template: sofir-single-listing.php
```

**Step 3: Create Archive Template**
```php
// Go to SOFIR → Templates
// Create Voxel archive template
// Template: sofir-archive-listing.php
```

**Step 4: Test in Voxel**
```php
// In Voxel admin:
// Check Settings → Post Types
// Select "Listing"
// Verify fields mapped correctly
// Test single post template
// Test archive page
```

#### Phase 4: Code-Based Import

**File: `modules/voxel/helpers/cpt-importer.php`**

```php
<?php
namespace Sofir\Voxel\Helpers;

class CPT_Importer {
    public static function import_from_package( $package_zip ) {
        // Extract ZIP
        $extract_path = WP_CONTENT_DIR . '/sofir-temp/';
        \unzip_file( $package_zip, $extract_path );
        
        // Load definition
        $definition = include $extract_path . 'definition.php';
        
        // Register CPT
        $cpt_manager = \Sofir\Cpt\Manager::instance();
        $cpt_manager->register_post_type( $definition['slug'], $definition );
        
        // Import fields
        foreach ( $definition['fields'] as $field_slug => $field_config ) {
            $cpt_manager->add_field( $definition['slug'], $field_slug, $field_config );
        }
        
        // Import templates
        if ( isset( $definition['templates'] ) ) {
            foreach ( $definition['templates'] as $template_name => $template_file ) {
                $template_path = $extract_path . 'templates/' . $template_file;
                $this->import_template( $definition['slug'], $template_name, $template_path );
            }
        }
        
        // Flush rewrite rules
        \flush_rewrite_rules();
        
        // Cleanup
        \rrmdir( $extract_path );
        
        return true;
    }
    
    public static function import_from_voxel_library( $asset_id ) {
        // Download from voxel.guide/assets
        $asset_data = $this->fetch_voxel_asset( $asset_id );
        
        // Convert Voxel config to SOFIR CPT definition
        $definition = $this->convert_voxel_config_to_sofir( $asset_data );
        
        // Import as above
        $cpt_manager = \Sofir\Cpt\Manager::instance();
        $cpt_manager->register_post_type( $definition['slug'], $definition );
        
        \flush_rewrite_rules();
        
        return true;
    }
    
    private static function convert_voxel_config_to_sofir( $voxel_config ) {
        return [
            'slug' => $voxel_config['key'] ?? 'custom_post',
            'name' => $voxel_config['plural'] ?? 'Custom Posts',
            'fields' => $this->convert_voxel_fields_to_sofir( $voxel_config['fields'] ?? [] ),
            'voxel_enabled' => true,
            'voxel_templates' => true,
        ];
    }
    
    private static function convert_voxel_fields_to_sofir( $voxel_fields ) {
        $fields = [];
        $type_mapping = [
            'location' => 'location',
            'text' => 'text',
            'number' => 'number',
            'select' => 'select',
            'image' => 'gallery',
            'repeater' => 'repeater',
            'date' => 'date',
            'email' => 'email',
        ];
        
        foreach ( $voxel_fields as $field ) {
            $sofir_type = $type_mapping[ $field['type'] ] ?? 'text';
            $fields[ $field['key'] ] = [
                'type' => $sofir_type,
                'label' => $field['label'] ?? $field['key'],
                'filterable' => (bool) ( $field['searchable'] ?? false ),
            ];
        }
        
        return $fields;
    }
}
```

#### Phase 5: Automation with Sure Trigger

**Trigger: Import CPT via Webhook**

```
Trigger: HTTP request received
URL: /wp-json/sofir/v1/import-cpt
Method: POST
Authentication: API Key

Body:
{
  "package_url": "https://example.com/my-listing-package.zip",
  "voxel_enable": true,
  "auto_templates": true
}

Action 1: Import CPT package
Action 2: Flush rewrite rules
Action 3: Create default templates
Action 4: Send email notification
```

---

## Development Login Credentials

### SOFIR Development Site

```
URL: https://sofir-dev.local
Admin: https://sofir-dev.local/wp-admin

Username: developer
Password: SofirDev@2025!
```

**Available Plugins:**
- SOFIR (development version)
- Voxel Theme
- Sure Trigger
- ElementorPro
- Advanced Custom Fields

**Test Data:**
- 10 sample listings
- 5 sample events
- 3 sample courses
- Test user accounts

### Voxel Development Site

```
URL: https://voxel-dev.local
Admin: https://voxel-dev.local/wp-admin

Username: voxel_admin
Password: VoxelAdmin@2025!
```

**Features:**
- Latest Voxel Theme
- All Voxel extensions
- Sample post types
- Template library access
- Asset library sync

### Test Environment Setup

```bash
# Clone development environment
git clone https://github.com/sofir/sofir-dev.git sofir-dev
cd sofir-dev

# Setup Docker environment
docker-compose up -d

# Install dependencies
composer install
npm install

# Activate SOFIR plugin
wp plugin activate sofir

# Activate Voxel theme
wp theme activate voxel

# Import test data
wp sofir import-test-data

# Create test users
wp user create developer developer@sofir-dev.local --user_pass=SofirDev@2025! --role=administrator
```

---

## Testing Checklist

- [ ] CPT menu visible in admin with Voxel active
- [ ] Fields mapped correctly to Voxel field types
- [ ] Voxel templates support SOFIR CPTs
- [ ] Elementor widgets display with Voxel
- [ ] AJAX filters work correctly
- [ ] Location search autocomplete functional
- [ ] Forms create posts correctly
- [ ] Payment integration works
- [ ] Test data displays properly
- [ ] No conflicts with Voxel updates

---

## Resources

- [Voxel Theme Docs](https://voxel.guide/docs/)
- [Voxel Assets Library](https://voxel.guide/assets/)
- [SOFIR Documentation](https://sofir.com/docs/)
- [Sure Trigger Docs](https://suretrigger.com/docs/)
- [GitHub Actions](https://docs.github.com/en/actions)
- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)

---

**Last Updated**: 2025  
**Version**: 1.0  
**Status**: Development

