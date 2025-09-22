<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Airbnb System - Home</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="home-page">
    <div class="page-wrapper">
        <main class="content">
            <div class="hero-image">
                <div class="hero-text">
                    <h1>Welcome to Amalia Residences Limited</h1>
                    <p>Your perfect getaway begins here</p>
                    <h2>Explore Our Airbnb Booking System</h2>

                    <nav class="nav">
                        <?php if(!isset($_SESSION['user_id'])): ?>
                            <!-- For visitors who are not logged in -->
                            <a href="signup.html">Sign Up</a>
                            <a href="login.html">Login</a>
                        <?php else: ?>
                            <!-- For logged-in users -->
                            <?php if($_SESSION['role'] === 'user'): ?>
                                <a href="viewproperties.php">Book Now</a>
                                <a href="reviews.php">Leave a Review</a>
                            <?php elseif($_SESSION['role'] === 'host'): ?>
                                <a href="hostaddlisting.php">Add Property</a>
                                <a href="viewproperties.php">View Your Listings</a>
                            <?php endif; ?>
                            <a href="logout.php">Logout</a>
                        <?php endif; ?>

                        <!-- Links visible to everyone -->
                        <a href="contact_us.php">Contact Us</a>
                        <a href="searchproperties.php">Search Now</a>
                        <a href="contact_us.php">Help & Support</a>
                        <a href="aboutus.html">About Us</a>
                        <a href="FAQs.html">FAQs</a>
                    </nav>
                </div>
            </div>
        </main>
    </div>

    <footer class="site-footer">
        <div class="footer-content">
            <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
            <p>Designed with Love by Susan Wangari Thuo</p>
            <div class="social-links">
                <a href="https://www.facebook.com/share/17UqepWgRy/" target="_blank" class="social-btn facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/thuo_rey" target="_blank" class="social-btn twitter"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/thuo_rey?igsh=MWVibmtqZmM2a2Y5MA==" target="_blank" class="social-btn instagram"><i class="fab fa-instagram"></i></a>
                <a href="https://wa.me/254708746900" target="_blank" class="social-btn whatsapp"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>