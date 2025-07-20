<?php
/**
 * header.php
 *
 * Semua link dan gambar sekarang menggunakan BASE_URL agar selalu benar.
 */
?>
<header class="navbar">
    <a href="<?= BASE_URL ?>/index.php" class="logo">
        <img src="<?= BASE_URL ?>/NextKarir.png" alt="Logo NextKarir">
        <span class="logo-text">NextKarir</span>
    </a>
    <nav class="nav-links">
        <a href="<?= BASE_URL ?>/index.php">Home</a>
        <a href="<?= BASE_URL ?>/search.php">Cari Lowongan</a>
        <a href="<?= BASE_URL ?>/about.php">Tentang Kami</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] == 'job_seeker'): ?>
                <a href="<?= BASE_URL ?>/jobseeker/dashboard.php">Dashboard</a>
            <?php elseif ($_SESSION['role'] == 'company'): ?>
                <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php">Login</a>
            <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary">Daftar</a>
        <?php endif; ?>
    </nav>
</header>
