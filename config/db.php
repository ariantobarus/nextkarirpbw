<?php
/**
 * db.php
 *
 * Konfigurasi koneksi database, session, dan path dasar aplikasi.
 */

// --- Konfigurasi Path dan URL ---

// Ganti nilai ini dengan alamat URL utama situs Anda saat sudah online.
// PASTIKAN TIDAK ADA TANDA / DI AKHIR
define('BASE_URL', 'http://kerjaa.project.com'); // CONTOH: Ganti dengan domain Anda

// --- Konfigurasi Database ---
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'projec15_root'); // Ganti dengan username DB hosting Anda
define('DB_PASSWORD', '@kaesquare123'); // Ganti dengan password DB hosting Anda
define('DB_NAME', 'projec15_nextkarir_db'); // Ganti dengan nama DB hosting Anda

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
