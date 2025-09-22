<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch payments related to host's properties
$sql = "SELECT pay.payment_id, p.description, pay.amount, pay.payment_date, pay.payment_status, u.name
        FROM payments pay
        JOIN bookings b ON pay.booking_id = b.booking_id
        JOIN properties p ON b.property_id = p.property_id
        JOIN users u ON b.user_id = u.user_id
        WHERE p.owner_id = ?
        ORDER BY pay.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>View Payments</title>
</head>
<body>
    <h2>Payment Details</h2>
    <a href="hostdashboard.php">Back to Dashboard</a>
    <br><br>

    <?php if ($result->num_rows > 0): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Property</th>
            <th>Guest</th>
            <th>Amount (Ksh)</th>
            <th>Payment Date</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo number_format($row['amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
            <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>No payment records found.</p>
    <?php endif; ?>
   <footer class="site-footer">
  <div class="footer-content">
        <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
        <p>Designed with Love by Susan Wangari Thuo</p>
    </div>
    </footer>
</body>
</html>
<?php
$stmt->close();
?>