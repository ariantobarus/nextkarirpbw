<?php
// Memanggil file konfigurasi untuk mengakses BASE_URL dan memulai session
require_once __DIR__ . '/config/db.php';

// Menghapus semua variabel sesi.
$_SESSION = [];

// Menghancurkan sesi.
session_destroy();

// Mengarahkan pengguna kembali ke halaman utama.
header("Location: " . BASE_URL . "/index.php");
exit(); // Pastikan tidak ada kode lain yang dieksekusi setelah redirect.
