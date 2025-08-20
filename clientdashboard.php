<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// --- Handle profile update form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');

    // Validate inputs
    if (!$name || !$email || !$id_number || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please fill all fields correctly.";
    } else {
        $profile_photo = null;

        // Handle file upload if exists
        if (!empty($_FILES['profile_photo']['name'])) {
            $file = $_FILES['profile_photo'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_file_size = 2 * 1024 * 1024; // 2 MB

            if (!in_array($file['type'], $allowed_types)) {
                $message = "Invalid image type. Only JPG, PNG, GIF allowed.";
            } elseif ($file['size'] > $max_file_size) {
                $message = "File size exceeds 2MB limit.";
            } elseif ($file['error'] === 0) {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_name = "user_" . $user_id . "." . $ext;
                $destination = "uploads/" . $new_name;

                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $message = "Failed to upload image.";
                } else {
                    $profile_photo = $new_name;
                }
            } else {
                $message = "Error uploading image.";
            }
        }

        // If no errors, update DB
        if (empty($message)) {
            if ($profile_photo) {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, id_number=?, profile_photo=? WHERE user_id=?");
                $stmt->bind_param("ssssi", $name, $email, $id_number, $profile_photo, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, id_number=? WHERE user_id=?");
                $stmt->bind_param("sssi", $name, $email, $id_number, $user_id);
            }

            if ($stmt->execute()) {
                $message = "Profile updated successfully.";
            } else {
                $message = "Error updating profile: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// -- Handle Mpesa payment submission --
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    $booking_id = intval($_POST['booking_id']);
    $mpesa_phone = trim($_POST['mpesa_phone']);
    $mpesa_receipt = trim($_POST['mpesa_receipt']);
    $amount = floatval($_POST['amount']);

    // Basic validation
    if (!preg_match('/^\d{10}$/', $mpesa_phone)) {
        $message = "Please enter a valid 10-digit Mpesa phone number.";
    } elseif (empty($mpesa_receipt)) {
        $message = "Please enter the Mpesa receipt number.";
    } else {
        // Insert payment record
        $stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, method, mpesa_receipt, payment_status) VALUES (?, ?, 'mpesa', ?, 'success')");
        
        if ($stmt === false) {
            $message = "Failed to prepare payment query: " . $conn->error;
        } else {
            $stmt->bind_param("ids", $booking_id, $amount, $mpesa_receipt);

            if ($stmt->execute()) {
                // Update booking payment status to 'paid'
                $stmt2 = $conn->prepare("UPDATE bookings SET status = 'paid' WHERE booking_id = ?");
                if ($stmt2 === false) {
                    $message = "Failed to prepare booking update: " . $conn->error;
                } else {
                    $stmt2->bind_param("i", $booking_id);
                    if (!$stmt2->execute()) {
                        $message = "Error updating booking status: " . $stmt2->error;
                    }
                    $stmt2->close();
                }

                if (!isset($message)) {
                    $message = "Payment successful!";
                }
            } else {
                $message = "Error processing payment: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// --- Fetch current user info ---
$stmt = $conn->prepare("SELECT name, email, id_number, profile_photo FROM users WHERE user_id = ?");
if (!$stmt) {
    die("prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// --- Fetch user bookings with payments ---
$bookings_sql = "
    SELECT b.booking_id, b.property_id, b.check_in, b.check_out, b.num_guests, 
           b.totalprice, b.status,
           p.description AS property_description,
           (SELECT payment_status FROM payments WHERE booking_id = b.booking_id ORDER BY payment_date DESC LIMIT 1) AS payment_status
    FROM bookings b
    JOIN properties p ON b.property_id = p.property_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
";
$stmt = $conn->prepare($bookings_sql);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Client Dashboard - Profile & Bookings</title>
    <style>
        /* Basic styling */
        body 

        { font-family: Arial, sans-serif; padding: 20px; }
        .profile-photo { width: 100px; border-radius: 50%; }
        .message { margin: 10px 0; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        form { max-width: 400px; margin-bottom: 40px; }
        label { display: block; margin-top: 10px; }
        input[type=text], input[type=email], input[type=file], input[type=number] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { margin-top: 15px; padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        table { border-collapse: collapse; width: 100%; margin-top: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .pay-form input { width: auto; display: inline-block; margin-right: 5px; }
        .pay-form button { padding: 5px 10px; margin-top: 0; }
    </style>
</head>
<body>
     <nav class="nav"> <!-- Move nav to the top inside body -->
        <a href="signup.html">Sign Up</a>
        <a href="login.html">Login</a>
        <a href="viewproperties.php">Book Now</a>
        <a href="contact_us.php">Contact Us</a>
        <a href="searchproperties.php">Search Now</a>
        <a href="contact_us.php">Help & Support</a>
        <a href="reviews.php">Leave a Review</a>
        <a href="FAQs.html">FAQs</a>
        <a href="logout.php">Log Out</a>
    </nav>

<h1>Welcome, <?= htmlspecialchars($user['name']) ?></h1>

<?php if ($message): ?>
    <div class="message <?= strpos($message, 'Error') === false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<!-- Show current profile photo -->
<?php if ($user['profile_photo']): ?>
    <img src="uploads/<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo" class="profile-photo" />
<?php else: ?>
    <p><em>No profile photo uploaded.</em></p>
<?php endif; ?>

<!-- Profile Edit Form -->
<h2>Edit Your Profile</h2>
<form action="" method="POST" enctype="multipart/form-data">
    <label for="name">Full Name:</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>

    <label for="email">Email Address:</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="id_number">ID Number:</label>
    <input type="text" name="id_number" id="id_number" value="<?= htmlspecialchars($user['id_number']) ?>" required>

    <label for="profile_photo">Profile Photo (optional, max 2MB):</label>
    <input type="file" name="profile_photo" id="profile_photo" accept="image/*">

    <button type="submit" name="update_profile">Update Profile</button>
</form>

<!-- Bookings List -->
<h2>Your Bookings</h2>
<?php if ($bookings_result->num_rows === 0): ?>
    <p>No bookings found.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Property</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Guests</th>
                <th>Total Price ($)</th>
                <th>Booking Status</th>
                <th>Payment Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $bookings_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['property_description']) ?></td>
                <td><?= htmlspecialchars($row['check_in']) ?></td>
                <td><?= htmlspecialchars($row['check_out']) ?></td>
                <td><?= htmlspecialchars($row['num_guests']) ?></td>
                <td><?= number_format($row['totalprice'], 2) ?></td>
                <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                <td><?= htmlspecialchars($row['payment_status'] ?? 'pending') ?></td>
                <td>
                    <?php if (empty($row['payment_status']) || strtolower($row['payment_status']) == 'pending'): ?>
                        <form method="POST" class="pay-form">
                            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['booking_id']) ?>">
                            <input type="hidden" name="amount" value="<?= htmlspecialchars($row['totalprice']) ?>">
                            <input type="text" name="mpesa_phone" placeholder="Mpesa Phone (10 digits)" required pattern="\d{10}" maxlength="10" size="12">
                            <input type="text" name="mpesa_receipt" placeholder="Mpesa Receipt No." required size="12">
                            <button type="submit" name="pay_now">Pay Now</button>
                        </form>
                    <?php else: ?>
                        <em>Paid</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

<footer class="site-footer">
  <div class="footer-content">
    <p&copy;> 2025 Amalia Residences Limited. All rights reserved.</p>
    <p>Designed with Love by Susan Wangari Thuo</p>
  </div>
</footer>
</body>
</html>