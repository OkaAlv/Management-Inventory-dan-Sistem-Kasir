<?php
$page_title = 'Kelola Karyawan';
require_once __DIR__ . '/../../includes/header.php';
check_login();
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';
// Cek Hak Akses (Role Management)
if ($_SESSION['jabatan'] != 'Owner') {
    // Jika bukan Owner, kembalikan ke dashboard dengan pesan error (opsional)
    $_SESSION['error_message'] = "Anda tidak memiliki hak akses untuk membuka halaman tersebut.";
    header("Location: " . $base_url . "/pages/dashboard.php");
    exit();
}
// Ambil semua data pengguna dari database
$karyawan = $pdo->query("SELECT * FROM users ORDER BY nama ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Area Konten Utama -->
<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Kelola Karyawan</h1>
        <a href="tambah.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
            + Tambah Karyawan
        </a>
    </header>

    <main class="flex-1 p-6 bg-gray-50">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">Username</th>
                            <th class="py-3 px-4 text-left">Jabatan</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-left">No. Telepon</th>
                            <th class="py-3 px-4 text-left">Tanggal Bergabung</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($karyawan)): ?>
                            <tr><td colspan="7" class="text-center py-4">Tidak ada data karyawan.</td></tr>
                        <?php else: foreach ($karyawan as $k): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-4"><?= htmlspecialchars($k['nama']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($k['username']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($k['jabatan']) ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($k['email'] ?? '-') ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($k['telepon'] ?? '-') ?></td>
                            <td class="py-2 px-4"><?= date('d F Y', strtotime($k['tanggal_bergabung'])) ?></td>
                            <td class="py-2 px-4 flex justify-center gap-2">
                                <a href="edit.php?id=<?= $k['id'] ?>" class="bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-600">Edit</a>
                                <?php if ($_SESSION['user_id'] != $k['id']): // Cegah user hapus diri sendiri ?>
                                <a href="hapus.php?id=<?= $k['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?')" class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-600">Hapus</a>
                                <?php endif; ?>
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