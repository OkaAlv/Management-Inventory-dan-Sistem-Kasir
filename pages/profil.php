<?php
// --- LOGIKA FORM SUBMIT DAN AMBIL DATA ---
$page_title = 'Profil Pengguna';
require_once __DIR__ . '/../includes/header.php'; // Pastikan ini memuat functions.php untuk $base_url
check_login();
require_once __DIR__ . '/../config/database.php';

// Dapatkan ID pengguna yang sedang login dari session
$user_id = $_SESSION['user_id'];

// Handle form submission untuk update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil semua data dari form
    $nama = $_POST['nama'];
    $nip = $_POST['nip'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    
    // BARU: Logika Upload Foto Profil
    $foto_profil_name = $_POST['foto_profil_lama'] ?? null; // Simpan nama foto lama
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['foto_profil'];
        $upload_dir = __DIR__ . '/../uploads/profil/';
        
        // Buat direktori jika belum ada
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // Validasi file (ukuran dan tipe)
        $allowed_types = ['image/jpeg', 'image/png'];
        if (in_array($file['type'], $allowed_types) && $file['size'] <= 2000000) { // Max 2MB
            // Buat nama file unik untuk menghindari tumpukan
            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $foto_profil_name = 'user-' . $user_id . '-' . time() . '.' . $file_extension;
            $target_path = $upload_dir . $foto_profil_name;

            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Hapus foto lama jika ada dan bukan default.png
                if (!empty($_POST['foto_profil_lama']) && $_POST['foto_profil_lama'] != 'default.png') {
                    $old_photo_path = $upload_dir . $_POST['foto_profil_lama'];
                    if (file_exists($old_photo_path)) {
                        unlink($old_photo_path);
                    }
                }
            } else {
                $error_message = "Gagal memindahkan file foto.";
                $foto_profil_name = $_POST['foto_profil_lama']; // Kembalikan ke nama lama jika gagal
            }
        } else {
            $error_message = "File tidak valid. Pastikan format JPG/PNG dan ukuran maksimal 2MB.";
            $foto_profil_name = $_POST['foto_profil_lama']; // Kembalikan ke nama lama jika validasi gagal
        }
    }
    
    // Siapkan query update dasar
    $sql_parts = [
        'nama' => 'nama = ?', 'nip' => 'nip = ?', 'username' => 'username = ?',
        'email' => 'email = ?', 'telepon' => 'telepon = ?', 'alamat' => 'alamat = ?',
        'foto_profil' => 'foto_profil = ?' // BARU
    ];
    $params = [$nama, $nip, $username, $email, $telepon, $alamat, $foto_profil_name]; // BARU

    // Jika password baru diisi, tambahkan ke query dan parameter
    if (!empty($_POST['password_baru'])) {
        $sql_parts['password'] = 'password = ?';
        $params[] = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
    }
    
    // Gabungkan query dan tambahkan kondisi WHERE
    $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = ?";
    $params[] = $user_id;

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['nama'] = $nama; // Update nama di session
        $success_message = "Profil berhasil diperbarui!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { $error_message = "Username atau NIP sudah digunakan."; } 
        else { $error_message = "Update gagal: " . $e->getMessage(); }
    }
}

// Ambil data terbaru pengguna untuk ditampilkan di form
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// --- TAMPILAN HALAMAN ---
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Area Konten Utama -->
<div class="flex flex-col flex-1 ml-64">
    <header class="flex justify-between items-center h-20 bg-white border-b-2 p-6">
        <h1 class="text-2xl font-bold text-gray-800">Profile Pengguna</h1>
    </header>
    
    <main class="flex-1 p-6 bg-gray-50">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl mx-auto">
            
            <!-- Notifikasi Sukses atau Error -->
            <?php if (isset($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p><?= $success_message ?></p></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert"><p><?= $error_message ?></p></div>
            <?php endif; ?>

            <form action="profil.php" method="POST" enctype="multipart/form-data">
                <!-- BARU: Input tersembunyi untuk menyimpan nama foto lama -->
                <input type="hidden" name="foto_profil_lama" value="<?= htmlspecialchars($user['foto_profil']) ?>">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Kolom Kiri: Form Data -->
                    <div class="md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div><label class="block font-semibold">Nama</label><input type="text" name="nama" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($user['nama']) ?>"></div>
                            <div><label class="block font-semibold">NIP</label><input type="text" name="nip" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($user['nip']) ?>"></div>
                            <div><label class="block font-semibold">Username</label><input type="text" name="username" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($user['username']) ?>"></div>
                            <div><label class="block font-semibold">Jabatan/Posisi</label><input type="text" name="jabatan" class="w-full mt-1 p-2 border rounded bg-gray-200" value="<?= htmlspecialchars($user['jabatan']) ?>" readonly></div>
                            <div><label class="block font-semibold">Email</label><input type="email" name="email" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($user['email']) ?>"></div>
                            <div><label class="block font-semibold">No. Telepon</label><input type="text" name="telepon" class="w-full mt-1 p-2 border rounded" value="<?= htmlspecialchars($user['telepon']) ?>"></div>
                        </div>
                        <div class="mt-6"><label class="block font-semibold">Alamat</label><textarea name="alamat" class="w-full mt-1 p-2 border rounded" rows="3"><?= htmlspecialchars($user['alamat']) ?></textarea></div>
                        <div class="mt-6">
                            <label class="block font-semibold">Password Baru</label>
                            <input type="password" name="password_baru" class="w-full mt-1 p-2 border rounded" placeholder="Kosongkan jika tidak ingin mengubah">
                        </div>
                    </div>
                    
                    <!-- Kolom Kanan: Foto Profil (Sudah Aktif) -->
<!-- Kolom Kanan: Foto Profil (Desain Sesuai Permintaan & Fungsional) -->
<div class="flex flex-col items-center">

    <!-- Wadah Gambar Lingkaran -->
    <div class="relative w-48 h-48 bg-gray-200 rounded-full flex items-center justify-center mb-4 overflow-hidden shadow-md border-4 border-white">
        
        <!-- Foto Asli Pengguna (Akan tampil jika ada, menutupi placeholder) -->
        <img id="preview_foto"
             src="<?= $base_url . '/uploads/profil/' . ($user['foto_profil'] ?? '') ?>" 
             alt="Foto Profil"
             class="absolute w-full h-full object-cover <?= $user['foto_profil'] ? '' : 'hidden' ?>">

        <!-- Ikon Placeholder Default (Akan tampil jika tidak ada foto) -->
        <div id="placeholder_container" class="<?= $user['foto_profil'] ? 'hidden' : 'flex' ?> items-center justify-center">
            <svg class="w-32 h-32 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
            </svg>
        </div>

    </div>
    
    <!-- Tombol "Ubah Foto" yang fungsional -->
    <label for="foto_profil" class="bg-gray-300 hover:bg-gray-400 cursor-pointer py-2 px-4 rounded-lg text-sm font-semibold text-gray-700">
        Ubah Foto
    </label>
    <input type="file" name="foto_profil" id="foto_profil" class="hidden" onchange="previewImage(event)">
    
    <p class="text-xs text-gray-500 mt-2">Max. 2MB (JPG, PNG)</p>
</div>

                </div>
                <div class="mt-8 text-right">
                     <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>

    <!-- BARU: Javascript untuk preview gambar -->
<script>
    function previewImage(event) {
        // Ambil elemen-elemen yang diperlukan
        const imagePreview = document.getElementById('preview_foto');
        const placeholder = document.getElementById('placeholder_container');
        const fileInput = event.target;

        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Set sumber gambar ke file yang dipilih
                imagePreview.src = e.target.result;
                // Tampilkan elemen gambar
                imagePreview.classList.remove('hidden');
                // Sembunyikan placeholder ikon
                placeholder.classList.add('hidden');
            };
            
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
</script>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>