<?php
include 'db.php';

// Check if the search form was submitted
$searchPerformed = !empty($_GET);

$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

$result = null;

if ($searchPerformed) {
    // Build SQL query
    $sql = "SELECT * FROM properties WHERE 1=1";
    $params = [];
    $types = '';

    if ($location !== '') {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
        $types .= 's';
    }
    if ($min_price > 0) {
        $sql .= " AND price >= ?";
        $params[] = $min_price;
        $types .= 'd';
    }
    if ($max_price > 0) {
        $sql .= " AND price <= ?";
        $params[] = $max_price;
        $types .= 'd';
    }
    if ($status === 'available' || $status === 'booked') {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= 's';
    }

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Browse Properties</title>
</head>
<body>
     <nav class="nav"> <!-- Move nav to the top inside body -->
        <a href="signup.html">Sign Up</a>
        <a href="login.html">Login</a>
        <a href="viewproperties.php">Book Now</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="contact_us.php">Help & Support</a>
        <a href="reviews.php">Leave a Review</a>
        <a href="FAQs.html">FAQs</a>
        <a href="logout.php">Log Out</a>
    </nav>
    <h2>Search Properties</h2>
    <form method="GET" action="">
        <label>Location:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($location); ?>" placeholder="Location">
        <label>Min Price:</label>
        <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" min="0" step="0.01">
        <label>Max Price:</label>
        <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" min="0" step="0.01">
        <label>Status:</label>
        <select name="status">
            <option value="" <?php if ($status == '') echo 'selected'; ?>>Any</option>
            <option value="available" <?php if ($status == 'available') echo 'selected'; ?>>Available</option>
            <option value="booked" <?php if ($status == 'booked') echo 'selected'; ?>>Booked</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <h2>Results</h2>
<?php if ($searchPerformed): ?>
    <?php if ($result && $result->num_rows > 0): ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Description</th>
                <th>Location</th>
                <th>Price</th>
                <th>Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No properties found.</p>
    <?php endif; ?>
<?php else: ?>
    <p>Please enter your search criteria above and click "Search".</p>
<?php endif; ?>
      <footer class="site-footer">
  <div class="footer-content">
        <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
        <p>Designed with Love by Susan Wangari Thuo</p>
    </div>
    </footer>
</body>
</html>