<?php
/**
 * =======================================================
 * FILE LOGOUT
 * =======================================================
 * 
 * Versi yang benar: Menggunakan $base_url yang terpusat
 * agar portabel dan bekerja di semua lingkungan (Laragon & server live).
 */

// 1. Memulai session agar bisa diakses.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    
// 2. PENTING: Memuat definisi $base_url yang benar dari satu tempat.
require_once __DIR__ . '/includes/functions.php';

// 3. Menghapus semua data dari session.
$_SESSION = [];

// 4. Menghancurkan session secara menyeluruh (termasuk cookie).
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();


// 5. Arahkan kembali ke halaman masuk utama (index.php) menggunakan $base_url.
// Ini akan menghasilkan URL yang benar: http://amira-bakery-pos.test/index.php
header("Location: " . $base_url . "/index.php");
exit(); // Selalu hentikan eksekusi setelah redirect.
?>