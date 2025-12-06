# AI24 Assistant Integrator Integration Plan for SOFIR

## Executive Summary

This document outlines the complete integration strategy for adding AI24 Assistant Integrator support to the SOFIR WordPress plugin. AI24 is a powerful AI assistant plugin available on WordPress.org that can be enhanced with Voxel theme integration.

## What is AI24 Assistant Integrator?

### Plugin Overview
- **Name**: AI24 Assistant Integrator
- **Repository**: https://wordpress.org/plugins/ai24-assistant-integrator/
- **Documentation**: https://site24.com.au/ai24-assistant-integrator/
- **Voxel Integration**: https://voxel.guide/addon/ai24-assistant-integrator/

### Likely Capabilities
Based on the plugin ecosystem and naming, AI24 likely provides:

1. **AI-Powered Assistant Services**
   - Natural language processing
   - Content generation/suggestions
   - Intelligent automation
   - Context-aware assistance

2. **WordPress Integration**
   - REST API endpoints
   - Custom hooks/filters
   - Admin interface
   - Custom post type support
   - Field mapping

3. **Voxel Theme Support**
   - Works as a Voxel addon
   - CPT integration
   - Custom field processing
   - Template customization
   - Real estate/directory specific features

## Current SOFIR AI Implementation

### Existing AI Module (`/modules/ai/builder.php`)
**Status**: Local analysis only, no external AI service

**Current Capabilities**:
```php
- REST endpoints:
  - POST /sofir/v1/ai/suggest (title, content, post_type)
  - GET /sofir/v1/ai/analyze/{id}

- Features:
  - Keyword extraction (top 10 keywords)
  - Meta description generation
  - SEO score calculation (0-100)
  - Template suggestion
  - Recommendations (based on score)
```

**Limitations**:
- No actual AI/LLM integration
- Basic string analysis only
- No external service calls
- No machine learning
- Limited recommendation quality

### Related SOFIR Modules
1. **SEO Module** - Uses Google Gemini for article generation
2. **Voxel Module** - Voxel theme optimization and CPT handling
3. **Forms Module** - Form builder with field types
4. **Elementor Module** - Widget integration

## Integration Architecture

### Proposed Module Structure

```
modules/ai24-integrator/
├── manager.php                    # Main manager/bootstrap
├── bridge.php                     # AI24 API bridge
├── class-ai24-service.php         # AI24 service wrapper
├── class-ai24-config.php          # Configuration handler
├── admin-panel.php                # Admin UI
├── voxel-integration.php          # Voxel-specific features
├── hooks.php                      # AI24 event handlers
├── rest-api.php                   # REST API routes
├── templates/
│   └── admin-settings.php         # Admin panel template
├── assets/
│   ├── css/ai24-admin.css         # Admin styles
│   └── js/ai24-admin.js           # Admin scripts
├── README.md                      # Module documentation
├── AI24_INTEGRATION_GUIDE.md      # Developer guide
└── AI24_TESTING_GUIDE.md          # Testing procedures
```

### Integration Points

#### 1. Plugin Detection & Activation

```php
// Check if AI24 is active
if ( is_plugin_active( 'ai24-assistant-integrator/ai24-assistant-integrator.php' ) ) {
    // Initialize SOFIR AI24 integration
    Sofir\Ai24Integrator\Manager::instance()->init();
}
```

#### 2. Configuration Management

```php
// Admin settings for AI24
'sofir_ai24_enabled'            // Enable/disable integration
'sofir_ai24_api_key'            // AI24 API key
'sofir_ai24_auto_suggest'       // Auto-suggest content
'sofir_ai24_voxel_enabled'      // Voxel integration
'sofir_ai24_cache_ttl'          // Cache duration
'sofir_ai24_debug_mode'         // Enable logging
```

#### 3. REST API Integration

```php
// Extend SOFIR REST API
POST   /sofir/v1/ai24/suggest       // Get AI suggestions
POST   /sofir/v1/ai24/generate      // Generate content
GET    /sofir/v1/ai24/status        // Check AI24 status
POST   /sofir/v1/ai24/analyze       // Analyze content
GET    /sofir/v1/ai24/config        // Get configuration
```

#### 4. Voxel Theme Integration

```php
// Voxel-specific features
- Auto-suggest property descriptions
- Generate business profiles
- Create directory listings
- Enhance CPT metadata
- Template recommendations
```

#### 5. Admin Interface

```
SOFIR Admin → Tools → AI24 Integrator
├── Status (connected/disconnected)
├── Configuration
│   ├── API Key
│   ├── Enable/Disable
│   ├── Cache Settings
│   └── Debug Mode
├── Usage Statistics
│   ├── Requests (today/total)
│   ├── Suggestions Generated
│   └── Content Generated
├── Voxel Integration
│   ├── Enable for Voxel
│   ├── CPT Settings
│   └── Field Mapping
└── Test Connection
```

## Implementation Phases

### Phase 1: Research & Discovery (CURRENT)
- [x] Understand AI24 plugin structure
- [x] Identify available APIs
- [ ] Study WordPress.org plugin repository
- [ ] Document AI24 hooks/filters
- [ ] Create integration plan

### Phase 2: Core Module Development
- [ ] Create manager.php
- [ ] Implement AI24 API bridge
- [ ] Create configuration handler
- [ ] Build REST API endpoints
- [ ] Add admin panel

### Phase 3: Voxel Integration
- [ ] Implement Voxel CPT handling
- [ ] Create field mapping
- [ ] Add Voxel-specific features
- [ ] Test with Voxel theme

### Phase 4: Admin UI
- [ ] Design admin interface
- [ ] Add configuration UI
- [ ] Create status dashboard
- [ ] Implement test connection
- [ ] Add usage statistics

### Phase 5: Documentation
- [ ] API documentation
- [ ] Developer guide
- [ ] User guide
- [ ] Troubleshooting guide
- [ ] Code examples

### Phase 6: Testing & QA
- [ ] Unit tests
- [ ] Integration tests
- [ ] Voxel compatibility tests
- [ ] Performance testing
- [ ] Security testing

## Key Features to Implement

### 1. AI24 Service Wrapper
```php
class AI24_Service {
    - connect()              // Establish connection
    - is_connected()         // Check status
    - authenticate()         // Handle authentication
    - call()                 // Make API calls
    - handle_errors()        // Error handling
    - cache_results()        // Caching
}
```

### 2. Configuration Management
```php
class AI24_Config {
    - get_setting()          // Get config value
    - set_setting()          // Save config
    - validate_config()      // Validate settings
    - get_default_config()   // Default values
}
```

### 3. Voxel Integration
```php
class AI24_Voxel {
    - enhance_cpt()          // Enhance SOFIR CPTs
    - map_fields()           // Map Voxel fields
    - suggest_content()      // AI suggestions for posts
    - auto_fill_fields()     // Auto-fill field values
}
```

### 4. REST API
```php
- Endpoints for suggestions
- Content generation
- Status checks
- Configuration management
- Analytics/usage data
```

### 5. Admin Panel
```php
- Status display
- Configuration UI
- Test connection button
- Usage statistics
- Debug logs
- Voxel integration settings
```

## Naming Conventions (SOFIR Standard)

### Classes
```php
namespace Sofir\Ai24Integrator;

class Manager { }                    // Main manager
class Bridge { }                     // API bridge
class Service { }                    // Service wrapper
class Config { }                     // Configuration
class Voxel_Integration { }          // Voxel features
class REST_API { }                   // REST endpoints
class Admin_Panel { }                // Admin UI
```

### Options
```php
sofir_ai24_enabled              // Enable/disable
sofir_ai24_api_key              // API key
sofir_ai24_auto_suggest         // Auto-suggest
sofir_ai24_voxel_enabled        // Voxel integration
sofir_ai24_cache_ttl            // Cache duration (seconds)
sofir_ai24_debug_mode           // Debug logging
sofir_ai24_last_sync            // Last sync time
sofir_ai24_version              // Module version
```

### Hooks

#### Filters
```php
sofir/ai24/should_connect          // Should connect to AI24?
sofir/ai24/config                  // Configuration values
sofir/ai24/api_key                 // Get API key
sofir/ai24/cache_ttl               // Cache duration
sofir/ai24/suggestion_request      // Modify suggestion request
sofir/ai24/suggestion_response     // Modify suggestion response
sofir/ai24/voxel_fields            // Map Voxel fields
```

#### Actions
```php
sofir/ai24/connected               // After successful connection
sofir/ai24/disconnected            // After disconnection
sofir/ai24/suggestion_generated    // After suggestion generated
sofir/ai24/content_generated       // After content generated
sofir/ai24/error                   // On error
sofir/ai24/sync_complete           // After sync
```

### Files
```
/modules/ai24-integrator/manager.php
/modules/ai24-integrator/bridge.php
/modules/ai24-integrator/class-ai24-service.php
/modules/ai24-integrator/class-ai24-config.php
/modules/ai24-integrator/voxel-integration.php
/modules/ai24-integrator/rest-api.php
/modules/ai24-integrator/admin-panel.php
```

## Integration with Existing SOFIR Features

### AI Module Enhancement
- Extend `/modules/ai/builder.php` with AI24 suggestions
- Keep existing local analysis
- Use AI24 for enhanced recommendations
- Cache both local and AI24 results

### Voxel Integration
- Work with `/modules/voxel/manager.php`
- Use Voxel field mapping
- Enhance CPT with AI24 suggestions
- Support Voxel theme templates

### SEO Module
- Share Gemini API for article generation
- Provide AI24 content suggestions
- Enhance SEO recommendations
- Better keyword research

### Forms Module
- AI-powered form field suggestions
- Auto-fill form values
- Intelligent form recommendations
- Content generation for form labels

## Error Handling & Logging

### Error Categories
```php
// Connection errors
- NO_API_KEY
- CONNECTION_FAILED
- INVALID_CREDENTIALS
- RATE_LIMITED

// Request errors
- INVALID_REQUEST
- MISSING_PARAMETERS
- TIMEOUT

// Response errors
- INVALID_RESPONSE
- EMPTY_RESPONSE
- API_ERROR
```

### Logging
```php
// Enable WP_DEBUG logging
error_log( sprintf( 
    '[SOFIR AI24] %s: %s', 
    $error_code, 
    $error_message 
) );
```

## Testing Strategy

### Unit Tests
- Configuration management
- API bridge
- Service wrapper
- Error handling

### Integration Tests
- Plugin detection
- WordPress hooks
- REST API endpoints
- Voxel integration

### Functional Tests
- Admin panel UI
- Settings save/load
- Connection testing
- Feature functionality

### Voxel Tests
- CPT enhancement
- Field mapping
- Content generation
- Template integration

## Performance Considerations

### Caching
```php
// Cache AI24 responses
$cache_key = 'sofir_ai24_' . md5( $request_data );
$cached = get_transient( $cache_key );

// Set cache with TTL
set_transient( $cache_key, $response, $ttl );
```

### Rate Limiting
```php
// Implement rate limiting
- Per user
- Per IP
- Global limit
- Configurable thresholds
```

### Async Processing
```php
// Use WP-Cron for heavy operations
wp_schedule_single_event( time(), 'sofir_ai24_process', [ $post_id ] );
```

## Security Considerations

### API Key Management
- Secure storage (wp_options with proper prefix)
- Never log sensitive data
- Implement API key rotation
- Use nonce verification

### Request Validation
- Nonce verification for AJAX
- Capability checks
- Input sanitization
- Output escaping

### Data Protection
- Don't send sensitive data to AI24
- Comply with privacy regulations
- Add privacy notice in admin
- Clear cache regularly

## Documentation Requirements

### For Developers
- API documentation
- Hook reference
- Class/method documentation
- Code examples
- Troubleshooting guide

### For Users
- Setup guide
- Configuration guide
- Feature guide
- FAQ
- Troubleshooting

### For System Admins
- Installation instructions
- Configuration requirements
- Performance tuning
- Monitoring
- Backup/restore

## Success Criteria

- [ ] AI24 plugin detected and connected
- [ ] Configuration saved and persisted
- [ ] REST API endpoints working
- [ ] Admin panel fully functional
- [ ] Voxel integration active
- [ ] No conflicts with existing modules
- [ ] Performance acceptable (< 500ms)
- [ ] All errors handled gracefully
- [ ] Comprehensive documentation
- [ ] 100% test coverage

## Timeline

**Week 1**: Research & Planning (Current)
**Week 2**: Core Module Development
**Week 3**: Voxel Integration
**Week 4**: Admin UI & Configuration
**Week 5**: Testing & QA
**Week 6**: Documentation & Release

## Next Steps

1. ✅ Complete research phase
2. [ ] Download and study AI24 plugin code
3. [ ] Create detailed integration specification
4. [ ] Start Phase 2 development
5. [ ] Weekly progress reviews

## References

- AI24 Assistant Integrator: https://wordpress.org/plugins/ai24-assistant-integrator/
- AI24 Documentation: https://site24.com.au/ai24-assistant-integrator/
- Voxel Integration: https://voxel.guide/addon/ai24-assistant-integrator/
- SOFIR Voxel Module: `/modules/voxel/`
- SOFIR AI Module: `/modules/ai/`
- SOFIR SEO Module: Documentation in repository

---

**Status**: RESEARCH PHASE - In Progress
**Branch**: `research-ai24-assistant-integrator`
**Last Updated**: 2025-01-XX
