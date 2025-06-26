<?php
$page_title = 'Kelola Produk';
require_once __DIR__ . '/../../includes/header.php';
check_login();
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

// Ambil semua data produk dari database, diurutkan berdasarkan nama
$products = $pdo->query("SELECT * FROM products ORDER BY nama_barang ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Area Konten Utama -->
<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Produk & Stok</h1>
        <div>
            <!-- Tombol Tambah Stok bisa dibuat nanti jika fiturnya kompleks -->
            <!-- <a href="tambah_stok.php" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg mr-2">+ Tambah Stok</a> -->
            <a href="tambah.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                + Tambah Barang
            </a>
        </div>
    </header>

    <main class="flex-1 p-6 bg-gray-50">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama Barang</th>
                            <th class="py-3 px-4 text-left">Merek</th>
                            <th class="py-3 px-4 text-left">Varian</th>
                            <th class="py-3 px-4 text-center">Stok</th>
                            <th class="py-3 px-4 text-right">Harga</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="6" class="text-center py-4">Tidak ada data produk.</td></tr>
                        <?php else: foreach ($products as $p): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4"><?= htmlspecialchars($p['nama_barang']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($p['merek']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($p['varian']) ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($p['stok']) ?></td>
                            <td class="py-2 px-4 text-right"><?= format_rupiah($p['harga']) ?></td>
                            <td class="py-2 px-4 flex justify-center gap-2">
                                <a href="edit.php?id=<?= $p['id'] ?>" class="bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600">Edit</a>
                                <a href="hapus.php?id=<?= $p['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>