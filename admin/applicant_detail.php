<?php
// ===================================================================
// FILE: admin/applicant_detail.php (LENGKAP DAN DIPERBAIKI)
// ===================================================================

require_once '../config/db.php';

// Autentikasi & Otorisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_URL . "/admin/applicants.php");
    exit();
}

$application_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$company_id = $conn->query("SELECT id FROM companies WHERE user_id = $user_id")->fetch_assoc()['id'];
$message = '';

// Proses Update Status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $allowed_statuses = ['pending', 'reviewed', 'accepted', 'rejected'];
    if (in_array($new_status, $allowed_statuses)) {
        $check_stmt = $conn->prepare("SELECT a.id FROM applications a JOIN jobs j ON a.job_id = j.id WHERE a.id = ? AND j.company_id = ?");
        $check_stmt->bind_param("ii", $application_id, $company_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows === 1) {
            $update_stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_status, $application_id);
            if ($update_stmt->execute()) {
                $message = '<div class="message success">Status lamaran berhasil diperbarui.</div>';
            }
            $update_stmt->close();
        }
        $check_stmt->close();
    }
}

// Ambil data detail pelamar
$stmt = $conn->prepare("
    SELECT js.*, a.status, a.application_date, j.title as job_title
    FROM applications a
    JOIN job_seekers js ON a.job_seeker_id = js.id
    JOIN jobs j ON a.job_id = j.id
    WHERE a.id = ? AND j.company_id = ?
");
$stmt->bind_param("ii", $application_id, $company_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Aplikasi tidak ditemukan atau Anda tidak memiliki akses.";
    exit();
}
$applicant = $result->fetch_assoc();
$stmt->close();

function get_initials($name) {
    $words = explode(" ", $name);
    $initials = ""; $i = 0;
    foreach ($words as $w) { if ($i < 2) { $initials .= mb_substr($w, 0, 1); $i++; } }
    return strtoupper($initials);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pelamar: <?= htmlspecialchars($applicant['full_name']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body class="detail-page-body">
    <?php include ROOT_PATH . '/partials/header.php'; ?>
    <div class="container">
        <a href="<?= BASE_URL ?>/admin/applicants.php" class="back-link">&larr; Kembali ke Daftar Pelamar</a>
        
        <?= $message ?>

        <!-- Profile Header -->
        <div class="profile-header card">
            <div class="profile-avatar"><?= get_initials($applicant['full_name']) ?></div>
            <div class="profile-info">
                <h1 class="profile-name"><?= htmlspecialchars($applicant['full_name']) ?></h1>
                <p class="profile-headline"><?= htmlspecialchars($applicant['headline'] ?: 'Headline belum diisi') ?></p>
                <div class="profile-meta">
                    <span>Melamar untuk: <strong><?= htmlspecialchars($applicant['job_title']) ?></strong></span>
                    <span>Tanggal Lamar: <strong><?= date('d F Y', strtotime($applicant['application_date'])) ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="applicant-detail-grid">
            <!-- Kolom Kiri: Detail -->
            <div class="main-details">
                <div class="detail-card card">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg> Profil Lengkap</h3>
                    <div class="detail-content">
                        <h4>Pendidikan</h4>
                        <p><?= nl2br(htmlspecialchars($applicant['education'] ?: 'Belum diisi.')) ?></p>
                        <h4>Pengalaman Kerja</h4>
                        <p><?= nl2br(htmlspecialchars($applicant['experience'] ?: 'Belum diisi.')) ?></p>
                        <h4>Keahlian</h4>
                        <p class="skills-pills">
                            <?php 
                            $skills = explode(',', $applicant['skills']);
                            foreach ($skills as $skill) {
                                if(trim($skill) != '') echo '<span>' . htmlspecialchars(trim($skill)) . '</span>';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Aksi -->
            <div class="action-sidebar">
                <div class="action-card card">
                    <h3><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg> Tindakan</h3>
                    <form class="action-form" action="<?= BASE_URL ?>/admin/applicant_detail.php?id=<?= $application_id ?>" method="POST">
                        <label for="status">Ubah Status Lamaran</label>
                        <div class="status-badge-current status-<?= $applicant['status'] ?>"><?= ucfirst(str_replace('_', ' ', $applicant['status'])) ?></div>
                        <select name="status" id="status">
                            <option value="pending" <?= $applicant['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="reviewed" <?= $applicant['status'] == 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                            <option value="accepted" <?= $applicant['status'] == 'accepted' ? 'selected' : '' ?>>Accepted</option>
                            <option value="rejected" <?= $applicant['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-secondary">Update Status</button>
                    </form>
                    <hr>
                    <?php if (!empty($applicant['resume_path'])): ?>
                        <a href="<?= BASE_URL ?>/uploads/resumes/<?= htmlspecialchars($applicant['resume_path']) ?>" class="btn btn-primary" target="_blank">Unduh Resume (PDF)</a>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Resume Tidak Tersedia</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
