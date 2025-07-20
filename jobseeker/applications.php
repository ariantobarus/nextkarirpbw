<?php
require_once '../config/db.php';

// Autentikasi: Pastikan hanya job seeker yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: /nextkarir/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$job_seeker_id = $conn->query("SELECT id FROM job_seekers WHERE user_id = $user_id")->fetch_assoc()['id'];
$message = '';

// Tampilkan pesan notifikasi berdasarkan parameter URL
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $message = '<div class="message success">Anda berhasil melamar pekerjaan! Pantau statusnya di halaman ini.</div>';
    } elseif ($_GET['status'] == 'already_applied') {
        $message = '<div class="message info">Anda sudah pernah melamar untuk posisi ini sebelumnya.</div>';
    }
}

// Ambil semua riwayat lamaran pengguna
$stmt = $conn->prepare("
    SELECT j.title, c.name as company_name, a.application_date, a.status, j.id as job_id
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN companies c ON j.company_id = c.id
    WHERE a.job_seeker_id = ?
    ORDER BY a.application_date DESC
");
$stmt->bind_param("i", $job_seeker_id);
$stmt->execute();
$applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Lamaran - NextKarir</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
        <h1>Riwayat Lamaran Anda</h1>
        <p>Pantau semua perjalanan karir yang telah Anda mulai di sini.</p>
        
        <?= $message ?>

        <?php if (!empty($applications)): ?>
            <table class="data-table" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th>Posisi</th>
                        <th>Perusahaan</th>
                        <th>Tanggal Melamar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['title']) ?></td>
                            <td><?= htmlspecialchars($app['company_name']) ?></td>
                            <td><?= date('d F Y', strtotime($app['application_date'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($app['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $app['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/nextkarir/job_detail.php?id=<?= $app['job_id'] ?>">Lihat Lowongan</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message info" style="margin-top: 20px;">Anda belum pernah melamar pekerjaan. <a href="/nextkarir/search.php">Cari lowongan sekarang!</a></div>
        <?php endif; ?>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>
</html>
