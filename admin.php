<?php
// admin.php
include 'db.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// ----------------------------
// Handle updates
// ----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $updateId  = (int)$_POST['update_id'];
    $status    = $_POST['status'] ?? 'Pending';
    $solvedUrl = $_POST['solved_url'] ?? '';

    // Get current solved_image
    $solvedImageName = '';
    $getImg = $conn->prepare("SELECT solved_image FROM issues WHERE id = ?");
    $getImg->bind_param("i", $updateId);
    $getImg->execute();
    $imgResult = $getImg->get_result();
    if ($r = $imgResult->fetch_assoc()) {
        $solvedImageName = $r['solved_image'];
    }

    // Upload solved image
    if (!empty($_FILES['solved_image']['name'])) {
        if (!is_dir("uploads")) mkdir("uploads", 0755, true);
        $solvedImageName = time() . "_" . basename($_FILES["solved_image"]["name"]);
        move_uploaded_file($_FILES["solved_image"]["tmp_name"], "uploads/" . $solvedImageName);
    }

    if ($status === 'Solved') {
        $solvedAt = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("UPDATE issues SET status=?, solved_image=?, solved_url=?, solved_at=? WHERE id=?");
        $stmt->bind_param("ssssi", $status, $solvedImageName, $solvedUrl, $solvedAt, $updateId);
    } else {
        $stmt = $conn->prepare("UPDATE issues SET status=?, solved_image=?, solved_url=?, solved_at=NULL WHERE id=?");
        $stmt->bind_param("sssi", $status, $solvedImageName, $solvedUrl, $updateId);
    }
    $stmt->execute();

    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// ----------------------------
// Dashboard counts
// ----------------------------
$totalPending = $conn->query("SELECT COUNT(*) AS cnt FROM issues WHERE status='Pending'")->fetch_assoc()['cnt'];
$totalSolved  = $conn->query("SELECT COUNT(*) AS cnt FROM issues WHERE status='Solved'")->fetch_assoc()['cnt'];
$totalEntries = $conn->query("SELECT COUNT(*) AS cnt FROM issues")->fetch_assoc()['cnt'];

$districtCounts = $conn->query("
    SELECT district,
           SUM(status='Pending') AS pending_count,
           SUM(status='Solved')  AS solved_count,
           COUNT(*)              AS total_count
    FROM issues
    GROUP BY district
    ORDER BY district
");

$view = $_GET['view'] ?? 'dashboard';
$filterDistrict = $_GET['district'] ?? '';
$filterStatus   = $_GET['status'] ?? '';
$search         = $_GET['search'] ?? '';

// Build filter
$conditions = [];
$types = '';
$params = [];

if ($filterDistrict !== '') {
    $conditions[] = "district = ?";
    $types .= 's';
    $params[] = $filterDistrict;
}
if ($filterStatus !== '') {
    $conditions[] = "status = ?";
    $types .= 's';
    $params[] = $filterStatus;
}
if ($search !== '') {
    $conditions[] = "(district LIKE ? OR token LIKE ?)";
    $types .= 'ss';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$where = $conditions ? "WHERE " . implode(' AND ', $conditions) : "";

$perPage = 10;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$countSql = "SELECT COUNT(*) AS cnt FROM issues $where";
$countStmt = $conn->prepare($countSql);
if ($conditions) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['cnt'];
$totalPages = max(1, (int)ceil($totalRows / $perPage));

$dataSql = "SELECT id, token, title, district, status, image, url, solved_image, solved_url, created_at, solved_at,
                   DATEDIFF(NOW(), created_at) AS pending_days
            FROM issues
            $where
            ORDER BY created_at DESC
            LIMIT ?, ?";
$dataTypes = $types . "ii";
$dataParams = $params;
$dataParams[] = $offset;
$dataParams[] = $perPage;

$dataStmt = $conn->prepare($dataSql);
if ($conditions) {
    $dataStmt->bind_param($dataTypes, ...$dataParams);
} else {
    $dataStmt->bind_param("ii", $offset, $perPage);
}
$dataStmt->execute();
$result = $dataStmt->get_result();

function qp($extra = []) {
    $base = $_GET;
    foreach ($extra as $k => $v) $base[$k] = $v;
    return htmlspecialchars(http_build_query($base));
}
?>
<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - IPMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .brand-header {
            background: #003366;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
        }
        .brand-header img {
            height: 70px;
        }
        .brand-header h2 {
            font-size: 1.1rem;
            margin: 0;
        }
        .navybar {
            background: #0a3d62;
            width: 100%;
            padding: 0 40px;
        }
        .navybar .nav-link, .navybar .navbar-brand {
            color: #fff !important;
        }
        .brand-header h2 {
    font-size: 1.4rem;
    font-weight: 600;
}
.brand-header h3 {
    font-size: 1.1rem;
    font-weight: 500;
    color: #f1f1f1;
}
.brand-header div {
    font-size: 0.95rem;
}
        .card-link { text-decoration: none; }
        .card-link:hover { opacity: 0.9; }
        .table thead th { white-space: nowrap; }
        .badge-wrap { font-size: .95rem; }
    </style>
</head>
<body>

<!-- Full Header -->
<div class="brand-header">
    <img src="TNlogo.jpeg" alt="TN Logo">
    
    <div class="text-center flex-grow-1">
        <h3 class="mb-2 text-uppercase">Rural Development and Panchayat Raj Department, Tamil Nadu</h3>
        <div>IPMS - Ticket Monitoring System</div>
    </div>
    
    <img src="e2e.jpg" alt="E2E Logo">
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg w-100" style="background-color: #87CEFA;">
  <div class="d-flex justify-content-between align-items-center w-100">
    <div class="navbar-nav me-auto">
      <a class="nav-link <?= $view==='dashboard'?'fw-bold text-dark':'' ?> text-dark" href="?view=dashboard">Dashboard</a>
      <a class="nav-link <?= $view==='reports'?'fw-bold text-dark':'' ?> text-dark" href="?view=reports">Total Reports</a>
    </div>
    <div class="d-flex align-items-center text-dark">
<span class="me-3">👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
      <a class="btn btn-sm btn-outline-dark" href="admin_logout.php">Logout</a>
    </div>
  </div>
</nav>

<!-- Welcome Section -->
<div class="text-center py-4 bg-light w-100">
    <h2 class="fw-bold mb-2">Welcome to Admin Dashboard</h2>
    <p class="text-success mb-3">
        ✅ Logged in as Admin: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
    </p>
</div>

<!-- Main Content -->
<div class="px-4 py-4">

<?php if ($view === 'dashboard'): ?>
    <!-- Dashboard Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-info text-dark shadow-sm text-center">
                <div class="card-body">
                    <div>Total Entries</div>
                    <h2><?= $totalEntries ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <a href="?view=reports&status=Pending" class="card-link">
                <div class="card bg-warning text-dark shadow-sm text-center">
                    <div class="card-body">
                        <div>Pending</div>
                        <h2><?= $totalPending ?></h2>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="?view=reports&status=Solved" class="card-link">
                <div class="card bg-success text-white shadow-sm text-center">
                    <div class="card-body">
                        <div>Solved</div>
                        <h2><?= $totalSolved ?></h2>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- District Summary -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5>District-wise Summary</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>District</th>
                            <th>Pending</th>
                            <th>Solved</th>
                            <th>Total</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $districtCounts->data_seek(0);
                    while ($d = $districtCounts->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['district']) ?></td>
                            <td><span class="badge bg-warning text-dark"><?= $d['pending_count'] ?></span></td>
                            <td><span class="badge bg-success"><?= $d['solved_count'] ?></span></td>
                            <td><?= $d['total_count'] ?></td>
                            <td><a href="?view=reports&district=<?= urlencode($d['district']) ?>" class="btn btn-sm btn-primary">Open</a></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Reports View -->
    <form method="GET" class="row g-2 mb-3">
        <input type="hidden" name="view" value="reports">
        <div class="col-md-3">
            <select name="district" class="form-select">
                <option value="">All Districts</option>
                <?php $districtCounts->data_seek(0);
                while ($d = $districtCounts->fetch_assoc()):
                    $selected = ($d['district'] == $filterDistrict) ? 'selected' : ''; ?>
                    <option value="<?= htmlspecialchars($d['district']) ?>" <?= $selected ?>>
                        <?= htmlspecialchars($d['district']) ?> — Pending: <?= $d['pending_count'] ?>, Solved: <?= $d['solved_count'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="Pending" <?= $filterStatus==='Pending'?'selected':'' ?>>Pending</option>
                <option value="Solved"  <?= $filterStatus==='Solved' ?'selected':'' ?>>Solved</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search District / Token" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3 d-flex">
            <button type="submit" class="btn btn-primary me-2">Filter</button>
            <a href="?view=reports" class="btn btn-secondary">Clear</a>
        </div>
    </form>

    <div class="text-end mb-3">
        <a class="btn btn-success" target="_blank" href="export_issues.php?<?= qp(['view'=>'reports','page'=>1]) ?>">⬇️ Download Excel</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Issues</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Token</th><th>Title</th><th>District</th><th>Status</th><th>Image</th><th>URL</th>
                            <th>Solved Image</th><th>Created At</th><th>Solved At</th><th>Pending Days</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows): while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['token']) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['district']) ?></td>
                            <td>
                                <span class="badge bg-<?= $row['status']==='Solved'?'success':'warning text-dark' ?>">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['image']): ?>
                                    <a href="uploads/<?= htmlspecialchars($row['image']) ?>" target="_blank">
                                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" style="max-height:70px" class="img-thumbnail">
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['url']): ?>
                                    <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank">Open</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['solved_image']): ?>
                                    <a href="uploads/<?= htmlspecialchars($row['solved_image']) ?>" target="_blank">
                                        <img src="uploads/<?= htmlspecialchars($row['solved_image']) ?>" style="max-height:70px" class="img-thumbnail">
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                            <td><?= $row['solved_at'] ?: '—' ?></td>
                            <td><?= $row['status']==='Pending' ? $row['pending_days'].' days' : '—' ?></td>
                            <td>
                                <?php if ($row['status']==='Pending'): ?>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#form-<?= $row['id'] ?>">✏️ Update</button>
                                <?php else: ?>
                                    <span class="badge bg-success">✅</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php if ($row['status']==='Pending'): ?>
                        <tr class="collapse" id="form-<?= $row['id'] ?>">
                            <td colspan="11">
                                <form method="POST" enctype="multipart/form-data" class="row g-3">
                                    <input type="hidden" name="update_id" value="<?= $row['id'] ?>">
                                    <div class="col-md-3">
                                        <select name="status" class="form-select" required>
                                            <option value="Pending" selected>Pending</option>
                                            <option value="Solved">Solved</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <input type="url" name="solved_url" class="form-control" placeholder="https://">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" name="solved_image" class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-success">✅ Save</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endwhile; else: ?>
                        <tr><td colspan="11" class="text-center text-danger">⚠️ No records found</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
