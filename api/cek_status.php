<?php
// Lokasi: /api/cek_status.php

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/midtrans.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo json_encode(['status' => 'error', 'message' => 'Order ID tidak ada.']);
    exit();
}

try {
    // Konfigurasi Midtrans
    \Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
    \Midtrans\Config::$isProduction = MIDTRANS_IS_PRODUCTION;

    $status = \Midtrans\Transaction::status($order_id);
    
    $response = ['status' => 'pending'];
    if (is_object($status) && isset($status->transaction_status) && $status->transaction_status == 'settlement') {
        $response['status'] = 'paid';
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}