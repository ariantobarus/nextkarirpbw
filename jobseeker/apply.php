<?php
require_once '../config/db.php';

// Autentikasi: Pastikan hanya job seeker yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: /nextkarir/login.php");
    exit();
}

// Validasi: Pastikan ID lowongan ada dan merupakan angka
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header("Location: /nextkarir/search.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$job_id = $_GET['job_id'];

// Ambil ID profil job seeker
$stmt = $conn->prepare("SELECT id FROM job_seekers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Error: Profil pencari kerja tidak ditemukan.");
}
$job_seeker_id = $result->fetch_assoc()['id'];
$stmt->close();

// Cek apakah sudah pernah melamar lowongan yang sama
$stmt = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND job_seeker_id = ?");
$stmt->bind_param("ii", $job_id, $job_seeker_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    // Jika sudah, redirect ke halaman riwayat dengan pesan
    header("Location: applications.php?status=already_applied");
    exit();
}
$stmt->close();

// Jika belum, masukkan data lamaran baru
$stmt = $conn->prepare("INSERT INTO applications (job_id, job_seeker_id, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("ii", $job_id, $job_seeker_id);

if ($stmt->execute()) {
    // Jika berhasil, redirect ke halaman riwayat dengan pesan sukses
    header("Location: applications.php?status=success");
    exit();
} else {
    // Jika gagal, bisa redirect dengan pesan error
    header("Location: /nextkarir/job_detail.php?id=$job_id&error=apply_failed");
    exit();
}
$stmt->close();
$conn->close();
