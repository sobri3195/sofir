# Informasi Loyalty Program CPT

## Apakah Loyalty Program Memiliki CPT?

**TIDAK**, Loyalty Program tidak memiliki Custom Post Type (CPT) khusus.

## Mengapa Tidak Ada CPT?

Loyalty Program menggunakan **User Meta** untuk menyimpan data, bukan CPT. Ini adalah desain yang lebih efisien karena:

1. **Data Points**: Disimpan di user meta dengan key `sofir_loyalty_points`
2. **Riwayat**: Disimpan di user meta dengan key `sofir_loyalty_history` (max 50 entries)
3. **Rewards**: Disimpan sebagai option di `sofir_loyalty_rewards`

## Keuntungan Menggunakan User Meta vs CPT

### User Meta (Implementasi Saat Ini):
✅ Lebih cepat - langsung terkait dengan user
✅ Lebih ringan - tidak perlu query post table
✅ Lebih sederhana - tidak perlu manage post status
✅ Built-in relationship - otomatis terhubung ke user

### CPT (Alternatif):
❌ Lebih lambat - perlu query + join ke users table
❌ Lebih kompleks - perlu manage post lifecycle
❌ Overhead lebih besar - post meta + post table
✅ Lebih fleksibel untuk reporting (bisa query kompleks)
✅ Bisa export/import via CPT Library

## Cara Akses Data Loyalty

### Via Admin Panel:
- **SOFIR Control Center → Users Tab** - Ada panel Loyalty Program Settings
- Edit user individual - Bisa lihat meta fields loyalty

### Via REST API:
```
GET  /wp-json/sofir/v1/loyalty/points/{user_id}
GET  /wp-json/sofir/v1/loyalty/history/{user_id}
GET  /wp-json/sofir/v1/loyalty/rewards
POST /wp-json/sofir/v1/loyalty/redeem
```

### Via Shortcode:
```
[sofir_loyalty_points] - Tampilkan points balance
[sofir_loyalty_rewards] - Tampilkan reward catalog
```

### Via Code:
```php
// Get user points
$points = get_user_meta( $user_id, 'sofir_loyalty_points', true );

// Get user history
$history = get_user_meta( $user_id, 'sofir_loyalty_history', true );

// Add points
$manager = \Sofir\Loyalty\Manager::instance();
$manager->add_points( $user_id, 100, 'Custom reward', 'custom_action' );
```

## Jika Butuh CPT untuk Loyalty

Jika Anda butuh CPT untuk loyalty (misalnya untuk reporting atau export), bisa ditambahkan dengan:

1. **loyalty_transaction** CPT - Untuk tracking semua transaksi points
2. **loyalty_reward** CPT - Untuk manage rewards sebagai post
3. Integration dengan **CPT Library** untuk export/import

Namun, untuk use case standar, implementasi user meta sudah optimal dan tidak perlu CPT tambahan.

## Summary

- ✅ Loyalty Program **TIDAK ada CPT menu**
- ✅ Data disimpan di **User Meta** (lebih efisien)
- ✅ Akses via **Admin Panel → Users Tab**
- ✅ Shortcode & REST API tersedia
- ✅ Event hooks untuk custom integration
