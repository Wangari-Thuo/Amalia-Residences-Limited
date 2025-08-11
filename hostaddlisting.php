<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $status = $_POST['status'] ?? 'available';
    $maxguests = $_POST['maxguests'] ?? 1;

    // Handle image upload
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed)) {
            $image_name = uniqid() . '.' . $file_ext;
            $destination = _DIR_ . '/uploads/' . $image_name;

            if (!is_dir(_DIR_ . '/uploads')) {
                mkdir(_DIR_ . '/uploads', 0755, true);
            }

            if (!move_uploaded_file($file_tmp, $destination)) {
                $message = "Failed to upload image.";
            }
        } else {
            $message = "Invalid image file type.";
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO properties (owner_id, description, location, price, status, image, maxguests) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdssi", $user_id, $description, $location, $price, $status, $image_name, $maxguests);

    if ($stmt->execute()) {
        $message = "Listing added successfully!";
    } else {
        $message = "Error adding listing: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Amalia Residences Limited - Add New Listing</title>
</head>
<body>
    <a href="hostdashboard.php">Back to Dashboard</a>
    <a href="hostproperties.php">View Current Listings</a>

    <h2>Add a New Property Listing</h2>
    <p style="color:green;"><?php echo $message; ?></p>

    <form method="POST" action="" enctype="multipart/form-data">
        <label>Description:</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Location:</label><br>
        <input type="text" name="location" required><br><br>

        <label>Price per night (Ksh):</label><br>
        <input type="number" name="price" step="0.01" required><br><br>

        <label>Status:</label><br>
        <input type="text" name="status" value="available"><br><br>

        <label>Image:</label><br>
        <input type="file" name="image" accept="image/*" required><br><br>

        <label>Maximum Guests Allowed:</label><br>
        <input type="number" name="maxguests" min="1" required><br><br>

        <button type="submit">Add Listing</button>
    </form>

    <footer>
            <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
            <p>Designed with Love by Susan</p>
        </div>
    </footer>
</body>
</html>