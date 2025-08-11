<?php
session_start();

// Check user is logged in and is a guest/client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guest') {
    header("Location: login.html");
    exit;
}

// Get property details from GET parameters
$property_id = $_GET['property_id'] ?? null;
$price = $_GET['price'] ?? null;
$maxguests = $_GET['maxguests'] ?? null;

if (!$property_id || !$price || !$maxguests) {
    echo "Invalid property selection.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AMALIA RESIDENCES LIMITED- Book Property</title>
    <link rel="stylesheet" href="main.css">
    <script>
        function calculateTotal() {
            const pricePerNight = <?php echo json_encode((float)$price); ?>;
            const maxGuests = <?php echo json_encode((int)$maxguests); ?>;

            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            const guestsInput = document.getElementById('num_guests');
            const guests = parseInt(guestsInput.value) || 0;

            const totalPriceSpan = document.getElementById('total_price');
            const guestsError = document.getElementById('guests_error');

            guestsError.textContent = '';
            if (guests > maxGuests) {
                guestsError.textContent = "Number of guests exceeds max allowed (" + maxGuests + ")";
                guestsInput.value = maxGuests;
                return;
            }

            if (checkIn && checkOut) {
                const inDate = new Date(checkIn);
                const outDate = new Date(checkOut);

                const diffTime = outDate - inDate;
                const diffDays = diffTime / (1000 * 60 * 60 * 24);

                if (diffDays > 0) {
                    const total = diffDays * pricePerNight;
                    totalPriceSpan.textContent = total.toFixed(2);
                } else {
                    totalPriceSpan.textContent = "0.00";
                }
            } else {
                totalPriceSpan.textContent = "0.00";
            }
        }
        window.onload = function() {
            document.getElementById('check_in').addEventListener('change', calculateTotal);
            document.getElementById('check_out').addEventListener('change', calculateTotal);
            document.getElementById('num_guests').addEventListener('input', calculateTotal);
            calculateTotal();
        }
    </script>
</head>
<body>
    <h2>Book Property With Us</h2>

    <form method="POST" action="bookingprocess.php">
        <input type="hidden" name="property_id" value="<?php echo htmlspecialchars($property_id); ?>">
        <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
        <input type="hidden" name="maxguests" value="<?php echo htmlspecialchars($maxguests); ?>">

        <p>Property ID: <?php echo htmlspecialchars($property_id); ?></p>
        <p>Price per night: $<?php echo htmlspecialchars($price); ?></p>
        <p>Max guests allowed: <?php echo htmlspecialchars($maxguests); ?></p>

        <label for="check_in">Check-in Date:</label>
        <input type="date" id="check_in" name="check_in" required><br><br>

        <label for="check_out">Check-out Date:</label>
        <input type="date" id="check_out" name="check_out" required><br><br>

        <label for="num_guests">Number of Guests:</label>
        <input type="number" id="num_guests" name="num_guests" min="1" max="<?php echo htmlspecialchars($maxguests); ?>" required>
        <span id="guests_error" style="color:red;"></span><br><br>

        <p><strong>Total Price: $<span id="total_price">0.00</span></strong></p>

        <button type="submit">Confirm Booking</button>
    </form>
    <p>&copy; 2025 Amalia Residences Limited. All rights reserved.</p>
    <p>Designed with 💜 by Susan Wangari Thuo</p>
</body>
</html>