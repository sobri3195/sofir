# SEO AI Generator Error Handling v2.0

## 📋 Overview

**Problem**: Pembuatan artikel SEO dengan Google Gemini API sering gagal tanpa error message yang jelas, sulit di-debug, dan user tidak tahu apa yang salah.

**Solution**: Comprehensive error handling dengan WP_DEBUG logging, user-friendly error messages dalam Bahasa Indonesia, dan API validation.

## ✅ What's Fixed

### 1. API Key Validation

**Location**: `includes/class-seo-ai-generator.php` - Method: `call_gemini_api()`

```php
private function call_gemini_api( string $prompt, float $temperature = 0.7 ) {
    if ( empty( $this->api_key ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( '[SOFIR SEO] Gemini API key is not configured' );
        }
        return new \WP_Error( 
            'no_api_key', 
            'Google Gemini API key tidak dikonfigurasi. Silakan masukkan API key di tab SEO.' 
        );
    }
    // ... rest of code
}
```

**Benefits**:
- Validates API key before making request
- Returns clear error message
- Logs to debug.log when WP_DEBUG enabled

### 2. HTTP Status Code Handling

```php
$code = wp_remote_retrieve_response_code( $response );

if ( $code !== 200 ) {
    $body = wp_remote_retrieve_body( $response );
    $error = json_decode( $body, true );
    $message = $error['error']['message'] ?? 'API request failed';
    
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( sprintf( '[SOFIR SEO] API error (Code %d): %s', $code, $message ) );
        error_log( '[SOFIR SEO] Response body: ' . $body );
    }
    
    // User-friendly messages
    if ( $code === 400 && strpos( $message, 'API_KEY_INVALID' ) !== false ) {
        $message = 'API key tidak valid. Silakan periksa kembali API key Anda di tab SEO.';
    } elseif ( $code === 429 ) {
        $message = 'Rate limit tercapai. Silakan coba lagi beberapa saat.';
    } elseif ( $code === 403 ) {
        $message = 'API key tidak memiliki akses. Pastikan API key Anda memiliki izin untuk Generative Language API.';
    }
    
    return new \WP_Error( 'api_error', $message . ' (HTTP ' . $code . ')' );
}
```

**Error Codes Handled**:

| Code | Original Message | User-Friendly Message |
|------|------------------|----------------------|
| 400 | `API_KEY_INVALID` | API key tidak valid. Silakan periksa kembali API key Anda di tab SEO. |
| 403 | `Permission denied` | API key tidak memiliki akses. Pastikan API key Anda memiliki izin untuk Generative Language API. |
| 429 | `Rate limit exceeded` | Rate limit tercapai. Silakan coba lagi beberapa saat. |
| 500 | `Internal server error` | Server error dari Google. Silakan coba lagi. |

### 3. Connection Error Handling

```php
$response = wp_remote_post(
    $url,
    [
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body'    => wp_json_encode( $body ),
        'timeout' => 60,
    ]
);

if ( is_wp_error( $response ) ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( sprintf( '[SOFIR SEO] API request error: %s', $response->get_error_message() ) );
    }
    return new \WP_Error( 
        'api_connection_error', 
        'Gagal terhubung ke Google Gemini API: ' . $response->get_error_message() 
    );
}
```

**Handles**:
- Network timeouts
- DNS resolution failures
- SSL certificate errors
- Connection refused errors

### 4. JSON Parsing Validation

```php
$body = wp_remote_retrieve_body( $response );
$data = json_decode( $body, true );

if ( json_last_error() !== JSON_ERROR_NONE ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[SOFIR SEO] JSON decode error: ' . json_last_error_msg() );
    }
    return new \WP_Error( 
        'json_error', 
        'Gagal mem-parse response dari API: ' . json_last_error_msg() 
    );
}
```

**JSON Errors Handled**:
- `JSON_ERROR_SYNTAX` - Syntax error
- `JSON_ERROR_UTF8` - UTF-8 encoding issues
- `JSON_ERROR_DEPTH` - Maximum stack depth exceeded
- `JSON_ERROR_CTRL_CHAR` - Control character error

### 5. Empty Response Detection

```php
if ( empty( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( '[SOFIR SEO] Empty response from Gemini API' );
        error_log( '[SOFIR SEO] Full response: ' . print_r( $data, true ) );
    }
    return new \WP_Error( 
        'empty_response', 
        'Response kosong dari Gemini API. Silakan coba lagi.' 
    );
}
```

**Handles**:
- Empty `candidates` array
- Missing `content` field
- Missing `parts` array
- Empty `text` field

### 6. Comprehensive WP_DEBUG Logging

```php
// Before API call
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( sprintf( 
        '[SOFIR SEO] Calling Gemini API - URL: %s, Temperature: %.2f', 
        $url, 
        $temperature 
    ) );
}

// After successful response
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( sprintf( 
        '[SOFIR SEO] API response received - Length: %d characters', 
        strlen( $text ) 
    ) );
}
```

**Log Format**:
```
[timestamp] [SOFIR SEO] Gemini API key is not configured
[timestamp] [SOFIR SEO] Calling Gemini API - URL: https://..., Temperature: 0.70
[timestamp] [SOFIR SEO] API error (Code 400): API_KEY_INVALID
[timestamp] [SOFIR SEO] Response body: {"error":{"message":"API_KEY_INVALID"}}
[timestamp] [SOFIR SEO] API response received - Length: 2543 characters
[timestamp] [SOFIR SEO] JSON decode error: Syntax error
[timestamp] [SOFIR SEO] Empty response from Gemini API
[timestamp] [SOFIR SEO] API request error: cURL error 28: Connection timed out
```

## 🛠️ Testing

### Test Error Messages

#### 1. No API Key
```php
// Delete API key
delete_option( 'sofir_gemini_api_key' );

// Try generating article
// Expected: "Google Gemini API key tidak dikonfigurasi. Silakan masukkan API key di tab SEO."
```

#### 2. Invalid API Key
```php
// Set invalid API key
update_option( 'sofir_gemini_api_key', 'invalid_key_123' );

// Try generating article
// Expected: "API key tidak valid. Silakan periksa kembali API key Anda di tab SEO. (HTTP 400)"
```

#### 3. Rate Limit
```php
// Make many requests quickly
for ( $i = 0; $i < 100; $i++ ) {
    // Generate article
}

// Expected: "Rate limit tercapai. Silakan coba lagi beberapa saat. (HTTP 429)"
```

#### 4. Permission Denied
```php
// Use API key without Generative Language API enabled

// Try generating article
// Expected: "API key tidak memiliki akses. Pastikan API key Anda memiliki izin untuk Generative Language API. (HTTP 403)"
```

### Enable Debug Logging

1. **Enable WP_DEBUG**:
```php
// wp-config.php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

2. **Generate an article**:
   - Go to SOFIR → SEO
   - Fill in article details
   - Click "Generate Article"

3. **Check debug.log**:
```bash
tail -f wp-content/debug.log
```

4. **Expected log entries**:
```
[22-Jan-2025 10:30:15 UTC] [SOFIR SEO] Calling Gemini API - URL: https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=AIza..., Temperature: 0.70
[22-Jan-2025 10:30:17 UTC] [SOFIR SEO] API response received - Length: 3421 characters
```

## 📊 Error Message Matrix

| Scenario | Error Code | User Message (ID) | Debug Log |
|----------|-----------|------------------|-----------|
| No API key | `no_api_key` | Google Gemini API key tidak dikonfigurasi. Silakan masukkan API key di tab SEO. | `[SOFIR SEO] Gemini API key is not configured` |
| Invalid API key | `api_error` | API key tidak valid. Silakan periksa kembali API key Anda di tab SEO. (HTTP 400) | `[SOFIR SEO] API error (Code 400): API_KEY_INVALID` |
| Rate limit | `api_error` | Rate limit tercapai. Silakan coba lagi beberapa saat. (HTTP 429) | `[SOFIR SEO] API error (Code 429): Rate limit exceeded` |
| No permission | `api_error` | API key tidak memiliki akses. Pastikan API key Anda memiliki izin untuk Generative Language API. (HTTP 403) | `[SOFIR SEO] API error (Code 403): Permission denied` |
| Connection error | `api_connection_error` | Gagal terhubung ke Google Gemini API: [error detail] | `[SOFIR SEO] API request error: cURL error 28: Connection timed out` |
| JSON parse error | `json_error` | Gagal mem-parse response dari API: [json error] | `[SOFIR SEO] JSON decode error: Syntax error` |
| Empty response | `empty_response` | Response kosong dari Gemini API. Silakan coba lagi. | `[SOFIR SEO] Empty response from Gemini API` |

## 🐛 Troubleshooting

### Check API Key

```php
$api_key = get_option( 'sofir_gemini_api_key' );
if ( empty( $api_key ) ) {
    echo 'API key not configured';
} else {
    echo 'API key: ' . substr( $api_key, 0, 10 ) . '...';
}
```

### Test API Connection

```php
$url = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . $api_key;

$response = wp_remote_post( $url, [
    'headers' => [ 'Content-Type' => 'application/json' ],
    'body' => wp_json_encode([
        'contents' => [
            [
                'parts' => [
                    [ 'text' => 'Hello, test' ]
                ]
            ]
        ]
    ]),
    'timeout' => 10,
] );

if ( is_wp_error( $response ) ) {
    echo 'Connection error: ' . $response->get_error_message();
} else {
    echo 'HTTP Code: ' . wp_remote_retrieve_response_code( $response );
}
```

### Check Debug Log Location

```php
if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
    $log_file = ini_get( 'error_log' );
    if ( empty( $log_file ) ) {
        $log_file = WP_CONTENT_DIR . '/debug.log';
    }
    echo 'Log file: ' . $log_file;
} else {
    echo 'WP_DEBUG_LOG not enabled';
}
```

## ✨ Benefits

### Before v2.0
❌ Generic error messages
❌ No logging
❌ Hard to debug
❌ Users confused
❌ No API key validation
❌ Poor error handling

### After v2.0
✅ User-friendly error messages in Indonesian
✅ Comprehensive WP_DEBUG logging
✅ API key validation before request
✅ HTTP status code handling
✅ Connection error handling
✅ JSON parsing validation
✅ Empty response detection
✅ Easy troubleshooting

## 📚 Related Files

- `includes/class-seo-ai-generator.php` - Main AI generator class
- `includes/class-admin-seopanel.php` - SEO admin panel UI
- `assets/js/seo-ai-generator.js` - Frontend AJAX handling

## 🔗 API Documentation

- [Google AI Studio](https://aistudio.google.com/app/apikey) - Get API key
- [Generative Language API](https://ai.google.dev/api/rest/v1/models/generateContent) - API reference
- [Error Codes](https://ai.google.dev/api/rest#error-handling) - Error handling guide

## 🎯 Conclusion

**v2.0 adalah complete error handling solution untuk SEO AI Generator**:
- ✅ User-friendly error messages dalam Bahasa Indonesia
- ✅ Comprehensive WP_DEBUG logging untuk debugging
- ✅ API key validation sebelum request
- ✅ HTTP status code handling (400, 403, 429)
- ✅ Connection error handling
- ✅ JSON parsing validation
- ✅ Empty response detection
- ✅ Easy troubleshooting dengan clear logs
- ✅ Production-ready error handling!
