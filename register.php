<?php
require_once __DIR__ . '/config/db.php';
$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role'];

    // --- Validation ---
    if (empty($email) || empty($password) || empty($role)) {
        $errors[] = "Semua field wajib diisi.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password minimal 8 karakter.";
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
    }
    $stmt->close();

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $conn->begin_transaction();
        try {
            // Insert into 'users' table
            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed_password, $role);
            $stmt->execute();
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Insert into the corresponding profile table
            if ($role == 'job_seeker') {
                $full_name = "Pengguna Baru"; // Default name
                $stmt = $conn->prepare("INSERT INTO job_seekers (user_id, full_name) VALUES (?, ?)");
                $stmt->bind_param("is", $user_id, $full_name);
            } else { // company
                $company_name = "Perusahaan Baru"; // Default name
                $stmt = $conn->prepare("INSERT INTO companies (user_id, name) VALUES (?, ?)");
                $stmt->bind_param("is", $user_id, $company_name);
            }
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            $success_message = "Registrasi berhasil! Silakan <a href='" . BASE_URL . "/login.php'>login</a>.";

        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $errors[] = "Terjadi kesalahan pada server. Silakan coba lagi nanti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - NextKarir</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>
    <div class="main-content">
        <div class="form-container">
            <h2>Buat Akun Baru</h2>
            <?php if(!empty($errors)): ?>
                <div class="message error">
                    <?php foreach($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if($success_message): ?>
                <div class="message success"><?= $success_message ?></div>
            <?php else: ?>
            <form action="<?= BASE_URL ?>/register.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                <div class="form-group">
                    <label>Saya mendaftar sebagai:</label>
                    <select name="role" required>
                        <option value="job_seeker" <?= (isset($role) && $role == 'job_seeker') ? 'selected' : '' ?>>Pencari Kerja</option>
                        <option value="company" <?= (isset($role) && $role == 'company') ? 'selected' : '' ?>>Perusahaan</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
            </form>
            <p style="text-align: center; margin-top: 20px;">Sudah punya akun? <a href="<?= BASE_URL ?>/login.php">Login di sini</a></p>
            <?php endif; ?>
        </div>
    </div>
    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
