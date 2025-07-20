<?php
// ===================================================================
// FILE: admin/edit_profile.php (LENGKAP DAN DIPERBAIKI)
// ===================================================================

require_once '../config/db.php';

// Autentikasi & Otorisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
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

// Ambil data perusahaan saat ini untuk ditampilkan dan diproses
$stmt = $conn->prepare("SELECT * FROM companies WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$company) {
    die("Error: Data perusahaan tidak ditemukan.");
}

// --- PROSES UPDATE DATA (Method: POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $website = trim($_POST['website']);
    $job_field = trim($_POST['job_field']);

    if (empty($name)) {
        $errors[] = "Nama perusahaan tidak boleh kosong.";
    }
    if (!empty($job_field) && !in_array($job_field, $job_fields)) {
        $errors[] = "Bidang pekerjaan tidak valid.";
    }

    $logo_to_db = $company['logo'];
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $target_dir = ROOT_PATH . "/uploads/logos/";
        $image_info = getimagesize($_FILES["logo"]["tmp_name"]);
        $file_type = $image_info['mime'];
        
        if ($file_type == "image/jpeg" || $file_type == "image/png" || $file_type == "image/gif") {
            $extension = pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
            $unique_filename = "logo_" . $company['id'] . "_" . time() . "." . $extension;
            $target_file = $target_dir . $unique_filename;

            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                if ($company['logo'] != 'default_logo.png' && file_exists($target_dir . $company['logo'])) {
                    unlink($target_dir . $company['logo']);
                }
                $logo_to_db = $unique_filename;
            } else {
                $errors[] = "Maaf, terjadi kesalahan saat mengupload logo.";
            }
        } else {
            $errors[] = "Maaf, hanya file JPG, PNG, & GIF yang diizinkan.";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE companies SET name = ?, description = ?, address = ?, website = ?, job_field = ?, logo = ? WHERE user_id = ?");
        $stmt->bind_param("ssssssi", $name, $description, $address, $website, $job_field, $logo_to_db, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "Profil perusahaan berhasil diperbarui!";
            // Ambil ulang data terbaru setelah update
            $company['name'] = $name;
            $company['description'] = $description;
            $company['address'] = $address;
            $company['website'] = $website;
            $company['job_field'] = $job_field;
            $company['logo'] = $logo_to_db;
        } else {
            $errors[] = "Gagal memperbarui profil di database.";
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
    <title>Edit Profil Perusahaan - NextKarir</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>

    <div class="container">
        <div class="form-container" style="max-width: 800px;">
            <h2>Edit Profil Perusahaan</h2>

            <?php if (!empty($errors)): ?>
                <div class="message error"><?php foreach ($errors as $error) echo "<p>" . htmlspecialchars($error) . "</p>"; ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="message success"><p><?= htmlspecialchars($success_message) ?></p></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/admin/edit_profile.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Logo Saat Ini</label>
                    <img src="<?= BASE_URL ?>/uploads/logos/<?= htmlspecialchars($company['logo']) ?>" alt="Logo Perusahaan" style="width: 150px; height: auto; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                </div>
                <div class="form-group">
                    <label for="logo">Ganti Logo (JPG, PNG, GIF)</label>
                    <input type="file" id="logo" name="logo" accept="image/*">
                </div>
                <hr style="margin: 30px 0;">
                <div class="form-group">
                    <label for="name">Nama Perusahaan</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($company['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="job_field">Bidang Pekerjaan Perusahaan</label>
                    <select name="job_field" id="job_field">
                        <option value="">-- Pilih Bidang (Opsional) --</option>
                        <?php foreach ($job_fields as $field): ?>
                            <option value="<?= $field ?>" <?= (isset($company['job_field']) && $company['job_field'] == $field) ? 'selected' : '' ?>>
                                <?= $field ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi Perusahaan</label>
                    <textarea id="description" name="description" rows="5"><?= htmlspecialchars($company['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="address">Alamat</label>
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($company['address'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="website">Situs Web</label>
                    <input type="text" id="website" name="website" value="<?= htmlspecialchars($company['website'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Simpan Perubahan</button>
            </form>
        </div>
    </div>
    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
