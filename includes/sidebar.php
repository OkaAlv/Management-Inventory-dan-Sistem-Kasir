<?php
// Ambil nama skrip halaman yang sedang diakses saat ini.
$current_page = $_SERVER['SCRIPT_NAME'];
?>

<div class="fixed flex flex-col top-0 left-0 w-64 bg-primary h-full shadow-lg">
    <div class="flex items-center justify-center h-20 border-b border-white/20">
        <a href="<?= $base_url ?>/pages/dashboard.php">
            <img src="<?= $base_url ?>/assets/images/amira-logo.png" alt="Amira Bakery Logo" class="h-12">
        </a>
    </div>
    <div class="overflow-y-auto overflow-x-hidden flex-grow">
        <ul class="flex flex-col py-4 space-y-1 text-white">
            <li class="px-5">
                <div class="flex flex-row items-center h-8">
                    <div class="text-sm font-light tracking-wide">Menu</div>
                </div>
            </li>
            
            <!-- Dashboard -->
            <li>
                <a href="<?= $base_url ?>/pages/dashboard.php" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-white/20 pr-6 <?= (strpos($current_page, 'dashboard.php') !== false) ? 'bg-white/20 border-l-4 border-white' : 'border-l-4 border-transparent' ?>">
                    <span class="inline-flex justify-center items-center ml-4"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></span>
                    <span class="ml-2 text-sm tracking-wide truncate">Dashboard</span>
                </a>
            </li>
            
            <!-- Kasir (POS) -->
            <li>
                <a href="<?= $base_url ?>/pages/kasir.php" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-white/20 pr-6 <?= (strpos($current_page, 'kasir.php') !== false) ? 'bg-white/20 border-l-4 border-white' : 'border-l-4 border-transparent' ?>">
                    <span class="inline-flex justify-center items-center ml-4"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg></span>
                    <span class="ml-2 text-sm tracking-wide truncate">Halaman Kasir (POS)</span>
                </a>
            </li>
            
            <!-- Kelola Karyawan -->
                        <!-- Link Kelola Karyawan (HANYA UNTUK OWNER) -->
            <?php if (isset($_SESSION['jabatan']) && $_SESSION['jabatan'] == 'Owner'): ?>
            <li>
                <a href="<?= $base_url ?>/pages/karyawan/" 
                   class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-white/20 pr-6 
                          <?= (strpos($current_page, '/pages/karyawan/') !== false) ? 'bg-white/20 border-l-4 border-white' : 'border-l-4 border-transparent' ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.125-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.125-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Kelola Karyawan</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Kelola Produk -->
            <li>
                <a href="<?= $base_url ?>/pages/produk/" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-white/20 pr-6 <?= (strpos($current_page, '/pages/produk/') !== false) ? 'bg-white/20 border-l-4 border-white' : 'border-l-4 border-transparent' ?>">
                    <span class="inline-flex justify-center items-center ml-4"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg></span>
                    <span class="ml-2 text-sm tracking-wide truncate">Kelola Produk</span>
                </a>
            </li>
            
            <li class="px-5 mt-4"><div class="flex flex-row items-center h-8"><div class="text-sm font-light tracking-wide">Laporan</div></div></li>
            
            <!-- ======================= PERUBAHAN DI SINI ======================= -->
            <!-- Link Laporan Harian -->
            <li>
                <a href="<?= $base_url ?>/pages/laporan/harian.php" 
                   class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-white/20 pr-6 
                          <?= (strpos($current_page, 'harian.php') !== false) ? 'bg-white/20 border-l-4 border-white' : 'border-l-4 border-transparent' ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Laporan Harian</span>
                </a>
            </li>

            <!-- Link Laporan Bulanan -->
            <li>
                <a href="<?= $base_url ?>/pages/laporan/bulanan.php" 
                   class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-white/20 pr-6 
                          <?= (strpos($current_page, 'bulanan.php') !== false) ? 'bg-white/20 border-l-4 border-white' : 'border-l-4 border-transparent' ?>">
                    <span class="inline-flex justify-center items-center ml-4">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                    <span class="ml-2 text-sm tracking-wide truncate">Laporan Bulanan</span>
                </a>
            </li>
            <!-- ======================= AKHIR PERUBAHAN ======================= -->

            <!-- Logout Link -->
            <li class="absolute bottom-0 w-full">
                 <a href="<?= $base_url ?>/logout.php" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-red-500/50 pr-6">
                    <span class="inline-flex justify-center items-center ml-4 text-red-300"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg></span>
                    <span class="ml-2 text-sm tracking-wide truncate">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>