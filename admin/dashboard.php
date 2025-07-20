<?php
require_once '../config/db.php'; // Path disesuaikan

// Autentikasi: Pastikan hanya admin/perusahaan yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: /nextkarir/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Ambil data profil perusahaan
$stmt = $conn->prepare("SELECT * FROM companies WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
$company_id = $company['id'];
$stmt->close();

// 2. Hitung statistik
// a. Jumlah lowongan aktif
$total_jobs_stmt = $conn->prepare("SELECT COUNT(id) as count FROM jobs WHERE company_id = ?");
$total_jobs_stmt->bind_param("i", $company_id);
$total_jobs_stmt->execute();
$total_jobs = $total_jobs_stmt->get_result()->fetch_assoc()['count'];
$total_jobs_stmt->close();

// b. Jumlah total pelamar
$total_applicants_stmt = $conn->prepare("SELECT COUNT(a.id) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.company_id = ?");
$total_applicants_stmt->bind_param("i", $company_id);
$total_applicants_stmt->execute();
$total_applicants = $total_applicants_stmt->get_result()->fetch_assoc()['count'];
$total_applicants_stmt->close();

// c. Jumlah pelamar baru (status 'pending')
$new_applicants_stmt = $conn->prepare("SELECT COUNT(a.id) as count FROM applications a JOIN jobs j ON a.job_id = j.id WHERE j.company_id = ? AND a.status = 'pending'");
$new_applicants_stmt->bind_param("i", $company_id);
$new_applicants_stmt->execute();
$new_applicants = $new_applicants_stmt->get_result()->fetch_assoc()['count'];
$new_applicants_stmt->close();

// 3. Ambil 5 lowongan terbaru
$recent_jobs_stmt = $conn->prepare("SELECT id, title, location, posted_at FROM jobs WHERE company_id = ? ORDER BY posted_at DESC LIMIT 5");
$recent_jobs_stmt->bind_param("i", $company_id);
$recent_jobs_stmt->execute();
$recent_jobs = $recent_jobs_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$recent_jobs_stmt->close();

// 4. Ambil 5 pelamar terbaru
$recent_applicants_stmt = $conn->prepare("
    SELECT js.full_name, j.title, a.application_date, a.id as application_id
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN job_seekers js ON a.job_seeker_id = js.id
    WHERE j.company_id = ?
    ORDER BY a.application_date DESC
    LIMIT 5
");
$recent_applicants_stmt->bind_param("i", $company_id);
$recent_applicants_stmt->execute();
$recent_applicants = $recent_applicants_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$recent_applicants_stmt->close();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin - NextKarir</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 1rem;">
        <img src="../uploads/logos/<?= htmlspecialchars($company['logo']) ?>" alt="Logo" style="width: 80px; height: 80px; object-fit: contain; border-radius: 8px; background-color: #f8f9fa; border: 1px solid #dee2e6;">
        <div>
            <h1> <?= htmlspecialchars($company['name']) ?></h1>
            
        </div>
    </div>

        <!-- Grid Statistik Cepat -->
        <div class="dashboard-grid" style="margin-top: 30px;">
            <div class="dashboard-card card-primary">
                <div class="count"><?= $total_jobs ?></div>
                <div class="title">Lowongan Aktif</div>
            </div>
            <div class="dashboard-card card-info">
                <div class="count"><?= $total_applicants ?></div>
                <div class="title">Total Pelamar</div>
            </div>
            <div class="dashboard-card card-success">
                <div class="count"><?= $new_applicants ?></div>
                <div class="title">Pelamar Baru</div>
            </div>
        </div>

        <!-- Aksi Cepat -->
        <div class="card" style="margin-top: 30px; padding: 25px;">
            <h4>Aksi Cepat</h4>
            <div class="quick-actions">
                <a href="post_job.php" class="btn btn-primary">Pasang Lowongan Baru</a>
                <a href="jobs.php" class="btn btn-secondary">Kelola Semua Lowongan</a>
                <a href="applicants.php" class="btn btn-secondary">Lihat Semua Pelamar</a>
                <a href="edit_profile.php" class="btn btn-secondary">Edit Profil Perusahaan</a>
            </div>
        </div>

        <!-- Dua Kolom: Lowongan Terbaru & Pelamar Terbaru -->
        <div class="dashboard-columns">
            <!-- Kolom Lowongan Terbaru -->
            <div class="column">
                <h2 style="margin-top: 40px;">Lowongan Terbaru Anda</h2>
                <?php if (!empty($recent_jobs)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Posisi</th>
                                <th>Lokasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_jobs as $job): ?>
                                <tr>
                                    <td><?= htmlspecialchars($job['title']) ?></td>
                                    <td><?= htmlspecialchars($job['location']) ?></td>
                                    <td><a href="applicants.php?job_id=<?= $job['id'] ?>">Lihat Pelamar</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="message info">Anda belum memasang lowongan. <a href="post_job.php">Pasang sekarang!</a></div>
                <?php endif; ?>
            </div>

            <!-- Kolom Pelamar Terbaru -->
            <div class="column">
                <h2 style="margin-top: 40px;">Pelamar Terbaru</h2>
                <?php if (!empty($recent_applicants)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Pelamar</th>
                                <th>Posisi yang Dilamar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_applicants as $applicant): ?>
                                <tr>
                                    <td><?= htmlspecialchars($applicant['full_name']) ?></td>
                                    <td><?= htmlspecialchars($applicant['title']) ?></td>
                                    <td><a href="applicant_detail.php?id=<?= $applicant['application_id'] ?>">Lihat Detail</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="message info">Belum ada pelamar untuk lowongan Anda.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../partials/footer.php'; ?>
</body>
</html>
