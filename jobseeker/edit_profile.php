<?php
require_once '../config/db.php';

// Autentikasi: Pastikan hanya job seeker yang bisa akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

// Daftar bidang pekerjaan yang tersedia
$job_fields = [
    'Teknologi Informasi', 'Keuangan & Akuntansi', 'Pemasaran & Penjualan', 'Otomotif',
    'Kesehatan', 'Logistik', 'Design, Media & Kreatif', 'Layanan Pelanggan', 'Pendidikan', 'Fashion'
];

$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = '';

// Ambil ID profil dari tabel job_seekers
$stmt = $conn->prepare("SELECT id FROM job_seekers WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Error Kritis! Profil pencari kerja untuk user_id " . $user_id . " tidak ditemukan di tabel job_seekers.");
}
$job_seeker_id = $result->fetch_assoc()['id'];
$stmt->close();


// --- PROSES UPDATE DATA (Method: POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $headline = trim($_POST['headline']);
    $education = trim($_POST['education']);
    $experience = trim($_POST['experience']);
    $skills = trim($_POST['skills']);
    $job_field_preference = trim($_POST['job_field_preference']);

    if (empty($full_name)) {
        $errors[] = "Nama lengkap tidak boleh kosong.";
    }
    if (!empty($job_field_preference) && !in_array($job_field_preference, $job_fields)) {
        $errors[] = "Bidang pekerjaan yang diminati tidak valid.";
    }

    $resume_path_to_db = null;
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $target_dir = "../uploads/resumes/";
        $file_extension = pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION);
        $unique_filename = "resume_" . $job_seeker_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $unique_filename;
        if (strtolower($file_extension) != "pdf") {
            $errors[] = "Maaf, hanya file PDF yang diizinkan untuk diupload.";
        } else {
            if (move_uploaded_file($_FILES["tmp_name"], $target_file)) {
                $resume_path_to_db = $unique_filename;
            } else {
                $errors[] = "Maaf, terjadi kesalahan saat mengupload file Anda.";
            }
        }
    }

    if (empty($errors)) {
        if ($resume_path_to_db) {
            $sql = "UPDATE job_seekers SET full_name=?, headline=?, education=?, experience=?, skills=?, job_field_preference=?, resume_path=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $full_name, $headline, $education, $experience, $skills, $job_field_preference, $resume_path_to_db, $job_seeker_id);
        } else {
            $sql = "UPDATE job_seekers SET full_name=?, headline=?, education=?, experience=?, skills=?, job_field_preference=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssi", $full_name, $headline, $education, $experience, $skills, $job_field_preference, $job_seeker_id);
        }

        if ($stmt->execute()) {
            $success_message = "Profil berhasil diperbarui!";
        } else {
            $errors[] = "Gagal memperbarui profil. Silakan coba lagi.";
        }
        $stmt->close();
    }
}

// --- AMBIL DATA TERBARU UNTUK DITAMPILKAN DI FORM ---
$stmt = $conn->prepare("SELECT * FROM job_seekers WHERE id = ?");
$stmt->bind_param("i", $job_seeker_id);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - NextKarir</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>
    <div class="container">
        <div class="form-container" style="max-width: 800px;">
            <h2>Lengkapi Profil Anda</h2>
            <p>Semakin lengkap profil Anda, semakin besar peluang Anda ditemukan oleh perusahaan impian.</p>

            <?php if (!empty($errors)): ?>
                <div class="message error"><?php foreach ($errors as $error) echo "<p>" . htmlspecialchars($error) . "</p>"; ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="message success"><p><?= htmlspecialchars($success_message) ?> <a href="<?= BASE_URL ?>/jobseeker/dashboard.php">Kembali ke Dashboard</a></p></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/jobseeker/edit_profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="full_name">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="headline">Headline Profesional</label>
                    <input type="text" id="headline" name="headline" value="<?= htmlspecialchars($profile['headline'] ?? '') ?>" placeholder="Contoh: Full-Stack Developer | Mahasiswa Teknik Informatika">
                </div>
                <!-- FORM GROUP UNTUK BIDANG PEKERJAAN -->
                <div class="form-group">
                    <label for="job_field_preference">Bidang Pekerjaan yang Diminati</label>
                    <select name="job_field_preference" id="job_field_preference">
                        <option value="">-- Pilih Bidang (Opsional) --</option>
                        <?php foreach ($job_fields as $field): ?>
                            <option value="<?= $field ?>" <?= (isset($profile['job_field_preference']) && $profile['job_field_preference'] == $field) ? 'selected' : '' ?>>
                                <?= $field ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="education">Pendidikan</label>
                    <textarea id="education" name="education" rows="4" placeholder="Contoh: S1 Teknik Informatika, Universitas Gadjah Mada (2020 - 2024)"><?= htmlspecialchars($profile['education'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="experience">Pengalaman Kerja</label>
                    <textarea id="experience" name="experience" rows="6" placeholder="Contoh: Web Developer Intern - PT. Teknologi Maju (Jan 2023 - Jun 2023)"><?= htmlspecialchars($profile['experience'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="skills">Keahlian</label>
                    <input type="text" id="skills" name="skills" value="<?= htmlspecialchars($profile['skills'] ?? '') ?>" placeholder="Contoh: PHP, JavaScript, MySQL, HTML5, CSS3">
                </div>
                <div class="form-group">
                    <label for="resume">Upload Resume/CV (Format: PDF)</label>
                    <input type="file" id="resume" name="resume" accept=".pdf">
                    <?php if (!empty($profile['resume_path'])): ?>
                        <p style="font-size: 0.9em; margin-top: 10px;">
                            File saat ini: <a href="<?= BASE_URL ?>/uploads/resumes/<?= htmlspecialchars($profile['resume_path']) ?>" target="_blank"><?= htmlspecialchars($profile['resume_path']) ?></a>
                        </p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
