<?php
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$job_id = $_GET['id'];

// Ambil data, termasuk ID perusahaan
$stmt = $conn->prepare("
    SELECT j.*, c.id as company_id, c.name as company_name, c.description as company_desc, c.website, c.logo 
    FROM jobs j 
    JOIN companies c ON j.company_id = c.id 
    WHERE j.id = ?
");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$job = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$job) {
    header("Location: index.php?error=notfound");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($job['title']) ?> - NextKarir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <div class="card" style="padding: 30px; margin-top: 20px;">
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <img src="uploads/logos/<?= htmlspecialchars($job['logo']) ?>" alt="Logo" style="width: 100px; height: 100px; object-fit: contain; border: 1px solid #eee; border-radius: 8px;">
                <div>
                    <h1><?= htmlspecialchars($job['title']) ?></h1>
                    <!-- NAMA PERUSAHAAN SEKARANG MENJADI LINK -->
                    <h3 style="margin-top: -10px; color: #555;">
                        <a href="company_profile.php?id=<?= $job['company_id'] ?>" class="company-profile-link"><?= htmlspecialchars($job['company_name']) ?></a>
                    </h3>
                    <div class="details" style="margin-bottom: 20px;">
                        <span>📍 Lokasi: <?= htmlspecialchars($job['location']) ?></span>
                        <span>💰 Gaji: <?= htmlspecialchars($job['salary']) ?></span>
                    </div>
                </div>
            </div>
            <hr style="margin: 20px 0;">
            <h3>Deskripsi Pekerjaan</h3>
            <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
            <h3 style="margin-top: 20px;">Kualifikasi</h3>
            <p><?= nl2br(htmlspecialchars($job['requirements'])) ?></p>
            <div style="margin-top: 30px;">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'job_seeker'): ?>
                    <a href="/nextkarir/jobseeker/apply.php?job_id=<?= $job['id'] ?>" class="btn btn-primary">Lamar Sekarang</a>
                <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'company'): ?>
                     <p class="message info">Anda login sebagai perusahaan. Silakan login sebagai pencari kerja untuk melamar.</p>
                <?php else: ?>
                    <p class="message info">Silakan <a href="/nextkarir/login.php">login</a> sebagai pencari kerja untuk melamar.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include 'partials/footer.php'; ?>
</body>
</html>
