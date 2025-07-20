<?php
require_once __DIR__ . '/config/db.php';
$errors = [];

// Redirect jika pengguna sudah login
if (isset($_SESSION['user_id'])) {
    $redirect_url = $_SESSION['role'] == 'company' ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/jobseeker/dashboard.php';
    header("Location: " . $redirect_url);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Email dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil: set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Arahkan berdasarkan peran
                if ($user['role'] == 'company') {
                    header("Location: " . BASE_URL . "/admin/dashboard.php");
                } else {
                    header("Location: " . BASE_URL . "/jobseeker/dashboard.php");
                }
                exit();
            } else {
                $errors[] = "Kombinasi email dan password salah.";
            }
        } else {
            $errors[] = "Kombinasi email dan password salah.";
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
    <title>Login - NextKarir</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>
    <div class="main-content">
        <div class="form-container">
            <h2>Login ke Akun Anda</h2>
            <?php if(!empty($errors)): ?>
                <div class="message error">
                    <?php foreach($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form action="<?= BASE_URL ?>/login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            <p style="text-align: center; margin-top: 20px;">Belum punya akun? <a href="<?= BASE_URL ?>/register.php">Daftar sekarang</a></p>
        </div>
    </div>
    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
