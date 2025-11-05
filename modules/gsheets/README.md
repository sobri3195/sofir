# Google Sheets Integration Module

Module ini menyediakan integrasi lengkap dengan Google Sheets untuk export dan import data WordPress.

## Fitur

- ✅ Export users, orders, dan posts ke Google Sheets
- ✅ Import data dari Google Sheets
- ✅ Auto-sync real-time dengan webhook
- ✅ Manual export dengan 1 klik
- ✅ OAuth 2.0 authentication
- ✅ REST API endpoints
- ✅ Shortcode untuk export button

## Setup

### 1. Google Cloud Console Setup

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Create new project atau pilih existing project
3. Enable Google Sheets API
4. Create OAuth 2.0 credentials (Client ID & Client Secret)
5. Create API Key
6. Copy Spreadsheet ID dari URL Google Sheets Anda

### 2. WordPress Settings

1. Buka **SOFIR Dashboard → Google Sheets**
2. Enable integration
3. Masukkan credentials:
   - API Key
   - Client ID
   - Client Secret
   - Spreadsheet ID
4. Pilih auto-sync options
5. Save settings

## Penggunaan

### Manual Export

Di halaman admin Google Sheets, klik tombol:
- **Export Users** - Export semua users
- **Export Orders** - Export semua transaksi
- **Export Posts** - Export semua posts

### Auto-Sync

Aktifkan auto-sync options untuk sync otomatis:
- ✅ Sync new users - Otomatis export user baru
- ✅ Sync orders - Otomatis export order/payment baru
- ✅ Sync posts - Otomatis export post yang dipublish

### Shortcode

Tambahkan export button di halaman manapun:

```php
[sofir_sheets_export type="users" text="Export to Sheets"]
```

**Parameters:**
- `type` - Data type: users, orders, posts
- `text` - Button text (optional)

### REST API

**Export Data:**
```bash
POST /wp-json/sofir/v1/gsheets/export
Content-Type: application/json

{
  "type": "users"
}
```

**Import Data:**
```bash
POST /wp-json/sofir/v1/gsheets/import
Content-Type: application/json

{
  "type": "users"
}
```

## Hooks

### Actions

**Export hook:**
```php
do_action( 'sofir/gsheets/exported', $type, $row_count );
```

**Import hook:**
```php
do_action( 'sofir/gsheets/imported', $type, $imported_count );
```

### Filters

**Filter export data:**
```php
add_filter( 'sofir/gsheets/export_data', function( $data, $type ) {
    // Modify data before export
    return $data;
}, 10, 2 );
```

## Data Format

### Users Export

| ID | Username | Email | Phone | Registered | Role |
|----|----------|-------|-------|------------|------|
| 1  | john     | ...   | ...   | ...        | ...  |

### Orders Export

| ID | Gateway | Amount | Status | User ID | Created | Updated |
|----|---------|--------|--------|---------|---------|---------|
| TRX-... | duitku | 100000 | completed | 1 | ... | ... |

### Posts Export

| ID | Title | Type | Author | Date | URL |
|----|-------|------|--------|------|-----|
| 1  | Hello | post | Admin  | ... | ... |

## Security

- ✅ API credentials encrypted in database
- ✅ OAuth 2.0 authentication
- ✅ Admin-only access
- ✅ Nonce verification
- ✅ Capability checks

## Troubleshooting

### Error: "Failed to export"

1. Pastikan API Key valid
2. Pastikan Spreadsheet ID benar
3. Pastikan Google Sheets API enabled
4. Check WordPress error logs

### Error: "Invalid credentials"

1. Regenerate OAuth credentials
2. Update Client ID & Client Secret
3. Re-authorize access

## Development

### Add Custom Export Type

```php
add_filter( 'sofir/gsheets/export_types', function( $types ) {
    $types['custom'] = 'Custom Data';
    return $types;
} );

add_action( 'sofir/gsheets/export_custom', function() {
    $data = [
        [ 'Column 1', 'Column 2' ],
        [ 'Value 1', 'Value 2' ],
    ];
    
    return $data;
} );
```

## License

GPL-2.0+

## Support

- Documentation: `/modules/gsheets/README.md`
- Issues: GitHub Issues
- Support: support@sofir.com
