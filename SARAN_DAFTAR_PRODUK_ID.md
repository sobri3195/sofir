# Fitur Saran Daftar Produk

## Ikhtisar

Fitur **Saran Daftar Produk** meningkatkan Generator Artikel AI SEO dengan menyediakan rekomendasi produk berbasis AI dari Google SERP dan memungkinkan manajemen link produk manual. Fitur ini tersedia untuk tipe artikel Product Roundup, Product Review, dan Comparison.

## Fitur-Fitur

### 1. Saran Produk Berbasis AI
- Dapatkan rekomendasi produk cerdas menggunakan Google Gemini AI
- AI menganalisis query pencarian Anda dan menyarankan 5-10 produk relevan
- Setiap saran mencakup:
  - Nama produk
  - URL produk (placeholder atau link yang disarankan)
  - Deskripsi singkat fitur utama

### 2. Manajemen Produk Manual
- Tambahkan produk secara manual dengan detail kustom
- Edit informasi produk secara inline:
  - Nama Produk
  - URL Produk
  - Deskripsi Produk
- Hapus produk dari daftar
- Kontrol penuh atas data produk

### 3. Manajer Daftar Produk Visual
- Interface tabel yang indah untuk mengelola produk
- Editing real-time dengan update instan
- Penghitung produk menampilkan total jumlah produk
- Desain responsif untuk semua ukuran layar

## Cara Penggunaan

### Mendapatkan Saran AI

1. Navigasi ke **SOFIR → SEO** di admin WordPress
2. Di AI Article Generator, pilih salah satu tipe artikel ini:
   - **Product Roundup**
   - **Product Review**
   - **Comparison Post**
3. Scroll ke bawah ke bagian **📦 Product List Manager**
4. Masukkan query pencarian Anda (misal: "headphone wireless terbaik 2024")
5. Klik **🤖 Get AI Suggestions**
6. AI akan menyarankan 5-10 produk relevan dengan nama, URL, dan deskripsi
7. Edit detail produk apa pun langsung di tabel

### Menambahkan Produk Manual

1. Di bagian **Product List Manager**
2. Klik **➕ Add Product**
3. Baris baru akan muncul di tabel
4. Isi:
   - **Product Name**: Nama produk
   - **Product URL**: Link ke halaman produk (misal: Amazon, website manufaktur)
   - **Description**: Deskripsi singkat fitur utama
5. Ulangi untuk produk tambahan

### Mengedit Informasi Produk

1. Klik pada field mana pun di tabel produk
2. Edit teks langsung
3. Perubahan disimpan otomatis
4. Lanjutkan mengedit field lain sesuai kebutuhan

### Menghapus Produk

1. Klik tombol **Remove** di samping produk mana pun
2. Produk akan dihapus dari daftar segera
3. Penghitung produk update otomatis

### Menghasilkan Artikel dengan Daftar Produk

1. Setelah menambah/mengedit produk, isi detail artikel lainnya:
   - Judul Artikel
   - Target Keyword
   - Tone, Word Count, dll.
2. Klik **Generate Article**
3. AI akan menggunakan daftar produk Anda untuk membuat:
   - Tabel perbandingan produk
   - Bagian produk individual
   - Kelebihan dan kekurangan untuk setiap produk
   - Link pembelian dan CTA
   - Deskripsi produk yang dioptimalkan SEO

## Kasus Penggunaan

### Artikel Product Roundup
- Pencarian: "smartphone terbaik 2024"
- Dapatkan saran AI untuk 10 smartphone teratas
- Edit URL untuk menyertakan link afiliasi
- Hasilkan roundup komprehensif dengan tabel perbandingan

### Artikel Product Review
- Pencarian: "review iPhone 15 Pro"
- Dapatkan saran AI untuk iPhone 15 Pro
- Tambahkan link produk resmi
- Hasilkan review mendalam dengan spesifikasi dan pengujian

### Post Perbandingan
- Pencarian: "iPhone vs Samsung Galaxy"
- Dapatkan saran AI untuk kedua produk
- Tambahkan URL kedua produk
- Hasilkan perbandingan head-to-head

## Detail Teknis

### API Endpoints

#### Get Product Suggestions
```
POST /wp-json/sofir/v1/seo-ai/product-suggestions
```

**Parameter:**
- `query` (required): Query pencarian untuk produk

**Response:**
```json
{
  "products": [
    {
      "name": "Nama Produk",
      "url": "https://example.com/product",
      "description": "Deskripsi singkat fitur utama"
    }
  ]
}
```

### AJAX Actions

#### Get Product Suggestions
```javascript
wp_ajax_sofir_get_product_suggestions
```

**Data:**
- `action`: "sofir_get_product_suggestions"
- `nonce`: AJAX nonce untuk keamanan
- `query`: String query pencarian

### Struktur Data

Produk disimpan sebagai array JSON:
```json
[
  {
    "name": "Nama Produk",
    "url": "https://example.com/product",
    "description": "Deskripsi produk"
  }
]
```

Data ini:
- Dikirim ke API pembuatan artikel
- Digunakan untuk membangun prompt khusus produk
- Disertakan dalam artikel yang dihasilkan dengan format yang tepat

## Integrasi dengan Tipe Artikel

### Product Roundup
- Semua produk dalam daftar disertakan dalam artikel
- Tabel perbandingan menampilkan semua produk side-by-side
- Setiap produk mendapat bagian khusus
- Terbaik untuk 5-10 produk

### Product Review
- Menggunakan produk pertama dalam daftar
- Fokus pada satu produk secara mendalam
- Menyertakan URL produk untuk CTA pembelian
- Terbaik untuk analisis detail

### Comparison Post
- Menggunakan 2-3 produk dari daftar
- Perbandingan head-to-head
- Deklarasi pemenang per kategori
- Terbaik untuk perbandingan langsung

## Best Practices

1. **Query Pencarian**: Spesifik dengan query pencarian Anda
   - ✅ "headphone wireless terbaik dibawah 2 juta 2024"
   - ❌ "headphone"

2. **URL Produk**: Gunakan link yang bersih dan dapat dilacak
   - Tambahkan parameter afiliasi jika diperlukan
   - Gunakan URL pendek untuk tracking lebih baik
   - Test link sebelum menghasilkan artikel

3. **Deskripsi**: Jaga deskripsi tetap ringkas
   - Highlight 1-2 fitur utama
   - Fokus pada unique selling points
   - Gunakan kata-kata kuat

4. **Jumlah Produk**:
   - Product Roundup: 5-10 produk ideal
   - Product Review: 1 produk
   - Comparison: maksimal 2-3 produk

5. **Urutan Produk**: Produk muncul di artikel sesuai urutan daftar
   - Letakkan produk terbaik pertama untuk ranking
   - Pertimbangkan user journey
   - Kelompokkan produk serupa

## Manfaat SEO

1. **Structured Data**: Link produk memungkinkan rich snippets
2. **User Intent**: Mencocokkan produk dengan search intent
3. **Internal Linking**: Hubungkan produk ke halaman kategori
4. **Affiliate Revenue**: Link produk langsung meningkatkan konversi
5. **Kualitas Konten**: Data produk nyata meningkatkan autentisitas

## Tips & Trik

### Untuk Affiliate Marketer
- Tambahkan parameter tracking ke URL
- Test click-through rate
- Update produk secara regular
- Monitor ketersediaan produk

### Untuk Reviewer
- Link ke halaman produk resmi
- Sertakan produk pembanding
- Tambahkan informasi harga di deskripsi
- Update spesifikasi seiring evolusi produk

### Untuk Situs E-commerce
- Link ke halaman produk Anda sendiri
- Sertakan status stok di deskripsi
- Tambahkan penawaran khusus di deskripsi
- Hubungkan ke sistem inventory

## Troubleshooting

### Saran AI Tidak Bekerja
- Cek apakah Google Gemini API key sudah dikonfigurasi
- Verifikasi API key memiliki quota yang cukup
- Coba query pencarian yang lebih spesifik
- Cek konektivitas jaringan

### Produk Tidak Muncul di Artikel
- Pastikan daftar produk memiliki data sebelum menghasilkan
- Cek bahwa tipe artikel sudah diatur dengan benar
- Verifikasi URL produk valid
- Review console untuk error JavaScript

### Masalah Formatting
- Gunakan plain text di field produk
- Hindari karakter khusus di URL
- Jaga deskripsi di bawah 200 karakter
- Test link produk sebelum publikasi

## Peningkatan Masa Depan

Segera hadir:
- Integrasi langsung Google Shopping
- Integrasi Amazon Product API
- Tracking dan update harga
- Saran gambar produk
- Agregasi skor review
- Pengecekan ketersediaan stok

## Dukungan

Untuk masalah atau pertanyaan:
1. Cek dokumentasi ini
2. Review console log untuk error
3. Test dengan query lebih sederhana
4. Hubungi dukungan SOFIR

---

**Versi**: 1.0.0  
**Terakhir Diupdate**: 2024  
**Kompatibilitas**: WordPress 5.8+, PHP 8.0+
