<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle delete request
if (isset($_GET['delete'])) {
    $property_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM properties WHERE property_id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $property_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: hostproperties.php");
    exit;
}

// Fetch host listings - added missing fields (image and max_guests)
$stmt = $conn->prepare("SELECT property_id, image, description, location, price, maxguests, status FROM properties WHERE owner_id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>My Listings</title>
</head>
<body>
    <h2>My Property Listings</h2>
    <a href="hostaddlisting.php">Add New Listing</a> | <a href="hostdashboard.php">Back to Dashboard</a>
    <br><br>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Image</th>
                <th>Description</th>
                <th>Location</th>
                <th>Price (Ksh)</th>
                <th>Max Guests Allowed</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if (isset($row['image']) && $row['image'] && file_exists('uploads/' . $row['image'])): ?>
                        <img src="<?php echo 'uploads/' . htmlspecialchars($row['image']); ?>" alt="Property Image" class="property-image">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['maxguests']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <a href="hosteditlisting.php?id=<?php echo $row['property_id']; ?>">Edit</a> | 
                    <a href="hostproperties.php?delete=<?php echo $row['property_id']; ?>" onclick="return confirm('Are you sure to delete this listing?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>You have no listings yet.</p>
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