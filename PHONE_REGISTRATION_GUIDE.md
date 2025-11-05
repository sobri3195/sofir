# Panduan Registrasi dengan Nomor HP

SOFIR mendukung registrasi pengguna hanya dengan nomor HP tanpa perlu email atau password. Fitur ini sangat berguna untuk aplikasi mobile-first atau bisnis yang fokus pada pengguna Indonesia.

## Fitur Utama

### 1. Registrasi Hanya dengan Nomor HP
- User cukup input nomor HP saja
- Sistem otomatis generate username dan email dummy
- Password otomatis di-generate secara aman
- User langsung login setelah registrasi

### 2. Login dengan Nomor HP
- User yang sudah register bisa login hanya dengan nomor HP
- Tidak perlu ingat password
- Cocok untuk aplikasi yang menggunakan OTP/SMS verification

## Cara Penggunaan

### A. Menggunakan Shortcode

#### Registrasi Phone-Only
```
[sofir_register_form phone_only="true"]
```

**Parameter:**
- `phone_only` (boolean, default: false) - Aktifkan mode registrasi hanya dengan HP
- `redirect` (string, default: home_url) - URL redirect setelah berhasil registrasi

**Contoh:**
```
[sofir_register_form phone_only="true" redirect="/dashboard"]
```

#### Registrasi Lengkap (Default)
```
[sofir_register_form]
```
Form ini akan menampilkan:
- Username (required)
- Email (required)
- Phone Number (optional)
- Password (required)

### B. Menggunakan Gutenberg Block

1. Buka editor Gutenberg
2. Klik tombol "+" untuk tambah block
3. Cari "SOFIR Register Form"
4. Pilih block dan atur settings:
   - **Phone Only**: Toggle on/off untuk mode registrasi HP saja
   - **Redirect URL**: URL tujuan setelah registrasi sukses

### C. Menggunakan REST API

#### Endpoint: Registrasi Phone-Only

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

#### Endpoint: Registrasi Lengkap

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

#### Endpoint: Login dengan Nomor HP

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

## Struktur Data User

Ketika user registrasi dengan phone-only:

```php
// WordPress User Data
username: "user_081234567890"  // Auto-generated dari phone
email: "user_081234567890@phone.local"  // Email dummy
password: [random 12 karakter]  // Auto-generated secure password

// User Meta
sofir_phone: "081234567890"  // Nomor HP asli
sofir_phone_only_registration: true  // Flag registrasi phone-only
```

## Penggunaan JavaScript

File JavaScript sudah otomatis ter-load saat shortcode digunakan:

```javascript
// File: assets/js/auth.js
// Automatically handles:
// - Form submission via AJAX
// - Phone-only mode detection
// - Success/error handling
// - Auto redirect after success
```

## Integrasi dengan Hook

### Action Hooks

```php
// Setelah user berhasil register
do_action('sofir/auth/user_registered', $user_id, $phone_only);

// Setelah user login dengan phone
do_action('sofir/auth/phone_login', $user_id);
```

### Filter Hooks

```php
// Modifikasi redirect URL
$redirect = apply_filters('sofir/auth/register_redirect', $redirect, $user_id);

// Validasi nomor HP custom
$is_valid = apply_filters('sofir/auth/validate_phone', true, $phone);

// Generate username custom
$username = apply_filters('sofir/auth/generate_username', $username, $phone);
```

## Validasi Nomor HP

Saat ini plugin menerima format nomor HP apa saja. Untuk validasi custom, gunakan filter hook:

```php
add_filter('sofir/auth/validate_phone', function($is_valid, $phone) {
    // Validasi hanya nomor Indonesia (08xxx atau +62xxx)
    if (!preg_match('/^(08|\\+62)\\d{8,12}$/', $phone)) {
        return false;
    }
    return $is_valid;
}, 10, 2);
```

## Integrasi dengan OTP/SMS

Plugin ini tidak include sistem OTP/SMS, tapi bisa mudah diintegrasikan:

```php
// Hook setelah registrasi
add_action('sofir/auth/user_registered', function($user_id, $phone_only) {
    if ($phone_only) {
        $phone = get_user_meta($user_id, 'sofir_phone', true);
        
        // Kirim SMS OTP ke $phone
        send_otp_sms($phone);
        
        // Set user status pending verification
        update_user_meta($user_id, 'phone_verified', false);
    }
}, 10, 2);
```

## Admin Panel

### Melihat User dengan Phone-Only

User yang registrasi dengan phone-only dapat dilihat di:
1. WordPress Admin → Users
2. Edit user mana saja
3. Scroll ke bagian "Phone Number"

User dengan phone-only registration memiliki:
- Username format: `user_081234567890`
- Email dummy: `user_081234567890@phone.local`
- Meta field: `sofir_phone_only_registration = true`

### Mengelola Nomor HP User

Admin bisa edit nomor HP user:
1. Buka WordPress Admin → Users
2. Klik Edit pada user yang diinginkan
3. Scroll ke section "Phone Number"
4. Update nomor HP
5. Klik "Update User"

## Security

### Auto-generated Password
Password yang di-generate otomatis menggunakan `wp_generate_password(12, true, true)`:
- 12 karakter
- Include special characters
- Cryptographically secure random

### Authentication
User dengan phone-only tetap menggunakan WordPress authentication system:
- Password disimpan dengan hash bcrypt
- Session menggunakan WordPress auth cookies
- Support "Remember Me" functionality

### Phone Number Privacy
Nomor HP disimpan sebagai user meta:
- Tidak terekspos di public user data
- Hanya admin dan user itu sendiri yang bisa melihat
- Bisa dienkripsi dengan plugin tambahan

## Use Case

### 1. Aplikasi E-commerce
```
[sofir_register_form phone_only="true" redirect="/checkout"]
```
User cepat registrasi saat checkout dengan nomor HP saja.

### 2. Membership Site
```
[sofir_register_form phone_only="true" redirect="/membership-plans"]
```
Registrasi cepat untuk lihat membership plans.

### 3. Directory/Listing
```
[sofir_register_form phone_only="true" redirect="/submit-listing"]
```
User bisa langsung submit listing setelah registrasi cepat.

### 4. Form Appointment
```
[sofir_register_form phone_only="true" redirect="/book-appointment"]
```
Booking janji temu tanpa ribet registrasi lengkap.

## Troubleshooting

### User tidak bisa login dengan phone
**Solusi:** Pastikan endpoint `/sofir/v1/auth/phone-login` aktif. Check dengan:
```bash
curl -X POST https://yoursite.com/wp-json/sofir/v1/auth/phone-login \
  -H "Content-Type: application/json" \
  -d '{"phone":"081234567890"}'
```

### Error "Phone number already registered"
**Solusi:** Nomor HP sudah digunakan user lain. Cek dengan:
```php
$users = get_users([
    'meta_key' => 'sofir_phone',
    'meta_value' => '081234567890'
]);
```

### Redirect tidak berfungsi
**Solusi:** Pastikan URL redirect valid dan accessible. Gunakan absolute URL:
```
[sofir_register_form phone_only="true" redirect="https://yoursite.com/dashboard"]
```

## Referensi File

- **PHP Backend:** `/modules/enhancement/auth.php`
- **JavaScript:** `/assets/js/auth.js`
- **CSS Styling:** `/assets/css/blocks.css`
- **Block Registration:** `/includes/class-blocks-registrar.php`

## Support & Dokumentasi

Untuk pertanyaan lebih lanjut, silakan hubungi tim support atau baca dokumentasi lengkap di README.md
