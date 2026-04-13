<?php
include 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT * FROM issues WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo "<h2>Issue Details for Token: {$row['token']}</h2>";
        echo "<p><strong>Title:</strong> {$row['title']}</p>";
        echo "<p><strong>Description:</strong> {$row['description']}</p>";
        echo "<p><strong>District:</strong> {$row['district']}</p>";
        echo "<p><strong>Status:</strong> {$row['status']}</p>";
        echo "<p><strong>Created At:</strong> {$row['created_at']}</p>";
        echo "<p><strong>Solved At:</strong> " . ($row['solved_at'] ?? 'Not yet solved') . "</p>";

       if ($row['image']) {
    echo "<p><strong>Uploaded Image:</strong><br>
        <a href='uploads/{$row['image']}' target='_blank'>
            <img src='uploads/{$row['image']}' width='200' class='img-thumbnail' alt='Uploaded Image'>
        </a><br>
    </p>";
}
if ($row['url']) {
            echo "<p><strong>Issue URL:</strong> <a href='{$row['url']}' target='_blank'>{$row['url']}</a></p>";
        }

if ($row['solved_image']) {
    echo "<p><strong>Solved Image:</strong><br>
        <a href='uploads/{$row['solved_image']}' target='_blank'>
            <img src='uploads/{$row['solved_image']}' width='200' class='img-thumbnail' alt='Solved Image'>
        </a><br>
    </p>";
        }
        if ($row['solved_url']) {
            echo "<p><strong>Solved URL:</strong> <a href='{$row['solved_url']}' target='_blank'>{$row['solved_url']}</a></p>";
        }
    } else {
        echo "❌ Invalid token.";
    }
}
?>
