<?php
require_once __DIR__ . '/config/db.php';

// Validasi: Pastikan ID lowongan ada dan merupakan angka
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_URL . "/index.php");
    exit();
}

$job_id = $_GET['id'];

// Ambil data detail lowongan beserta informasi perusahaan, termasuk ID perusahaan
$stmt = $conn->prepare("
    SELECT j.*, c.id as company_id, c.name as company_name, c.description as company_desc, c.website, c.logo 
    FROM jobs j 
    JOIN companies c ON j.company_id = c.id 
    WHERE j.id = ?
");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();

// Jika lowongan dengan ID tersebut tidak ditemukan, kembalikan ke halaman utama
if (!$job) {
    header("Location: " . BASE_URL . "/index.php?error=notfound");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($job['title']) ?> - NextKarir</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>

    <div class="main-content">
        <div class="container">
            <div class="card" style="padding: 30px; margin-top: 20px;">
                <div style="display: flex; gap: 20px; align-items: flex-start;">
                    <img src="<?= BASE_URL ?>/uploads/logos/<?= htmlspecialchars($job['logo']) ?>" alt="Logo" style="width: 100px; height: 100px; object-fit: contain; border: 1px solid #eee; border-radius: 8px;">
                    <div>
                        <h1><?= htmlspecialchars($job['title']) ?></h1>
                        <h3 style="margin-top: -10px; color: #555;">
                            <a href="<?= BASE_URL ?>/company_profile.php?id=<?= $job['company_id'] ?>" class="company-profile-link"><?= htmlspecialchars($job['company_name']) ?></a>
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
                        <a href="<?= BASE_URL ?>/jobseeker/apply.php?job_id=<?= $job['id'] ?>" class="btn btn-primary btn-apply">Lamar Sekarang</a>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'company'): ?>
                         <p class="message info">Anda login sebagai perusahaan. Silakan login sebagai pencari kerja untuk melamar.</p>
                    <?php else: ?>
                        <p class="message info">Silakan <a href="<?= BASE_URL ?>/login.php">login</a> sebagai pencari kerja untuk melamar.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include ROOT_PATH . '/partials/footer.php'; ?>
    <script src="<?= BASE_URL ?>/script.js"></script>
</body>
</html>
