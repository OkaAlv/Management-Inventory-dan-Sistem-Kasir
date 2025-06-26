<?php
// --- LOGIKA FORM SUBMIT ---
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

check_login();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $merek = $_POST['merek'];
    $varian = $_POST['varian'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO products (nama_barang, merek, varian, stok, harga) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nama_barang, $merek, $varian, $stok, $harga]);
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error_message = "Gagal menyimpan produk: " . $e->getMessage();
    }
}

// --- TAMPILAN HALAMAN ---
$page_title = 'Tambah Produk';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Area Konten Utama -->
<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Produk Baru</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
            ← Kembali
        </a>
    </header>
    
    <main class="flex-1 p-6 bg-gray-50">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl mx-auto">
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>
            <form action="tambah.php" method="POST">
                <div class="mb-4">
                    <label for="nama_barang" class="block text-gray-700 font-semibold mb-2">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <label for="merek" class="block text-gray-700 font-semibold mb-2">Merek</label>
                        <input type="text" name="merek" id="merek" class="w-full px-4 py-2 border rounded-lg" value="Amira" required>
                    </div>
                    <div>
                        <label for="varian" class="block text-gray-700 font-semibold mb-2">Varian</label>
                        <input type="text" name="varian" id="varian" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                </div>
                 <div class="grid grid-cols-2 gap-6 mb-4">
                    <div>
                        <label for="stok" class="block text-gray-700 font-semibold mb-2">Stok Awal</label>
                        <input type="number" name="stok" id="stok" class="w-full px-4 py-2 border rounded-lg" min="0" required>
                    </div>
                    <div>
                        <label for="harga" class="block text-gray-700 font-semibold mb-2">Harga Jual (Rp)</label>
                        <input type="number" name="harga" id="harga" class="w-full px-4 py-2 border rounded-lg" min="0" required>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">Simpan Produk</button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>