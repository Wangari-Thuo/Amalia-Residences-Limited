<?php
session_start();

// Check user logged in and is client/guest
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guest') {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT property_id, description, location, price, maxguests, image 
        FROM properties WHERE status = 'available'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Properties</title>
    <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
    <h2>Available Properties</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($property = $result->fetch_assoc()): ?>
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <h3><?php echo htmlspecialchars($property['description']); ?></h3>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
                <p><strong>Price per night:</strong> $<?php echo htmlspecialchars($property['price']); ?></p>
                <p><strong>Max guests:</strong> <?php echo htmlspecialchars($property['maxguests']); ?></p>
                <?php if (!empty($property['image'])): ?>
                    <img src="<?php echo htmlspecialchars($property['image']); ?>" alt="Property Image" width="200" />
                <?php endif; ?>
                <form method="GET" action="bookingform.php">
                    <input type="hidden" name="property_id" value="<?php echo $property['property_id']; ?>" />
                    <input type="hidden" name="price" value="<?php echo $property['price']; ?>" />
                    <input type="hidden" name="maxguests" value="<?php echo $property['maxguests']; ?>" />
                    <button type="submit">Book Now</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No available properties right now.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>