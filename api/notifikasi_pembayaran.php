<?php
// notifikasi.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/midtrans.php';

\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;

try {
    $notif = new \Midtrans\Notification();
} catch (Exception $e) {
    error_log("Gagal memproses notifikasi: " . $e->getMessage());
    http_response_code(500); // Beri tahu midtrans ada error
    exit();
}

$order_id = $notif->order_id;
$status_code = $notif->status_code;
$gross_amount = $notif->gross_amount;
$signature_key = hash('sha512', $order_id . $status_code . $gross_amount . MIDTRANS_SERVER_KEY);

if ($signature_key != $notif->signature_key) {
    error_log("Signature notif tidak valid untuk order_id: " . $order_id);
    http_response_code(403); // Forbidden
    exit();
}

$transaction_status = $notif->transaction_status;
$payment_type = $notif->payment_type;
$fraud_status = $notif->fraud_status;

$status_pembayaran_final = '';

if ($transaction_status == 'settlement') {
    $status_pembayaran_final = 'paid';
} else if ($transaction_status == 'pending') {
    $status_pembayaran_final = 'pending';
} else if ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
    $status_pembayaran_final = 'failed';
}

if (!empty($status_pembayaran_final)) {
    // ---- MODIFIKASI DIMULAI DI SINI ----

    // 1. Lakukan finalisasi transaksi (logika dari file finalisasi_transaksi.php)
    // Ini penting agar stok dan data transaksi tercatat HANYA jika pembayaran lunas ('settlement')
    if ($status_pembayaran_final == 'paid') {
        // Ambil detail transaksi dari DB atau dari tabel sementara jika perlu
        // Lalu jalankan logika INSERT ke transactions dan UPDATE stok
        // NOTE: Logika ini perlu disesuaikan agar bisa mengambil data cart berdasarkan order_id
        // Untuk sekarang, kita asumsikan finalisasi terjadi di sini
    }

    // 2. Buat file notifikasi untuk SSE
    $update_dir = __DIR__ . '/../status_updates';
    if (!is_dir($update_dir)) {
        mkdir($update_dir, 0775, true);
    }
    $file_path = $update_dir . '/' . $order_id . '.json';
    $data_to_write = json_encode([
        'order_id' => $order_id,
        'status' => $status_pembayaran_final,
        'message' => "Pembayaran " . ucfirst($status_pembayaran_final)
    ]);
    file_put_contents($file_path, $data_to_write);

    // ---- MODIFIKASI SELESAI ----
}

// Beri tahu Midtrans bahwa notifikasi sudah diterima
http_response_code(200);