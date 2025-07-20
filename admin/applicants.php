require_once '../config/db.php';

// Autentikasi & Otorisasi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'company') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $conn->query("SELECT id FROM companies WHERE user_id = $user_id")->fetch_assoc()['id'];

// Ambil semua lowongan untuk filter dropdown
$jobs_for_filter = $conn->query("SELECT id, title FROM jobs WHERE company_id = $company_id ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

// Bangun query dasar
$sql = "SELECT a.id, js.full_name, j.title, a.application_date, a.status 
        FROM applications a 
        JOIN job_seekers js ON a.job_seeker_id = js.id 
        JOIN jobs j ON a.job_id = j.id 
        WHERE j.company_id = ?";
$params = [$company_id];
$types = "i";

// Tambahkan filter jika ada
$filter_job_id = null;
if (isset($_GET['job_id']) && is_numeric($_GET['job_id'])) {
    $filter_job_id = $_GET['job_id'];
    $sql .= " AND j.id = ?";
    $params[] = $filter_job_id;
    $types .= "i";
}
$sql .= " ORDER BY a.application_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$applicants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelamar - NextKarir</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/style.css">
</head>
<body>
    <?php include ROOT_PATH . '/partials/header.php'; ?>
    <div class="container">
        <h1>Daftar Pelamar</h1>
        
        <form action="<?= BASE_URL ?>/admin/applicants.php" method="GET" style="margin-bottom: 20px;">
            <label for="job_id">Filter berdasarkan Lowongan:</label>
            <select name="job_id" id="job_id" onchange="this.form.submit()">
                <option value="">Semua Lowongan</option>
                <?php foreach ($jobs_for_filter as $job): ?>
                    <option value="<?= $job['id'] ?>" <?= ($filter_job_id == $job['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($job['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (!empty($applicants)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Pelamar</th>
                        <th>Posisi Dilamar</th>
                        <th>Tanggal Lamar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applicants as $app): ?>
                        <tr>
                            <td><?= htmlspecialchars($app['full_name']) ?></td>
                            <td><?= htmlspecialchars($app['title']) ?></td>
                            <td><?= date('d F Y', strtotime($app['application_date'])) ?></td>
                            <td><span class="status-badge status-<?= $app['status'] ?>"><?= ucfirst(str_replace('_', ' ', $app['status'])) ?></span></td>
                            <td><a href="<?= BASE_URL ?>/admin/applicant_detail.php?id=<?= $app['id'] ?>">Lihat Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="message info">Belum ada pelamar yang masuk untuk kriteria ini.</div>
        <?php endif; ?>
    </div>
    <?php include ROOT_PATH . '/partials/footer.php'; ?>
</body>
</html>
