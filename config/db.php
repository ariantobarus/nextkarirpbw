<?php
/**
 * db.php
 *
 * Konfigurasi koneksi database, session, dan path dasar aplikasi.
 */

// --- Konfigurasi Path dan URL (BAGIAN BARU) ---

// ROOT_PATH: Alamat folder fisik di server.
// __DIR__ akan otomatis mendapatkan path ke folder 'config', '/..' akan membuatnya naik satu level
// sehingga menunjuk ke folder utama 'nextkarir'.
define('ROOT_PATH', dirname(__DIR__));

// BASE_URL: Alamat URL utama situs Anda.
// Ganti 'http://localhost/nextkarir' dengan nama domain Anda saat sudah online.
define('BASE_URL', 'http://kerjaa/nextkarir');


// --- Konfigurasi Database ---
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'projec15_root');
define('DB_PASSWORD', '@kaesquare123');
define('DB_NAME', 'projec15_nextkarir_db');

// --- Membuat Koneksi ---
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

// --- Manajemen Session ---
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


Contoh di partials/header.php (Gunakan BASE_URL untuk link dan gambar):

<header class="navbar">
    <a href="<?= BASE_URL ?>/index.php" class="logo">
        <img src="<?= BASE_URL ?>/NextKarir.png" alt="Logo NextKarir">
        <span class="logo-text">NextKarir</span>
    </a>
    <nav class="nav-links">
        <a href="<?= BASE_URL ?>/index.php">Home</a>
        <a href="<?= BASE_URL ?>/search.php">Cari Lowongan</a>
        <!-- dan seterusnya untuk semua link -->
    </nav>
</header>

Contoh di index.php (Gunakan ROOT_PATH untuk include):

<?php
// Cara lama: require_once 'config/db.php';
// Cara baru yang lebih baik:
require_once __DIR__ . '/config/db.php'; // __DIR__ menunjuk ke folder saat ini

// ... kode lainnya ...

// Cara lama: include 'partials/header.php';
// Cara baru yang lebih baik:
include ROOT_PATH . '/partials/header.php';

// ... kode lainnya ...

// Cara lama: include 'partials/footer.php';
// Cara baru yang lebih baik:
include ROOT_PATH . '/partials/footer.php';
?>
