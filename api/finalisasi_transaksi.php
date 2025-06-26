<?php
// Lokasi: /api/finalisasi_transaksi.php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
session_start();

// Periksa otorisasi
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit();
}

$request_data = json_decode(file_get_contents('php://input'), true);
$request_order_id = $request_data['order_id'] ?? null;

$pending_transaction = $_SESSION['qris_pending_transaction'] ?? null;

// Validasi untuk memastikan transaksi yang akan difinalisasi adalah transaksi yang benar
if (!$pending_transaction || $pending_transaction['order_id'] !== $request_order_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Transaksi tidak valid atau sudah diproses.']);
    exit();
}

$cart_items = $pending_transaction['cart_items'];
$nama_customer = $pending_transaction['nama_customer'];
$user_id = $pending_transaction['user_id'];
$total = 0;

try {
    $pdo->beginTransaction();

    // Loop untuk menyimpan setiap item dan mengurangi stok
    foreach ($cart_items as $item) {
        $sub_total = $item['harga'] * $item['kuantitas'];
        $total += $sub_total;

        // Simpan ke tabel transactions
        $stmt = $pdo->prepare(
            "INSERT INTO transactions (user_id, nama_customer, product_id, kuantitas, sub_total, order_id_midtrans, status_pembayaran) 
             VALUES (?, ?, ?, ?, ?, ?, 'paid')"
        );
        $stmt->execute([$user_id, $nama_customer, $item['id'], $item['kuantitas'], $sub_total, $request_order_id]);
        
        // Kurangi stok produk
        $stmt_stok = $pdo->prepare("UPDATE products SET stok = stok - ? WHERE id = ?");
        $stmt_stok->execute([$item['kuantitas'], $item['id']]);
    }
    
    $pdo->commit();

    // Hapus data dari session agar tidak bisa diproses ulang
    unset($_SESSION['qris_pending_transaction']);

    echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan!']);

} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    error_log('Finalisasi Gagal: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}