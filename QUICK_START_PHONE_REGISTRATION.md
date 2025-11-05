# Quick Start: Phone-Only Registration

Panduan cepat untuk mengaktifkan dan menggunakan fitur registrasi dengan nomor HP saja di SOFIR.

## 🚀 Mulai Cepat (5 Menit)

### 1. Aktifkan Registrasi User

Pastikan WordPress mengizinkan registrasi user:

1. Login ke WordPress Admin
2. Pergi ke **Settings → General**
3. Centang **"Anyone can register"**
4. Klik **Save Changes**

### 2. Tambahkan Form Registrasi

Pilih salah satu metode berikut:

#### A. Menggunakan Shortcode (Paling Mudah)

Tambahkan shortcode ini ke halaman mana saja:

```
[sofir_register_form phone_only="true"]
```

**Dengan redirect custom:**
```
[sofir_register_form phone_only="true" redirect="/dashboard"]
```

#### B. Menggunakan Gutenberg Block

1. Edit halaman dengan Gutenberg
2. Klik tombol **[+]** untuk tambah block
3. Cari **"SOFIR Register Form"**
4. Toggle **"Phone Only"** ke ON
5. Isi **Redirect URL** (opsional)
6. Publish halaman

### 3. Testing

1. Buka halaman yang berisi form registrasi
2. Input nomor HP (contoh: 081234567890)
3. Klik tombol **Register**
4. Anda akan otomatis login dan redirect

## ✅ Fitur Yang Sudah Aktif

Setelah instalasi, fitur-fitur ini langsung bisa digunakan:

- ✅ Registrasi hanya dengan nomor HP
- ✅ Auto-generate username
- ✅ Auto-generate secure password
- ✅ Auto-login setelah registrasi
- ✅ Validasi nomor HP duplikat
- ✅ REST API endpoints
- ✅ User meta management
- ✅ Admin user profile integration

## 📱 Use Cases Populer

### E-commerce: Quick Checkout

```
[sofir_register_form phone_only="true" redirect="/checkout"]
```

User bisa langsung checkout setelah registrasi cepat.

### Booking/Appointment

```
[sofir_register_form phone_only="true" redirect="/book-appointment"]
```

User registrasi cepat untuk booking appointment.

### Membership Access

```
[sofir_register_form phone_only="true" redirect="/membership-plans"]
```

User registrasi untuk akses membership plans.

### Submit Listing

```
[sofir_register_form phone_only="true" redirect="/submit-listing"]
```

User registrasi untuk submit business listing.

## 🔧 Konfigurasi Lanjutan

### Custom Redirect Berdasarkan User Role

Tambahkan kode ini ke `functions.php`:

```php
add_filter('sofir/auth/register_redirect', function($redirect, $user_id) {
    $user = get_userdata($user_id);
    
    if (in_array('customer', $user->roles)) {
        return home_url('/customer-dashboard');
    }
    
    if (in_array('vendor', $user->roles)) {
        return home_url('/vendor-dashboard');
    }
    
    return $redirect;
}, 10, 2);
```

### Validasi Format Nomor HP Indonesia

Tambahkan kode ini ke `functions.php`:

```php
add_filter('sofir/auth/validate_phone', function($is_valid, $phone) {
    // Hanya terima nomor format Indonesia (08xxx atau +62xxx)
    if (!preg_match('/^(08|\+62)\d{8,12}$/', $phone)) {
        return false;
    }
    return $is_valid;
}, 10, 2);
```

### Custom Username Generation

Tambahkan kode ini ke `functions.php`:

```php
add_filter('sofir/auth/generate_username', function($username, $phone) {
    // Generate username dengan prefix custom
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    return 'customer_' . $clean_phone;
}, 10, 2);
```

### Kirim SMS Setelah Registrasi

Tambahkan kode ini ke `functions.php`:

```php
add_action('sofir/auth/user_registered', function($user_id, $phone_only) {
    if ($phone_only) {
        $phone = get_user_meta($user_id, 'sofir_phone', true);
        
        // Kirim SMS welcome message
        // Contoh menggunakan Twilio, Vonage, atau SMS gateway Indonesia
        send_welcome_sms($phone, $user_id);
    }
}, 10, 2);

function send_welcome_sms($phone, $user_id) {
    // Implementasi SMS gateway Anda di sini
    // Contoh: curl ke API SMS gateway
}
```

## 🔐 Security Best Practices

### 1. Aktifkan HTTPS

Pastikan website menggunakan HTTPS untuk keamanan data.

### 2. Rate Limiting (Opsional)

Batasi jumlah registrasi per IP untuk mencegah spam:

```php
add_filter('sofir/auth/validate_phone', function($is_valid, $phone) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $registrations = get_transient('sofir_reg_' . $ip) ?: 0;
    
    if ($registrations >= 5) {
        return false; // Max 5 registrasi per jam per IP
    }
    
    set_transient('sofir_reg_' . $ip, $registrations + 1, HOUR_IN_SECONDS);
    return $is_valid;
}, 10, 2);
```

### 3. Verify Phone dengan OTP (Recommended)

Setelah registrasi, kirim OTP untuk verifikasi:

```php
add_action('sofir/auth/user_registered', function($user_id, $phone_only) {
    if ($phone_only) {
        $phone = get_user_meta($user_id, 'sofir_phone', true);
        
        // Generate OTP
        $otp = wp_generate_password(6, false, false);
        update_user_meta($user_id, 'sofir_otp', $otp);
        update_user_meta($user_id, 'sofir_phone_verified', false);
        
        // Kirim OTP via SMS
        send_otp_sms($phone, $otp);
        
        // Redirect ke halaman verify OTP
        add_filter('sofir/auth/register_redirect', function() {
            return home_url('/verify-phone');
        });
    }
}, 10, 2);
```

## 🎨 Styling Form

### Custom CSS

Tambahkan CSS custom untuk styling form:

```css
.sofir-register {
    max-width: 500px;
    margin: 3em auto;
    padding: 3em;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.sofir-register input[type="tel"] {
    padding: 15px;
    font-size: 1.1em;
    border: 2px solid #ddd;
    border-radius: 8px;
}

.sofir-register button {
    padding: 15px 30px;
    font-size: 1.1em;
    background: #28a745;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
}

.sofir-register button:hover {
    background: #218838;
    transform: translateY(-2px);
}
```

### Dark Mode Support

```css
@media (prefers-color-scheme: dark) {
    .sofir-register {
        background: #1a1a1a;
        color: #fff;
    }
    
    .sofir-register input[type="tel"] {
        background: #2a2a2a;
        color: #fff;
        border-color: #444;
    }
}
```

## 📊 Tracking & Analytics

### Track Registrasi dengan Google Analytics

```php
add_action('sofir/auth/user_registered', function($user_id, $phone_only) {
    if ($phone_only) {
        // Track event ke Google Analytics
        ?>
        <script>
        gtag('event', 'phone_registration', {
            'event_category': 'user',
            'event_label': 'phone_only'
        });
        </script>
        <?php
    }
}, 10, 2);
```

### Track dengan Facebook Pixel

```php
add_action('sofir/auth/user_registered', function($user_id, $phone_only) {
    if ($phone_only) {
        ?>
        <script>
        fbq('track', 'CompleteRegistration', {
            content_name: 'Phone Only Registration'
        });
        </script>
        <?php
    }
}, 10, 2);
```

## 🐛 Troubleshooting

### Form tidak muncul
- Pastikan shortcode ditulis dengan benar
- Check apakah plugin SOFIR sudah aktif
- Periksa browser console untuk error JavaScript

### Error "Phone number required"
- Pastikan field phone number terisi
- Check validasi HTML5 di browser
- Coba disable browser extensions yang block forms

### Error "Phone number already registered"
- Nomor HP sudah digunakan user lain
- Cek di WordPress Admin → Users
- Filter by meta key: `sofir_phone`

### Redirect tidak berfungsi
- Pastikan URL redirect valid dan accessible
- Gunakan absolute URL, bukan relative
- Check redirect sudah tidak ada trailing slash

### User tidak auto-login
- Check PHP session aktif
- Pastikan cookies tidak di-block browser
- Periksa HTTPS configuration

## 📚 Dokumentasi Lengkap

Untuk informasi lebih detail, baca:
- [PHONE_REGISTRATION_GUIDE.md](PHONE_REGISTRATION_GUIDE.md) - Panduan lengkap (ID)
- [PHONE_REGISTRATION_DOCUMENTATION.md](PHONE_REGISTRATION_DOCUMENTATION.md) - Full documentation (EN)
- [README.md](README.md) - Plugin documentation

## 💬 Support

Butuh bantuan? Hubungi:
- Email: support@sofir.id
- GitHub Issues: https://github.com/sofir/sofir/issues
- Documentation: https://sofir.id/docs
