# AI24 Assistant Integrator - Technical Specification

## Document Overview

This technical specification defines the complete implementation details for integrating AI24 Assistant Integrator with SOFIR WordPress Plugin. This document is for developers implementing the integration.

## System Architecture

### Component Diagram

```
┌─────────────────────────────────────────────────────┐
│            SOFIR WordPress Plugin                    │
│                                                      │
│  ┌───────────────────────────────────────────────┐  │
│  │    AI24 Integrator Module                     │  │
│  │  (/modules/ai24-integrator/)                  │  │
│  │                                               │  │
│  │  ┌─────────────────────────────────────────┐ │  │
│  │  │ Manager (Singleton)                     │ │  │
│  │  │ - Bootstrap & initialization            │ │  │
│  │  │ - Dependency management                 │ │  │
│  │  └─────────────────────────────────────────┘ │  │
│  │           ▼                                   │  │
│  │  ┌─────────────────────────────────────────┐ │  │
│  │  │ Bridge (API Communication)              │ │  │
│  │  │ - HTTP requests to AI24                 │ │  │
│  │  │ - Response parsing                      │ │  │
│  │  │ - Error handling                        │ │  │
│  │  └─────────────────────────────────────────┘ │  │
│  │           ▼                                   │  │
│  │  ┌─────────────────────────────────────────┐ │  │
│  │  │ Config (Settings Management)            │ │  │
│  │  │ - API key storage/retrieval             │ │  │
│  │  │ - Configuration validation              │ │  │
│  │  │ - Default values                        │ │  │
│  │  └─────────────────────────────────────────┘ │  │
│  │           ▼                                   │  │
│  │  ┌─────────────────────────────────────────┐ │  │
│  │  │ REST API (External Integration)         │ │  │
│  │  │ - /sofir/v1/ai24/suggest                │ │  │
│  │  │ - /sofir/v1/ai24/generate               │ │  │
│  │  │ - /sofir/v1/ai24/analyze                │ │  │
│  │  └─────────────────────────────────────────┘ │  │
│  │           ▼                                   │  │
│  │  ┌─────────────────────────────────────────┐ │  │
│  │  │ Voxel Integration (Theme Support)       │ │  │
│  │  │ - CPT enhancement                       │ │  │
│  │  │ - Field mapping                         │ │  │
│  │  │ - Content suggestions for Voxel posts   │  │  │
│  │  └─────────────────────────────────────────┘ │  │
│  │           ▼                                   │  │
│  │  ┌─────────────────────────────────────────┐ │  │
│  │  │ Admin Panel (UI)                        │ │  │
│  │  │ - Configuration interface               │ │  │
│  │  │ - Status display                        │ │  │
│  │  │ - Usage statistics                      │  │  │
│  │  └─────────────────────────────────────────┘ │  │
│  └───────────────────────────────────────────────┘  │
│                       ▼                              │
│  ┌───────────────────────────────────────────────┐  │
│  │ Existing SOFIR Modules                       │  │
│  │ ├─ AI (/modules/ai/)                         │  │
│  │ ├─ Voxel (/modules/voxel/)                   │  │
│  │ ├─ SEO (/modules/seo/)                       │  │
│  │ └─ Forms (/modules/forms/)                   │  │
│  └───────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────┘
           ▼
    ┌──────────────────┐
    │  AI24 Plugin API │
    │ (External Service)
    │                  │
    │ - Authentication │
    │ - Suggestions    │
    │ - Generation     │
    │ - Analysis       │
    └──────────────────┘
```

## Module Structure & Classes

### 1. Manager Class (`manager.php`)

**File**: `/modules/ai24-integrator/manager.php`

```php
namespace Sofir\Ai24Integrator;

class Manager {
    private static ?Manager $instance = null;
    
    // Properties
    private Bridge $bridge;
    private Config $config;
    private REST_API $rest_api;
    private Voxel_Integration $voxel;
    private Admin_Panel $admin;
    
    // Methods
    public static function instance(): Manager
    public function boot(): void
    private function init_dependencies(): void
    private function register_hooks(): void
    public function is_active(): bool
    public function get_bridge(): Bridge
    public function get_config(): Config
}
```

**Responsibilities**:
- Singleton pattern implementation
- Dependency injection
- Boot sequence management
- Hook registration

### 2. Bridge Class (`bridge.php`)

**File**: `/modules/ai24-integrator/bridge.php`

```php
namespace Sofir\Ai24Integrator;

class Bridge {
    // Properties
    private Config $config;
    private Cache $cache;
    
    // Methods
    public function suggest(array $params): array
    public function generate(array $params): string
    public function analyze(array $params): array
    public function get_status(): array
    
    private function call_api(string $endpoint, array $params, string $method = 'POST'): array
    private function build_request(array $params): array
    private function handle_response(\WP_Response $response): array
    private function handle_error(\WP_Error $error): array
    private function cache_result(string $key, $value, int $ttl): void
    private function get_cached(string $key): ?array
}
```

**Methods**:
- `suggest()` - Get AI suggestions
- `generate()` - Generate content
- `analyze()` - Analyze content
- `get_status()` - Check AI24 status
- `call_api()` - Make HTTP request
- `build_request()` - Format request
- `handle_response()` - Parse response
- `handle_error()` - Process errors
- `cache_result()` - Cache response
- `get_cached()` - Retrieve from cache

### 3. Config Class (`class-ai24-config.php`)

**File**: `/modules/ai24-integrator/class-ai24-config.php`

```php
namespace Sofir\Ai24Integrator;

class Config {
    // Constants
    const OPTION_PREFIX = 'sofir_ai24_';
    const DEFAULT_CACHE_TTL = 3600;
    
    // Methods
    public function get(string $key, $default = null): mixed
    public function set(string $key, $value): bool
    public function delete(string $key): bool
    public function get_api_key(): ?string
    public function set_api_key(string $key): bool
    public function is_enabled(): bool
    public function enable(): void
    public function disable(): void
    public function is_voxel_enabled(): bool
    public function get_cache_ttl(): int
    public function reset_to_defaults(): void
    public function validate(): array
}
```

**Settings**:
```php
sofir_ai24_enabled              // bool
sofir_ai24_api_key              // string (encrypted?)
sofir_ai24_auto_suggest         // bool
sofir_ai24_voxel_enabled        // bool
sofir_ai24_cache_ttl            // int (seconds)
sofir_ai24_debug_mode           // bool
sofir_ai24_last_sync            // int (timestamp)
sofir_ai24_request_count        // int
```

### 4. Voxel Integration Class (`voxel-integration.php`)

**File**: `/modules/ai24-integrator/voxel-integration.php`

```php
namespace Sofir\Ai24Integrator;

class Voxel_Integration {
    // Properties
    private Bridge $bridge;
    private Config $config;
    
    // Methods
    public function boot(): void
    public function enhance_cpt_for_ai24(array $args, string $cpt_slug): array
    public function register_ai24_metabox(): void
    public function render_ai24_metabox(\WP_Post $post): void
    public function save_ai24_data(\WP_Post $post): void
    public function suggest_post_content(int $post_id): array
    public function auto_fill_voxel_fields(int $post_id, array $suggestions): void
    public function handle_post_save(int $post_id, \WP_Post $post): void
    private function get_voxel_cpt_mapping(): array
    private function map_ai24_to_voxel_fields(array $ai24_data): array
}
```

**Features**:
- Metabox for AI suggestions
- Voxel field mapping
- Auto-fill capabilities
- Post save handler

### 5. REST API Class (`rest-api.php`)

**File**: `/modules/ai24-integrator/rest-api.php`

```php
namespace Sofir\Ai24Integrator;

class REST_API {
    // Properties
    private Bridge $bridge;
    
    // Methods
    public function register_routes(): void
    private function register_suggest_route(): void
    private function register_generate_route(): void
    private function register_analyze_route(): void
    private function register_status_route(): void
    private function register_config_route(): void
    
    public function rest_suggest(\WP_REST_Request $request): \WP_REST_Response
    public function rest_generate(\WP_REST_Request $request): \WP_REST_Response
    public function rest_analyze(\WP_REST_Request $request): \WP_REST_Response
    public function rest_status(\WP_REST_Request $request): \WP_REST_Response
    public function rest_config(\WP_REST_Request $request): \WP_REST_Response
    
    private function check_permission(): bool
}
```

**Endpoints**:

| Method | Endpoint | Parameters | Response |
|--------|----------|------------|----------|
| POST | `/sofir/v1/ai24/suggest` | content, type, context | suggestions[] |
| POST | `/sofir/v1/ai24/generate` | prompt, length, tone | content: string |
| POST | `/sofir/v1/ai24/analyze` | content | analysis: object |
| GET | `/sofir/v1/ai24/status` | - | status, connected, quota |
| GET | `/sofir/v1/ai24/config` | - | config: object |

### 6. Admin Panel Class (`admin-panel.php`)

**File**: `/modules/ai24-integrator/admin-panel.php`

```php
namespace Sofir\Ai24Integrator;

class Admin_Panel {
    // Properties
    private Config $config;
    private Bridge $bridge;
    
    // Methods
    public function register_menu(): void
    public function render_page(): void
    public function handle_settings_save(): void
    public function render_settings_form(): void
    public function render_status_display(): void
    public function render_usage_stats(): void
    public function render_voxel_settings(): void
    private function enqueue_assets(): void
    private function verify_nonce(): void
}
```

**Sections**:
1. Status Display
2. Configuration
3. Voxel Integration Settings
4. Usage Statistics
5. Debug Information

## Data Flow Diagrams

### Suggestion Request Flow

```
┌──────────────────────┐
│  AJAX Request        │
│ (Suggest Content)    │
└──────────────┬───────┘
               │
               ▼
       ┌───────────────────┐
       │  Nonce Verify     │
       │  Capability Check │
       └───────────┬───────┘
               Fail│  ├─ Success
                   │  │
                   ▼  ▼
               Reject  REST_API::rest_suggest()
                       │
                       ▼
                  Bridge::suggest()
                       │
                       ├─ Check cache
                       │  ├─ Found ──► Return cached
                       │  └─ Not found
                       │
                       ▼
                  call_api()
                       │
                       ├─ Success ────► Cache result
                       │                │
                       ▼                ▼
                  parse_response()   Store cache
                       │                │
                       └────────┬───────┘
                               │
                               ▼
                        Return to AJAX
                               │
                               ▼
                        Update UI
```

### Post Save with AI24 Suggestions

```
┌────────────────────────────────┐
│  Post Save Action              │
│  (save_post hook)              │
└────────────────────────┬────────┘
                         │
                         ▼
              Check if Voxel post
                         │
                    ├─ Not Voxel
                    │  └─ Exit
                    │
                    └─ Voxel post
                       │
                       ▼
         Check if AI24 enabled
                       │
                    ├─ Disabled
                    │  └─ Exit
                    │
                    └─ Enabled
                       │
                       ▼
            Get post content
                       │
                       ▼
       Bridge::suggest_content()
                       │
                       ▼
            Parse suggestions
                       │
                       ▼
         Map to Voxel fields
                       │
                       ▼
         Store in post meta
                       │
                       ▼
    Trigger sofir/ai24/suggested
            action hook
```

## API Integration Details

### AI24 API Assumptions

Based on plugin ecosystem, likely API structure:

```php
Base URL: https://api.ai24.com/v1/

Endpoints:
POST   /auth              // Authentication
POST   /suggest           // Get suggestions
POST   /generate          // Generate content
POST   /analyze           // Analyze content
GET    /status            // Check service status
GET    /quota             // Check usage quota

Authentication:
- Header: X-API-Key: {key}
- OR OAuth 2.0
- OR Custom token

Request Format:
{
    "action": "suggest",
    "content": "...",
    "context": "voxel_property",
    "parameters": {
        "tone": "professional",
        "length": "short"
    }
}

Response Format:
{
    "status": "success",
    "data": {
        "suggestions": [],
        "metadata": {}
    },
    "timestamp": 1234567890,
    "quota": {
        "remaining": 100,
        "reset_at": 1234567890
    }
}

Error Response:
{
    "status": "error",
    "error": {
        "code": "INVALID_API_KEY",
        "message": "Invalid API key"
    }
}
```

## Caching Strategy

### Cache Types

```php
// Suggestion cache
sofir_ai24_suggest_{hash}       // 1 hour (3600s)

// Generated content cache
sofir_ai24_generate_{hash}      // 24 hours (86400s)

// Analysis cache
sofir_ai24_analyze_{hash}       // 6 hours (21600s)

// Status cache
sofir_ai24_status               // 10 minutes (600s)

// API quota cache
sofir_ai24_quota                // 1 hour (3600s)
```

### Cache Key Generation

```php
$cache_key = 'sofir_ai24_' . $action . '_' . md5(
    serialize([
        $post_id,
        $content,
        $context,
        $parameters
    ])
);
```

### Cache Invalidation

```php
// Clear on post update
function invalidate_post_cache($post_id) {
    $pattern = 'sofir_ai24_*_' . $post_id . '_*';
    // Clear matching transients
}

// Clear on configuration change
function invalidate_all_cache() {
    // Clear all sofir_ai24_* transients
}

// Clear on manual request
function clear_cache_ajax() {
    // Handle AJAX clear request
}
```

## Error Handling

### Error Codes

```php
// Connection errors
const ERROR_NO_API_KEY = 'NO_API_KEY';
const ERROR_INVALID_API_KEY = 'INVALID_API_KEY';
const ERROR_CONNECTION_FAILED = 'CONNECTION_FAILED';
const ERROR_TIMEOUT = 'TIMEOUT';
const ERROR_RATE_LIMITED = 'RATE_LIMITED';

// Request errors
const ERROR_INVALID_REQUEST = 'INVALID_REQUEST';
const ERROR_MISSING_PARAMS = 'MISSING_PARAMS';
const ERROR_INVALID_PARAM = 'INVALID_PARAM';

// Response errors
const ERROR_INVALID_RESPONSE = 'INVALID_RESPONSE';
const ERROR_EMPTY_RESPONSE = 'EMPTY_RESPONSE';
const ERROR_API_ERROR = 'API_ERROR';
const ERROR_UNKNOWN = 'UNKNOWN';
```

### Error Handling Flow

```php
try {
    // Make request
    $response = wp_remote_post($url, $args);
    
    if (is_wp_error($response)) {
        // Handle WP_Error
        throw new Exception($response->get_error_message());
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Invalid JSON
        throw new Exception('Invalid JSON response');
    }
    
    if (empty($body)) {
        // Empty response
        throw new Exception('Empty response from API');
    }
    
    if ('error' === $body['status']) {
        // API error
        throw new Exception($body['error']['message']);
    }
    
    // Success
    return $body['data'];
    
} catch (Exception $e) {
    // Log error
    error_log('[SOFIR AI24] Error: ' . $e->getMessage());
    
    // Return error response
    return [
        'error' => ERROR_API_ERROR,
        'message' => $e->getMessage()
    ];
}
```

## Security Implementation

### API Key Storage

```php
// Store API key in options table
$api_key = get_option('sofir_ai24_api_key');

// Best practices:
// 1. Encrypt in database
// 2. Never expose in frontend
// 3. Validate before use
// 4. Implement key rotation
// 5. Log all API key operations
```

### Request Security

```php
// Nonce verification
$nonce = $_REQUEST['nonce'] ?? '';
if (!wp_verify_nonce($nonce, 'sofir_ai24_action')) {
    wp_die('Security check failed');
}

// Capability check
if (!current_user_can('manage_options')) {
    wp_die('Insufficient permissions');
}

// Input sanitization
$input = sanitize_text_field($_POST['content']);
$input = wp_kses_post($input);  // For HTML content

// Output escaping
echo esc_html($value);
echo wp_kses_post($html);
```

### Data Protection

```php
// Don't send sensitive data to AI24
$sanitized_data = [
    'title' => $post->post_title,        // OK
    'content' => $post->post_content,    // OK
    // 'author_email' => ???,            // NO - Sensitive
    // 'user_data' => ???,               // NO - Sensitive
];

// Implement privacy controls
add_option('sofir_ai24_privacy_notice', 'Data sent to AI24...');
```

## Admin UI Wireframe

```
┌──────────────────────────────────────────────────────┐
│ SOFIR → Tools → AI24 Integrator                      │
├──────────────────────────────────────────────────────┤
│                                                       │
│ Status Panel                                          │
│ ┌─ Connected ✓                                       │
│ │ Last Sync: 2 minutes ago                           │
│ │ API Quota: 450/500 remaining                       │
│ │ [Test Connection]  [Sync Now]  [Clear Cache]       │
│ └                                                     │
│                                                       │
│ Configuration Tab                                     │
│ ┌ API Key: ••••••••••••••••••••••  [Change]         │
│ │ Enable AI24: [✓]                                   │
│ │ Auto-Suggest: [✓]                                  │
│ │ Cache TTL: 3600 seconds                            │
│ │ Debug Mode: [ ]                                    │
│ │ [Save Settings]                                    │
│ └                                                     │
│                                                       │
│ Voxel Integration Tab                                │
│ ┌ Enable for Voxel: [✓]                             │
│ │ Auto-fill Fields: [✓]                              │
│ │ CPT Mappings:                                      │
│ │   Property → Property Description, Highlights     │
│ │   Agent → Bio, Services                            │
│ │ [Update Mappings]                                  │
│ └                                                     │
│                                                       │
│ Usage Statistics Tab                                 │
│ ┌ Requests (Today): 42                              │
│ │ Requests (Total): 1,234                            │
│ │ Suggestions Generated: 156                         │
│ │ Content Generated: 89                              │
│ │ [Download Report]                                  │
│ └                                                     │
│                                                       │
│ Debug Tab                                            │
│ ┌ Last 10 Operations:                               │
│ │ [2024-01-15 14:32] POST suggest - Success (234ms) │
│ │ [2024-01-15 14:30] POST suggest - Cache hit       │
│ │ ...                                                │
│ │ [Clear Logs]                                       │
│ └                                                     │
└──────────────────────────────────────────────────────┘
```

## Testing Requirements

### Unit Tests
```php
// tests/unit/test-config.php
- test_get_setting()
- test_set_setting()
- test_validate_config()
- test_default_values()

// tests/unit/test-bridge.php
- test_suggest()
- test_generate()
- test_analyze()
- test_caching()
- test_error_handling()
```

### Integration Tests
```php
// tests/integration/test-plugin-detection.php
- test_ai24_plugin_detected()
- test_ai24_plugin_not_detected()

// tests/integration/test-rest-api.php
- test_suggest_endpoint()
- test_generate_endpoint()
- test_analyze_endpoint()
```

### Voxel Tests
```php
// tests/voxel/test-voxel-integration.php
- test_voxel_cpt_enhancement()
- test_field_mapping()
- test_auto_fill_fields()
```

## Performance Benchmarks

### Expected Performance
- Suggest request: < 500ms (with cache)
- Generate request: < 2000ms
- Analyze request: < 1000ms
- Admin page load: < 3000ms

### Optimization Strategies
1. Aggressive caching
2. Lazy loading admin UI
3. Background processing with WP-Cron
4. Connection pooling
5. Request batching

## Version Compatibility

### WordPress
- Minimum: WordPress 6.3
- Tested: 6.3, 6.4, 6.5, 6.6

### PHP
- Minimum: PHP 8.0
- Tested: PHP 8.0, 8.1, 8.2, 8.3

### SOFIR
- Minimum: 0.1.0
- Required: Voxel Module for Voxel features

### AI24 Plugin
- Minimum: Latest stable version
- Required: For integration to work

## Deployment Checklist

- [ ] API key obtained from AI24
- [ ] API endpoint documented
- [ ] Security review completed
- [ ] Performance testing done
- [ ] Unit tests passing
- [ ] Integration tests passing
- [ ] Admin UI functional
- [ ] Voxel integration working
- [ ] Documentation complete
- [ ] Error handling tested
- [ ] Caching verified
- [ ] Production deployment

## Maintenance & Support

### Regular Tasks
- Monitor API quota usage
- Review error logs weekly
- Test API connectivity monthly
- Update documentation as needed

### Troubleshooting
- Check API key validity
- Verify WordPress versions
- Test API endpoints
- Review error logs
- Clear cache if needed

## References

- SOFIR Plugin: `/sofir.php`
- AI Module: `/modules/ai/builder.php`
- Voxel Module: `/modules/voxel/manager.php`
- SOFIR Naming Conventions: Memory
- WordPress Coding Standards: https://developer.wordpress.org/coding-standards/

---

**Document Version**: 1.0
**Last Updated**: 2025-01-XX
**Status**: DRAFT - Ready for Review
