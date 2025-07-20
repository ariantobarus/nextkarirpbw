<?php
require_once '../config/db.php';

// Autentikasi & Otorisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: /nextkarir/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $conn->query("SELECT id FROM companies WHERE user_id = $user_id")->fetch_assoc()['id'];
$message = '';

// Proses Hapus Lowongan
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $job_id_to_delete = $_GET['delete_id'];
    // Pastikan menghapus job milik company yang login
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ? AND company_id = ?");
    $stmt->bind_param("ii", $job_id_to_delete, $company_id);
    if ($stmt->execute()) {
        $message = '<div class="message success">Lowongan berhasil dihapus.</div>';
    } else {
        $message = '<div class="message error">Gagal menghapus lowongan.</div>';
    }
    $stmt->close();
}

if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = '<div class="message success">Lowongan berhasil disimpan.</div>';
}

// Ambil semua lowongan dari perusahaan ini
$stmt = $conn->prepare("SELECT id, title, location, posted_at FROM jobs WHERE company_id = ? ORDER BY posted_at DESC");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lowongan - NextKarir</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Kelola Lowongan Anda</h1>
            <a href="post_job.php" class="btn btn-primary">Pasang Lowongan Baru</a>
        </div>
        
        <?= $message ?>

        <?php if (!empty($jobs)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Judul Posisi</th>
                        <th>Lokasi</th>
                        <th>Tanggal Posting</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?= htmlspecialchars($job['title']) ?></td>
                            <td><?= htmlspecialchars($job['location']) ?></td>
                            <td><?= date('d F Y', strtotime($job['posted_at'])) ?></td>
                            <td>
                                <a href="applicants.php?job_id=<?= $job['id'] ?>" style="margin-right:10px;">Pelamar</a>
                                <a href="post_job.php?id=<?= $job['id'] ?>" style="margin-right:10px;">Edit</a>
                                <a href="jobs.php?delete_id=<?= $job['id'] ?>" class="text-danger btn-delete">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message info">Anda belum memiliki lowongan aktif. <a href="post_job.php">Pasang lowongan pertama Anda!</a></div>
        <?php endif; ?>
    </div>
    <?php include '../partials/footer.php'; ?>
    <script src="../script.js"></script>
</body>
</html>
