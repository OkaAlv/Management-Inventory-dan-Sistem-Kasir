<?php
// File: config/database.example.php

$host = 'localhost';
$dbname = 'ganti_dengan_nama_db_anda'; // Contoh: amira_bakery_db
$user = 'ganti_dengan_user_db_anda';   // Contoh: root
$pass = 'ganti_dengan_password_anda'; // Contoh: '' (kosong)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi ke database gagal. Periksa file config/database.php Anda. Error: " . $e->getMessage());
}
?>