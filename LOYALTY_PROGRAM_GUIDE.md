# Panduan Lengkap Loyalty Program SOFIR

## 📌 Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Fitur Utama](#fitur-utama)
3. [Pengaturan Admin](#pengaturan-admin)
4. [Cara Kerja Sistem Poin](#cara-kerja-sistem-poin)
5. [Reward & Penukaran](#reward--penukaran)
6. [Shortcode](#shortcode)
7. [REST API](#rest-api)
8. [Event Hooks](#event-hooks)
9. [Contoh Penggunaan](#contoh-penggunaan)

---

## Pengenalan

**SOFIR Loyalty Program** adalah sistem poin loyalitas yang terintegrasi penuh dengan WordPress. Sistem ini memberikan reward kepada pengguna untuk berbagai aktivitas seperti pendaftaran, login harian, komentar, posting, dan pembelian.

### Keunggulan:

✅ **Otomatis** - Sistem memberikan poin secara otomatis berdasarkan aktivitas pengguna  
✅ **Fleksibel** - Konfigurasi poin untuk setiap aktivitas dapat disesuaikan  
✅ **Terintegrasi** - Bekerja dengan Payment Gateway (Manual, Duitku, Xendit, Midtrans)  
✅ **REST API** - Akses data poin melalui REST API  
✅ **Developer-Friendly** - Event hooks untuk customisasi lanjutan

---

## Fitur Utama

### 1. Sistem Poin Otomatis

| Aktivitas | Poin Default | Deskripsi |
|-----------|--------------|-----------|
| **Sign Up** | 100 poin | Diberikan saat pengguna mendaftar akun baru |
| **Login Harian** | 10 poin | Diberikan sekali per hari saat login pertama |
| **Komentar** | 5 poin | Diberikan saat komentar disetujui |
| **Publish Post** | 20 poin | Diberikan saat menerbitkan post |
| **Pembelian** | 1 poin/Rp | Berdasarkan total pembelian (dapat disesuaikan) |

### 2. Reward System

Sistem reward default:

- **Diskon 10%** - 500 poin
- **Diskon 20%** - 1000 poin  
- **Gratis Ongkir** - 750 poin

Reward dapat dikustomisasi sesuai kebutuhan bisnis Anda.

### 3. User History

Setiap transaksi poin dicatat dengan detail:
- Jumlah poin yang ditambahkan/dikurangi
- Alasan transaksi
- Tanggal dan waktu
- History disimpan hingga 50 entri terakhir

---

## Pengaturan Admin

### Mengakses Pengaturan

1. Login ke WordPress Admin
2. Buka menu **SOFIR → Users**
3. Scroll ke bawah ke section **Loyalty Program**

### Konfigurasi Poin

Di halaman pengaturan, Anda dapat mengatur:

#### **1. Enable/Disable Program**
Toggle untuk mengaktifkan atau menonaktifkan seluruh program loyalitas.

#### **2. Sign Up Bonus**
Poin yang diberikan saat pengguna mendaftar akun baru.

**Default:** 100 poin  
**Contoh:** Jika diset 200, pengguna baru akan langsung mendapat 200 poin.

#### **3. Daily Login Bonus**
Poin yang diberikan untuk login harian (maksimal 1x per hari).

**Default:** 10 poin  
**Catatan:** Sistem mengecek tanggal login terakhir, jadi hanya 1 reward per hari.

#### **4. Comment Posted**
Poin yang diberikan saat komentar disetujui.

**Default:** 5 poin  
**Catatan:** Komentar spam atau pending tidak mendapat poin.

#### **5. Post Published**
Poin yang diberikan saat publish post/artikel.

**Default:** 20 poin  
**Catatan:** Berlaku untuk semua post type.

#### **6. Points per Currency Unit**
Poin yang diberikan per unit mata uang saat pembelian.

**Default:** 1 poin per Rp 1  
**Contoh:**
- Pembelian Rp 100.000 = 100 poin (jika ratio 1)
- Pembelian Rp 100.000 = 1.000 poin (jika ratio 10)

---

## Cara Kerja Sistem Poin

### 1. Pemberian Poin (Add Points)

Poin diberikan secara otomatis melalui WordPress hooks:

```php
// Saat user register
add_action( 'user_register', function( $user_id ) {
    // Otomatis dapat 100 poin (jika enabled)
} );

// Saat user login
add_action( 'wp_login', function( $user_login, $user ) {
    // Dapat 10 poin (maksimal 1x per hari)
}, 10, 2 );

// Saat payment completed
add_action( 'sofir/payment/status_changed', function( $transaction_id, $status ) {
    // Dapat poin berdasarkan total pembelian
}, 10, 2 );
```

### 2. Pengurangan Poin (Deduct Points)

Poin dikurangi saat:
- Pengguna menukar reward
- Admin manual adjustment (via code)

### 3. Validasi Poin

Sistem secara otomatis:
- ✅ Cek saldo poin sebelum redeem
- ✅ Tidak bisa poin minus (minimum 0)
- ✅ Update history setiap transaksi

---

## Reward & Penukaran

### Melihat Reward

Pengguna dapat melihat reward yang tersedia dengan:

**Shortcode:**
```
[sofir_loyalty_rewards]
```

**REST API:**
```
GET /wp-json/sofir/v1/loyalty/rewards
```

### Menukar Reward

**Via Shortcode:**
Tombol "Redeem" otomatis muncul di samping setiap reward (jika poin cukup).

**Via REST API:**
```bash
POST /wp-json/sofir/v1/loyalty/redeem
Content-Type: application/json

{
  "reward_id": "discount_10"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Reward redeemed successfully",
  "remaining_points": 250
}
```

### Customize Rewards

Untuk mengubah atau menambah reward, edit file `modules/loyalty/manager.php` bagian `load_rewards()`:

```php
private function load_rewards(): array {
    return [
        'vip_access' => [
            'id' => 'vip_access',
            'name' => 'VIP Access 1 Bulan',
            'description' => 'Akses VIP premium 30 hari',
            'points_cost' => 2000,
        ],
        // Tambahkan reward lainnya...
    ];
}
```

---

## Shortcode

### 1. [sofir_loyalty_points]

Menampilkan saldo poin pengguna yang sedang login.

**Penggunaan:**
```
[sofir_loyalty_points]
```

**Output:**
```html
<div class="sofir-loyalty-points">
  <h3>Your Loyalty Points</h3>
  <div class="sofir-points-balance">1250</div>
  <p class="sofir-points-label">Points</p>
</div>
```

**Catatan:** Jika user belum login, akan muncul pesan "Please log in to view your points."

### 2. [sofir_loyalty_rewards]

Menampilkan katalog reward yang tersedia.

**Penggunaan:**
```
[sofir_loyalty_rewards]
```

**Output:**
```html
<div class="sofir-loyalty-rewards">
  <h3>Available Rewards</h3>
  <div class="sofir-reward-item can-redeem">
    <h4>10% Discount Coupon</h4>
    <p class="sofir-reward-description">Get 10% off your next purchase</p>
    <div class="sofir-reward-cost">500 points</div>
    <button class="button button-primary sofir-redeem-btn" data-reward-id="discount_10">Redeem</button>
  </div>
  <!-- More rewards... -->
</div>
```

**CSS Classes:**
- `.can-redeem` - User memiliki poin cukup
- `.insufficient-points` - Poin tidak cukup (tombol disabled)

---

## REST API

### Base URL
```
/wp-json/sofir/v1/loyalty/
```

### 1. Get User Points

**Endpoint:**
```
GET /loyalty/points/{user_id}
```

**Permission:** User harus login dan dapat mengakses data user tersebut.

**Response:**
```json
{
  "user_id": 5,
  "points": 1250
}
```

### 2. Get User History

**Endpoint:**
```
GET /loyalty/history/{user_id}
```

**Permission:** User harus login dan dapat mengakses data user tersebut.

**Response:**
```json
[
  {
    "points": 100,
    "reason": "Sign up bonus",
    "date": "2024-01-15 10:30:00"
  },
  {
    "points": 10,
    "reason": "Daily login bonus",
    "date": "2024-01-16 08:15:00"
  },
  {
    "points": -500,
    "reason": "Redeemed: 10% Discount Coupon",
    "date": "2024-01-16 14:20:00"
  }
]
```

### 3. Redeem Reward

**Endpoint:**
```
POST /loyalty/redeem
```

**Permission:** User harus login.

**Request Body:**
```json
{
  "reward_id": "discount_10"
}
```

**Response (Success):**
```json
{
  "status": "success",
  "message": "Reward redeemed successfully",
  "remaining_points": 750
}
```

**Response (Error - Insufficient Points):**
```json
{
  "message": "Insufficient points"
}
```

### 4. Get All Rewards

**Endpoint:**
```
GET /loyalty/rewards
```

**Permission:** Public (tidak perlu login).

**Response:**
```json
[
  {
    "id": "discount_10",
    "name": "10% Discount Coupon",
    "description": "Get 10% off your next purchase",
    "points_cost": 500
  },
  {
    "id": "discount_20",
    "name": "20% Discount Coupon",
    "description": "Get 20% off your next purchase",
    "points_cost": 1000
  }
]
```

---

## Event Hooks

### Action Hooks

#### 1. sofir/loyalty/points_added

Triggered saat poin ditambahkan.

**Parameters:**
- `$user_id` (int) - ID user
- `$points` (int) - Jumlah poin yang ditambahkan
- `$new_total` (int) - Total poin setelah ditambahkan
- `$reason` (string) - Alasan penambahan poin

**Contoh:**
```php
add_action( 'sofir/loyalty/points_added', function( $user_id, $points, $new_total, $reason ) {
    // Kirim notifikasi email
    if ( $new_total >= 1000 ) {
        wp_mail( 
            get_user_by( 'id', $user_id )->user_email,
            'Selamat! Anda mencapai 1000 poin',
            'Anda sekarang bisa menukar reward premium!'
        );
    }
}, 10, 4 );
```

#### 2. sofir/loyalty/points_deducted

Triggered saat poin dikurangi.

**Parameters:**
- `$user_id` (int) - ID user
- `$points` (int) - Jumlah poin yang dikurangi
- `$new_total` (int) - Total poin setelah dikurangi
- `$reason` (string) - Alasan pengurangan poin

**Contoh:**
```php
add_action( 'sofir/loyalty/points_deducted', function( $user_id, $points, $new_total, $reason ) {
    // Log aktivitas penukaran reward
    error_log( "User $user_id redeemed $points points: $reason" );
}, 10, 4 );
```

#### 3. sofir/loyalty/reward_redeemed

Triggered saat reward berhasil ditukar.

**Parameters:**
- `$user_id` (int) - ID user
- `$reward_id` (string) - ID reward
- `$reward` (array) - Data lengkap reward

**Contoh:**
```php
add_action( 'sofir/loyalty/reward_redeemed', function( $user_id, $reward_id, $reward ) {
    // Generate kode kupon diskon
    if ( $reward_id === 'discount_10' ) {
        $coupon_code = 'LOYAL10-' . strtoupper( wp_generate_password( 6, false ) );
        update_user_meta( $user_id, 'pending_coupon', $coupon_code );
        
        // Kirim email dengan kode kupon
        $user = get_user_by( 'id', $user_id );
        wp_mail(
            $user->user_email,
            'Kode Kupon Diskon Anda',
            "Terima kasih! Kode kupon Anda: $coupon_code"
        );
    }
}, 10, 3 );
```

---

## Contoh Penggunaan

### 1. Menampilkan Poin di Template

**Single Post:**
```php
<?php
// Tampilkan poin penulis
if ( function_exists( 'Sofir\Loyalty\Manager::instance' ) ) {
    $loyalty = Sofir\Loyalty\Manager::instance();
    $author_id = get_the_author_meta( 'ID' );
    $points = $loyalty->get_user_points( $author_id );
    
    echo '<div class="author-points">';
    echo '<span>Poin Penulis:</span> ' . number_format( $points );
    echo '</div>';
}
?>
```

### 2. Custom Point Award

**Berikan poin untuk aktivitas custom:**
```php
add_action( 'gform_after_submission', function( $entry, $form ) {
    if ( is_user_logged_in() && function_exists( 'Sofir\Loyalty\Manager::instance' ) ) {
        $loyalty = Sofir\Loyalty\Manager::instance();
        $user_id = get_current_user_id();
        
        // Berikan 50 poin untuk submit form
        $loyalty->add_points( $user_id, 50, 'Submitted contact form' );
    }
}, 10, 2 );
```

### 3. Badge Berdasarkan Poin

**Tampilkan badge di profile:**
```php
function sofir_get_user_badge( $user_id ) {
    $loyalty = Sofir\Loyalty\Manager::instance();
    $points = $loyalty->get_user_points( $user_id );
    
    if ( $points >= 10000 ) {
        return '🏆 Platinum Member';
    } elseif ( $points >= 5000 ) {
        return '⭐ Gold Member';
    } elseif ( $points >= 1000 ) {
        return '🥈 Silver Member';
    } else {
        return '🥉 Bronze Member';
    }
}

// Penggunaan
echo sofir_get_user_badge( get_current_user_id() );
```

### 4. Halaman Leaderboard

**Buat halaman leaderboard top users:**
```php
function sofir_display_leaderboard() {
    global $wpdb;
    
    $top_users = $wpdb->get_results( "
        SELECT user_id, meta_value as points
        FROM {$wpdb->usermeta}
        WHERE meta_key = 'sofir_loyalty_points'
        ORDER BY CAST(meta_value AS UNSIGNED) DESC
        LIMIT 10
    " );
    
    echo '<div class="sofir-leaderboard">';
    echo '<h2>🏆 Top 10 Members</h2>';
    echo '<ol>';
    
    foreach ( $top_users as $user_data ) {
        $user = get_userdata( $user_data->user_id );
        echo '<li>';
        echo '<strong>' . esc_html( $user->display_name ) . '</strong> - ';
        echo number_format( $user_data->points ) . ' points';
        echo '</li>';
    }
    
    echo '</ol>';
    echo '</div>';
}

// Shortcode
add_shortcode( 'sofir_leaderboard', 'sofir_display_leaderboard' );
```

### 5. AJAX Redeem Reward

**JavaScript untuk redeem reward via AJAX:**
```javascript
jQuery(document).ready(function($) {
    $('.sofir-redeem-btn').on('click', function(e) {
        e.preventDefault();
        
        var rewardId = $(this).data('reward-id');
        var button = $(this);
        
        button.prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: '/wp-json/sofir/v1/loyalty/redeem',
            method: 'POST',
            contentType: 'application/json',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            },
            data: JSON.stringify({ reward_id: rewardId }),
            success: function(response) {
                alert(response.message);
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON.message);
                button.prop('disabled', false).text('Redeem');
            }
        });
    });
});
```

---

## Tips & Best Practices

### 1. Keamanan

✅ Selalu validasi poin sebelum redeem  
✅ Gunakan nonce untuk form submission  
✅ Check user capability sebelum adjustment manual

### 2. Performance

✅ History dibatasi 50 entri terakhir untuk efisiensi  
✅ Gunakan caching untuk leaderboard  
✅ Query points by meta_key untuk performa optimal

### 3. User Experience

✅ Tampilkan progress bar menuju reward  
✅ Notifikasi email saat dapat milestone poin  
✅ Gamifikasi dengan badge dan level

### 4. Customization

✅ Gunakan filter hooks untuk modify rewards  
✅ Tambahkan custom point events sesuai kebutuhan  
✅ Integrasikan dengan plugin lain (WooCommerce, EDD, dll)

---

## Troubleshooting

### Poin tidak otomatis bertambah

**Solusi:**
1. Cek apakah Loyalty Program sudah enabled di admin
2. Pastikan user sudah login
3. Cek apakah aktivitas memenuhi syarat (contoh: login bonus hanya 1x per hari)

### Reward tidak bisa ditukar

**Solusi:**
1. Cek saldo poin user
2. Pastikan reward_id valid
3. Cek permission callback di REST API

### History tidak muncul

**Solusi:**
1. History disimpan di user meta `sofir_loyalty_history`
2. Maximum 50 entri terakhir
3. Query dengan: `get_user_meta( $user_id, 'sofir_loyalty_history', true )`

---

## Support & Dokumentasi

- **Plugin Repository:** [GitHub - SOFIR](https://github.com/sofir/sofir)
- **Dokumentasi Lengkap:** Lihat `/modules/loyalty/manager.php`
- **REST API:** `/wp-json/sofir/v1/loyalty/`

---

**Version:** 0.1.0  
**Last Updated:** 2024  
**Status:** ✅ Production Ready
