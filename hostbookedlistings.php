<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get bookings for properties owned by host including property image
$sql = "SELECT b.booking_id, p.description, p.image, b.check_in, b.check_out, b.num_guests, 
               b.totalprice, b.status, u.name 
        FROM bookings b 
        JOIN properties p ON b.property_id = p.property_id 
        JOIN users u ON b.user_id = u.user_id 
        WHERE p.owner_id = ? 
        ORDER BY b.check_in DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Booked Listings</title>
    <style>
        .property-thumbnail {
            max-width: 100px;
            max-height: 80px;
            border-radius: 4px;
            object-fit: cover;
        }
        .property-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <h2>Bookings on Your Properties</h2>
    <a href="hostdashboard.php">Back to Dashboard</a>
    <br><br>

    <?php if ($result->num_rows > 0): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Property</th>
            <th>Guest</th>
            <th>Check-in</th>
            <th>Check-out</th>
            <th>Guests</th>
            <th>Total Price (Ksh)</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td class="property-cell">
                <?php 
                $imageFile = trim($row['image']); 
                if (!empty($imageFile)) {
                    $imagePath = 'uploads/' . $imageFile;
                } else {
                    $imagePath = 'images/default-property.jpg';
                }
                ?>
                <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                     alt="Property Image" 
                     class="property-thumbnail">
                <?php echo htmlspecialchars($row['description']); ?>
            </td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['check_in']); ?></td>
            <td><?php echo htmlspecialchars($row['check_out']); ?></td>
            <td><?php echo intval($row['num_guests']); ?></td>
            <td><?php echo number_format($row['totalprice'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>No bookings found.</p>
    <?php endif; ?>
    
    <footer class="site-footer">
        <div class="footer-content">
            <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
            <p>Designed with Love by Susan</p>
        </div>
    </footer>
</body>
</html>
<?php
$stmt->close();
?>