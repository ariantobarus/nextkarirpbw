<?php
require_once '../config/db.php';

// Autentikasi & Otorisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: /nextkarir/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $conn->query("SELECT id FROM companies WHERE user_id = $user_id")->fetch_assoc()['id'];

$errors = [];
$job = ['id' => null, 'title' => '', 'description' => '', 'requirements' => '', 'location' => '', 'salary' => ''];
$page_title = "Pasang Lowongan Baru";
$button_text = "Publikasikan Lowongan";

// Mode Edit: Jika ada ID di URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $job_id = $_GET['id'];
    // Ambil data job, pastikan job tsb milik company yg login
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND company_id = ?");
    $stmt->bind_param("ii", $job_id, $company_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $job = $result->fetch_assoc();
        $page_title = "Edit Lowongan";
        $button_text = "Simpan Perubahan";
    } else {
        // Jika job tidak ditemukan atau bukan milik company ini, redirect
        header("Location: jobs.php");
        exit();
    }
    $stmt->close();
}

// Proses form (Create atau Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job['title'] = trim($_POST['title']);
    $job['description'] = trim($_POST['description']);
    $job['requirements'] = trim($_POST['requirements']);
    $job['location'] = trim($_POST['location']);
    $job['salary'] = trim($_POST['salary']);

    if (empty($job['title']) || empty($job['description']) || empty($job['requirements']) || empty($job['location'])) {
        $errors[] = "Semua field kecuali Gaji wajib diisi.";
    }

    if (empty($errors)) {
        // Jika ada ID, lakukan UPDATE. Jika tidak, lakukan INSERT.
        if (isset($_POST['job_id']) && !empty($_POST['job_id'])) { // UPDATE
            $stmt = $conn->prepare("UPDATE jobs SET title=?, description=?, requirements=?, location=?, salary=? WHERE id=? AND company_id=?");
            $stmt->bind_param("sssssii", $job['title'], $job['description'], $job['requirements'], $job['location'], $job['salary'], $_POST['job_id'], $company_id);
        } else { // INSERT
            $stmt = $conn->prepare("INSERT INTO jobs (company_id, title, description, requirements, location, salary) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $company_id, $job['title'], $job['description'], $job['requirements'], $job['location'], $job['salary']);
        }

        if ($stmt->execute()) {
            header("Location: jobs.php?status=success");
            exit();
        } else {
            $errors[] = "Terjadi kesalahan pada server. Gagal menyimpan data.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - NextKarir</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>
    <div class="container">
        <div class="form-container" style="max-width: 800px;">
            <h2><?= $page_title ?></h2>
            <?php if (!empty($errors)): ?>
                <div class="message error"><?php foreach ($errors as $error) echo "<p>" . htmlspecialchars($error) . "</p>"; ?></div>
            <?php endif; ?>
            <form action="post_job.php<?= $job['id'] ? '?id=' . $job['id'] : '' ?>" method="POST">
                <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                <div class="form-group">
                    <label for="title">Judul Posisi</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($job['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi Pekerjaan</label>
                    <textarea id="description" name="description" rows="6" required><?= htmlspecialchars($job['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="requirements">Kualifikasi / Persyaratan</label>
                    <textarea id="requirements" name="requirements" rows="6" required><?= htmlspecialchars($job['requirements']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="location">Lokasi</label>
                    <input type="text" id="location" name="location" value="<?= htmlspecialchars($job['location']) ?>" placeholder="Contoh: Jakarta, Remote" required>
                </div>
                <div class="form-group">
                    <label for="salary">Gaji (Opsional)</label>
                    <input type="text" id="salary" name="salary" value="<?= htmlspecialchars($job['salary']) ?>" placeholder="Contoh: Rp 5.000.000 - Rp 8.000.000">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;"><?= $button_text ?></button>
            </form>
        </div>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>
</html>
