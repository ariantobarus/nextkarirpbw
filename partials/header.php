<?php
/**
 * header.php
 *
 * Menampilkan navigasi utama situs, termasuk link "Home".
 */
?>
<header class="navbar">
    <a href="/nextkarir/index.php" class="logo">
        <img src="/nextkarir/NextKarir.png" alt="Logo NextKarir">
        <span class="logo-text">NextKarir</span>
    </a>
    <nav class="nav-links">
        <a href="/nextkarir/index.php">Home</a>
        <a href="/nextkarir/search.php">Cari Lowongan</a>
        <a href="/nextkarir/about.php">Tentang Kami</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] == 'job_seeker'): ?>
                <a href="/nextkarir/jobseeker/dashboard.php">Dashboard</a>
            <?php elseif ($_SESSION['role'] == 'company'): ?>
                <a href="/nextkarir/admin/dashboard.php">Dashboard</a>
            <?php endif; ?>
            <a href="/nextkarir/logout.php">Logout</a>
        <?php else: ?>
            <a href="/nextkarir/login.php">Login</a>
            <a href="/nextkarir/register.php" class="btn btn-primary">Daftar</a>
        <?php endif; ?>
    </nav>
</header>
