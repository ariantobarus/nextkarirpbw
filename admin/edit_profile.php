<?php
require_once '../config/db.php';

// Autentikasi & Otorisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: /nextkarir/login.php");
    exit();
}

// Daftar bidang pekerjaan yang tersedia (disamakan dengan halaman utama)
$job_fields = [
    'Teknologi Informasi', 'Keuangan & Akuntansi', 'Pemasaran & Penjualan', 'Otomotif',
    'Kesehatan', 'Logistik', 'Design, Media & Kreatif', 'Layanan Pelanggan', 'Pendidikan', 'Fashion'
];

$user_id = $_SESSION['user_id'];
$errors = [];
$success_message = '';

// --- PROSES UPDATE DATA (Method: POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $website = trim($_POST['website']);
    $job_field = trim($_POST['job_field']); // Ambil data bidang pekerjaan

    if (empty($name)) {
        $errors[] = "Nama perusahaan tidak boleh kosong.";
    }
    // Validasi hanya jika bidang pekerjaan diisi
    if (!empty($job_field) && !in_array($job_field, $job_fields)) {
        $errors[] = "Bidang pekerjaan tidak valid.";
    }

    if (empty($errors)) {
        // Update query untuk menyertakan job_field
        $stmt = $conn->prepare("UPDATE companies SET name = ?, description = ?, address = ?, website = ?, job_field = ? WHERE user_id = ?");
        $stmt->bind_param("sssssi", $name, $description, $address, $website, $job_field, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "Profil perusahaan berhasil diperbarui!";
        } else {
            $errors[] = "Gagal memperbarui profil. Silakan coba lagi.";
        }
        $stmt->close();
    }
}

// --- AMBIL DATA TERBARU UNTUK DITAMPILKAN DI FORM ---
$stmt = $conn->prepare("SELECT * FROM companies WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$company = $stmt->get_result()->fetch_assoc();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Perusahaan - NextKarir</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <div class="container">
        <div class="form-container" style="max-width: 800px;">
            <h2>Edit Profil Perusahaan</h2>
            
            <?php if ($success_message): ?>
                <div class="message success"><p><?= htmlspecialchars($success_message) ?></p></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="message error"><?php foreach ($errors as $error) echo "<p>" . htmlspecialchars($error) . "</p>"; ?></div>
            <?php endif; ?>

            <form action="edit_profile.php" method="POST">
                <div class="form-group">
                    <label for="name">Nama Perusahaan</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($company['name'] ?? '') ?>" required>
                </div>

                <!-- FORM GROUP UNTUK BIDANG PEKERJAAN YANG DIPERBARUI -->
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

    <?php include '../partials/footer.php'; ?>
</body>
</html>
