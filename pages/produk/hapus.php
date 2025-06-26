<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

check_login();

// 1. Validasi ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// 2. Proses Penghapusan Data
try {
    // Siapkan query DELETE dan jalankan dengan ID yang valid
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

} catch (PDOException $e) {
    // Tangani jika terjadi error foreign key (produk sudah ada di tabel transaksi)
    if ($e->getCode() == 23000) {
        // Ini adalah cara penanganan error yang lebih baik, menggunakan session untuk pesan
        $_SESSION['error_message'] = "Produk tidak bisa dihapus karena sudah tercatat dalam transaksi.";
        // Kemudian redirect kembali, dan di index.php kita bisa menampilkan pesan error ini
        header("Location: index.php"); 
        exit();
    }
    // Untuk error lainnya
    die("Error: Tidak dapat menghapus data produk. " . $e->getMessage());
}

// 3. Redirect kembali ke halaman utama
header("Location: index.php");
exit();
?>