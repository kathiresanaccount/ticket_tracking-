<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $url = trim($_POST['url']);
    $district = trim($_POST['district']);

    // Check for duplicate issues today
    $dateToday = date('Y-m-d');
    $stmt = $conn->prepare("SELECT 1 FROM issues WHERE title = ? AND district = ? AND DATE(created_at) = ?");
    $stmt->bind_param("sss", $title, $district, $dateToday);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<div style='color:red;'>⚠️ Duplicate issue detected. You already submitted this issue today.</div>";
        exit;
    }

    // Generate unique token
    $token = strtoupper(uniqid('ISSUE-'));

    // Handle image upload
    $imageName = "";
    if (!empty($_FILES['image']['name'])) {
        if (!is_dir("uploads")) {
            mkdir("uploads", 0755, true);
        }

        $targetDir = "uploads/";
        $originalName = basename($_FILES["image"]["name"]);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array(strtolower($extension), $allowedTypes)) {
            echo "<div style='color:red;'>❌ Invalid image format. Only JPG, PNG, GIF, and WebP are allowed.</div>";
            exit;
        }

        $imageName = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $originalName);
        $targetPath = $targetDir . $imageName;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            echo "<div style='color:red;'>❌ Failed to upload image.</div>";
            exit;
        }
    }

    // Insert issue into database
    $sql = "INSERT INTO issues (token, title, description, image, url, district) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $token, $title, $description, $imageName, $url, $district);

    if ($stmt->execute()) {
        echo "<div style='color:green;'>✅ Issue submitted successfully. Your Token: <strong>$token</strong></div>";
    } else {
        echo "<div style='color:red;'>❌ Failed to submit issue. Please try again later.</div>";
    }
} else {
    echo "Invalid request method.";
}
?>
