<?php
$page_title = 'Dashboard';
// Mengubah urutan untuk best practice: Logika dulu, baru tampilan.
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/database.php';
check_login();

// --- LOGIKA PENGAMBILAN DATA DINAMIS BERDASARKAN FILTER ---

// 1. Tentukan rentang tanggal
$filter = $_GET['filter'] ?? 'hari_ini';
$start_date = '';
$end_date = '';
$chart_group_format = '%Y-%m-%d';
$chart_label_format = 'd M';

switch ($filter) {
    case 'minggu_ini':
        $start_date = date('Y-m-d', strtotime('monday this week'));
        $end_date = date('Y-m-d', strtotime('sunday this week'));
        break;
    case 'bulan_ini':
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        break;
    case 'tahun_ini':
        $start_date = date('Y-01-01');
        $end_date = date('Y-12-31');
        $chart_group_format = '%Y-%m';
        $chart_label_format = 'M Y';
        break;
    case 'kustom':
        $start_date = $_GET['start_date'] ?? date('Y-m-d');
        $end_date = $_GET['end_date'] ?? date('Y-m-d');
        break;
    case 'hari_ini':
    default:
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $chart_group_format = '%H:00';
        $chart_label_format = 'H:00';
        break;
}

// 2. Query data untuk Kartu Ringkasan
$sql_summary = "SELECT SUM(sub_total) AS total_pendapatan, SUM(kuantitas) AS total_terjual 
                FROM transactions 
                WHERE waktu_transaksi BETWEEN :start_date AND DATE_ADD(:end_date, INTERVAL 1 DAY)";
$stmt_summary = $pdo->prepare($sql_summary);
$stmt_summary->execute([':start_date' => $start_date, ':end_date' => $end_date]);
$summary = $stmt_summary->fetch(PDO::FETCH_ASSOC);

$total_pendapatan = $summary['total_pendapatan'] ?? 0;
$total_terjual = $summary['total_terjual'] ?? 0;

$total_barang = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_karyawan = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

$user_id = $_SESSION['user_id'];
$stmt_user = $pdo->prepare("SELECT foto_profil FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$current_user = $stmt_user->fetch(PDO::FETCH_ASSOC);
$foto_profil_pengguna = $current_user['foto_profil'] ?? null;

// Tabel Penjualan Terbaru
$stmt_penjualan_terbaru = $pdo->query(
    "SELECT t.*, u.nama as kasir_nama, p.nama_barang 
     FROM transactions t 
     JOIN users u ON t.user_id = u.id 
     JOIN products p ON t.product_id = p.id
     ORDER BY t.waktu_transaksi DESC LIMIT 5"
);
$penjualan_terbaru = $stmt_penjualan_terbaru->fetchAll(PDO::FETCH_ASSOC);

// 3. Query data untuk Grafik Statistik Penjualan
$sql_chart = "SELECT DATE_FORMAT(waktu_transaksi, :group_format) AS tanggal, SUM(sub_total) AS pendapatan
              FROM transactions
              WHERE waktu_transaksi BETWEEN :start_date AND DATE_ADD(:end_date, INTERVAL 1 DAY)
              GROUP BY tanggal
              ORDER BY tanggal ASC";
$stmt_chart = $pdo->prepare($sql_chart);
$stmt_chart->execute([':group_format' => $chart_group_format, ':start_date' => $start_date, ':end_date' => $end_date]);
$chart_raw_data = $stmt_chart->fetchAll(PDO::FETCH_ASSOC);

$sales_chart_data = ['labels' => [], 'data' => []];
foreach ($chart_raw_data as $row) {
    $sales_chart_data['labels'][] = date($chart_label_format, strtotime($row['tanggal']));
    $sales_chart_data['data'][] = (int) $row['pendapatan'];
}

// --- BARU: 4. Query data untuk Grafik Produk Terlaris ---
$sql_top_products = "SELECT p.nama_barang, SUM(t.kuantitas) as total_kuantitas
                     FROM transactions t
                     JOIN products p ON t.product_id = p.id
                     WHERE t.waktu_transaksi BETWEEN :start_date AND DATE_ADD(:end_date, INTERVAL 1 DAY)
                     GROUP BY p.nama_barang
                     ORDER BY total_kuantitas DESC
                     LIMIT 5";
$stmt_top_products = $pdo->prepare($sql_top_products);
$stmt_top_products->execute([':start_date' => $start_date, ':end_date' => $end_date]);
$top_products_raw_data = $stmt_top_products->fetchAll(PDO::FETCH_ASSOC);

$top_products_chart_data = ['labels' => [], 'data' => []];
foreach ($top_products_raw_data as $row) {
    $top_products_chart_data['labels'][] = $row['nama_barang'];
    $top_products_chart_data['data'][] = (int)$row['total_kuantitas'];
}
// --- AKHIR BAGIAN BARU ---

// --- BARULAH KITA MEMULAI TAMPILAN HALAMAN ---
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="flex flex-col flex-1 ml-64">

    <!-- BAGIAN HEADER HALAMAN -->
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard Analitik</h1>
        <div class="flex items-center">
            <span class="mr-3 font-semibold text-right">
                <span class="block text-sm text-gray-800"><?= htmlspecialchars($_SESSION['nama']) ?></span>
                <span class="block text-xs text-gray-500"><?= htmlspecialchars($_SESSION['jabatan']) ?></span>
            </span>
            <a href="<?= $base_url ?>/pages/profil.php" class="block">
                <img src="<?= $base_url . '/uploads/profil/' . ($foto_profil_pengguna ?? 'default.png') ?>" 
                     alt="Foto Profil" 
                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 hover:border-blue-500">
            </a>
        </div>
    </header>

    <!-- BAGIAN KONTEN UTAMA HALAMAN -->
    <main class="flex-1 p-6 bg-gray-50">
        <!-- Filter Section -->
        <div class="bg-white p-4 rounded-lg shadow-md mb-6">
            <form id="filterForm" action="dashboard.php" method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="filter" class="block text-sm font-medium text-gray-700">Filter Berdasarkan</label>
                    <select name="filter" id="filter" class="mt-1 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md">
                        <option value="hari_ini" <?= $filter == 'hari_ini' ? 'selected' : '' ?>>Hari Ini</option>
                        <option value="minggu_ini" <?= $filter == 'minggu_ini' ? 'selected' : '' ?>>Minggu Ini</option>
                        <option value="bulan_ini" <?= $filter == 'bulan_ini' ? 'selected' : '' ?>>Bulan Ini</option>
                        <option value="tahun_ini" <?= $filter == 'tahun_ini' ? 'selected' : '' ?>>Tahun Ini</option>
                        <option value="kustom" <?= $filter == 'kustom' ? 'selected' : '' ?>>Pilih Rentang...</option>
                    </select>
                </div>
                <div id="kustomDateRange" class="<?= $filter == 'kustom' ? '' : 'hidden' ?> flex items-end gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium">Dari Tanggal</label>
                        <input type="date" name="start_date" id="start_date" class="mt-1 block py-2 px-3 border-gray-300 rounded-md" value="<?= htmlspecialchars($start_date) ?>">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" class="mt-1 block py-2 px-3 border-gray-300 rounded-md" value="<?= htmlspecialchars($end_date) ?>">
                    </div>
                </div>
                <div><button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">Terapkan</button></div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-primary p-6 rounded-lg shadow text-white"><h2 class="text-lg font-semibold">Total Pendapatan</h2><p class="text-3xl font-bold"><?= format_rupiah($total_pendapatan) ?></p></div>
            <div class="bg-primary p-6 rounded-lg shadow text-white"><h2 class="text-lg font-semibold">Total Roti Terjual</h2><p class="text-3xl font-bold"><?= (int)$total_terjual ?></p></div>
            <div class="bg-gray-700 p-6 rounded-lg shadow text-white"><h2 class="text-lg font-semibold">Total Item Produk</h2><p class="text-3xl font-bold"><?= $total_barang ?></p></div>
            <div class="bg-gray-700 p-6 rounded-lg shadow text-white"><h2 class="text-lg font-semibold">Total Karyawan</h2><p class="text-3xl font-bold"><?= $total_karyawan ?></p></div>
        </div>

        <!-- Chart & Tabel Terbaru Section -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
            <div class="lg:col-span-3 bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold text-xl mb-4">Data Penjualan Terbaru</h3>
                <div class="overflow-x-auto max-h-[300px]">
                    <table class="min-w-full">
                        <tbody>
                            <?php if (empty($penjualan_terbaru)): ?>
                                <tr><td class="text-center py-10 text-gray-500">Belum ada data penjualan terbaru.</td></tr>
                            <?php else: foreach ($penjualan_terbaru as $jual): 
                                $harga_satuan = ($jual['kuantitas'] > 0) ? ($jual['sub_total'] / $jual['kuantitas']) : 0;
                            ?>
                                <tr class="border-b last:border-b-0 hover:bg-gray-50">
                                    <td class="py-4">
                                        <p class="font-bold text-gray-900"><?= htmlspecialchars($jual['nama_barang']) ?></p>
                                        <p class="text-sm text-gray-600">oleh <span class="font-semibold"><?= htmlspecialchars($jual['kasir_nama']) ?></span></p>
                                        <p class="text-xs text-gray-400 mt-1"><?= date('d M Y, H:i', strtotime($jual['waktu_transaksi'])) ?></p>
                                    </td>
                                    <td class="py-4 text-right">
                                        <p class="font-bold text-lg text-gray-900"><?= format_rupiah($jual['sub_total']) ?></p>
                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($jual['kuantitas']) ?> x <?= format_rupiah($harga_satuan) ?></p>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow">
                <h3 class="font-bold text-xl mb-4">Statistik Penjualan</h3>
                <canvas id="salesChart" height="200"></canvas>
            </div>
        </div>

        <!-- --- BARU: Bagian Grafik Produk Terlaris --- -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="font-bold text-xl mb-4">5 Produk Terlaris (Berdasarkan Kuantitas)</h3>
            <div class="w-full lg:w-2/5 mx-auto">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>
        <!-- --- AKHIR BAGIAN BARU --- -->

    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterSelect = document.getElementById('filter');
    const kustomDateRange = document.getElementById('kustomDateRange');

    filterSelect.addEventListener('change', function() {
        kustomDateRange.classList.toggle('hidden', this.value !== 'kustom');
    });

    // Chart Statistik Penjualan (BAR)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($sales_chart_data['labels']) ?>,
            datasets: [{
                label: 'Pendapatan',
                data: <?= json_encode($sales_chart_data['data']) ?>,
                backgroundColor: 'rgba(212, 167, 90, 0.6)',
                borderColor: 'rgba(212, 167, 90, 1)',
                borderWidth: 1,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } }
            },
            plugins: { tooltip: { callbacks: { label: context => 'Pendapatan: ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.raw) } } }
        }
    });

    // --- BARU: Chart Produk Terlaris (DOUGHNUT) ---
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(topProductsCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($top_products_chart_data['labels']) ?>,
            datasets: [{
                label: 'Jumlah Terjual',
                data: <?= json_encode($top_products_chart_data['data']) ?>,
                backgroundColor: [
                    'rgba(212, 167, 90, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed !== null) { label += context.parsed + ' pcs'; }
                            return label;
                        }
                    }
                }
            }
        }
    });
    // --- AKHIR BAGIAN BARU ---
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>