<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Logged in user's district
$loggedDistrict = $_SESSION['district'];

// Total Pending and Solved Counts (for that district only)
$totalPending = $conn->query("
    SELECT COUNT(*) AS cnt 
    FROM issues 
    WHERE status = 'Pending' AND district = '{$conn->real_escape_string($loggedDistrict)}'
")->fetch_assoc()['cnt'];

$totalSolved  = $conn->query("
    SELECT COUNT(*) AS cnt 
    FROM issues 
    WHERE status = 'Solved' AND district = '{$conn->real_escape_string($loggedDistrict)}'
")->fetch_assoc()['cnt'];

// Handle status filter if clicked
$filterStatus = $_GET['status'] ?? '';
$result = null;

if ($filterStatus !== '') {
    $escapedStatus = $conn->real_escape_string($filterStatus);
    $escapedDistrict = $conn->real_escape_string($loggedDistrict);
    $result = $conn->query("
        SELECT * FROM issues 
        WHERE status = '$escapedStatus' AND district = '$escapedDistrict'
        ORDER BY created_at DESC
    ");
}

// Always fetch district-wise summary (only for logged-in district)
$districtResult = $conn->query("
    SELECT district,
           SUM(status = 'Pending') AS pending_count,
           SUM(status = 'Solved') AS solved_count
    FROM issues
    WHERE district = '{$conn->real_escape_string($loggedDistrict)}'
    GROUP BY district
");

$districtSummary = [];
while ($row = $districtResult->fetch_assoc()) {
    $districtSummary[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>📊 Dashboard - <?= htmlspecialchars($loggedDistrict) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light clean background */
        }
        .card {
            border-radius: 12px;
            box-shadow: 0px 3px 6px rgba(0,0,0,0.1);
        }
        .navbar {
            border-radius: 8px;
        }
        .table img {
            max-height: 60px;
        }
    </style>
</head>
<body>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 px-3">
    <a class="navbar-brand fw-bold" href="#">📊 <?= htmlspecialchars($loggedDistrict) ?> Dashboard</a>
    <div class="ms-auto">
        <a href="index.php" class="btn btn-light btn-sm me-2">🏠 Home</a>
        <a href="dashboard.php" class="btn btn-warning btn-sm me-2">📋 Dashboard</a>
        <a href="logout.php" class="btn btn-danger btn-sm">🚪 Logout</a>
    </div>
</nav>

<div class="container">

    <!-- Dashboard Cards -->
    <div class="row mb-4 text-center">
        <div class="col-md-6 mb-3">
            <a href="dashboard.php?status=Pending" class="text-dark text-decoration-none">
                <div class="card bg-warning">
                    <div class="card-body">
                        <h5>Pending Issues</h5>
                        <h2><?= htmlspecialchars($totalPending) ?></h2>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 mb-3">
            <a href="dashboard.php?status=Solved" class="text-white text-decoration-none">
                <div class="card bg-success">
                    <div class="card-body">
                        <h5>Solved Issues</h5>
                        <h2><?= htmlspecialchars($totalSolved) ?></h2>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Issues Table -->
    <?php if ($filterStatus !== '' && $result && $result->num_rows > 0): ?>
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-secondary text-white">
                <?= htmlspecialchars($filterStatus) ?> Issues (<?= htmlspecialchars($loggedDistrict) ?>)
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Token</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Issue Image</th>
                            <th>URL</th>
                            <th>Solved Image</th>
                            <th>Created At</th>
                            <?php if ($filterStatus === 'Solved'): ?>
                                <th>Solved At</th>
                            <?php else: ?>
                                <th>⏳ Pending Days</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['token']) ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $row['status'] === 'Solved' ? 'success' : 'warning text-dark' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <a href="uploads/<?= htmlspecialchars($row['image']) ?>" target="_blank">
                                            <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="img-thumbnail">
                                        </a>
                                    <?php else: ?>N/A<?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['url'])): ?>
                                        <a href="<?= htmlspecialchars($row['url']) ?>" target="_blank"><?= htmlspecialchars($row['url']) ?></a>
                                    <?php else: ?>N/A<?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['solved_image'])): ?>
                                        <a href="uploads/<?= htmlspecialchars($row['solved_image']) ?>" target="_blank">
                                            <img src="uploads/<?= htmlspecialchars($row['solved_image']) ?>" class="img-thumbnail">
                                        </a>
                                    <?php else: ?>N/A<?php endif; ?>
                                </td>
                               <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <?php if ($filterStatus === 'Solved'): ?>
                                    <td><?= htmlspecialchars($row['solved_at']) ?: 'N/A' ?></td>
                                <?php else: ?>
                                    <td>
                                        <?php
                                        if (!empty($row['created_at'])) {
                                            $created = new DateTime($row['created_at']);
                                            $today = new DateTime();
                                            $interval = $today->diff($created);
                                            echo $interval->days . " days";
                                        } else {
                                            echo "N/A";
                                        }
                                        ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($filterStatus !== ''): ?>
        <p class="text-center text-muted">No <?= htmlspecialchars($filterStatus) ?> issues found in <?= htmlspecialchars($loggedDistrict) ?>.</p>
    <?php endif; ?>

    <!-- District Summary -->
    <?php if ($filterStatus === ''): ?>
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-info text-white">
            🗺️ <?= htmlspecialchars($loggedDistrict) ?> Summary
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>District</th>
                            <th>Pending</th>
                            <th>Solved</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($districtSummary as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['district']) ?></td>
                                <td><?= (int)$row['pending_count'] ?></td>
                                <td><?= (int)$row['solved_count'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
