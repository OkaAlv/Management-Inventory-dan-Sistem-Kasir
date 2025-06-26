Amira Bakery - Aplikasi Point of Sale (POS)
(Ganti dengan path ke screenshot dashboard Anda)

Aplikasi Point of Sale (POS) berbasis web yang dirancang untuk Amira Bakery. Dibangun dengan PHP native, aplikasi ini menyediakan fungsionalitas kasir, manajemen produk, manajemen karyawan, dan pelaporan penjualan yang interaktif, serta dilengkapi dengan integrasi pembayaran digital melalui QRIS.

Fitur Utama

- Dashboard Analitik: Tampilan ringkasan penjualan yang dinamis dengan filter waktu (Harian, Mingguan, Bulanan, Tahunan, Kustom) dan dilengkapi chart visual untuk statistik penjualan serta produk terlaris.
- Halaman Kasir (POS): Antarmuka yang intuitif untuk melakukan transaksi penjualan dengan keranjang belanja real-time.
- Integrasi Pembayaran QRIS: Mendukung pembayaran digital menggunakan QRIS melalui Payment Gateway Midtrans (mode Sandbox untuk development).
- Manajemen Produk: Fungsionalitas CRUD (Create, Read, Update, Delete) untuk mengelola data produk dan stok.
- Manajemen Karyawan: Fungsionalitas CRUD untuk mengelola data karyawan.
- Manajemen Hak Akses: Pembatasan akses berdasarkan peran (Owner vs. Karyawan). Hanya Owner yang dapat mengakses halaman sensitif.
- Laporan Penjualan: Laporan transaksi harian dan bulanan yang mendetail dengan fungsionalitas ekspor ke Excel.
- Profil Pengguna: Pengguna dapat mengelola informasi dan foto profil pribadinya.
- Desain Responsif: Antarmuka yang dirancang menggunakan Tailwind CSS agar dapat diakses dengan baik di berbagai ukuran layar.

Teknologi yang Digunakan
- Backend: PHP 8.0+ (Native, Prosedural & OOP)
- Frontend: HTML, Tailwind CSS, JavaScript (ES6+)
- Database: MySQL / MariaDB
- Grafik (Charts): Chart.js
- Payment Gateway: Midtrans
- Manajemen Dependency PHP: Composer

Prasyarat & Persyaratan
Sebelum menjalankan aplikasi ini, pastikan sistem Anda memenuhi persyaratan berikut:

- PHP Versi 7.4 atau lebih baru (Direkomendasikan PHP 8.0+).
- Web Server: Apache atau Nginx.
- Database: MySQL atau MariaDB.
- Composer: Terinstall secara global di sistem Anda.
- Ekstensi PHP: pdo_mysql, json, mbstring (biasanya sudah aktif secara default).
- Akun Sandbox Midtrans: Untuk mendapatkan API Key untuk testing pembayaran.

Panduan Instalasi
Ikuti langkah-langkah berikut untuk menginstal dan menjalankan aplikasi ini di lingkungan lokal (misalnya XAMPP, Laragon).

1. Clone Repository
Buka terminal atau Git Bash, lalu clone repository ini ke dalam direktori server web Anda (htdocs untuk XAMPP, www untuk Laragon).

Bash

git clone https://github.com/NAMA_USER_ANDA/NAMA_REPO_ANDA.git
cd NAMA_REPO_ANDA
2. Install Dependencies
Jalankan Composer untuk mengunduh library yang dibutuhkan (seperti Midtrans).

Bash

composer install
3. Konfigurasi Database
Buka phpMyAdmin (atau klien database lainnya).

Buat sebuah database baru, contoh: amira_bakery_db.

Impor file database.sql (pastikan Anda sudah mengekspornya dari proyek Anda) ke dalam database yang baru saja Anda buat. Ini akan membuat semua tabel dan data awal yang dibutuhkan.

4. Konfigurasi Lingkungan
Salin file config/database.example.php dan beri nama config/database.php.

Salin file config/midtrans.example.php dan beri nama config/midtrans.php.

Buka config/database.php dan sesuaikan dengan kredensial database lokal Anda:

PHP

// config/database.php
$db_host = 'localhost';
$db_name = 'amira_bakery_db'; // Sesuaikan dengan nama DB Anda
$db_user = 'root';            // User database Anda
$db_pass = '';                // Password database Anda
Buka config/midtrans.php dan masukkan API Key dari akun Sandbox Midtrans Anda:

PHP

// config/midtrans.php
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-xxxxxxxxxxxx'); // Ganti dengan Server Key Anda
define('MIDTRANS_IS_PRODUCTION', false);
Buka config/app.php (atau di mana Anda mendefinisikan base URL) dan sesuaikan nama folder proyek.

PHP

// config/app.php
$base_url = 'http://localhost/amira-bakery-pos'; // Sesuaikan jika nama foldernya berbeda
5. Jalankan Aplikasi
Jalankan server Apache dan MySQL Anda melalui XAMPP.

Buka browser dan akses URL proyek Anda: http://localhost/NAMA_FOLDER_PROYEK/

Aplikasi akan mengarahkan Anda ke halaman login.

6. Akun Default
Username: admin

Password: admin123

Struktur Folder Proyek
/
├── api/              # Logika untuk API (QRIS, cek status, dll.)
├── assets/           # File statis seperti CSS kustom, gambar, dll.
├── config/           # File konfigurasi (database, midtrans)
├── includes/         # Komponen yang digunakan berulang (header, sidebar, functions)
├── pages/            # File-file halaman utama (dashboard, kasir, produk)
├── uploads/          # Folder untuk menyimpan file yang di-upload (foto profil)
├── vendor/           # Folder dependensi dari Composer (otomatis dibuat)
├── .htaccess         # Konfigurasi URL (jika ada)
├── composer.json     # Definisi proyek untuk Composer
└── index.php         # Titik masuk utama aplikasi
Kontribusi & Lisensi
Proyek ini adalah proyek portofolio/studi kasus. Silakan fork, modifikasi, dan gunakan sebagai referensi. Jika Anda menemukan bug atau ingin menyarankan perbaikan, jangan ragu untuk membuat Issue atau Pull Request.

Dibuat dengan ❤️ oleh [Oka Alvansyah].
Proyek ini dilisensikan di bawah Lisensi MIT.