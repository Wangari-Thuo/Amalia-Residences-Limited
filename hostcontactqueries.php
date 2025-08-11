<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['query_id']) && isset($_POST['response'])) {
    $query_id = intval($_POST['query_id']);
    $response = trim($_POST['response']);

    $stmt = $conn->prepare("UPDATE contact_queries SET response = ?, status = 'responded' WHERE query_id = ?");
    $stmt->bind_param("si", $response, $query_id);

    if ($stmt->execute()) {
        $message = "Response sent successfully.";
    } else {
        $message = "Error sending response.";
    }
    $stmt->close();
}

// Fetch all contact queries
$sql = "SELECT cq.query_id, u.name, cq.subject, cq.message, cq.response, cq.status, cq.created_at
        FROM contact_queries cq
        JOIN users u ON cq.user_id = u.user_id
        ORDER BY cq.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Contact Queries</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; vertical-align: top; }
        th { background-color: #f2f2f2; }
        textarea { width: 100%; height: 60px; }
        form { margin: 0; }
        .message { color: green; }
    </style>
</head>
<body>
    <h2>Contact Us Queries</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <a href="hostdashboard.php">Back to Dashboard</a><br><br>

    <?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>User</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Response</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
            <td>
                <?php 
                if ($row['response']) {
                    echo nl2br(htmlspecialchars($row['response']));
                } else {
                    echo "<em>No response yet</em>";
                }
                ?>
            </td>
            <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td>
                <?php if ($row['status'] !== 'responded'): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="query_id" value="<?php echo $row['query_id']; ?>">
                        <textarea name="response" required placeholder="Write your response here..."></textarea><br>
                        <button type="submit">Send Response</button>
                    </form>
                <?php else: ?>
                    <em>Responded</em>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>No contact queries found.</p>
    <?php endif; ?>
<footer class="site-footer">
  <div class="footer-content">
     <p&copy;> 2025 Amalia Residences Limited. All rights reserved.</p>
     <p>Designed with Love by Susan</p>
 </footer>

</body>
</html>