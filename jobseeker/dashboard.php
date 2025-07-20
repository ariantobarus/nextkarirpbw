<?php
require_once '../config/db.php'; // Path disesuaikan karena file ada di dalam subfolder

// Autentikasi: Pastikan hanya job seeker yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Ambil data profil job seeker
$stmt = $conn->prepare("SELECT * FROM job_seekers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$job_seeker = $result->fetch_assoc();
$job_seeker_id = $job_seeker['id'];
$stmt->close();

// 2. Hitung statistik lamaran
$stats = ['pending' => 0, 'reviewed' => 0, 'accepted' => 0, 'rejected' => 0, 'total' => 0];
$stmt = $conn->prepare("SELECT status, COUNT(id) as count FROM applications WHERE job_seeker_id = ? GROUP BY status");
$stmt->bind_param("i", $job_seeker_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $stats[$row['status']] = $row['count'];
    $stats['total'] += $row['count'];
}
$stmt->close();

// 3. Ambil 3 lamaran terbaru
$stmt = $conn->prepare("
    SELECT j.title, c.name as company_name, a.application_date, a.status 
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN companies c ON j.company_id = c.id
    WHERE a.job_seeker_id = ?
    ORDER BY a.application_date DESC
    LIMIT 3
");
$stmt->bind_param("i", $job_seeker_id);
$stmt->execute();
$recent_applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 4. Hitung kelengkapan profil
$profile_completeness = 20; // Dasar 20% untuk data nama
if (!empty($job_seeker['headline'])) $profile_completeness += 20;
if (!empty($job_seeker['education'])) $profile_completeness += 20;
if (!empty($job_seeker['experience'])) $profile_completeness += 20;
if (!empty($job_seeker['skills'])) $profile_completeness += 20;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NextKarir</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>

    <div class="container">
        <h1>Selamat Datang, <?= htmlspecialchars($job_seeker['full_name']) ?>!</h1>
        <p>Ini adalah pusat kendali karir Anda. Pantau lamaran dan kelola profil Anda dari sini.</p>

        <!-- Grid Statistik Cepat -->
        <div class="dashboard-grid" style="margin-top: 30px;">
            <div class="dashboard-card card-info"><div class="count"><?= $stats['total'] ?></div><div class="title">Lamaran Terkirim</div></div>
            <div class="dashboard-card card-warning"><div class="count"><?= $stats['reviewed'] ?></div><div class="title">Lamaran Dilihat</div></div>
            <div class="dashboard-card card-success"><div class="count"><?= $stats['accepted'] ?></div><div class="title">Diterima Kerja</div></div>
            <div class="dashboard-card card-danger"><div class="count"><?= $stats['rejected'] ?></div><div class="title">Lamaran Ditolak</div></div>
        </div>

        <!-- Kelengkapan Profil & Aksi Cepat -->
        <div class="dashboard-actions">
            <div class="card profile-completeness">
                <h4>Kelengkapan Profil Anda</h4>
                <div class="progress-bar"><div class="progress" style="width: <?= $profile_completeness ?>%;"></div></div>
                <span class="progress-text"><?= $profile_completeness ?>% Lengkap</span>
                <p>Profil yang lengkap meningkatkan peluang Anda dilirik perusahaan hingga 70%!</p>
                <a href="<?= BASE_URL ?>/jobseeker/edit_profile.php" class="btn btn-primary">Lengkapi Profil Sekarang</a>
            </div>
            <div class="card quick-search">
                 <h4>Siap untuk Langkah Berikutnya?</h4>
                 <p>Ribuan lowongan baru menanti Anda. Temukan yang paling cocok untuk Anda sekarang.</p>
                 <a href="<?= BASE_URL ?>/search.php" class="btn btn-secondary">Cari Lowongan Kerja</a>
            </div>
        </div>

        <!-- Riwayat Lamaran Terbaru -->
        <h2 style="margin-top: 40px;">Riwayat Lamaran Terbaru</h2>
        <?php if (!empty($recent_applications)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Posisi</th>
                        <th>Perusahaan</th>
                        <th>Tanggal Melamar</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_applications as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['title']) ?></td>
                            <td><?= htmlspecialchars($app['company_name']) ?></td>
                            <td><?= date('d F Y', strtotime($app['application_date'])) ?></td>
                            <td><span class="status-badge status-<?= htmlspecialchars($app['status']) ?>"><?= ucfirst(str_replace('_', ' ', $app['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="text-align: right; margin-top: 15px;">
                <a href="<?= BASE_URL ?>/jobseeker/applications.php">Lihat Semua Riwayat Lamaran &rarr;</a>
            </div>
        <?php else: ?>
            <div class="message info">Anda belum pernah melamar pekerjaan. <a href="<?= BASE_URL ?>/search.php">Cari lowongan sekarang!</a></div>
        <?php endif; ?>

    </div>

    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
