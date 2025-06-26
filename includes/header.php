<?php
// Pastikan functions.php (yang berisi $base_url) sudah di-include oleh file pemanggil
// Tapi untuk jaga-jaga, kita bisa require_once di sini
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Amira Bakery' ?></title>
    <link rel="icon" type="image/png" href="<?= $base_url ?>/assets/images/amira-favicon.png">
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Warna kustom sesuai desain Anda */
        :root {
            --primary: #D4A75A; /* Coklat/Kuning utama dari mockup */
            --secondary: #3C3C3C; /* Abu-abu gelap */
        }
        .bg-primary { background-color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
        .text-primary { color: var(--primary); }
    </style>
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-200">
    <!-- Konten akan dimulai setelah div ini -->