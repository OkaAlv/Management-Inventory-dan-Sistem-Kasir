<?php
// --- LOGIKA FORM SUBMIT DAN AMBIL DATA ---
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

check_login();

// Validasi ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'];

// Handle form submission untuk update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $merek = $_POST['merek'];
    $varian = $_POST['varian'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    
    try {
        $stmt = $pdo->prepare("UPDATE products SET nama_barang = ?, merek = ?, varian = ?, stok = ?, harga = ? WHERE id = ?");
        $stmt->execute([$nama_barang, $merek, $varian, $stok, $harga, $id]);
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error_message = "Update gagal: " . $e->getMessage();
    }
}

// Ambil data produk saat ini untuk ditampilkan di form
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php");
    exit();
}

// --- TAMPILAN HALAMAN ---
$page_title = 'Edit Produk';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Area Konten Utama -->
<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Data Produk</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
            ← Kembali
        </a>
    </header>
    
    <main class="flex-1 p-6 bg-gray-50">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl mx-auto">
             <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert"><?= $error_message ?></div>
            <?php endif; ?>
            <form action="edit.php?id=<?= $id ?>" method="POST">
                <div class="mb-4">
                    <label for="nama_barang" class="block text-gray-700 font-semibold mb-2">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang" class="w-full px-4 py-2 border rounded-lg" value="<?= htmlspecialchars($product['nama_barang']) ?>" required>
                </div>
                <div class="grid grid-cols-2 gap-6 mb-4">
                    <div><label for="merek" class="block font-semibold">Merek</label><input type="text" name="merek" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($product['merek']) ?>" required></div>
                    <div><label for="varian" class="block font-semibold">Varian</label><input type="text" name="varian" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($product['varian']) ?>" required></div>
                </div>
                 <div class="grid grid-cols-2 gap-6 mb-4">
                    <div><label for="stok" class="block font-semibold">Stok</label><input type="number" name="stok" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($product['stok']) ?>" min="0" required></div>
                    <div><label for="harga" class="block font-semibold">Harga Jual (Rp)</label><input type="number" name="harga" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($product['harga']) ?>" min="0" required></div>
                </div>
                <div class="flex justify-end mt-6"><button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">Simpan Perubahan</button></div>
            </form>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>