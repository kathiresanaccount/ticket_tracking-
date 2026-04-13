<?php
session_start();
include 'db.php';

// If already logged in → go to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username    = trim($_POST['username']);
    $password    = $_POST['password'];
    $department  = $_POST['department'];
    $designation = $_POST['designation'];
    $mobile      = $_POST['mobile'];
    $dob         = $_POST['dob'];

    // Check if username exists
    $check = $conn->prepare("SELECT id FROM admin_users WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "<p class='text-danger'>⚠️ Username already exists!</p>";
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO admin_users 
            (username, password, department, designation, mobile, dob) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $username, $hashed, $department, $designation, $mobile, $dob);

        if ($stmt->execute()) {
            $message = "<p class='text-success'>✅ Registration successful! <a href='admin_login.php'>Login here</a></p>";
        } else {
            $message = "<p class='text-danger'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
    $check->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row">
        <!-- Left Column: Registration Form -->
        <div class="col-lg-5">
            <h2 class="fw-bold mb-4">Admin Registration</h2>

            <?= $message ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="">
                        
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="department" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Designation</label>
                            <input type="text" name="designation" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" name="mobile" class="form-control" pattern="[0-9]{10}" maxlength="10" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Register</button>
                        <p class="mt-3">Already registered? <a href="admin_login.php">Login</a></p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Optional Right Column: Image or Info -->
        <div class="col-lg-7 d-none d-lg-block">
            <div class="h-100 d-flex align-items-center justify-content-center">
                <img src="register_side_image.jpg" class="img-fluid" alt="Welcome Image">
            </div>
        </div>
    </div>
</div>

</body>
</html>
