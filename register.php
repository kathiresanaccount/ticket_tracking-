<?php 
include 'db.php'; 

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $district   = $_POST['district'];
    $office     = $_POST['office'];
    $username   = $_POST['username'];
    $password   = password_hash($_POST['password'], PASSWORD_BCRYPT); // secure password

    $stmt = $conn->prepare("INSERT INTO users (district, office, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $district, $office, $username, $password);

    if ($stmt->execute()) {
        echo "<p style='color:green;text-align:center;'>Registration successful!</p>";
    } else {
        echo "<p style='color:red;text-align:center;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 450px;
            margin: 50px auto;
        }
        .card {
            border-radius: 15px;
        }
        label {
            font-weight: 500;
            text-align: left;
            display: block;
        }
    </style>
</head>
<body>

<div class="container text-center py-4">
    <!-- TN Logo -->
    <img src="TNlogo.jpeg" alt="TN Logo" class="mb-3" style="max-width:100px;">

    <!-- Title -->
    <h2 class="fw-bold mb-4">Registration Form</h2>

    <div class="form-container">
        <div class="card shadow-sm border-0">
            <div class="card-body text-start"> <!-- ✅ text-start aligns labels properly -->
                <form method="POST" action="">
                    
                    <div class="mb-3">
                        <label class="form-label">District Name</label>
                        <select name="district" class="form-select" required>
                            <option value="">-- Select District --</option>
                            <option>Ariyalur</option>
                            <option>Chengalpattu</option>
                            <option>Chennai</option>
                            <option>Coimbatore</option>
                            <option>Cuddalore</option>
                            <option>Dharmapuri</option>
                            <option>Dindigul</option>
                            <option>Erode</option>
                            <option>Kallakurichi</option>
                            <option>Kancheepuram</option>
                            <option>Karur</option>
                            <option>Krishnagiri</option>
                            <option>Madurai</option>
                            <option>Mayiladuthurai</option>
                            <option>Nagapattinam</option>
                            <option>Namakkal</option>
                            <option>Nilgiris</option>
                            <option>Perambalur</option>
                            <option>Pudukkottai</option>
                            <option>Ramanathapuram</option>
                            <option>Ranipet</option>
                            <option>Salem</option>
                            <option>Sivagangai</option>
                            <option>Tenkasi</option>
                            <option>Thanjavur</option>
                            <option>Theni</option>
                            <option>Thoothukudi</option>
                            <option>Tiruchirappalli</option>
                            <option>Tirunelveli</option>
                            <option>Tirupathur</option>
                            <option>Tiruppur</option>
                            <option>Tiruvallur</option>
                            <option>Tiruvannamalai</option>
                            <option>Tiruvarur</option>
                            <option>Vellore</option>
                            <option>Viluppuram</option>
                            <option>Virudhunagar</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Office Name</label>
                        <select name="office" class="form-select" required>
                            <option value="">-- Select Office --</option>
                            <option>DRDA</option>
                            <option>AD Panchayat</option>
                            <option>District Panchayat</option>
                            <option>AD Audit</option>
                            <option>PAPD</option>
                            <option>PASS</option>
                            <option>PANM</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Register</button>

                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
