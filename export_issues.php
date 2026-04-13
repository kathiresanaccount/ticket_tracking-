<?php
include 'db.php';
session_start();

// Only allow admin
if (!isset($_SESSION['admin_id'])) {
    die("Access denied!");
}

// Filters
$filterDistrict = $_GET['district'] ?? '';
$filterStatus   = $_GET['status'] ?? '';
$search         = $_GET['search'] ?? '';

$conditions = [];
$params = [];
$types = '';

if ($filterDistrict !== '') { $conditions[] = "district = ?"; $types.='s'; $params[] = $filterDistrict; }
if ($filterStatus !== '') { $conditions[] = "status = ?"; $types.='s'; $params[] = $filterStatus; }
if ($search !== '') { $conditions[] = "(district LIKE ? OR token LIKE ?)"; $types.='ss'; $params[]="%$search%"; $params[]="%$search%"; }

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

// Fetch data with Pending Days
$sql = "SELECT token, title, district, status, image, url, solved_image, created_at, solved_at,
               DATEDIFF(NOW(), created_at) AS pending_days
        FROM issues $where
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($conditions) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Headers to force download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=issues_report_" . date('Ymd_His') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

// Output table
echo "<table border='1'>";
echo "<tr>
        <th>Token</th>
        <th>Title</th>
        <th>District</th>
        <th>Status</th>
        <th>Image</th>
        <th>Issue URL</th>
        <th>Solved Image</th>
        <th>Created At</th>
        <th>Solved At</th>
        <th>Pending Days</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".htmlspecialchars($row['token'])."</td>";
    echo "<td>".htmlspecialchars($row['title'])."</td>";
    echo "<td>".htmlspecialchars($row['district'])."</td>";
    echo "<td>".htmlspecialchars($row['status'])."</td>";
    echo "<td>".($row['image'] ? htmlspecialchars($row['image']) : 'N/A')."</td>";
    echo "<td>".($row['url'] ? htmlspecialchars($row['url']) : 'N/A')."</td>";
    echo "<td>".($row['solved_image'] ? htmlspecialchars($row['solved_image']) : 'N/A')."</td>";
    echo "<td>".htmlspecialchars($row['created_at'])."</td>";
    echo "<td>".($row['solved_at'] ?: '—')."</td>";
    echo "<td>".($row['status']==='Pending' ? (int)$row['pending_days'] : '—')."</td>";
    echo "</tr>";
}

echo "</table>";
exit;
