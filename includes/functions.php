<?php
// Mulai session jika belum ada.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * =================================================================
 * VARIABEL KONFIGURASI GLOBAL
 * =================================================================
 */

// GANTI 'amira-bakery-pos' jika nama folder proyek Anda di server berbeda.
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';

// $_SERVER['SERVER_NAME'] di Laragon sudah otomatis menjadi 'amira-bakery-pos.test'
// Jadi kita tidak perlu menambahkan nama folder lagi.
$base_url = $protocol . "://" . $_SERVER['SERVER_NAME'];

/**
 * =================================================================
 * FUNGSI-FUNGSI UTILITY APLIKASI
 * =================================================================
 */

/**
 * Memeriksa apakah pengguna sudah login. Jika belum, akan diarahkan ke halaman login.
 * @return void
 */
function check_login() 
{
    global $base_url; // Gunakan variabel global $base_url.
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . $base_url . "/login.php");
        exit();
    }
}

/**
 * Memformat angka menjadi format mata uang Rupiah.
 * @param int|float $angka Angka yang akan diformat.
 * @return string String dalam format Rupiah.
 */
function format_rupiah($angka) 
{
    if (!is_numeric($angka)) {
        return "Rp 0";
    }
    return "Rp " . number_format($angka, 0, ',', '.');
}

/**
 * Fungsi debugging "Die and Dump". Mencetak variabel lalu menghentikan eksekusi script.
 * @param mixed $data Variabel atau data yang ingin ditampilkan.
 * @return void
 */
function dd($data) 
{
    echo '<pre style="background-color: #1a202c; color: #a0aec0; padding: 1rem; border-radius: 0.5rem; font-family: monospace;">';
    print_r($data);
    echo '</pre>';
    die();
}
?>