<?php
// --- LOGIKA FORM SUBMIT ---
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
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $jabatan = $_POST['jabatan'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $tanggal_bergabung = $_POST['tanggal_bergabung'];

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO users (nama, nip, username, password, jabatan, email, telepon, alamat, tanggal_bergabung) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$nama, $nip, $username, $password, $jabatan, $email, $telepon, $alamat, $tanggal_bergabung]);
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        // Cek jika ada error duplikat username atau NIP
        if ($e->getCode() == 23000) {
            $error_message = "Username atau NIP sudah terdaftar. Silakan gunakan yang lain.";
        } else {
            $error_message = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// --- TAMPILAN HALAMAN ---
$page_title = 'Tambah Karyawan';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Area Konten Utama -->
<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Karyawan Baru</h1>
        <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
            ← Kembali
        </a>
    </header>
    
    <main class="flex-1 p-6 bg-gray-50">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-4xl mx-auto">
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>
            <form action="tambah.php" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><label for="nama" class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label><input type="text" name="nama" id="nama" class="w-full px-4 py-2 border rounded-lg" required></div>
                    <div><label for="nip" class="block text-gray-700 font-semibold mb-2">NIP</label><input type="text" name="nip" id="nip" class="w-full px-4 py-2 border rounded-lg"></div>
                    <div><label for="username" class="block text-gray-700 font-semibold mb-2">Username</label><input type="text" name="username" id="username" class="w-full px-4 py-2 border rounded-lg" required></div>
                    <div><label for="password" class="block text-gray-700 font-semibold mb-2">Password</label><input type="password" name="password" id="password" class="w-full px-4 py-2 border rounded-lg" required></div>
                    <div><label for="jabatan" class="block text-gray-700 font-semibold mb-2">Jabatan</label><select name="jabatan" id="jabatan" class="w-full px-4 py-2 border rounded-lg"><option value="Karyawan">Karyawan</option><option value="Owner">Owner</option></select></div>
                    <div><label for="tanggal_bergabung" class="block text-gray-700 font-semibold mb-2">Tanggal Bergabung</label><input type="date" name="tanggal_bergabung" id="tanggal_bergabung" class="w-full px-4 py-2 border rounded-lg" required></div>
                    <div><label for="email" class="block text-gray-700 font-semibold mb-2">Email</label><input type="email" name="email" id="email" class="w-full px-4 py-2 border rounded-lg"></div>
                    <div><label for="telepon" class="block text-gray-700 font-semibold mb-2">No. Telepon</label><input type="text" name="telepon" id="telepon" class="w-full px-4 py-2 border rounded-lg"></div>
                </div>
                <div class="mt-6"><label for="alamat" class="block text-gray-700 font-semibold mb-2">Alamat</label><textarea name="alamat" id="alamat" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea></div>
                <div class="flex justify-end mt-6"><button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">Simpan</button></div>
            </form>
        </div>
    </main>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>