<?php
/**
 * footer.php
 *
 * Semua link dan gambar sekarang menggunakan BASE_URL agar selalu benar.
 */
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Kolom 1: Tentang NextKarir & Social Media -->
            <div class="footer-col about">
                <a href="<?= BASE_URL ?>/index.php" class="footer-logo">
                    <img src="<?= BASE_URL ?>/NextKarir.png" alt="Logo NextKarir Footer">
                </a>
                <p class="footer-desc">
                    NextKarir adalah Situs lowongan kerja (Job Portal) layanan berbasis software fokus dibidang rekrutmen untuk mempermudah cari pekerjaan dan perekrutan karyawan.
                </p>
                <div class="social-icons">
                    <a href="#" title="X/Twitter">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>
                    </a>
                    <a href="#" title="Instagram">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.85s-.012 3.584-.07 4.85c-.148 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07s-3.584-.012-4.85-.07c-3.252-.148-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.85s.012-3.584.07-4.85c.148-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.85-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948s.014 3.667.072 4.947c.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072s3.667-.014 4.947-.072c4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.947s-.014-3.667-.072-4.947c-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.162 6.162 6.162 6.162-2.759 6.162-6.162-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4s1.791-4 4-4 4 1.79 4 4-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44 1.441-.645 1.441-1.44-.645-1.44-1.441-1.44z"></path></svg>
                    </a>
                    <a href="#" title="LinkedIn">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Kolom 2: Tentang Kami -->
            <div class="footer-col links">
                <h4>Tentang Kami</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/about.php">Hubungi Kami</a></li>
                    <li><a href="#">Pusat Bantuan</a></li>
                    <li><a href="#">Logo</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Kondisi dan Ketentuan</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Pencari Kerja -->
            <div class="footer-col links">
                <h4>Pencari Kerja</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/register.php?role=job_seeker">Registrasi Pencari Kerja</a></li>
                    <li><a href="<?= BASE_URL ?>/jobseeker/edit_profile.php">Buat Resume Online</a></li>
                    <li><a href="<?= BASE_URL ?>/search.php">Cari Lowongan Kerja</a></li>
                    <li><a href="#">Remote Jobs</a></li>
                    <li><a href="#">Jobs Alert</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Perusahaan -->
            <div class="footer-col links">
                <h4>Perusahaan</h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/register.php?role=company">Registrasi Perusahaan</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/post_job.php">Pasang Loker</a></li>
                    <li><a href="#">Produk dan Layanan</a></li>
                    <li><a href="#">Harga</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> PT. NextKarir Asli Indonesia</p>
        </div>
    </div>
</footer>
