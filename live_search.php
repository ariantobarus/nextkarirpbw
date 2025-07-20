<?php
require_once 'config/db.php';

// Endpoint ini untuk AJAX request dari search bar.

if (isset($_POST['query']) && !empty(trim($_POST['query']))) {
    $query = "%" . trim($_POST['query']) . "%";
    
    // Gunakan prepared statement dan JOIN untuk mencari berdasarkan judul, deskripsi, atau nama perusahaan
    $stmt = $conn->prepare(
        "SELECT j.id, j.title, c.name as company_name 
         FROM jobs j
         JOIN companies c ON j.company_id = c.id
         WHERE j.title LIKE ? OR j.description LIKE ? OR c.name LIKE ? 
         LIMIT 5"
    );
    $stmt->bind_param("sss", $query, $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Tampilkan hasil dengan nama perusahaan
            echo '<a href="job_detail.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . ' <small style="color:#777;">di ' . htmlspecialchars($row['company_name']) . '</small></a>';
        }
    } else {
        echo '<a href="#" style="pointer-events: none; color: #888;">Tidak ada saran ditemukan</a>';
    }
    $stmt->close();
}
$conn->close();
