# Phone Number Only Registration - Documentation

SOFIR supports user registration with phone number only, without requiring email or password. This feature is perfect for mobile-first applications or businesses targeting Indonesian users.

## Key Features

### 1. Phone-Only Registration
- Users only need to input their phone number
- System automatically generates username and dummy email
- Password is auto-generated securely
- Users are logged in immediately after registration

### 2. Phone Number Login
- Registered users can login with phone number only
- No need to remember passwords
- Perfect for apps using OTP/SMS verification

## Usage

### A. Using Shortcode

#### Phone-Only Registration
```
[sofir_register_form phone_only="true"]
```

**Parameters:**
- `phone_only` (boolean, default: false) - Enable phone-only registration mode
- `redirect` (string, default: home_url) - Redirect URL after successful registration

**Example:**
```
[sofir_register_form phone_only="true" redirect="/dashboard"]
```

#### Full Registration (Default)
```
[sofir_register_form]
```
This form displays:
- Username (required)
- Email (required)
- Phone Number (optional)
- Password (required)

### B. Using Gutenberg Block

1. Open Gutenberg editor
2. Click "+" button to add block
3. Search for "SOFIR Register Form"
4. Select the block and configure:
   - **Phone Only**: Toggle on/off for phone-only registration
   - **Redirect URL**: Destination URL after successful registration

### C. Using REST API

#### Endpoint: Phone-Only Registration

**URL:** `/wp-json/sofir/v1/auth/register`

**Method:** `POST`

**Body (Phone Only):**
```json
{
    "phone_only": true,
    "phone": "081234567890"
}
```

**Response (Success):**
```json
{
    "status": "success",
    "user_id": 123,
    "message": "Registration successful"
}
```

**Response (Error):**
```json
{
    "message": "Phone number already registered"
}
```

#### Endpoint: Full Registration

**Body (Full Registration):**
```json
{
    "phone_only": false,
    "username": "johndoe",
    "email": "john@example.com",
    "phone": "081234567890",
    "password": "securepassword123"
}
```

#### Endpoint: Phone Number Login

**URL:** `/wp-json/sofir/v1/auth/phone-login`

**Method:** `POST`

**Body:**
```json
{
    "phone": "081234567890"
}
```

**Response (Success):**
```json
{
    "status": "success",
    "user_id": 123,
    "message": "Login successful"
}
```

**Response (Error):**
```json
{
    "message": "User not found"
}
```

## User Data Structure

When a user registers with phone-only:

```php
// WordPress User Data
username: "user_081234567890"  // Auto-generated from phone
email: "user_081234567890@phone.local"  // Dummy email
password: [random 12 characters]  // Auto-generated secure password

// User Meta
sofir_phone: "081234567890"  // Actual phone number
sofir_phone_only_registration: true  // Phone-only registration flag
```

## JavaScript Integration

The JavaScript file is automatically loaded when the shortcode is used:

```javascript
// File: assets/js/auth.js
// Automatically handles:
// - Form submission via AJAX
// - Phone-only mode detection
// - Success/error handling
// - Auto redirect after success
```

## Hook Integration

### Action Hooks

```php
// After user successfully registers
do_action('sofir/auth/user_registered', $user_id, $phone_only);

// After user logs in with phone
do_action('sofir/auth/phone_login', $user_id);
```

### Filter Hooks

```php
// Modify redirect URL
$redirect = apply_filters('sofir/auth/register_redirect', $redirect, $user_id);

// Custom phone number validation
$is_valid = apply_filters('sofir/auth/validate_phone', true, $phone);

// Custom username generation
$username = apply_filters('sofir/auth/generate_username', $username, $phone);
```

## Phone Number Validation

Currently, the plugin accepts any phone number format. For custom validation, use the filter hook:

```php
add_filter('sofir/auth/validate_phone', function($is_valid, $phone) {
    // Validate Indonesian numbers only (08xxx or +62xxx)
    if (!preg_match('/^(08|\\+62)\\d{8,12}$/', $phone)) {
        return false;
    }
    return $is_valid;
}, 10, 2);
```

## OTP/SMS Integration

The plugin doesn't include OTP/SMS systems, but can be easily integrated:

```php
// Hook after registration
add_action('sofir/auth/user_registered', function($user_id, $phone_only) {
    if ($phone_only) {
        $phone = get_user_meta($user_id, 'sofir_phone', true);
        
        // Send OTP SMS to $phone
        send_otp_sms($phone);
        
        // Set user status as pending verification
        update_user_meta($user_id, 'phone_verified', false);
    }
}, 10, 2);
```

## Admin Panel

### Viewing Phone-Only Users

Users registered with phone-only can be viewed at:
1. WordPress Admin → Users
2. Edit any user
3. Scroll to "Phone Number" section

Phone-only registered users have:
- Username format: `user_081234567890`
- Dummy email: `user_081234567890@phone.local`
- Meta field: `sofir_phone_only_registration = true`

### Managing User Phone Numbers

Admins can edit user phone numbers:
1. Open WordPress Admin → Users
2. Click Edit on the desired user
3. Scroll to "Phone Number" section
4. Update phone number
5. Click "Update User"

## Security

### Auto-generated Password
Auto-generated passwords use `wp_generate_password(12, true, true)`:
- 12 characters
- Includes special characters
- Cryptographically secure random

### Authentication
Phone-only users still use WordPress authentication system:
- Passwords stored with bcrypt hash
- Sessions use WordPress auth cookies
- Supports "Remember Me" functionality

### Phone Number Privacy
Phone numbers are stored as user meta:
- Not exposed in public user data
- Only admin and the user can view it
- Can be encrypted with additional plugins

## Use Cases

### 1. E-commerce Application
```
[sofir_register_form phone_only="true" redirect="/checkout"]
```
Users can quickly register at checkout with just a phone number.

### 2. Membership Site
```
[sofir_register_form phone_only="true" redirect="/membership-plans"]
```
Quick registration to view membership plans.

### 3. Directory/Listing
```
[sofir_register_form phone_only="true" redirect="/submit-listing"]
```
Users can submit listings immediately after quick registration.

### 4. Appointment Form
```
[sofir_register_form phone_only="true" redirect="/book-appointment"]
```
Book appointments without complicated registration.

## Troubleshooting

### User can't login with phone
**Solution:** Ensure the `/sofir/v1/auth/phone-login` endpoint is active. Check with:
```bash
curl -X POST https://yoursite.com/wp-json/sofir/v1/auth/phone-login \
  -H "Content-Type: application/json" \
  -d '{"phone":"081234567890"}'
```

### Error "Phone number already registered"
**Solution:** Phone number is already used by another user. Check with:
```php
$users = get_users([
    'meta_key' => 'sofir_phone',
    'meta_value' => '081234567890'
]);
```

### Redirect not working
**Solution:** Ensure redirect URL is valid and accessible. Use absolute URL:
```
[sofir_register_form phone_only="true" redirect="https://yoursite.com/dashboard"]
```

## File References

- **PHP Backend:** `/modules/enhancement/auth.php`
- **JavaScript:** `/assets/js/auth.js`
- **CSS Styling:** `/assets/css/blocks.css`
- **Block Registration:** `/includes/class-blocks-registrar.php`

## Technical Implementation

### Form Rendering

The registration form is rendered with different fields based on `phone_only` parameter:

**Phone-Only Mode:**
- Single phone number field (required)
- Hidden fields for nonce and redirect
- Submit button

**Full Registration Mode:**
- Username field (required)
- Email field (required)
- Phone number field (optional)
- Password field (required)
- Hidden fields for nonce and redirect
- Submit button

### AJAX Submission Flow

1. User submits form
2. JavaScript prevents default submission
3. Collects form data
4. Sends POST request to `/wp-json/sofir/v1/auth/register`
5. Backend validates and creates user
6. Sets authentication cookie
7. Returns success/error response
8. JavaScript redirects on success or shows error

### Username Generation Logic

For phone-only registration:
```php
$username = 'user_' . sanitize_title($phone);
// Example: "081234567890" becomes "user_081234567890"
```

### Email Generation Logic

For phone-only registration:
```php
$email = $username . '@phone.local';
// Example: "user_081234567890@phone.local"
```

This ensures:
- Unique email for each user
- Clearly identifiable as phone-only user
- Won't conflict with real email addresses
- Compatible with WordPress email system

## API Response Codes

### Success Responses
- `200 OK` - Registration/login successful
- Returns JSON with `status`, `user_id`, and `message`

### Error Responses
- `400 Bad Request` - Missing required fields or validation error
- `404 Not Found` - User not found (login endpoint)
- Returns JSON with error `message`

## Browser Compatibility

The registration form works on:
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile browsers (iOS Safari, Chrome Mobile)
- Requires JavaScript enabled
- Uses `wp.apiFetch` (WordPress 5.0+)

## Accessibility

The form includes:
- Proper label associations
- Required field indicators
- Focus states
- ARIA attributes (inherited from WordPress)
- Keyboard navigation support

## Future Enhancements

Potential features for future releases:
- Built-in SMS/OTP verification
- Phone number formatting by country
- Duplicate phone detection UI
- Admin dashboard for phone-only users
- Bulk import users by phone
- Export phone numbers
- Integration with popular SMS gateways (Twilio, Vonage, etc.)

## Support & Documentation

For additional questions, please contact support or read the complete documentation in README.md
