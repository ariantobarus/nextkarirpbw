<?php
require_once 'config/db.php';
$errors = [];

// Redirect if user is already logged in
if (isset($_SESSION['user_id'])) {
    $redirect_url = $_SESSION['role'] == 'company' ? 'admin/dashboard.php' : 'jobseeker/dashboard.php';
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
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Login successful: set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] == 'company') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: jobseeker/dashboard.php");
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>
    <div class="form-container">
        <h2>Login ke Akun Anda</h2>
        <?php if(!empty($errors)): ?>
            <div class="message error">
                <?php foreach($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="login.php" method="POST">
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
        <p style="text-align: center; margin-top: 20px;">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
    </div>
    <?php include 'partials/footer.php'; ?>
</body>
</html>
