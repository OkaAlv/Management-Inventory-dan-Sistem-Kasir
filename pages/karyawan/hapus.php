<?php
// Selalu sertakan file fungsi untuk check_login() dan koneksi DB
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Pastikan hanya user yang sudah login yang bisa mengakses
check_login();
// Cek Hak Akses (Role Management)
if ($_SESSION['jabatan'] != 'Owner') {
    // Jika bukan Owner, kembalikan ke dashboard dengan pesan error (opsional)
    $_SESSION['error_message'] = "Anda tidak memiliki hak akses untuk membuka halaman tersebut.";
    header("Location: " . $base_url . "/pages/dashboard.php");
    exit();
}
// 1. Validasi ID
// Pastikan ID ada di URL, merupakan angka, dan tidak kosong.
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || empty($_GET['id'])) {
    // Jika tidak valid, kembalikan ke halaman utama tanpa melakukan apapun.
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// 2. Keamanan Tambahan: Mencegah pengguna menghapus akunnya sendiri
// Ini adalah praktik yang baik untuk menghindari user mengunci diri sendiri dari sistem.
if ($id == $_SESSION['user_id']) {
    // Jika user mencoba menghapus diri sendiri, kembalikan ke halaman utama.
    // Anda bisa juga menambahkan pesan error via session jika diinginkan.
    header("Location: index.php");
    exit();
}


// 3. Proses Penghapusan Data
try {
    // Siapkan query DELETE dan jalankan dengan ID yang sudah divalidasi.
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    // Opsi: Tambahkan pesan sukses via session jika diinginkan
    // $_SESSION['success_message'] = "Data karyawan berhasil dihapus.";

} catch (PDOException $e) {
    // Tangani jika terjadi error saat menghapus (misalnya karena foreign key constraint)
    // Opsi: Tambahkan pesan error via session jika diinginkan
    // $_SESSION['error_message'] = "Gagal menghapus data: " . $e->getMessage();
    
    // Untuk saat ini, kita bisa hentikan proses jika ada error database kritis.
    // Dalam aplikasi production, ini harus ditangani dengan lebih baik (logging, pesan error yang ramah).
    die("Error: Tidak dapat menghapus data. Kemungkinan data ini terhubung dengan data lain (misalnya transaksi).");
}

// 4. Redirect Kembali
// Setelah selesai (baik berhasil atau gagal dengan die()), arahkan kembali pengguna
// ke halaman daftar karyawan.
header("Location: index.php");
exit(); // Selalu exit setelah header redirect untuk menghentikan eksekusi skrip.
?>