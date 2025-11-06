# CPT Library Tab - Documentation

## Overview
Tab **Library** adalah fitur baru di SOFIR Control Center yang memisahkan ekspor/impor Custom Post Type dari tab Content, memberikan fokus khusus untuk manajemen library CPT.

## Lokasi
**SOFIR Control Center → Library Tab**

## Fitur Utama

### 1. Export CPT Package
- **Fungsi**: Mengekspor Custom Post Type beserta konten, taxonomies, dan terms ke file JSON
- **Format Output**: JSON file yang dapat dibagikan
- **Yang Diekspor**:
  - Definisi Custom Post Type (CPT)
  - Field & metadata configuration
  - Taxonomies & terms
  - Filter & query settings
  - Konten posts (opsional)

#### Cara Menggunakan:
1. Pilih Custom Post Type yang ingin diekspor (checkbox)
2. Klik **Preview Data** untuk melihat ringkasan data
3. Isi nama file (default: `sofir-cpt-YYYY-MM-DD`)
4. Klik **Download Ekspor** untuk mengunduh file JSON

### 2. Import CPT Package
- **Fungsi**: Upload dan install paket Custom Post Type dari file JSON
- **Format Input**: File JSON hasil ekspor dari SOFIR
- **Proses Otomatis**:
  - Mendaftarkan CPT beserta field dan filter
  - Import taxonomies dan terms
  - Import posts dengan metadata
  - Flush rewrite rules

#### Cara Menggunakan:
1. Upload file JSON hasil ekspor
2. Klik **Import Paket CPT**
3. Sistem akan otomatis mendaftarkan CPT
4. Setelah import, refresh permalink di **Settings → Permalinks**

### 3. CPT Library Guide
Panel informasi yang menjelaskan:
- Apa itu CPT Library
- Yang diekspor
- Use cases (clone website, backup, migrasi, dll)

## Struktur Kode

### File Baru
- `/includes/class-admin-library-panel.php` - Panel utama untuk Library tab

### Class yang Dimodifikasi
- `Sofir\Admin\Manager` - Menambahkan tab 'library' dan render method
- `Sofir\Admin\ContentPanel` - Menghapus kode ekspor/impor (dipindah ke LibraryPanel)

### Class Helper
- `Sofir\Admin\LibraryPanel` - Class utama untuk Library panel
- `Sofir\Admin\CptExporter` - Helper untuk ekspor CPT package
- `Sofir\Admin\CptImporter` - Helper untuk import CPT package

## REST API & AJAX

### AJAX Endpoints
- `wp_ajax_sofir_get_export_preview` - Preview data ekspor

### Admin Post Actions
- `admin_post_sofir_export_cpt` - Handle ekspor CPT
- `admin_post_sofir_import_cpt` - Handle import CPT

## Workflow

### Export Flow
1. User memilih CPT yang akan diekspor
2. JavaScript mengaktifkan tombol preview & download
3. Preview mengambil data via AJAX
4. Download trigger form submit ke `admin_post_sofir_export_cpt`
5. Server generate JSON dan kirim sebagai download

### Import Flow
1. User upload file JSON
2. Form submit ke `admin_post_sofir_import_cpt`
3. Server parse JSON dan extract package
4. CPT & taxonomy didaftarkan via `CptManager::save_post_type()` dan `save_taxonomy()`
5. Posts di-import dengan metadata & terms
6. Rewrite rules di-flush
7. Redirect ke Library tab dengan success message

## Format JSON Package

```json
{
  "version": "0.1.0",
  "exported": "2024-01-01 00:00:00",
  "post_types": {
    "listing": {
      "args": { ... },
      "fields": { ... },
      "taxonomies": [ ... ]
    }
  },
  "taxonomies": {
    "listing_category": {
      "args": { ... },
      "object_type": [ ... ],
      "filterable": true
    }
  },
  "posts": [
    {
      "post_type": "listing",
      "post_title": "...",
      "post_content": "...",
      "meta": { ... },
      "terms": { ... }
    }
  ]
}
```

## Use Cases

### 1. Clone Website
Ekspor CPT dari website A, import ke website B dengan struktur yang sama.

### 2. Backup & Restore
Backup konfigurasi CPT sebelum perubahan besar, restore jika diperlukan.

### 3. Client Handover
Bagikan template CPT yang sudah dikonfigurasi ke client.

### 4. Development → Production
Migrasi CPT dari environment development ke production.

### 5. CPT Marketplace
Membuat dan membagikan library CPT siap pakai untuk komunitas.

## Validasi & Error Handling

### Validasi Saat Export:
- Minimal satu CPT harus dipilih
- Filename harus valid (sanitized)

### Validasi Saat Import:
- File harus format JSON atau ZIP (ZIP belum diimplementasi penuh)
- JSON harus valid dan memiliki struktur yang benar
- Nonce & capability check untuk keamanan

### Error Messages:
- "Tidak ada post type dipilih untuk diekspor"
- "Format file tidak valid. Gunakan JSON"
- "File JSON tidak valid"
- Success: "Berhasil import X CPT, Y taxonomies, dan Z posts"

## Security

- Nonce verification pada semua form submission
- Capability check: `manage_options` required
- File sanitization untuk upload
- JSON validation sebelum import

## Integrasi dengan CptManager

LibraryPanel menggunakan method yang sudah ada di CptManager:
- `CptManager::get_post_types()` - Ambil daftar CPT
- `CptManager::save_post_type($payload)` - Simpan CPT
- `CptManager::save_taxonomy($payload)` - Simpan taxonomy

## Future Enhancements

1. **ZIP Support**: Full implementation untuk import/export ZIP files
2. **Cloud Library**: Connect ke cloud marketplace untuk download CPT templates
3. **Preview Before Import**: Show preview of CPT structure before importing
4. **Merge Strategies**: Options untuk merge atau replace saat import CPT yang sudah ada
5. **Import Options**: Checkbox untuk pilih apa saja yang di-import (CPT only, with posts, etc)
6. **Export with Media**: Include featured images and media files in export

## Changelog

### Version 0.1.0 (2024)
- Initial release
- Tab Library terpisah dari Content
- Export CPT to JSON
- Import CPT from JSON
- Preview data export via AJAX
- CPT Library Guide panel
