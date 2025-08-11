<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch reviews for host's properties
$sql = "SELECT r.review_id, p.description, r.review_text, r.rating, u.name, r.created_at
        FROM reviews r
        JOIN properties p ON r.property_id = p.property_id
        JOIN users u ON r.user_id = u.user_id
        WHERE p.owner_id = ?
        ORDER BY r.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>User Reviews</title>
</head>
<body>
    <h2>Reviews on Your Properties</h2>
    <a href="hostdashboard.php">Back to Dashboard</a>
    <br><br>

    <?php if ($result->num_rows > 0): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Property</th>
            <th>User</th>
            <th>Review</th>
            <th>Rating</th>
            <th>Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($row['review_text'])); ?></td>
            <td><?php echo intval($row['rating']); ?>/5</td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>
    <footer class="site-footer">
  <div class="footer-content">
     <p&copy;> 2025 Amalia Residences Limited. All rights reserved.</p>
     <p>Designed with Love by Susan</p>
 </footer>


</body>
</html>
<?php
$stmt->close();
?>