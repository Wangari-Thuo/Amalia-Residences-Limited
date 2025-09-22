<?php
// =========================================================
// bookproperty.php
// Description: Allows a logged-in guest to book a property,
// calculates total price dynamically, and validates input.
// =========================================================

// Start PHP session to track user login
session_start();

// Check that user is logged in and is a guest
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guest') {
    header("Location: login.html"); // Redirect to login if not a guest
    exit;
}

// Retrieve property details passed via GET from previous page
$property_id = $_GET['property_id'] ?? null; // Property ID
$price = $_GET['price'] ?? null; // Price per night
$maxguests = $_GET['maxguests'] ?? null; // Maximum allowed guests

// Validate that all required data is present
if (!$property_id || !$price || !$maxguests) {
    echo "Invalid property selection."; // Show error if any info missing
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AMALIA RESIDENCES LIMITED - Book Property</title>
    <link rel="stylesheet" href="main.css">

    <script>
        // =========================================================
        // calculateTotal() - calculates total price based on input
        // =========================================================
        function calculateTotal() {
            // Convert PHP variables to JS variables
            const pricePerNight = <?php echo json_encode((float)$price); ?>; // nightly price
            const maxGuests = <?php echo json_encode((int)$maxguests); ?>; // max guests allowed

            const checkIn = document.getElementById('check_in').value; // check-in date
            const checkOut = document.getElementById('check_out').value; // check-out date
            const guestsInput = document.getElementById('num_guests'); // guests input
            const guests = parseInt(guestsInput.value) || 0; // number of guests

            const totalPriceSpan = document.getElementById('total_price'); // display element
            const guestsError = document.getElementById('guests_error'); // error message element

            // Reset guest error message
            guestsError.textContent = '';

            // Validate guest number
            if (guests > maxGuests) {
                guestsError.textContent = "Number of guests exceeds max allowed (" + maxGuests + ")";
                guestsInput.value = maxGuests; // auto-set to max
                return; // stop calculation
            }

            // Calculate total only if both dates are selected
            if (checkIn && checkOut) {
                const inDate = new Date(checkIn); // convert string to Date
                const outDate = new Date(checkOut); // convert string to Date

                // Calculate difference in milliseconds
                const diffTime = outDate - inDate;

                // Convert milliseconds to days
                const diffDays = diffTime / (1000 * 60 * 60 * 24);
                // Explanation:
                // 1000 ms = 1 second
                // 60 sec * 1000 = 1 min
                // 60 min * 60 * 1000 = 1 hour
                // 24 hours * 60 * 60 * 1000 = 1 day

                // Only calculate total if dates are valid
                if (diffDays > 0) {
                    const total = diffDays * pricePerNight; // total price = nights * price per night
                    totalPriceSpan.textContent = total.toFixed(2); // show 2 decimals
                } else {
                    totalPriceSpan.textContent = "0.00"; // invalid date range
                }
            } else {
                totalPriceSpan.textContent = "0.00"; // if dates missing
            }
        }

        // Add event listeners on page load
        window.onload = function() {
            document.getElementById('check_in').addEventListener('change', calculateTotal);
            document.getElementById('check_out').addEventListener('change', calculateTotal);
            document.getElementById('num_guests').addEventListener('input', calculateTotal);
            calculateTotal(); // initial calculation
        }
    </script>

    <style>
        /* Footer styling */
        .site-footer {
            background: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 15px 10px;
            margin-top: auto;
        }
        .site-footer p {
            margin: 5px 0;
            font-size: 0.9rem;
        }

        /* General container and form styling */
        .container, form, table, img, p, h2 {
            margin-top: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto; /* center horizontally */
            padding: 20px;
        }
    </style>
</head>
<body>
    <h2>Book Property With Us</h2>

    <!-- Navigation links for clients -->
    <nav class="nav"> 
        <a href="viewproperties.php">Book Now</a>
        <a href="searchproperties.php">Search Now</a>
        <a href="contact_us.php">Help & Support</a>
        <a href="reviews.php">Leave a Review</a>
        <a href="FAQs.html">FAQs</a>
        <a href="logout.php">Log Out</a>
    </nav>

    <div class="container">
        <!-- Booking form -->
        <form method="POST" action="bookingprocess.php">
            <!-- Hidden inputs to pass property info to backend -->
            <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property_id); ?>">
            <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
            <input type="hidden" name="maxguests" value="<?php echo htmlspecialchars($maxguests); ?>">

            <!-- Display property details -->
            <p>Property ID: <?php echo htmlspecialchars($property_id); ?></p>
            <p>Price per night: KES <?php echo htmlspecialchars($price); ?></p>
            <p>Max guests allowed: <?php echo htmlspecialchars($maxguests); ?></p>

            <!-- Check-in date input -->
            <label for="check_in">Check-in Date:</label>
            <input type="date" id="check_in" name="check_in" required><br><br>

            <!-- Check-out date input -->
            <label for="check_out">Check-out Date:</label>
            <input type="date" id="check_out" name="check_out" required><br><br>

            <!-- Number of guests input with validation -->
            <label for="num_guests">Number of Guests:</label>
            <input type="number" id="num_guests" name="num_guests" min="1" max="<?php echo htmlspecialchars($maxguests); ?>" required>
            <span id="guests_error" style="color:red;"></span><br><br>

            <!-- Total price display -->
            <p><strong>Total Price: KES <span id="total_price">0</span></strong></p>

            <!-- Submit booking -->
            <button type="submit">Confirm Booking</button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-content">
            <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
            <p>Designed with Love by Susan Wangari Thuo</p>
        </div>
    </footer>
</body>
</html>