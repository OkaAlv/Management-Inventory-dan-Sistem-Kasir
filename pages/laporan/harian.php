<?php
// --- LOGIKA HALAMAN ---
$page_title = 'Laporan Harian';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/header.php';
check_login();

// Set tanggal default ke hari ini. Jika ada tanggal di URL, gunakan itu.
$selected_date = $_GET['tanggal'] ?? date('Y-m-d');

// Proses ekspor ke Excel jika parameter 'export' ada
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=laporan-harian-" . $selected_date . ".xls");
    
    // Query data untuk ekspor
    $stmt_export = $pdo->prepare(
        "SELECT t.*, u.nama as kasir_nama, p.nama_barang, p.varian 
         FROM transactions t
         JOIN users u ON t.user_id = u.id
         JOIN products p ON t.product_id = p.id
         WHERE DATE(t.waktu_transaksi) = ? ORDER BY t.waktu_transaksi ASC"
    );
    $stmt_export->execute([$selected_date]);
    $transactions_export = $stmt_export->fetchAll(PDO::FETCH_ASSOC);

    // Tulis data ke output buffer sebagai tabel HTML (yang akan diunduh sebagai Excel)
    $output = '<table border="1"><thead><tr><th>Tanggal & Waktu</th><th>Customer</th><th>Kasir</th><th>Produk</th><th>Varian</th><th>Kuantitas</th><th>Harga</th><th>Sub Total</th></tr></thead><tbody>';
    $total_pendapatan_export = 0;
    foreach ($transactions_export as $tx) {
        $harga_satuan = $tx['sub_total'] / $tx['kuantitas'];
        $output .= '<tr><td>' . date('d/m/Y H:i', strtotime($tx['waktu_transaksi'])) . '</td><td>' . htmlspecialchars($tx['nama_customer']) . '</td><td>' . htmlspecialchars($tx['kasir_nama']) . '</td><td>' . htmlspecialchars($tx['nama_barang']) . '</td><td>' . htmlspecialchars($tx['varian']) . '</td><td>' . $tx['kuantitas'] . '</td><td align="right">' . $harga_satuan . '</td><td align="right">' . $tx['sub_total'] . '</td></tr>';
        $total_pendapatan_export += $tx['sub_total'];
    }
    $output .= '<tr><td colspan="7" align="right"><b>Total Pendapatan</b></td><td align="right"><b>' . $total_pendapatan_export . '</b></td></tr>';
    $output .= '</tbody></table>';
    echo $output;
    exit();
}

// Ambil data untuk tampilan halaman biasa
$stmt = $pdo->prepare(
    "SELECT t.*, u.nama as kasir_nama, p.nama_barang, p.varian 
     FROM transactions t
     JOIN users u ON t.user_id = u.id
     JOIN products p ON t.product_id = p.id
     WHERE DATE(t.waktu_transaksi) = ? ORDER BY t.waktu_transaksi ASC"
);
$stmt->execute([$selected_date]);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_pendapatan = array_sum(array_column($transactions, 'sub_total'));


// --- TAMPILAN HALAMAN ---
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Area Konten Utama -->
<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Keuntungan Harian</h1>
    </header>

    <main class="flex-1 p-6 bg-gray-50">
        <!-- Filter Section -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <form action="harian.php" method="GET" class="flex flex-wrap items-center gap-4">
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Pilih Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md" value="<?= htmlspecialchars($selected_date) ?>">
                </div>
                <div class="pt-5">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Tampilkan Laporan</button>
                </div>
                <div class="pt-5">
                    <a href="harian.php?tanggal=<?= htmlspecialchars($selected_date) ?>&export=excel" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Ekspor ke Excel</a>
                </div>
            </form>
        </div>

        <!-- Transaction Table -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Data Penjualan Tanggal: <?= date('d F Y', strtotime($selected_date)) ?></h2>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-4 text-left">Waktu</th>
                            <th class="py-3 px-4 text-left">Customer</th>
                            <th class="py-3 px-4 text-left">Kasir</th>
                            <th class="py-3 px-4 text-left">Produk</th>
                            <th class="py-3 px-4 text-center">Qty</th>
                            <th class="py-3 px-4 text-right">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr><td colspan="6" class="text-center py-4">Tidak ada transaksi pada tanggal ini.</td></tr>
                        <?php else: foreach ($transactions as $tx): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4"><?= date('H:i', strtotime($tx['waktu_transaksi'])) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($tx['nama_customer']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($tx['kasir_nama']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($tx['nama_barang'] . ' - ' . $tx['varian']) ?></td>
                            <td class="py-2 px-4 text-center"><?= $tx['kuantitas'] ?></td>
                            <td class="py-2 px-4 text-right"><?= format_rupiah($tx['sub_total']) ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                         <tr>
                            <td colspan="5" class="py-3 px-4 text-right text-lg">Total Pendapatan:</td>
                            <td class="py-3 px-4 text-right text-lg"><?= format_rupiah($total_pendapatan) ?></td>
                         </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>