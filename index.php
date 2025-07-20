<?php
// Memanggil file konfigurasi. __DIR__ memastikan path selalu benar.
require_once __DIR__ . '/config/db.php';

// Daftar bidang pekerjaan yang tersedia
$job_fields = [
    'Teknologi Informasi' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>',
    'Keuangan & Akuntansi' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>',
    'Pemasaran & Penjualan' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>',
    'Otomotif' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C20.7 10.1 21 9.6 21 9c0-1.1-.9-2-2-2h-1c-.6 0-1.1.4-1.4.9L13 12H4c-.6 0-1 .4-1 1v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>',
    'Kesehatan' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>',
    'Logistik' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 18v-4a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v4"></path><path d="M17 18v2"></path><path d="M7 18v2"></path><path d="M12 12v9"></path><path d="M22 12V6.4A2.4 2.4 0 0 0 20.2 4H3.8A2.4 2.4 0 0 0 2 6.4V12"></path></svg>',
    'Design, Media & Kreatif' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
    'Layanan Pelanggan' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 18a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><circle cx="12" cy="10" r="2"></circle><line x1="8" y1="2" x2="8" y2="4"></line><line x1="16" y1="2" x2="16" y2="4"></line></svg>',
    'Pendidikan' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c0 5 6 3 6 3s6-2 6-3v-5"></path></svg>',
    'Fashion' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path></svg>'
];

// Ambil 6 lowongan terbaru
$stmt = $conn->prepare("SELECT j.id, j.title, j.location, c.name as company_name, c.logo FROM jobs j JOIN companies c ON j.company_id = c.id ORDER BY j.posted_at DESC LIMIT 6");
$stmt->execute();
$latest_jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextKarir - Temukan Pekerjaan Impian Anda</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>
    <div class="hero-wrapper">
        <div class="container">
            <div class="hero-section">
                <h1>Temukan Pekerjaan Impian Anda</h1>
                <p>Jelajahi ribuan lowongan dari perusahaan-perusahaan terbaik di Indonesia.</p>
                <form action="<?= BASE_URL ?>/search.php" method="GET" class="hero-search-form"><input type="text" name="q" placeholder="Contoh: Web Developer" required><button type="submit">Cari</button></form>
            </div>
        </div>
    </div>
    <div class="main-content">
        <div class="container">
            <div class="category-section">
                <h2>Jelajahi Berdasarkan Bidang</h2>
                <div class="category-grid">
                    <?php foreach ($job_fields as $field => $icon): ?>
                        <a href="<?= BASE_URL ?>/search.php?field=<?= urlencode($field) ?>" class="category-card card"><?= $icon ?><h4><?= $field ?></h4></a>
                    <?php endforeach; ?>
                </div>
            </div>
            <h2 style="margin-top: 40px;">Lowongan Terbaru</h2>
            <div class="job-listings">
                <?php if (!empty($latest_jobs)): ?>
                    <?php foreach ($latest_jobs as $job): ?>
                        <div class="card job-card">
                            <img src="<?= BASE_URL ?>/uploads/logos/<?= htmlspecialchars($job['logo']) ?>" alt="Logo">
                            <div>
                                <h3><a href="<?= BASE_URL ?>/job_detail.php?id=<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?></a></h3>
                                <p class="company-name"><?= htmlspecialchars($job['company_name']) ?></p>
                                <div class="details"><span>📍 <?= htmlspecialchars($job['location']) ?></span></div>
                            </div>
                            <a href="<?= BASE_URL ?>/job_detail.php?id=<?= $job['id'] ?>" class="btn btn-primary">Detail</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="message info">Belum ada lowongan yang tersedia.</div>
                <?php endif; ?>
            </div>
            <div style="text-align: center; margin-top: 40px;"><a href="<?= BASE_URL ?>/search.php" class="btn btn-secondary">Lihat Semua Lowongan</a></div>
            <div class="promo-section card">
                <div class="promo-grid">
                    <div class="promo-image-col"><img src="<?= BASE_URL ?>/promosi.png" alt="Promosi Job Fair"></div>
                    <div class="promo-text-col">
                        <h4 class="promo-pre-header"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 2.5a2.5 2.5 0 0 1 5 0v1a.5.5 0 0 1-1 0v-1a1.5 1.5 0 0 0-3 0v1a.5.5 0 0 1-1 0v-1z"/><path d="M3.5 6.5A1.5 1.5 0 0 1 5 5h6a1.5 1.5 0 0 1 1.5 1.5v7a1.5 1.5 0 0 1-1.5 1.5H5a1.5 1.5 0 0 1-1.5-1.5v-7zM5 6a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h6a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5H5z"/></svg> Jobfair oleh NextKarir</h4>
                        <h2>Hadiri job fair kami dan temukan peluang karirmu!</h2>
                        <p>Mempertemukan para pemberi kerja terbaik dari berbagai industri untuk bertemu dengan para pencari kerja. Pelajari bagaimana kami membantu menemukan peluang untuk karirmu.</p>
                        <a href="#" class="btn btn-primary">Pelajari Lebih Lanjut</a>
                    </div>
                </div>
            </div>
            <div class="testimonial-section">
                <h2>Yang mereka katakan tentang NextKarir</h2>
                <p class="testimonial-subtitle">Wawasan berharga dari karyawan dan mantan karyawan tentang pengalaman mereka.</p>
                <div class="testimonial-grid">
                    <div class="testimonial-card card"><div class="testimonial-header"><div class="testimonial-avatar">A</div><div><div class="testimonial-name">Aristia</div><div class="testimonial-role">Human Resource</div></div></div><p class="testimonial-quote">"Makasih banyak NextKarir! Lowongannya sangat membantu aku yang belum bekerja ini, semoga yang lain juga bisa merasakan keuntungan dari NextKarir! sekali lagi terima kasih atas lowongannya."</p></div>
                    <div class="testimonial-card card"><div class="testimonial-header"><div class="testimonial-avatar">AA</div><div><div class="testimonial-name">Ani Aristiyani</div><div class="testimonial-role">Manajer Operasional</div></div></div><p class="testimonial-quote">"Senangnyaaa akhirnya aku ketemu dream job aku di sini. Aplikasinya informatif banget dan mudah dimengerti. Recommended 👍"</p></div>
                    <div class="testimonial-card card"><div class="testimonial-header"><div class="testimonial-avatar">Y</div><div><div class="testimonial-name">Yuli</div><div class="testimonial-role">Staf Keuangan</div></div></div><p class="testimonial-quote">"Pas lagi nunggu wisuda aku kayak ngga ada kerjaan. Nyoba daftar disini alhamdulillah dapet kerja sebelum wisuda. Makasih yah NextKarir buat aplikasinya. Sangat membantu"</p></div>
                </div>
            </div>
        </div>
    </div>
    <div class="cta-section">
        <div class="container">
            <div class="cta-grid">
                <div class="cta-text">
                    <h2>Temukan pekerjaan yang diinginkan</h2>
                    <p>Dapatkan kemudahan akses ke berbagai daftar pekerjaan, dan informasi tentang lowongan pekerjaan terbaru.</p>
                    <a href="<?= BASE_URL ?>/search.php" class="btn btn-light">Cari Lowongan Sekarang</a>
                </div>
                <div class="cta-image">
                    <img src="https://placehold.co/600x400/FFFFFF/1A73E8?text=Gambar+Anda" alt="Temukan Pekerjaan">
                </div>
            </div>
        </div>
    </div>

    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
