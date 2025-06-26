<?php
// api/sse_server.php
session_start();

// Header wajib untuk Server-Sent Events
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// Dapatkan order_id yang sedang dipantau oleh klien ini
$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    exit();
}

$update_dir = __DIR__ . '/../status_updates';
$file_path = $update_dir . '/' . $order_id . '.json';

// Loop ini akan terus berjalan selama koneksi dari browser terbuka
while (true) {
    // Cek apakah file notifikasi untuk order_id ini sudah ada
    if (file_exists($file_path)) {
        // Baca isinya
        $data = file_get_contents($file_path);
        
        // Kirim data ke browser dengan format SSE
        // "data: " adalah prefix wajib, diakhiri dengan dua kali baris baru "\n\n"
        echo "data: " . $data . "\n\n";
        
        // Hapus file agar tidak dikirim berulang kali
        unlink($file_path);

        // Hentikan script setelah mengirim notifikasi
        break;
    }

    // Wajib ada untuk membersihkan output buffer
    ob_flush();
    flush();

    // Tunggu 1 detik sebelum cek lagi, untuk mengurangi beban server
    sleep(1);
}