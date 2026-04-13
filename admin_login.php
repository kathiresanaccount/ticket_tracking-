<?php
session_start();
include 'db.php';

// If already logged in → go to admin.php
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$message = "";

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!empty($_POST['mobile']) && !empty($_POST['password'])) {
        $mobile   = trim($_POST['mobile']);
        $password = $_POST['password'];

        // ✅ Fixed: changed 'name' → 'username'
        $stmt = $conn->prepare("SELECT id, username, password FROM admin_users WHERE mobile=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $mobile);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $username, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                $_SESSION['admin_id']   = $id;
                $_SESSION['admin_name'] = $username; // ✅ use username
                header("Location: admin.php");
                exit;
            } else {
                $message = "<p class='text-danger'>❌ Invalid password!</p>";
            }
        } else {
            $message = "<p class='text-danger'>❌ Mobile number not found!</p>";
        }
        $stmt->close();
    } else {
        $message = "<p class='text-danger'>❌ Please enter both mobile and password.</p>";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row">
        <!-- Left Column: Login Form -->
        <div class="col-lg-4">
            <h2 class="fw-bold mb-4">Admin Login</h2>

            <?= $message ?>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile" class="form-control" pattern="[0-9]{10}" maxlength="10" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                        <p class="mt-3">New user? <a href="admin_register.php">Register</a></p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Optional Right Column: Image or Info -->
        <div class="col-lg-8 d-none d-lg-block">
            <!-- You can place an image or welcome message here -->
            <div class="h-100 d-flex align-items-center justify-content-center">
                <img src="login_side_image.jpg" class="img-fluid" alt="Welcome Image">
            </div>
        </div>
    </div>
</div>

</body>
</html>
