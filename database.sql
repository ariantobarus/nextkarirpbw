-- Menghapus database lama jika ada, untuk memulai dari awal (opsional)
DROP DATABASE IF EXISTS nextkarir_db;

-- Membuat Database baru
CREATE DATABASE IF NOT EXISTS nextkarir_db;
USE nextkarir_db;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `users`
-- Menyimpan data login untuk semua tipe pengguna.
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('job_seeker','company') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `companies`
-- Menyimpan profil detail perusahaan.
--
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `job_field` varchar(255) DEFAULT NULL, -- Kolom bidang pekerjaan
  `logo` varchar(255) DEFAULT 'default_logo.png',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `job_seekers`
-- Menyimpan profil detail pencari kerja.
--
CREATE TABLE `job_seekers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `education` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `job_field_preference` varchar(255) DEFAULT NULL, -- Kolom preferensi bidang
  `resume_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `job_seekers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `jobs`
-- Menyimpan semua data lowongan pekerjaan.
--
CREATE TABLE `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text NOT NULL,
  `location` varchar(100) NOT NULL,
  `salary` varchar(100) DEFAULT 'Dirahasiakan',
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `applications`
-- Menyimpan data lamaran dari pencari kerja ke lowongan.
--
CREATE TABLE `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','reviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `job_id` (`job_id`),
  KEY `job_seeker_id` (`job_seeker_id`),
  CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`job_seeker_id`) REFERENCES `job_seekers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Contoh Data Awal (Minimal 10 baris)
--

-- 1. User Perusahaan
INSERT INTO `users` (`email`, `password`, `role`) VALUES
('admin@techcorp.com', '$2y$10$examplehashedpassword1', 'company'),
('hr@inovasidigital.com', '$2y$10$examplehashedpassword2', 'company');

-- 2. Profil Perusahaan
INSERT INTO `companies` (`user_id`, `name`, `description`, `address`, `job_field`) VALUES
(1, 'TechCorp Indonesia', 'Perusahaan teknologi terdepan di Asia Tenggara.', 'Jl. Jenderal Sudirman Kav. 52-53, Jakarta', 'Teknologi Informasi'),
(2, 'Inovasi Digital', 'Agensi digital kreatif yang berfokus pada solusi web dan mobile.', 'Jl. Gatot Subroto No. 12, Bandung', 'Pemasaran & Penjualan');

-- 3. User Pencari Kerja
INSERT INTO `users` (`email`, `password`, `role`) VALUES
('budi.santoso@email.com', '$2y$10$examplehashedpassword3', 'job_seeker'),
('siti.rahayu@email.com', '$2y$10$examplehashedpassword4', 'job_seeker'),
('ahmad.wijaya@email.com', '$2y$10$examplehashedpassword5', 'job_seeker');

-- 4. Profil Pencari Kerja
INSERT INTO `job_seekers` (`user_id`, `full_name`, `headline`, `skills`, `job_field_preference`) VALUES
(3, 'Budi Santoso', 'Full-Stack Web Developer', 'PHP, JavaScript, MySQL, HTML5, CSS3', 'Teknologi Informasi'),
(4, 'Siti Rahayu', 'UI/UX Designer', 'Figma, Adobe XD, Prototyping, User Research', 'Design, Media & Kreatif'),
(5, 'Ahmad Wijaya', 'Digital Marketing Specialist', 'SEO, SEM, Google Analytics, Social Media Ads', 'Pemasaran & Penjualan');

-- 5. Lowongan Pekerjaan (Minimal 10)
INSERT INTO `jobs` (`company_id`, `title`, `description`, `requirements`, `location`, `salary`) VALUES
(1, 'Senior PHP Developer', 'Membangun dan memelihara aplikasi web back-end.', 'Minimal 5 tahun pengalaman dengan PHP & Laravel.', 'Jakarta', 'Rp 15.000.000 - Rp 25.000.000'),
(1, 'Frontend Developer (React)', 'Mengembangkan antarmuka pengguna yang interaktif.', 'Menguasai React.js, Redux, dan Webpack.', 'Jakarta', 'Rp 12.000.000 - Rp 20.000.000'),
(2, 'Project Manager', 'Mengelola proyek pengembangan software dari awal hingga akhir.', 'Pengalaman 3 tahun sebagai PM di agensi digital.', 'Bandung', 'Rp 10.000.000 - Rp 18.000.000'),
(1, 'DevOps Engineer', 'Mengelola infrastruktur cloud dan CI/CD pipeline.', 'Mahir menggunakan AWS, Docker, dan Jenkins.', 'Remote', 'Rp 18.000.000 - Rp 30.000.000'),
(2, 'Content Writer', 'Menulis konten menarik untuk blog dan media sosial klien.', 'Portofolio tulisan yang kuat di bidang teknologi.', 'Bandung', 'Rp 6.000.000 - Rp 9.000.000'),
(1, 'Quality Assurance Engineer', 'Memastikan kualitas produk software melalui pengujian manual dan otomatis.', 'Pengalaman dengan Selenium atau Cypress.', 'Jakarta', 'Rp 8.000.000 - Rp 14.000.000'),
(2, 'Graphic Designer', 'Membuat aset visual untuk kebutuhan marketing dan produk.', 'Mahir menggunakan Adobe Creative Suite.', 'Bandung', 'Rp 7.000.000 - Rp 11.000.000'),
(1, 'Data Analyst', 'Menganalisis data untuk memberikan insight bisnis.', 'Menguasai SQL, Python (Pandas), dan Tableau.', 'Jakarta', 'Rp 9.000.000 - Rp 16.000.000'),
(2, 'Social Media Manager', 'Mengelola semua akun media sosial perusahaan dan klien.', 'Terbukti dapat meningkatkan engagement dan followers.', 'Remote', 'Rp 7.000.000 - Rp 12.000.000'),
(1, 'IT Support Specialist', 'Memberikan dukungan teknis internal untuk karyawan.', 'Memahami troubleshooting hardware dan software.', 'Jakarta', 'Rp 5.000.000 - Rp 8.000.000');
