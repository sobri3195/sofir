# Widget Elementor SOFIR untuk Booking & Reservasi

## Ringkasan

Plugin SOFIR sekarang memiliki **12 widget Elementor baru** untuk sistem booking, reservasi, event, order restoran, marketplace, dan e-learning. Widget-widget ini diorganisir dalam **3 kategori baru** untuk kemudahan penggunaan.

Total Widget Elementor: **38 widget** (sebelumnya 26)

## Kategori Widget

### 1. SOFIR Booking & Events (7 widget)
- Daftar Event
- Kalender Event
- Form Registrasi Event
- Form Booking (Universal)
- Menu Restoran
- Form Order Restoran (Dine-in)
- Form Delivery Restoran

### 2. SOFIR E-Commerce (16 widget)
#### Yang Sudah Ada:
- WooCommerce (5): Products, Cart, Checkout, Categories, Account
- EDD (5): Products, Cart, Checkout, Download Button, Categories
- North Commerce (4): Products, Cart, Checkout, Categories

#### Baru:
- Produk Vendor
- Daftar Toko Vendor

### 3. SOFIR E-Learning (3 widget)
- Daftar Kursus
- Progress Kursus
- Kursus Saya

### 4. SOFIR Elements (12 widget - sudah ada)
- Post Feed, Term Feed, Search Form, Map, Contact Info, Review Stats, Visit Chart, Ring Chart, Countdown, Create Post, Dynamic Data, Appointment Form

---

## Dokumentasi Widget

### 1. Event List (Daftar Event)

**Kategori:** SOFIR Booking & Events  
**Ikon:** Kalender  
**Fungsi:** Menampilkan daftar event dalam bentuk grid atau list

#### Kontrol:
- **Events Per Page** - Jumlah event yang ditampilkan (1-100)
- **Layout** - Grid atau List
- **Columns** - 1, 2, 3, 4, atau 6 kolom
- **Gap** - Jarak antar item (0-100px)
- **Show Date** - Tampilkan tanggal event
- **Show Location** - Tampilkan lokasi
- **Show Capacity** - Tampilkan kapasitas/kursi tersedia
- **Show Thumbnail** - Tampilkan gambar unggulan
- **Show Upcoming Only** - Filter hanya event mendatang

#### Penggunaan:
Sempurna untuk halaman daftar event, showcase event di homepage, atau arsip event.

---

### 2. Event Calendar (Kalender Event)

**Kategori:** SOFIR Booking & Events  
**Ikon:** Kalender  
**Fungsi:** Kalender interaktif untuk menampilkan event

#### Kontrol:
- **Default View** - Tampilan Bulan, Minggu, atau Hari
- **Show Navigation** - Tombol navigasi bulan
- **Show Event Details** - Tampilkan detail event di kalender
- **Enable Popup** - Popup detail event saat diklik

#### Fitur:
- Kalender interaktif dengan navigasi bulan
- Penanda event di tanggal kalender
- Klik untuk melihat detail event
- Responsive untuk mobile

#### Penggunaan:
Bagus untuk halaman event, jadwal konferensi, atau kalender booking.

---

### 3. Event Registration Form (Form Registrasi Event)

**Kategori:** SOFIR Booking & Events  
**Ikon:** Form  
**Fungsi:** Form pendaftaran peserta event

#### Kontrol:
- **Event ID** - Event tertentu (0 = post saat ini)
- **Show Event Info** - Tampilkan judul, tanggal, lokasi event
- **Show Capacity** - Tampilkan kursi tersedia
- **Show Terms & Conditions** - Checkbox syarat & ketentuan
- **Button Text** - Teks tombol submit
- **Success Message** - Pesan konfirmasi sukses

#### Field Form:
- Nama Lengkap (wajib)
- Email (wajib)
- Telepon
- Catatan/Permintaan Khusus
- Checkbox Syarat & Ketentuan

#### Penggunaan:
Embed di halaman single event atau halaman registrasi standalone.

---

### 4. Booking Form (Form Booking Universal)

**Kategori:** SOFIR Booking & Events  
**Ikon:** Form  
**Fungsi:** Form booking universal untuk semua jenis post type

#### Kontrol:
- **Booking Type** - Pilih post type apapun
- **Item ID** - Item tertentu (0 = post saat ini)
- **Show Calendar** - Date picker
- **Show Time Slots** - Dropdown pilihan waktu
- **Show Notes Field** - Textarea permintaan khusus
- **Require Payment** - Notice pembayaran diperlukan
- **Button Text** - Teks tombol submit

#### Field Form:
- Nama Lengkap (wajib)
- Email (wajib)
- Telepon (wajib)
- Pilih Tanggal
- Pilih Waktu (09:00 - 17:30, interval 30 menit)
- Jumlah Tamu
- Permintaan Khusus

#### Penggunaan:
Form booking serbaguna untuk appointment, reservasi hotel, sewa mobil, atau item apapun yang bisa di-booking.

---

### 5. Restaurant Menu (Menu Restoran)

**Kategori:** SOFIR Booking & Events  
**Ikon:** Product Categories  
**Fungsi:** Menampilkan menu restoran

#### Kontrol:
- **Items Per Page** - Jumlah menu item (1-100)
- **Layout** - Grid atau List
- **Columns** - 1, 2, 3, 4, atau 6 kolom
- **Gap** - Jarak antar item
- **Show Category Filter** - Filter/tab kategori menu
- **Show Price** - Tampilkan harga item
- **Show Description** - Tampilkan deskripsi item
- **Show Image** - Tampilkan foto item
- **Show Add to Cart** - Tombol tambah ke keranjang/order

#### Penggunaan:
Sempurna untuk website restoran, menu cafe, atau situs food delivery.

---

### 6. Restaurant Order Form (Form Order Dine-in)

**Kategori:** SOFIR Booking & Events  
**Ikon:** Form  
**Fungsi:** Membuat order untuk makan di tempat (dine-in)

#### Kontrol:
- **Show Menu Selection** - Tampilkan pilihan menu dengan checkbox
- **Show Table Number** - Field nomor meja
- **Show Special Requests** - Textarea permintaan khusus
- **Button Text** - Teks tombol submit

#### Field Form:
- Nama Customer (wajib)
- Nomor Meja (wajib)
- Pilihan Menu (multiple dengan jumlah)
- Permintaan Khusus
- Ringkasan Order dengan Total Harga

#### Fitur:
- Kalkulasi ringkasan order real-time
- Pilih multiple menu item
- Kontrol jumlah per item
- Kalkulasi total harga otomatis

#### Penggunaan:
Untuk sistem order meja restoran atau QR code ordering.

---

### 7. Restaurant Delivery Form (Form Delivery Restoran)

**Kategori:** SOFIR Booking & Events  
**Ikon:** Form  
**Fungsi:** Membuat order untuk delivery

#### Kontrol:
- **Show Menu Selection** - Tampilkan pilihan menu
- **Show Delivery Time** - Pilihan waktu delivery
- **Button Text** - Teks tombol submit

#### Field Form:
- Nama Customer (wajib)
- Telepon (wajib)
- Email
- Alamat Pengiriman (wajib)
- Waktu Delivery (ASAP atau jadwal)
- Pilihan Menu
- Catatan/Permintaan Khusus
- Ringkasan Order dengan Biaya Delivery

#### Fitur:
- Kalkulasi biaya delivery
- Pilihan slot waktu (10:00 - 21:30)
- Opsi delivery ASAP
- Kalkulasi total otomatis

#### Penggunaan:
Website food delivery, sistem delivery restoran, atau halaman order online.

---

### 8. Vendor Products (Produk Vendor)

**Kategori:** SOFIR E-Commerce  
**Ikon:** Products  
**Fungsi:** Menampilkan produk dari marketplace vendor

#### Kontrol:
- **Vendor Store ID** - Filter vendor tertentu (0 = semua)
- **Products Per Page** - Jumlah produk (1-100)
- **Layout** - Grid atau List
- **Columns** - 1, 2, 3, 4, atau 6 kolom
- **Show Price** - Tampilkan harga produk
- **Show Vendor Info** - Tampilkan nama vendor/toko
- **Show Rating** - Tampilkan rating produk
- **Show Add to Cart** - Tombol tambah ke keranjang

#### Penggunaan:
Marketplace multi-vendor, halaman profil vendor, atau listing produk.

---

### 9. Vendor Store List (Daftar Toko Vendor)

**Kategori:** SOFIR E-Commerce  
**Ikon:** Sitemap  
**Fungsi:** Menampilkan daftar toko vendor

#### Kontrol:
- **Stores Per Page** - Jumlah toko (1-100)
- **Layout** - Grid atau List
- **Columns** - 1, 2, 3, 4, atau 6 kolom
- **Show Store Logo** - Tampilkan logo vendor
- **Show Description** - Deskripsi toko
- **Show Product Count** - Jumlah produk
- **Show Rating** - Rating toko
- **Show Location** - Lokasi toko

#### Penggunaan:
Homepage marketplace, direktori vendor, atau halaman pencari toko.

---

### 10. Course List (Daftar Kursus)

**Kategori:** SOFIR E-Learning  
**Ikon:** Archive Posts  
**Fungsi:** Menampilkan daftar kursus

#### Kontrol:
- **Courses Per Page** - Jumlah kursus (1-100)
- **Layout** - Grid atau List
- **Columns** - 1, 2, 3, 4, atau 6 kolom
- **Show Price** - Harga kursus
- **Show Instructor** - Nama instruktur
- **Show Duration** - Durasi kursus
- **Show Lessons Count** - Jumlah pelajaran
- **Show Rating** - Rating kursus
- **Show Enroll Button** - Tombol pendaftaran

#### Penggunaan:
Halaman katalog kursus, homepage platform learning, atau kategori kursus.

---

### 11. Course Progress (Progress Kursus)

**Kategori:** SOFIR E-Learning  
**Ikon:** Skill Bar  
**Fungsi:** Menampilkan progress siswa dalam kursus

#### Kontrol:
- **Course ID** - Kursus tertentu (0 = post saat ini)
- **Show Percentage** - Tampilkan persentase penyelesaian
- **Show Lesson List** - Daftar pelajaran dengan status
- **Show Completion Status** - Indikator selesai/sedang berlangsung
- **Progress Bar Color** - Warna progress bar

#### Fitur:
- Progress bar visual
- Tracking penyelesaian per pelajaran
- Kalkulasi persentase
- Badge status penyelesaian

#### Penggunaan:
Dashboard siswa, halaman single kursus, atau halaman progress learning.

---

### 12. My Courses (Kursus Saya)

**Kategori:** SOFIR E-Learning  
**Ikon:** My Account  
**Fungsi:** Menampilkan kursus yang diikuti user

#### Kontrol:
- **Layout** - Grid atau List
- **Columns** - 1, 2, 3, 4, atau 6 kolom
- **Show Progress** - Tampilkan progress bar per kursus
- **Show Continue Button** - Tombol lanjutkan belajar
- **Show Certificate Link** - Link download sertifikat
- **Filter by Status** - Semua, Sedang Berjalan, atau Selesai

#### Fitur:
- Daftar kursus khusus user
- Tracking progress per kursus
- Fungsi lanjutkan belajar
- Download sertifikat (untuk yang selesai)

#### Penggunaan:
Dashboard siswa, halaman profil user, atau section my-account learning.

---

## Implementasi

Semua widget terdaftar di `/modules/elementor/manager.php` dan diorganisir dalam kategori:

```php
$widget_files = [
    // Widget yang sudah ada...
    'event-list',
    'event-calendar',
    'event-registration',
    'booking-form',
    'restaurant-menu',
    'restaurant-order-form',
    'restaurant-delivery-form',
    'vendor-products',
    'vendor-store-list',
    'course-list',
    'course-progress',
    'my-courses',
];
```

## Lokasi File Widget

Semua file widget berada di:
```
/modules/elementor/widgets/
├── event-list.php
├── event-calendar.php
├── event-registration.php
├── booking-form.php
├── restaurant-menu.php
├── restaurant-order-form.php
├── restaurant-delivery-form.php
├── vendor-products.php
├── vendor-store-list.php
├── course-list.php
├── course-progress.php
└── my-courses.php
```

## Best Practices

1. **Forms** - Semua form include proteksi CSRF via `wp_nonce_field()`
2. **Responsive** - Semua widget include kontrol kolom responsive
3. **Styling** - Widget inherit dari `BaseWidget` dengan kontrol style standar
4. **Integrasi** - Widget terintegrasi dengan modul SOFIR yang ada (Events, Restaurant, Multi-Vendor, E-Course)
5. **Fleksibilitas** - Semua opsi tampilan bisa di-toggle via kontrol widget
6. **User Experience** - Kalkulasi real-time, validasi, dan pesan feedback

## Kebutuhan Frontend

Widget mungkin memerlukan JavaScript dan CSS tambahan untuk fitur interaktif:
- Navigasi kalender (`assets/js/calendar.js`)
- Handling submit form (`assets/js/forms.js`)
- Kalkulasi order (`assets/js/restaurant.js`)
- Tracking progress (`assets/js/ecourse.js`)

## Integrasi Backend

Widget terintegrasi dengan REST API endpoint SOFIR yang ada:
- Events: `/sofir/v1/events/`
- Restaurant: `/sofir/v1/restaurant/`
- Multi-Vendor: `/sofir/v1/vendors/`
- E-Course: `/sofir/v1/ecourse/`

---

## Ringkasan

✅ **12 widget Elementor baru** dibuat  
✅ **3 kategori baru** ditambahkan (Booking & Events, E-Learning)  
✅ **Solusi form lengkap** untuk booking, event, order restoran  
✅ **Widget marketplace** untuk produk dan toko vendor  
✅ **Widget e-learning** untuk kursus dan tracking progress  
✅ **Total 38 widget** sekarang tersedia di integrasi SOFIR Elementor

Semua widget mengikuti standar coding SOFIR dan terintegrasi seamless dengan modul yang ada.

---

## Cara Menggunakan

1. **Install Elementor** - Pastikan plugin Elementor sudah terinstall
2. **Buka Elementor Editor** - Edit halaman dengan Elementor
3. **Cari Widget** - Cari kategori "SOFIR Booking & Events", "SOFIR E-Learning", atau "SOFIR E-Commerce"
4. **Drag & Drop** - Tarik widget ke halaman
5. **Konfigurasi** - Atur kontrol sesuai kebutuhan
6. **Publish** - Simpan dan publish halaman

## Support

Untuk pertanyaan atau masalah terkait widget ini, hubungi:
- GitHub Issues
- Support Forum
- Email Support

---

**Version:** 1.0.0  
**Last Updated:** November 2024  
**Compatibility:** Elementor 3.0+, WordPress 6.0+
