<?php
require_once '../config/db.php';

// Autentikasi & Otorisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: " . BASE_URL . "/login.php");
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

// Tampilkan pesan sukses setelah membuat/mengedit lowongan
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Kelola Lowongan Anda</h1>
            <a href="<?= BASE_URL ?>/admin/post_job.php" class="btn btn-primary">Pasang Lowongan Baru</a>
        </div>
        
        <?= $message ?>

        <?php if (!empty($jobs)): ?>
            <div class="card" style="padding: 0;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Judul Posisi</th>
                            <th>Lokasi</th>
                            <th>Tanggal Posting</th>
                            <th style="width: 25%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <tr>
                                <td><?= htmlspecialchars($job['title']) ?></td>
                                <td><?= htmlspecialchars($job['location']) ?></td>
                                <td><?= date('d F Y', strtotime($job['posted_at'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/applicants.php?job_id=<?= $job['id'] ?>" class="btn btn-info" style="margin-right:5px; background-color: #17a2b8;">Pelamar</a>
                                    <a href="<?= BASE_URL ?>/admin/post_job.php?id=<?= $job['id'] ?>" class="btn btn-secondary" style="margin-right:5px;">Edit</a>
                                    <a href="<?= BASE_URL ?>/admin/jobs.php?delete_id=<?= $job['id'] ?>" class="btn btn-danger btn-delete">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="message info">Anda belum memiliki lowongan aktif. <a href="<?= BASE_URL ?>/admin/post_job.php">Pasang lowongan pertama Anda!</a></div>
        <?php endif; ?>
    </div>
    <?php include ROOT_PATH . '/partials/footer.php'; ?>
    <script src="<?= BASE_URL ?>/script.js"></script>
</body>
</html>
