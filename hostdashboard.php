<?php
session_start();
include 'db.php';

// Check if host is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="main.css">
    <title>Amalia Residences Limited-Host Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        nav a { margin-right: 15px; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Welcome to Host Dashboard</h1>
    <nav>
        <a href="hostaddlisting.php">Add Listing</a>
        <a href="hostproperties.php">My Listings</a>
        <a href="hostbookedlistings.php">Booked Listings</a>
        <a href="hostpayments.php">Payments</a>
        <a href="hostreviews.php">Reviews</a>
        <a href="hostcontactqueries.php">Contact Queries</a>
        <a href="logout.php">Logout</a>
    </nav>
    <footer class="site-footer">
  <div class="footer-content">
     <p&copy;> 2025 Amalia Residences Limited. All rights reserved.</p>
     <p>Designed with Love by Susan</p>
 </footer>

</body>
</html>