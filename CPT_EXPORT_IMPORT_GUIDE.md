# Panduan Export/Import Custom Post Type - SOFIR

## 🎯 Fitur Export/Import CPT

Fitur ini memungkinkan Anda untuk mengekspor dan mengimpor Custom Post Type lengkap dengan konten, taxonomies, terms, dan metadata ke dalam file JSON yang dapat dibagikan atau digunakan sebagai demo content.

---

## 📦 Ekspor CPT Package

### Cara Menggunakan:

1. **Buka SOFIR Control Center** → **Content Tab**
2. **Scroll ke bagian "Export CPT Package"**
3. **Pilih Post Types** yang ingin diekspor dengan mencentang checkbox
4. **Preview Data** (Opsional):
   - Klik tombol **"Preview Data"** untuk melihat ringkasan data yang akan diekspor
   - Preview akan menampilkan:
     - Jumlah posts per post type
     - Daftar fields yang akan diekspor
     - Taxonomies dan terms terkait dengan jumlahnya
5. **Masukkan nama file** (tanpa ekstensi)
6. **Klik "⬇ Download Ekspor"** untuk mengunduh file JSON

### Data yang Diekspor:

- ✅ **Post Type Definitions**: Struktur dan konfigurasi CPT
- ✅ **Taxonomy Definitions**: Struktur dan konfigurasi taxonomies
- ✅ **Terms**: Semua terms dari taxonomies terkait
- ✅ **Posts**: Semua konten posts (title, content, excerpt, status, dll)
- ✅ **Post Meta**: Semua metadata posts (fields, custom fields)
- ✅ **Taxonomy Relationships**: Relasi antara posts dan terms
- ✅ **Featured Images**: ID thumbnail untuk setiap post

### Format File Export:

File yang dihasilkan adalah JSON dengan struktur:
```json
{
  "version": "1.0.0",
  "plugin": "SOFIR",
  "timestamp": "2024-01-15 10:30:00",
  "post_types": {...},
  "taxonomies": {...},
  "posts": [...],
  "terms": [...],
  "meta": [...]
}
```

---

## 📥 Import CPT Package

### Cara Menggunakan:

1. **Buka SOFIR Control Center** → **Content Tab**
2. **Scroll ke bagian "Import CPT Package"**
3. **Klik "Browse"** atau **"Choose File"**
4. **Pilih file JSON** hasil ekspor dari SOFIR
5. **Klik "⬆ Import Paket CPT"**
6. **Tunggu proses import** selesai
7. **Lihat notifikasi sukses** dengan detail:
   - Jumlah Post Types imported
   - Jumlah Taxonomies imported
   - Jumlah Terms imported
   - Jumlah Posts imported

### Proses Import:

1. **Validasi File**: Memastikan format JSON valid dan berasal dari SOFIR
2. **Import Post Types**: Mendaftarkan semua CPT definitions
3. **Import Taxonomies**: Mendaftarkan semua taxonomy definitions
4. **Import Terms**: Membuat terms (skip jika sudah ada)
5. **Import Posts**: Membuat posts baru (skip jika post dengan slug yang sama sudah ada)
6. **Import Meta**: Menambahkan metadata ke posts
7. **Set Taxonomy Relationships**: Menghubungkan posts dengan terms
8. **Flush Rewrite Rules**: Refresh permalink structure

### Fitur Smart Import:

- 🔄 **Duplicate Detection**: Tidak akan membuat duplikat post jika slug sudah ada
- 🔗 **ID Mapping**: Otomatis memetakan ID lama ke ID baru
- ✅ **Safe Import**: Tidak akan menghapus data yang sudah ada
- 🎯 **Preserves Relationships**: Mempertahankan semua relasi antar data

---

## 🎨 UI Features

### Export Section:

- **Checkbox untuk setiap Post Type** dengan label dan slug
- **Preview Button** untuk melihat data detail sebelum ekspor
- **Preview Panel** menampilkan:
  - Post count per CPT
  - List of fields
  - Taxonomies dengan term count dan term list
- **Custom filename** input untuk nama file ekspor
- **Download button** diaktifkan setelah memilih minimal 1 post type

### Import Section:

- **File uploader** dengan validasi tipe file (.json, .zip)
- **Support ZIP files**: Jika diupload ZIP, akan otomatis extract file JSON
- **Clear instructions** tentang format file yang diterima
- **Success notification** dengan detail lengkap hasil import

---

## 💡 Use Cases

### 1. Demo Content untuk Template
Ekspor CPT dengan sample content untuk distribusikan bersama theme/plugin sebagai demo content.

### 2. Backup & Migration
Backup CPT structure dan content sebelum update atau migrasi ke site lain.

### 3. Development to Production
Transfer CPT definitions dan sample data dari development ke production environment.

### 4. Content Sharing
Share CPT structure dan content antar team members atau clients.

### 5. Testing & Staging
Clone CPT structure dan data untuk testing di staging environment.

---

## 🔧 Technical Details

### Export Function
```php
CptManager::instance()->export_cpt_package( $post_types );
```
Returns: Array dengan complete package data

### Import Function
```php
CptManager::instance()->import_cpt_package( $package );
```
Returns: Array dengan import results (counts & errors)

### Preview Function
```php
CptManager::instance()->get_export_preview( $post_types );
```
Returns: Array dengan preview data (tidak include posts content)

---

## 🚀 REST API Integration (Coming Soon)

Future enhancement akan menambahkan REST API endpoints:
- `GET /wp-json/sofir/v1/cpt/export?post_types=listing,event`
- `POST /wp-json/sofir/v1/cpt/import`

---

## 📋 Requirements

- WordPress 5.0+
- PHP 8.0+
- SOFIR Plugin aktif
- User dengan capability `manage_options`

---

## ⚠️ Important Notes

1. **Featured Images**: Export hanya menyimpan ID thumbnail, bukan file imagenya. Untuk full migration termasuk images, gunakan plugin migration lengkap.

2. **User IDs**: Post author IDs akan dipertahankan. Pastikan user dengan ID tersebut ada di site tujuan, atau posts akan dimiliki oleh current user.

3. **File Size**: Untuk site dengan banyak content, file JSON bisa besar. Pastikan PHP `memory_limit` dan `upload_max_filesize` mencukupi.

4. **Rewrite Rules**: Setelah import, rewrite rules akan di-flush otomatis. Jika ada masalah permalink, kunjungi Settings → Permalinks dan save.

---

## 🆘 Troubleshooting

### Export tidak berfungsi:
- Pastikan minimal 1 post type dipilih
- Check browser console untuk error JavaScript
- Pastikan PHP `output_buffering` tidak mengganggu download

### Import gagal:
- Pastikan file format JSON valid
- Check error message untuk detail spesifik
- Verify file size tidak melebihi `upload_max_filesize`
- Pastikan permission folder uploads mencukupi

### Posts tidak muncul setelah import:
- Check post status (bisa jadi masih draft)
- Flush permalink: Settings → Permalinks → Save
- Check apakah user memiliki permission untuk melihat post type

---

## 📞 Support

Untuk pertanyaan atau bantuan:
- GitHub Issues: [SOFIR Repository]
- Email: support@sofir.dev
- Documentation: https://docs.sofir.dev

---

**Happy Exporting & Importing! 🎉**
