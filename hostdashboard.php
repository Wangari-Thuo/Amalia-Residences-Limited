<?php
/**
 * Host Dashboard Page
 * -------------------
 * This page serves as the main dashboard for hosts in the Amalia Residences Limited system.
 * It allows hosts to access key functionalities such as:
 *  - Adding property listings
 *  - Viewing their properties
 *  - Checking booked listings
 *  - Managing payments
 *  - Reading reviews
 *  - Handling guest contact queries
 * It also provides a personalized welcome message to the logged-in host.
 */

session_start(); // Start a session to track logged-in user
include 'db.php'; // Connect to the database

// Check if host is logged in and has the correct role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html"); // Redirect to login if not authenticated
    exit;
}

$user_id = $_SESSION['user_id']; // Store the logged-in user's ID

// Fetch host's name for personalization
$query = "SELECT name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Amalia Residences Limited - Host Dashboard</title>
    <link rel="stylesheet" type="text/css" href="main.css">
    <style>
        /* Layout */
        body { margin: 0; font-family: Arial, sans-serif; background: #f4f8fb; }
        .container { display: flex; height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background: #2c3e50;
            color: #fff;
            padding-top: 20px;
            flex-shrink: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #f1f1f1;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            font-weight: bold;
        }
        .sidebar a:hover {
            background: #34495e;
            color: #1abc9c;
        }

        /* Content area */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .content-header {
            background: #ecf0f1;
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
        }
        .content-body {
            flex: 1;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Footer */
        footer.site-footer {
            background: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <h2>Host Panel</h2>
        <a href="hostaddlisting.php" target="contentFrame">➕ Add Listing</a>
        <a href="hostproperties.php" target="contentFrame">🏠 My Listings</a>
        <a href="hostbookedlistings.php" target="contentFrame">📅 Booked Listings</a>
        <a href="hostpayments.php" target="contentFrame">💰 Payments</a>
        <a href="hostreviews.php" target="contentFrame">⭐ Reviews</a>
        <a href="hostcontactqueries.php" target="contentFrame">📨 Contact Queries</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="content-header">
            <h1>Welcome, <?php echo htmlspecialchars($name); ?> 🎉</h1>
        </div>
        <div class="content-body">
            <!-- Pages load here -->
            <iframe name="contentFrame" src="hostwelcome.php"></iframe>
        </div>
        <footer class="site-footer">
            <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
            <p>Designed with Love by Susan Wangari Thuo</p>
        </footer>
    </div>
</div>
</body>
</html>