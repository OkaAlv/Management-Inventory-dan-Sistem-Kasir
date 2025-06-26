<?php
// ---- LOGIKA PHP ----
// Mulai session dan muat file yang diperlukan
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php'; // untuk $base_url

// Jika pengguna sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "/pages/dashboard.php");
    exit();
}

// Proses form saat disubmit
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Username dan password tidak boleh kosong!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login sukses, simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['jabatan'] = $user['jabatan'];
            header("Location: " . $base_url . "/pages/dashboard.php");
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Amira Bakery</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= $base_url ?>/assets/images/amira-logo.png">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* CSS Kustom untuk efek visual yang tidak bisa dicapai dengan utility class Tailwind saja */
        
        /* Warna utama sesuai mockup */
        :root {
            --primary-gold:rgb(228, 201, 90);
        }

        /*
         * Trik untuk membuat efek background diagonal.
         * Kita membuat pseudo-element '::before' pada container form,
         * memberinya warna yang sama, lalu memiringkannya (skew)
         * dan memposisikannya di belakang form agar tercipta ilusi
         * bidang diagonal.
        */
body.login-wallpaper {
            /* Path ke gambar wallpaper Anda */
            background-image: url('<?= $base_url ?>/assets/images/login-wallpaper.jpg');

            /* Properti agar wallpaper memenuhi layar dengan baik */
            background-size: cover;          /* Gambar akan diperbesar/diperkecil agar menutupi seluruh layar */
            background-position: center;     /* Posisikan gambar di tengah */
            background-repeat: no-repeat;    /* Jangan ulangi gambar jika ukurannya kecil */
            background-attachment: fixed;    /* (Opsional) Wallpaper akan tetap diam saat halaman di-scroll */
        }

        /* OPSI: Tambahkan overlay gelap agar teks/form lebih mudah dibaca */
        body.login-wallpaper::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3); /* Overlay hitam dengan 30% transparansi */
            z-index: -1; /* Posisikan overlay di antara background dan konten */
        }
        /* -------------------------------------- */
        
        /* Trik untuk background diagonal pada form (tetap sama) */
        .diagonal-bg {
            position: relative;
            background-color: var(--primary-gold);
            z-index: 1;
        }

        .diagonal-bg::before {
            content: '';
            position: absolute;
            top: 0; left: -80px; width: 150px; height: 100%;
            background-color: var(--primary-gold);
            transform: skewX(-15deg);
            z-index: -1;
            border-radius: 0.5rem 0 0 0;
        }
    </style>
</head>
<!-- Ganti class pada tag body -->
<body class="login-wallpaper">

    <div class="min-h-screen flex items-center justify-center p-4">
        
        <!-- Kartu Login Utama -->
        <div class="w-full max-w-4xl flex bg-white rounded-xl shadow-2xl overflow-hidden">
            
            <!-- Sisi Kiri: Logo dan Branding -->
            <div class="w-1/2 p-12 flex-col justify-center items-center hidden md:flex">
                <img src="<?= $base_url ?>/assets/images/amira-logo.png" alt="Amira Bakery Logo" class="h-32 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-red-600 text-center">Amira Bakery</h1>
                <p class="text-center text-gray-500 mt-2">Point Of Sales Application</p>
            </div>

            <!-- Sisi Kanan: Form Login dengan Latar Diagonal -->
            <div class="w-full md:w-1/2 p-8 md:p-12 diagonal-bg">
                
                <form method="POST" action="login.php" autocomplete="off">
                    
                    <!-- Menampilkan pesan error jika ada -->
                    <?php if ($error): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-center" role="alert">
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Input Username -->
                    <div class="mb-5">
                        <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                        <input type="text" name="username" id="username" placeholder="Enter your username"
                               class="w-full px-4 py-3 rounded-lg bg-white shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Input Password -->
                    <div class="mb-5">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter your password"
                               class="w-full px-4 py-3 rounded-lg bg-white shadow-inner focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Checkbox "Remember Me" -->
                    <div class="flex items-center mb-6">
                        <input id="remember-me" type="checkbox" name="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember-me" class="ml-2 text-sm font-medium text-gray-800">Remember me</label>
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-colors duration-200">
                        Login
                    </button>

                </form>
            </div>

        </div>
    </div>
</body>
</html>