# 🍽️ Dapur Rumpin — Sistem Pemesanan Restoran & Manajemen Terpadu

Aplikasi web pemesanan makanan & minuman dan manajemen operasional restoran berbasis PHP Native dan MySQL. Sistem ini dirancang untuk mendigitalkan proses pemesanan dari meja pelanggan menggunakan QR Code, alur kerja dapur (*kitchen display/queue*), kasir (*point of sale* & konfirmasi pembayaran), hingga dashboard administratif dan laporan bagi pemilik restoran.

---

## 🌟 Fitur Utama

### 1. 📱 Pelanggan (Customer Facing)
- **Pemesanan via Meja / QR Code:** Pelanggan dapat memilih nomor meja secara manual atau otomatis terpilih saat memindai QR Code di meja (`?meja=XX`).
- **Katalog Menu Interaktif:** Filter kategori (Makanan, Minuman, Cemilan), pencarian langsung (*live search*), dan label menu (Favorit, Rekomendasi, Promo).
- **Keranjang Belanja Real-time:** Panel keranjang (*cart drawer*) dinamis dengan kalkulasi harga otomatis.
- **Metode Pembayaran:** Mendukung pembayaran **Tunai di Kasir** atau **QRIS**.
- **Live Order Tracking (`cek-pesanan.php`):** Pelanggan dapat memantau status pesanan secara *real-time* (Menunggu Konfirmasi ➜ Dimasak ➜ Siap Disajikan ➜ Selesai).

### 2. 👨‍🍳 Modul Dapur (`/dapur`)
- **Kitchen Queue Display:** Tampilan antrian pesanan masuk secara langsung.
- **Status Progres Masakan:** Memperbarui status pesanan dari proses memasak hingga makanan siap diantar ke meja pelanggan.
- **Notifikasi Pesanan Baru:** Mendeteksi pesanan baru secara berkala via AJAX polling.

### 3. 💵 Modul Kasir (`/kasir`)
- **Daftar Pesanan & Status Pembayaran:** Memantau transaksi yang belum dibayar dan yang telah lunas.
- **Konfirmasi Pembayaran:** Verifikasi pembayaran pelanggan (Tunai / QRIS).
- **Cetak Struk Transaksi (`cetak_struk.php`):** Format cetak struk siap print untuk pelanggan.

### 4. 📊 Modul Admin & Owner (`/admin`)
- **Dashboard Statistik:** Ringkasan pendapatan, jumlah transaksi, total menu aktif, dan status meja.
- **Manajemen Menu:** Tambah, edit, hapus, upload foto makanan/minuman, serta pengaturan status ketersediaan menu.
- **Manajemen Meja & QR Code:** Kelola nomor meja dan buat QR Code otomatis untuk masing-masing meja.
- **Manajemen Pengguna / Staff:** Kelola akun dan hak akses karyawan (*Owner, Admin, Kasir, Dapur*).
- **Laporan Penjualan:** Rekapitulasi transaksi, filter tanggal, dan statistik penjualan.

---

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP Native (PDO MySQL)
- **Database:** MariaDB / MySQL
- **Frontend:** HTML5, CSS3 Modern (Custom Responsive Design, CSS Grid & Flexbox), Vanilla JavaScript
- **Komunikasi Data:** Asynchronous AJAX (JSON API)
- **Web Server:** Apache (dengan `.htaccess` rewrite & route security)

---

## 📂 Struktur Direktori Proyek

```plaintext
dapur-rumpin/
├── admin/                  # Panel Admin & Owner (Menu, Meja, Staff, Laporan)
│   ├── index.php           # Dashboard statistik
│   ├── laporan.php         # Laporan penjualan
│   ├── meja_*.php          # Manajemen meja & QR
│   ├── menu_*.php          # CRUD katalog menu
│   └── staff_*.php         # Manajemen user/staff
├── api/                    # Endpoint AJAX API untuk interaksi data
│   ├── antrian_dapur.php
│   ├── bayar_pesanan.php
│   ├── cek_status.php
│   ├── notif_pesanan.php
│   ├── pesanan_list.php
│   └── update_status_pesanan.php
├── config/                 # Konfigurasi koneksi database (PDO)
│   └── db.php
├── css/                    # Stylesheet aplikasi
│   ├── dashboard.css       # Style panel admin, kasir, dan dapur
│   └── style.css           # Style halaman pelanggan & utama
├── dapur/                  # Modul Dapur (Kitchen Display)
│   ├── _topbar.php
│   └── antrian.php
├── img/                    # Asset gambar, logo, QRIS, & foto menu
│   └── menu/
├── includes/               # Komponen modular & helper
│   ├── auth_check.php      # Proteksi sesi & role authorization
│   ├── cart_panel.php      # Modal keranjang belanja
│   └── footer.php
├── js/                     # Skrip JavaScript interaktif
│   ├── dashboard.js
│   └── main.js
├── kasir/                  # Modul Kasir (POS & Payment Confirmation)
│   ├── _sidebar.php
│   ├── _topbar.php
│   └── pesanan.php
├── .gitignore              # Daftar file yang diabaikan Git
├── .htaccess               # Konfigurasi Apache & proteksi file
├── 403.php                 # Halaman error Forbidden
├── 404.php                 # Halaman error Not Found
├── cek-pesanan.php         # Tracking pesanan pelanggan
├── cetak_struk.php         # Halaman cetak struk kasir
├── dapur_rumpin.sql        # Skema & data database MySQL
├── index.php               # Halaman utama / landing page
├── kontak.php              # Halaman kontak
├── login.php               # Halaman login multi-role
├── logout.php              # Proses logout
├── menu.php                # Halaman katalog menu & pemesanan
├── proses_checkout.php     # Handler transaksi checkout
└── tentang.php             # Halaman informasi tentang resto
```

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Prasyarat
- Web Server & Database: **XAMPP** / Laragon / LAMP stack (disarankan PHP 8.0 ke atas dan MySQL/MariaDB).
- Git (opsional untuk cloning).

### 2. Clone atau Pindahkan Proyek
Tempatkan folder proyek ini ke dalam root web server lokal:
- Untuk **XAMPP** di Windows:
  ```bash
  C:\xampp\htdocs\dapur-rumpin
  ```
- Atau clone langsung melalui Git:
  ```bash
  cd C:\xampp\htdocs
  git clone https://github.com/Billierdtya/dapur-rumpin.git
  ```

### 3. Import Database
1. Nyalakan service **Apache** dan **MySQL** melalui XAMPP Control Panel.
2. Buka browser dan buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
3. Buat database baru bernama: `dapur_rumpin`.
4. Pilih tab **Import**, lalu pilih file `dapur_rumpin.sql` dari root direktori proyek ini, kemudian klik **Go** / **Kirim**.

### 4. Konfigurasi Koneksi Database
Buka file `config/db.php` jika Anda menggunakan konfigurasi database yang berbeda dari default XAMPP:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dapur_rumpin');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 5. Akses Aplikasi
Buka web browser dan akses alamat berikut:
- **Halaman Pelanggan:** `http://localhost/dapur-rumpin/`
- **Simulasi Pesan di Meja:** `http://localhost/dapur-rumpin/menu.php?meja=1`
- **Halaman Login Staff / Admin:** `http://localhost/dapur-rumpin/login.php`

---

## 👥 Role & Hak Akses Pengguna

| Role | Akses Halaman | Fungsi Utama |
|---|---|---|
| **Pelanggan** | `/index.php`, `/menu.php`, `/cek-pesanan.php` | Memesan menu, memilih meja, memilih pembayaran, melihat status |
| **Dapur** | `/dapur/antrian.php` | Melihat antrian pesanan yang masuk dan memperbarui status masak |
| **Kasir** | `/kasir/pesanan.php`, `/cetak_struk.php` | Konfirmasi pembayaran pelanggan dan mencetak struk transaksi |
| **Admin & Owner** | `/admin/*` | Mengelola data menu, meja, staf, serta memantau laporan pendapatan |

---

## 🔒 Keamanan
- Prepared Statements (PDO) untuk perlindungan terhadap SQL Injection.
- Proteksi CSRF Token pada formulir login dan aksi penting.
- Validasi session dan role-based access control di setiap dashboard internal.
- Proteksi file sensitif melalui `.htaccess`.

---

## 📄 Lisensi
Proyek ini dikembangkan untuk kebutuhan operasional **Dapur Rumpin**. Bebas digunakan dan dikembangkan untuk tujuan edukasi dan referensi.
