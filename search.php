<?php
require_once 'config/db.php';

$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$search_field = isset($_GET['field']) ? trim($_GET['field']) : '';
$results = [];

$sql = "SELECT j.id, j.title, j.location, j.salary, c.name as company_name, c.logo 
        FROM jobs j 
        JOIN companies c ON j.company_id = c.id";

$where_clauses = [];
$params = [];
$types = "";

// Tambahkan filter untuk kata kunci
if (!empty($search_query)) {
    $where_clauses[] = "(j.title LIKE ? OR j.description LIKE ? OR c.name LIKE ?)";
    $param_like = "%" . $search_query . "%";
    $params[] = $param_like;
    $params[] = $param_like;
    $params[] = $param_like;
    $types .= "sss";
}

// Tambahkan filter untuk bidang pekerjaan
if (!empty($search_field)) {
    $where_clauses[] = "c.job_field = ?";
    $params[] = $search_field;
    $types .= "s";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY j.posted_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Lowongan - NextKarir</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">
        <h1>Cari Lowongan Pekerjaan</h1>
        <div class="search-container"><form action="search.php" method="GET"><input type="text" id="search-box" name="q" placeholder="Ketik jabatan..." value="<?= htmlspecialchars($search_query) ?>" autocomplete="off"></form><div id="search-suggestions"></div></div>
        
        <h2 style="margin-top: 30px;">
            <?php 
            if (!empty($search_field)) {
                echo 'Menampilkan Lowongan Bidang "' . htmlspecialchars($search_field) . '"';
            } elseif (!empty($search_query)) {
                echo 'Hasil Pencarian untuk "' . htmlspecialchars($search_query) . '"';
            } else {
                echo 'Menampilkan Semua Lowongan Terbaru';
            }
            ?>
        </h2>
        <div class="job-listings">
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $job): ?>
                    <div class="card job-card">
                        <img src="uploads/logos/<?= htmlspecialchars($job['logo']) ?>" alt="Logo">
                        <div>
                            <h3><a href="job_detail.php?id=<?= $job['id'] ?>"><?= htmlspecialchars($job['title']) ?></a></h3>
                            <p class="company-name"><?= htmlspecialchars($job['company_name']) ?></p>
                            <div class="details"><span>📍 <?= htmlspecialchars($job['location']) ?></span><span>💰 <?= htmlspecialchars($job['salary']) ?></span></div>
                        </div>
                        <a href="job_detail.php?id=<?= $job['id'] ?>" class="btn btn-primary">Lihat Detail</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada lowongan yang cocok dengan kriteria Anda.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'partials/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
