<?php
/**
 * =================================================================
 * FILE INDEX UTAMA (ROOT INDEX FILE)
 * =================================================================
 * 
 * Tugas file ini adalah sebagai "pintu gerbang" aplikasi (router).
 * Ia tidak menampilkan HTML apapun, hanya berfungsi untuk mengarahkan
 * pengguna ke halaman yang tepat berdasarkan status login mereka.
 */

// 1. Memulai session untuk bisa memeriksa status login.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Memuat file konfigurasi dan fungsi dasar, terutama untuk $base_url.
// Pastikan path ini benar. __DIR__ memastikan path selalu benar dari lokasi file ini.
require_once __DIR__ . '/includes/functions.php';

// 3. Memeriksa apakah pengguna sudah login atau belum.
if (isset($_SESSION['user_id'])) {
    // Jika ada session 'user_id' (artinya pengguna sudah login),
    // arahkan mereka langsung ke halaman dashboard.
    header("Location: " . $base_url . "/pages/dashboard.php");

} else {
    // Jika tidak ada session 'user_id' (pengguna belum login),
    // arahkan mereka ke halaman login.
    header("Location: " . $base_url . "/login.php");
}

// 4. Wajib: Hentikan eksekusi skrip setelah melakukan redirect.
// Ini untuk memastikan tidak ada kode lain yang dieksekusi setelah pengalihan.
exit();
?>