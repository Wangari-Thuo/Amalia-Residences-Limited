<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

if (!isset($_GET['id'])) {
    header("Location:hostproperties.php");
    exit;
}

$property_id = intval($_GET['id']);

// Fetch listing info
$stmt = $conn->prepare("SELECT description, location, price, status FROM properties WHERE property_id = ? AND owner_id = ?");
$stmt->bind_param("ii", $property_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Listing not found or you don't have permission.";
    exit;
}

$listing = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    $update_stmt = $conn->prepare("UPDATE properties SET description=?, location=?, price=?, status=? WHERE property_id=? AND owner_id=?");
    $update_stmt->bind_param("ssdiii", $description, $location, $price, $status, $property_id, $owner_id);

    if ($update_stmt->execute()) {
        $message = "Listing updated successfully!";
    } else {
        $message = "Error updating listing.";
    }
    $update_stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Edit Listing</title>
</head>
<body>
    <h2>Edit Your Property Listing</h2>
    <p style="color:green;"><?php echo $message; ?></p>

    <form method="POST" action="">
        <label>Description:</label><br>
        <textarea name="description" required><?php echo htmlspecialchars($property['description']); ?></textarea><br><br>

        <label>Location:</label><br>
        <input type="text" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required><br><br>

        <label>Price per night (Ksh):</label><br>
        <input type="number" step="0.01" name="price" value="<?php echo $property['totalprice']; ?>" required><br><br>

        <label>Status:</label><br>
        <select name="status">
            <option value="available" <?php if ($property['status'] == 'available') echo 'selected'; ?>>Available</option>
            <option value="booked" <?php if ($property['status'] == 'booked') echo 'selected'; ?>>Booked</option>
            <option value="unavailable" <?php if ($property['status'] == 'unavailable') echo 'selected'; ?>>Unavailable</option>
        </select><br><br>

        <button type="submit">Update Listing</button>
    </form>
    <br>
    <a href="hostproperties.php">Back to Listings</a>
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