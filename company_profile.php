<?php
require_once __DIR__ . '/config/db.php';

// Validasi ID perusahaan dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}
$company_id = $_GET['id'];

// Ambil data detail perusahaan
$stmt = $conn->prepare("SELECT * FROM companies WHERE id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$company = $result->fetch_assoc();
$stmt->close();

// Jika perusahaan tidak ditemukan, redirect
if (!$company) {
    header("Location: " . BASE_URL . "/index.php?error=company_not_found");
    exit();
}

// Ambil semua lowongan aktif dari perusahaan ini
$stmt = $conn->prepare("SELECT * FROM jobs WHERE company_id = ? ORDER BY posted_at DESC");
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
    <title>Profil <?= htmlspecialchars($company['name']) ?> - NextKarir</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>

    <div class="company-profile-header">
        <div class="container">
            <img src="<?= BASE_URL ?>/uploads/logos/<?= htmlspecialchars($company['logo']) ?>" alt="Logo <?= htmlspecialchars($company['name']) ?>" class="company-logo-large">
            <h1><?= htmlspecialchars($company['name']) ?></h1>
            <p class="company-field"><?= htmlspecialchars($company['job_field'] ?: 'Bidang tidak ditentukan') ?></p>
            <?php if ($company['website']): ?>
                <a href="<?= htmlspecialchars($company['website']) ?>" class="company-website-link" target="_blank">Kunjungi Situs Web</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="card" style="padding: 30px;">
                <h3>Tentang Perusahaan</h3>
                <p><?= nl2br(htmlspecialchars($company['description'] ?: 'Deskripsi perusahaan belum tersedia.')) ?></p>
            </div>

            <h2 style="margin-top: 40px;">Lowongan Tersedia di <?= htmlspecialchars($company['name']) ?></h2>
            <div class="job-listings">
                <?php if (!empty($jobs)): ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="card job-card">
                            <img src="<?= BASE_URL ?>/uploads/logos/<?= htmlspecialchars($company['logo']) ?>" alt="Logo">
                            <div>
                                <h3><a href="<?= BASE_URL ?>/job_detail.php?id=<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?></a></h3>
                                <p class="company-name"><?= htmlspecialchars($company['name']) ?></p>
                                <div class="details">
                                    <span>📍 <?= htmlspecialchars($job['location']) ?></span>
                                    <span>💰 <?= htmlspecialchars($job['salary']) ?></span>
                                </div>
                            </div>
                            <a href="<?= BASE_URL ?>/job_detail.php?id=<?= $job['id'] ?>" class="btn btn-primary">Lihat Detail</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="message info">Saat ini belum ada lowongan tersedia dari perusahaan ini.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
