<?php
// Lokasi: /api/buat_transaksi_qris.php

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/midtrans.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit();
}

// --- FUNGSI BARU UNTUK MEMBERSIHKAN STRING ---
// Fungsi ini akan menghapus semua karakter kecuali huruf, angka, spasi, tanda hubung, dan titik.
function sanitize_for_midtrans($string) {
    return preg_replace('/[^a-zA-Z0-9\s\-\.]/', '', $string);
}
// ---------------------------------------------

try {
    \Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
    \Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    $json_data = json_decode(file_get_contents('php://input'), true);
    $cart_items = $json_data['cart_items'] ?? [];
    
    // Gunakan fungsi sanitasi pada nama customer
    $nama_customer = !empty($json_data['nama_customer']) ? sanitize_for_midtrans($json_data['nama_customer']) : 'Pelanggan';

    if (empty($cart_items)) {
        throw new Exception('Keranjang belanja tidak boleh kosong.');
    }

    $total_belanja = 0;
    $item_details = [];
    foreach ($cart_items as $item) {
        $total_belanja += $item['harga'] * $item['kuantitas'];
        $item_details[] = [
            'id'       => $item['id'],
            'price'    => (int)$item['harga'],

            // Gunakan fungsi sanitasi pada nama barang dan batasi panjangnya
            'name'     => substr(sanitize_for_midtrans($item['nama']), 0, 50),
            
            'quantity' => (int)$item['kuantitas'],
            'brand'    => 'Amira Bakery', // Contoh tambahan
            'category' => 'Food'
        ];
    }

    $order_id = 'ORDER-' . time() . '-' . $_SESSION['user_id'];
    
    $params = [
        'payment_type' => 'qris',
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => (int)$total_belanja,
        ],
        'item_details' => $item_details,
        'customer_details' => [
            'first_name' => $nama_customer,
        ]
    ];
    
    $response = \Midtrans\CoreApi::charge($params);
    
    $_SESSION['qris_pending_transaction'] = [
        'order_id' => $order_id,
        'cart_items' => $cart_items,
        'nama_customer' => $nama_customer,
        'user_id' => $_SESSION['user_id']
    ];

    echo json_encode([
        'status' => 'ok',
        'order_id' => $order_id,
        'qr_string' => $response->qr_string,
        'qr_image_url' => $response->actions[0]->url
    ]);

} catch (Exception $e) {
    http_response_code(500);
    error_log('Midtrans API Error: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan internal saat membuat transaksi.'
    ]);
}