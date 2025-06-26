<?php
// --- LOGIKA FORM SUBMIT DAN AMBIL DATA ---
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

check_login();

// Cek Hak Akses (Role Management)
if ($_SESSION['jabatan'] != 'Owner') {
    // Jika bukan Owner, kembalikan ke dashboard dengan pesan error (opsional)
    $_SESSION['error_message'] = "Anda tidak memiliki hak akses untuk membuka halaman tersebut.";
    header("Location: " . $base_url . "/pages/dashboard.php");
    exit();
}
// Validasi ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id = $_GET['id'];

// Handle form submission untuk update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $username = $_POST['username'];
    $jabatan = $_POST['jabatan'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $tanggal_bergabung = $_POST['tanggal_bergabung'];
    
    // Base SQL dan parameter
    $sql = "UPDATE users SET nama = ?, nip = ?, username = ?, jabatan = ?, email = ?, telepon = ?, alamat = ?, tanggal_bergabung = ? WHERE id = ?";
    $params = [$nama, $nip, $username, $jabatan, $email, $telepon, $alamat, $tanggal_bergabung, $id];
    
    // Jika password diisi, tambahkan ke query update
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama = ?, nip = ?, username = ?, jabatan = ?, email = ?, telepon = ?, alamat = ?, tanggal_bergabung = ?, password = ? WHERE id = ?";
        $params = [$nama, $nip, $username, $jabatan, $email, $telepon, $alamat, $tanggal_bergabung, $password, $id];
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { $error_message = "Username atau NIP sudah terdaftar."; } 
        else { $error_message = "Update gagal: " . $e->getMessage(); }
    }
}

// Ambil data karyawan saat ini untuk ditampilkan di form
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$karyawan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$karyawan) {
    header("Location: index.php");
    exit();
}

// --- TAMPILAN HALAMAN ---
$page_title = 'Edit Karyawan';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Area Konten Utama -->
<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Data Karyawan</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
            ← Kembali
        </a>
    </header>
    
    <main class="flex-1 p-6 bg-gray-50">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-4xl mx-auto">
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert"><?= $error_message ?></div>
            <?php endif; ?>
            <form action="edit.php?id=<?= $id ?>" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label class="block font-semibold">Nama Lengkap</label><input type="text" name="nama" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($karyawan['nama']) ?>" required></div>
                    <div><label class="block font-semibold">NIP</label><input type="text" name="nip" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($karyawan['nip']) ?>"></div>
                    <div><label class="block font-semibold">Username</label><input type="text" name="username" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($karyawan['username']) ?>" required></div>
                    <div><label class="block font-semibold">Password Baru</label><input type="password" name="password" class="w-full mt-1 p-2 border rounded" placeholder="Kosongkan jika tidak diubah"></div>
                    <div><label class="block font-semibold">Jabatan</label><select name="jabatan" class="w-full mt-1 p-2 border rounded"><option value="Karyawan" <?= $karyawan['jabatan'] == 'Karyawan' ? 'selected' : '' ?>>Karyawan</option><option value="Owner" <?= $karyawan['jabatan'] == 'Owner' ? 'selected' : '' ?>>Owner</option></select></div>
                    <div><label class="block font-semibold">Tanggal Bergabung</label><input type="date" name="tanggal_bergabung" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($karyawan['tanggal_bergabung']) ?>" required></div>
                    <div><label class="block font-semibold">Email</label><input type="email" name="email" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($karyawan['email']) ?>"></div>
                    <div><label class="block font-semibold">No. Telepon</label><input type="text" name="telepon" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($karyawan['telepon']) ?>"></div>
                </div>
                <div class="mt-6"><label class="block font-semibold">Alamat</label><textarea name="alamat" rows="3" class="w-full mt-1 p-2 border rounded"><?= htmlspecialchars($karyawan['alamat']) ?></textarea></div>
                <div class="flex justify-end mt-6"><button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">Simpan Perubahan</button></div>
            </form>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>