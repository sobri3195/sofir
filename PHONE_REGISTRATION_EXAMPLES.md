# Phone Registration - Code Examples

Contoh kode implementasi lengkap untuk berbagai use case registrasi dengan nomor HP.

## 📱 Basic Implementation

### 1. Simple Phone Registration Page

```html
<!-- page-register.php -->
<div class="registration-page">
    <h1>Daftar Akun Baru</h1>
    <p>Registrasi cepat hanya dengan nomor HP</p>
    
    [sofir_register_form phone_only="true" redirect="/welcome"]
    
    <p class="terms">
        Dengan mendaftar, Anda menyetujui 
        <a href="/terms">Syarat & Ketentuan</a> kami.
    </p>
</div>
```

### 2. Combined Login & Register Page

```html
<!-- page-auth.php -->
<div class="auth-tabs">
    <button class="sofir-tab-btn active" data-tab="login">Login</button>
    <button class="sofir-tab-btn" data-tab="register">Daftar</button>
</div>

<div class="sofir-tab-content active" data-content="login">
    [sofir_login_form]
</div>

<div class="sofir-tab-content" data-content="register">
    [sofir_register_form phone_only="true"]
</div>
```

## 🔧 Advanced Hooks

### 1. Auto-Assign User Role Based on Phone

```php
// functions.php
add_action('sofir/auth/user_registered', 'assign_role_by_phone', 10, 2);

function assign_role_by_phone($user_id, $phone_only) {
    if (!$phone_only) {
        return;
    }
    
    $phone = get_user_meta($user_id, 'sofir_phone', true);
    $user = get_userdata($user_id);
    
    // Customer role untuk nomor 08xxx
    if (substr($phone, 0, 2) === '08') {
        $user->set_role('customer');
    }
    
    // Vendor role untuk nomor +62xxx
    if (substr($phone, 0, 3) === '+62') {
        $user->set_role('vendor');
    }
}
```

### 2. Send Welcome Email After Registration

```php
// functions.php
add_action('sofir/auth/user_registered', 'send_welcome_email', 10, 2);

function send_welcome_email($user_id, $phone_only) {
    $user = get_userdata($user_id);
    $phone = get_user_meta($user_id, 'sofir_phone', true);
    
    $to = $user->user_email;
    $subject = 'Selamat Datang di ' . get_bloginfo('name');
    
    $message = sprintf(
        "Halo %s,\n\n" .
        "Terima kasih telah mendaftar di %s\n" .
        "Nomor HP Anda: %s\n\n" .
        "Login di: %s\n\n" .
        "Salam,\nTeam %s",
        $user->display_name,
        get_bloginfo('name'),
        $phone,
        home_url('/login'),
        get_bloginfo('name')
    );
    
    wp_mail($to, $subject, $message);
}
```

### 3. Phone Number Formatting

```php
// functions.php
add_filter('sofir/auth/validate_phone', 'format_indonesian_phone', 10, 2);

function format_indonesian_phone($is_valid, $phone) {
    // Remove all non-numeric characters
    $clean = preg_replace('/[^0-9]/', '', $phone);
    
    // Convert 08xxx to +628xxx
    if (substr($clean, 0, 1) === '0') {
        $clean = '62' . substr($clean, 1);
    }
    
    // Validate length (Indonesia: 10-13 digits after +62)
    if (strlen($clean) < 10 || strlen($clean) > 13) {
        return false;
    }
    
    // Update phone with formatted version
    $_POST['phone'] = '+' . $clean;
    
    return $is_valid;
}
```

### 4. OTP Verification System

```php
// functions.php

// Generate and send OTP after registration
add_action('sofir/auth/user_registered', 'send_registration_otp', 10, 2);

function send_registration_otp($user_id, $phone_only) {
    if (!$phone_only) {
        return;
    }
    
    $phone = get_user_meta($user_id, 'sofir_phone', true);
    
    // Generate 6-digit OTP
    $otp = sprintf('%06d', mt_rand(0, 999999));
    
    // Store OTP (expires in 10 minutes)
    set_transient('sofir_otp_' . $user_id, $otp, 10 * MINUTE_IN_SECONDS);
    
    // Mark phone as unverified
    update_user_meta($user_id, 'sofir_phone_verified', false);
    
    // Send OTP via SMS
    send_otp_sms($phone, $otp);
}

function send_otp_sms($phone, $otp) {
    // Example with generic SMS gateway
    $message = "Kode OTP Anda: {$otp}. Berlaku 10 menit.";
    
    $api_url = 'https://sms-gateway.com/api/send';
    $api_key = 'YOUR_API_KEY';
    
    wp_remote_post($api_url, [
        'body' => [
            'api_key' => $api_key,
            'phone' => $phone,
            'message' => $message
        ]
    ]);
}

// Verify OTP endpoint
add_action('rest_api_init', 'register_verify_otp_endpoint');

function register_verify_otp_endpoint() {
    register_rest_route('sofir/v1', '/auth/verify-otp', [
        'methods' => 'POST',
        'callback' => 'verify_user_otp',
        'permission_callback' => 'is_user_logged_in'
    ]);
}

function verify_user_otp($request) {
    $user_id = get_current_user_id();
    $otp = sanitize_text_field($request->get_param('otp'));
    
    $stored_otp = get_transient('sofir_otp_' . $user_id);
    
    if ($stored_otp === $otp) {
        update_user_meta($user_id, 'sofir_phone_verified', true);
        delete_transient('sofir_otp_' . $user_id);
        
        return rest_ensure_response([
            'status' => 'success',
            'message' => 'Phone verified successfully'
        ]);
    }
    
    return new WP_REST_Response([
        'message' => 'Invalid OTP'
    ], 400);
}
```

### 5. Rate Limiting Registration

```php
// functions.php
add_filter('sofir/auth/validate_phone', 'rate_limit_registration', 10, 2);

function rate_limit_registration($is_valid, $phone) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'sofir_reg_limit_' . md5($ip);
    
    $attempts = get_transient($key);
    
    if ($attempts === false) {
        // First attempt
        set_transient($key, 1, HOUR_IN_SECONDS);
        return $is_valid;
    }
    
    if ($attempts >= 3) {
        // Max 3 registrations per hour
        add_filter('sofir/auth/register_error', function() {
            return 'Too many registration attempts. Please try again later.';
        });
        return false;
    }
    
    // Increment attempts
    set_transient($key, $attempts + 1, HOUR_IN_SECONDS);
    return $is_valid;
}
```

## 🎨 Frontend Integration

### 1. AJAX Form with Custom Styling

```html
<!-- custom-register.html -->
<div class="custom-register-form">
    <h2>Daftar Sekarang</h2>
    
    <form id="phone-register-form">
        <div class="form-group">
            <label for="phone">Nomor HP</label>
            <input 
                type="tel" 
                id="phone" 
                name="phone" 
                placeholder="08123456789"
                required
            >
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" required>
                Saya setuju dengan <a href="/terms">Syarat & Ketentuan</a>
            </label>
        </div>
        
        <button type="submit" id="register-btn">
            <span class="btn-text">Daftar</span>
            <span class="btn-loader" style="display:none;">Loading...</span>
        </button>
    </form>
    
    <p class="login-link">
        Sudah punya akun? <a href="/login">Login di sini</a>
    </p>
</div>

<script>
document.getElementById('phone-register-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var btn = document.getElementById('register-btn');
    var phone = document.getElementById('phone').value;
    
    // Show loading state
    btn.disabled = true;
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loader').style.display = 'inline';
    
    // Call API
    wp.apiFetch({
        path: '/sofir/v1/auth/register',
        method: 'POST',
        data: {
            phone_only: true,
            phone: phone
        }
    }).then(function(response) {
        if (response.status === 'success') {
            // Show success message
            alert('Registrasi berhasil! Redirecting...');
            
            // Redirect
            window.location.href = response.redirect || '/dashboard';
        }
    }).catch(function(error) {
        // Show error
        alert('Error: ' + error.message);
        
        // Reset button
        btn.disabled = false;
        btn.querySelector('.btn-text').style.display = 'inline';
        btn.querySelector('.btn-loader').style.display = 'none';
    });
});
</script>
```

### 2. Phone Input with Country Code

```html
<div class="phone-input-wrapper">
    <select id="country-code" class="country-code">
        <option value="+62" selected>🇮🇩 +62</option>
        <option value="+60">🇲🇾 +60</option>
        <option value="+65">🇸🇬 +65</option>
        <option value="+66">🇹🇭 +66</option>
    </select>
    
    <input 
        type="tel" 
        id="phone-number" 
        placeholder="812345678"
        pattern="[0-9]{9,12}"
    >
</div>

<script>
// Combine country code with phone number on submit
document.querySelector('form').addEventListener('submit', function(e) {
    var countryCode = document.getElementById('country-code').value;
    var phoneNumber = document.getElementById('phone-number').value;
    var fullPhone = countryCode + phoneNumber;
    
    // Use fullPhone for registration
    console.log('Full phone:', fullPhone);
});
</script>
```

## 🔐 Security Examples

### 1. Block Disposable Phone Numbers

```php
// functions.php
add_filter('sofir/auth/validate_phone', 'block_disposable_phones', 10, 2);

function block_disposable_phones($is_valid, $phone) {
    // List of known disposable/temporary phone service prefixes
    $blocked_prefixes = [
        '+62999', // Example disposable service
        '+62888', // Example temp number service
    ];
    
    foreach ($blocked_prefixes as $prefix) {
        if (substr($phone, 0, strlen($prefix)) === $prefix) {
            return false;
        }
    }
    
    return $is_valid;
}
```

### 2. Check Phone Against Blacklist

```php
// functions.php
add_filter('sofir/auth/validate_phone', 'check_phone_blacklist', 10, 2);

function check_phone_blacklist($is_valid, $phone) {
    global $wpdb;
    
    $blacklist = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}phone_blacklist WHERE phone = %s",
        $phone
    ));
    
    if ($blacklist > 0) {
        add_filter('sofir/auth/register_error', function() {
            return 'This phone number is not allowed to register.';
        });
        return false;
    }
    
    return $is_valid;
}
```

### 3. Verify Phone with External Service

```php
// functions.php
add_filter('sofir/auth/validate_phone', 'verify_phone_with_service', 10, 2);

function verify_phone_with_service($is_valid, $phone) {
    // Example with Twilio Lookup API
    $twilio_sid = 'YOUR_TWILIO_SID';
    $twilio_token = 'YOUR_TWILIO_TOKEN';
    
    $url = "https://lookups.twilio.com/v1/PhoneNumbers/{$phone}";
    
    $response = wp_remote_get($url, [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode("{$twilio_sid}:{$twilio_token}")
        ]
    ]);
    
    if (is_wp_error($response)) {
        return $is_valid;
    }
    
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    // Check if phone is valid and not VoIP
    if (!isset($body['phone_number']) || !isset($body['carrier'])) {
        return false;
    }
    
    return $is_valid;
}
```

## 📊 Analytics & Tracking

### 1. Track Registration Source

```php
// functions.php
add_action('sofir/auth/user_registered', 'track_registration_source', 10, 2);

function track_registration_source($user_id, $phone_only) {
    // Get UTM parameters
    $utm_source = isset($_GET['utm_source']) ? sanitize_text_field($_GET['utm_source']) : '';
    $utm_medium = isset($_GET['utm_medium']) ? sanitize_text_field($_GET['utm_medium']) : '';
    $utm_campaign = isset($_GET['utm_campaign']) ? sanitize_text_field($_GET['utm_campaign']) : '';
    
    // Save to user meta
    if ($utm_source) {
        update_user_meta($user_id, 'registration_source', $utm_source);
    }
    if ($utm_medium) {
        update_user_meta($user_id, 'registration_medium', $utm_medium);
    }
    if ($utm_campaign) {
        update_user_meta($user_id, 'registration_campaign', $utm_campaign);
    }
    
    // Save registration method
    update_user_meta($user_id, 'registration_method', $phone_only ? 'phone' : 'email');
    
    // Save timestamp
    update_user_meta($user_id, 'registration_timestamp', current_time('mysql'));
    
    // Save IP address
    update_user_meta($user_id, 'registration_ip', $_SERVER['REMOTE_ADDR']);
}
```

### 2. Custom Conversion Tracking

```php
// functions.php
add_action('sofir/auth/user_registered', 'track_conversion', 10, 2);

function track_conversion($user_id, $phone_only) {
    if (!$phone_only) {
        return;
    }
    
    // Track to Google Analytics
    ?>
    <script>
    gtag('event', 'conversion', {
        'send_to': 'AW-XXXXX/XXXXX',
        'value': 1.0,
        'currency': 'IDR',
        'transaction_id': '<?php echo $user_id; ?>'
    });
    </script>
    <?php
    
    // Track to Facebook Pixel
    ?>
    <script>
    fbq('track', 'CompleteRegistration', {
        content_name: 'Phone Registration',
        value: 1.0,
        currency: 'IDR'
    });
    </script>
    <?php
}
```

## 🔔 Notification Examples

### 1. Admin Email on New Registration

```php
// functions.php
add_action('sofir/auth/user_registered', 'notify_admin_new_user', 10, 2);

function notify_admin_new_user($user_id, $phone_only) {
    $user = get_userdata($user_id);
    $phone = get_user_meta($user_id, 'sofir_phone', true);
    
    $to = get_option('admin_email');
    $subject = 'New User Registration';
    
    $message = sprintf(
        "New user registered:\n\n" .
        "Username: %s\n" .
        "Phone: %s\n" .
        "Method: %s\n" .
        "Time: %s\n\n" .
        "View user: %s",
        $user->user_login,
        $phone,
        $phone_only ? 'Phone Only' : 'Full Registration',
        current_time('mysql'),
        admin_url('user-edit.php?user_id=' . $user_id)
    );
    
    wp_mail($to, $subject, $message);
}
```

### 2. Slack Notification

```php
// functions.php
add_action('sofir/auth/user_registered', 'notify_slack_new_user', 10, 2);

function notify_slack_new_user($user_id, $phone_only) {
    $user = get_userdata($user_id);
    $phone = get_user_meta($user_id, 'sofir_phone', true);
    
    $webhook_url = 'YOUR_SLACK_WEBHOOK_URL';
    
    $message = [
        'text' => 'New User Registration',
        'attachments' => [
            [
                'color' => 'good',
                'fields' => [
                    [
                        'title' => 'Username',
                        'value' => $user->user_login,
                        'short' => true
                    ],
                    [
                        'title' => 'Phone',
                        'value' => $phone,
                        'short' => true
                    ],
                    [
                        'title' => 'Method',
                        'value' => $phone_only ? 'Phone Only' : 'Full',
                        'short' => true
                    ],
                    [
                        'title' => 'Time',
                        'value' => current_time('mysql'),
                        'short' => true
                    ]
                ]
            ]
        ]
    ];
    
    wp_remote_post($webhook_url, [
        'body' => json_encode($message),
        'headers' => ['Content-Type' => 'application/json']
    ]);
}
```

## 📱 Mobile App Integration

### 1. React Native Example

```javascript
// RegisterScreen.js
import React, { useState } from 'react';
import { View, TextInput, Button, Alert } from 'react-native';

const RegisterScreen = () => {
  const [phone, setPhone] = useState('');
  const [loading, setLoading] = useState(false);

  const handleRegister = async () => {
    setLoading(true);
    
    try {
      const response = await fetch('https://yoursite.com/wp-json/sofir/v1/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          phone_only: true,
          phone: phone
        })
      });
      
      const data = await response.json();
      
      if (data.status === 'success') {
        Alert.alert('Success', 'Registration successful!');
        // Navigate to next screen
      } else {
        Alert.alert('Error', data.message);
      }
    } catch (error) {
      Alert.alert('Error', error.message);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View>
      <TextInput
        placeholder="Phone Number"
        value={phone}
        onChangeText={setPhone}
        keyboardType="phone-pad"
      />
      <Button
        title={loading ? 'Loading...' : 'Register'}
        onPress={handleRegister}
        disabled={loading}
      />
    </View>
  );
};

export default RegisterScreen;
```

### 2. Flutter Example

```dart
// register_screen.dart
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class RegisterScreen extends StatefulWidget {
  @override
  _RegisterScreenState createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _phoneController = TextEditingController();
  bool _loading = false;

  Future<void> _register() async {
    setState(() => _loading = true);
    
    try {
      final response = await http.post(
        Uri.parse('https://yoursite.com/wp-json/sofir/v1/auth/register'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({
          'phone_only': true,
          'phone': _phoneController.text
        })
      );
      
      final data = jsonDecode(response.body);
      
      if (data['status'] == 'success') {
        // Show success
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Registration successful!'))
        );
        
        // Navigate to next screen
      } else {
        // Show error
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(data['message']))
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e'))
      );
    } finally {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Padding(
        padding: EdgeInsets.all(20),
        child: Column(
          children: [
            TextField(
              controller: _phoneController,
              decoration: InputDecoration(
                labelText: 'Phone Number',
                hintText: '08123456789'
              ),
              keyboardType: TextInputType.phone,
            ),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: _loading ? null : _register,
              child: Text(_loading ? 'Loading...' : 'Register')
            )
          ],
        ),
      ),
    );
  }
}
```

## 🧪 Testing Examples

### 1. PHPUnit Test

```php
// tests/test-phone-registration.php
class PhoneRegistrationTest extends WP_UnitTestCase {
    
    public function test_phone_registration_success() {
        $request = new WP_REST_Request('POST', '/sofir/v1/auth/register');
        $request->set_param('phone_only', true);
        $request->set_param('phone', '081234567890');
        
        $response = rest_do_request($request);
        $data = $response->get_data();
        
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('user_id', $data);
    }
    
    public function test_duplicate_phone_rejected() {
        // Create user with phone
        $user_id = wp_create_user('testuser', 'password', 'test@test.com');
        update_user_meta($user_id, 'sofir_phone', '081234567890');
        
        // Try to register with same phone
        $request = new WP_REST_Request('POST', '/sofir/v1/auth/register');
        $request->set_param('phone_only', true);
        $request->set_param('phone', '081234567890');
        
        $response = rest_do_request($request);
        
        $this->assertEquals(400, $response->get_status());
    }
}
```

### 2. JavaScript E2E Test (Cypress)

```javascript
// cypress/integration/phone-registration.spec.js
describe('Phone Registration', () => {
  it('should register with phone number', () => {
    cy.visit('/register');
    
    cy.get('input[name="sofir_phone"]')
      .type('081234567890');
    
    cy.get('button[type="submit"]').click();
    
    cy.url().should('include', '/dashboard');
    cy.contains('Registration successful');
  });
  
  it('should show error for duplicate phone', () => {
    // First registration
    cy.visit('/register');
    cy.get('input[name="sofir_phone"]').type('081111111111');
    cy.get('button[type="submit"]').click();
    cy.wait(1000);
    
    // Logout
    cy.visit('/logout');
    
    // Try to register again with same phone
    cy.visit('/register');
    cy.get('input[name="sofir_phone"]').type('081111111111');
    cy.get('button[type="submit"]').click();
    
    cy.contains('Phone number already registered');
  });
});
```

## 📚 More Resources

- [PHONE_REGISTRATION_GUIDE.md](PHONE_REGISTRATION_GUIDE.md) - Complete guide (Indonesian)
- [PHONE_REGISTRATION_DOCUMENTATION.md](PHONE_REGISTRATION_DOCUMENTATION.md) - Full documentation (English)
- [QUICK_START_PHONE_REGISTRATION.md](QUICK_START_PHONE_REGISTRATION.md) - Quick start guide
- [README.md](README.md) - Main plugin documentation
