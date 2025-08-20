<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) {
    die("Database connection failed.");
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT category, message, created_at FROM contact_us WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if(!$stmt)
{
    die("prepare failed: (" .$conn->errno .")".$conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Messages - Amalia Residences</title>
  <link rel="stylesheet" href="main.css" />
  <style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
    .container { max-width: 700px; margin: auto; background: white; padding: 20px; border-radius: 6px; box-shadow: 0 0 10px #ccc;}
    .message { border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 4px; background: #fafafa; }
    .category { font-weight: bold; color: #007bff; }
    .date { font-size: 0.9em; color: #666; margin-top: 5px; }
    .no-messages { font-style: italic; color: #666; }
    a.back-link { display: inline-block; margin-bottom: 20px; text-decoration: none; color: #007bff; }
    a.back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="container">
    <a href="index.html" class="back-link">&larr; Go to Home</a>
    <h1>My Previous Messages</h1>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='message'>";
            echo "<div class='category'>Category: " . htmlspecialchars($row['category']) . "</div>";
            echo "<div class='content'>" . nl2br(htmlspecialchars($row['message'])) . "</div>";
            echo "<div class='date'>Sent on: " . $row['created_at'] . "</div>";
            echo "</div>";
        }
    } else {
        echo "<p class='no-messages'>You have not sent any messages yet.</p>";
    }

    $stmt->close();
    $conn->close();
    ?>
  </div>
   <footer class="site-footer">
  <div class="footer-content">
        <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
        <p>Designed with Love by Susan Wangari Thuo</p>
    </div>
    </footer>
</body>
</html>