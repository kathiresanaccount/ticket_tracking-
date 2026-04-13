<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$loggedDistrict = $_SESSION['district'];
?>

<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Issue Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Logo -->
<div class="text-center py-3">
    <img src="TNlogo.jpeg" alt="TN Logo" style="max-width:100px;">
    <h3 class="fw-bold mt-2">IPMS - E2E Ticket Token</h3>
</div>

<!-- Navbar with Buttons -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">
        Tamil Nadu – Welcome to <?= htmlspecialchars($loggedDistrict); ?>
    </span>
   <ul class="navbar-nav ms-auto nav nav-pills"> 
    <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#submit">📝 Submit Issue</a> 
    </li> <li class="nav-item"> 
        <a class="nav-link" data-bs-toggle="tab" href="#track">🔎 Track Issue</a> </li> 
    <li class="nav-item"> <a href="dashboard.php" class="nav-link text-warning">🏠 Dashboard</a> 
    </li> <li class="nav-item"> 
        <a href="logout.php" class="nav-link text-danger">🚪 Logout</a>
         </li> 
     </ul>
      </div> 
  </nav>

<!-- Page Content -->
<div class="container py-4">
    <div class="tab-content">

        <!-- Submit Issue Section -->
        <div class="tab-pane fade show active" id="submit">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📝 Submit an Issue</h4>
                </div>
                <div class="card-body">
                    <form action="submit_issue.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Issue Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Upload an Image (optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Related URL (optional)</label>
                            <input type="url" name="url" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">District</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($loggedDistrict); ?>" readonly>
                            <input type="hidden" name="district" value="<?= htmlspecialchars($loggedDistrict); ?>">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">📤 Submit Issue</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Track Issue Section -->
        <div class="tab-pane fade" id="track">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">🔎 Check Issue Status</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="track.php">
                        <div class="mb-3">
                            <label class="form-label">Token ID</label>
                            <input type="text" name="token" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">🔍 Track</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
