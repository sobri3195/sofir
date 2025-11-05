# Multi-Vendor Marketplace Module

Module ini menyediakan sistem marketplace multi-vendor lengkap untuk WordPress.

## Fitur

- ✅ Vendor registration & approval system
- ✅ Vendor store management
- ✅ Product management per vendor
- ✅ Commission calculation otomatis
- ✅ Vendor dashboard dengan earnings
- ✅ Withdrawal management
- ✅ REST API lengkap
- ✅ 4 shortcodes siap pakai

## Setup

### 1. Aktifkan Module

Module sudah otomatis aktif setelah plugin SOFIR diaktifkan.

### 2. Configure Settings

1. Buka **Multi-Vendor → Settings**
2. Set commission rate (%)
3. Choose vendor approval method:
   - **Automatic** - Vendor langsung approved
   - **Manual Review** - Butuh approval admin
4. Enable/disable product creation
5. Set minimum withdrawal amount
6. Save settings

## Penggunaan

### Admin Side

**Vendor Management:**
1. Buka **Multi-Vendor** di admin menu
2. View vendors overview dengan statistik
3. Manage vendors di **Multi-Vendor → Vendors**
4. Manage products di **Multi-Vendor → Products**

**Settings:**
- Commission Rate: Persentase komisi platform
- Vendor Approval: Auto atau manual
- Product Creation: Allow vendors create products
- Min Withdrawal: Minimum jumlah withdraw

### Vendor Side

**Menjadi Vendor:**

Gunakan shortcode di halaman manapun:
```php
[sofir_become_vendor]
```

Atau gunakan REST API:
```bash
POST /wp-json/sofir/v1/vendors/apply
Content-Type: application/json

{
  "store_name": "My Store",
  "store_description": "Store description"
}
```

**Vendor Dashboard:**

```php
[sofir_vendor_dashboard]
```

Dashboard menampilkan:
- Total earnings
- Total products
- Product list dengan edit links

### Frontend Display

**Vendors List:**

```php
[sofir_vendors_list limit="12"]
```

**Vendor Products:**

```php
[sofir_vendor_products vendor_id="10" limit="12"]
```

## REST API

### Get All Vendors

```bash
GET /wp-json/sofir/v1/vendors?per_page=10&page=1
```

**Response:**
```json
[
  {
    "id": 10,
    "name": "My Store",
    "description": "...",
    "logo": "...",
    "owner": "John Doe",
    "commission": "10",
    "earnings": "1000000",
    "url": "...",
    "created": "2024-01-01 00:00:00"
  }
]
```

### Get Vendor Details

```bash
GET /wp-json/sofir/v1/vendors/10
```

### Apply as Vendor

```bash
POST /wp-json/sofir/v1/vendors/apply
Authorization: Bearer {token}
Content-Type: application/json

{
  "store_name": "My Store",
  "store_description": "Description"
}
```

### Get Vendor Products

```bash
GET /wp-json/sofir/v1/vendors/10/products
```

### Get Vendor Earnings

```bash
GET /wp-json/sofir/v1/vendors/earnings
Authorization: Bearer {token}
```

**Response:**
```json
{
  "total_earnings": 1000000,
  "pending_earnings": 50000,
  "available_withdrawal": 1000000
}
```

## Custom Post Types

### vendor_store

Toko vendor dengan metadata:
- `vendor_owner` - User ID pemilik
- `vendor_commission` - Commission rate
- `vendor_earnings` - Total earnings
- `vendor_pending_earnings` - Pending earnings

### vendor_product

Produk vendor dengan metadata:
- `vendor_id` - ID toko vendor
- `product_price` - Harga produk
- Standard WordPress post meta

## Hooks

### Actions

**Vendor applied:**
```php
do_action( 'sofir/vendor/applied', $vendor_id, $user_id );
```

**Commission calculated:**
```php
do_action( 'sofir/vendor/commission_calculated', $vendor_id, $transaction_id, $vendor_earning, $commission );
```

### Filters

**Add custom vendor data:**
```php
add_filter( 'sofir/vendor/data', function( $data, $vendor ) {
    $data['custom_field'] = get_post_meta( $vendor->ID, 'custom', true );
    return $data;
}, 10, 2 );
```

## Commission System

### How it Works

1. Customer membeli produk vendor
2. Payment gateway process payment
3. Hook `sofir/payment/status_changed` triggered
4. Module calculate commission:
   - Platform commission = amount × commission_rate%
   - Vendor earning = amount - platform_commission
5. Update vendor earnings
6. Trigger `sofir/vendor/commission_calculated` hook

### Example

**Sale:** Rp 100.000
**Commission Rate:** 10%

- Platform gets: Rp 10.000 (10%)
- Vendor gets: Rp 90.000 (90%)

## User Roles

### sofir_vendor

Capabilities:
- `read`
- `edit_posts`
- `edit_published_posts`
- `publish_posts`
- `delete_posts`
- `delete_published_posts`
- `upload_files`

Role diberikan otomatis saat user apply jadi vendor.

## Styling

Add CSS untuk styling vendor components:

```css
.sofir-vendor-dashboard {}
.sofir-vendor-stats {}
.sofir-stat-box {}
.sofir-vendor-products {}
.sofir-product-item {}
.sofir-vendors-grid {}
.sofir-vendor-card {}
.sofir-vendor-products-grid {}
.sofir-product-card {}
```

## Customization

### Custom Commission per Vendor

```php
add_filter( 'sofir/vendor/commission_rate', function( $rate, $vendor_id ) {
    // VIP vendors get lower commission
    if ( get_post_meta( $vendor_id, 'vip_vendor', true ) ) {
        return 5; // 5% instead of default
    }
    return $rate;
}, 10, 2 );
```

### Custom Vendor Approval

```php
add_action( 'sofir/vendor/applied', function( $vendor_id, $user_id ) {
    // Send notification to admin
    wp_mail(
        get_option('admin_email'),
        'New Vendor Application',
        'User ' . $user_id . ' applied as vendor.'
    );
}, 10, 2 );
```

## Security

- ✅ Capability checks
- ✅ Nonce verification
- ✅ User authentication
- ✅ Input sanitization
- ✅ Output escaping
- ✅ CSRF protection

## Development

### Testing

1. Create test vendor:
```php
$vendor_id = wp_insert_post([
    'post_title' => 'Test Store',
    'post_type' => 'vendor_store',
    'post_status' => 'publish',
]);

update_post_meta( $vendor_id, 'vendor_owner', get_current_user_id() );
```

2. Create test product:
```php
$product_id = wp_insert_post([
    'post_title' => 'Test Product',
    'post_type' => 'vendor_product',
    'post_status' => 'publish',
]);

update_post_meta( $product_id, 'vendor_id', $vendor_id );
update_post_meta( $product_id, 'product_price', '100000' );
```

## Roadmap

- [ ] Vendor payout/withdrawal system
- [ ] Vendor analytics dashboard
- [ ] Vendor ratings & reviews
- [ ] Product approval workflow
- [ ] Vendor communication system
- [ ] Commission history report
- [ ] Multi-currency support

## License

GPL-2.0+

## Support

- Documentation: `/modules/multivendor/README.md`
- Issues: GitHub Issues
- Support: support@sofir.com
