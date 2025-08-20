<?php
session_start();
include("db.php");

// Ensure only logged-in users can review
if (!isset($_SESSION['user_id'])) {
    echo "❌ You must be logged in as a guest to leave a review.";
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $property_id = intval($_POST['property_id']);
    $review_text = mysqli_real_escape_string($con, $_POST['review_text']);
    $rating = intval($_POST['rating']);

    // ✅ Check if user has booked this property
    $check_booking = "SELECT * FROM bookings 
                      WHERE user_id = '$user_id' 
                      AND property_id = '$property_id' 
                      AND status = 'confirmed' 
                      LIMIT 1";
    $result_booking = mysqli_query($con, $check_booking);

    if (mysqli_num_rows($result_booking) == 0) {
        $message = "❌ You can only review properties you have booked and confirmed.";
    } else {
        // ✅ Check for duplicate review
        $check_review = "SELECT * FROM reviews 
                         WHERE user_id = '$user_id' 
                         AND property_id = '$property_id' 
                         LIMIT 1";
        $result_review = mysqli_query($con, $check_review);

        if (mysqli_num_rows($result_review) > 0) {
            $message = "⚠️ You have already reviewed this property.";
        } else {
            // ✅ Insert review
            $query = "INSERT INTO reviews (user_id, property_id, review_text, rating) 
                      VALUES ('$user_id', '$property_id', '$review_text', '$rating')";
            if (mysqli_query($con, $query)) {
                $message = "✅ Review submitted successfully!";
            } else {
                $message = "❌ Error: " . mysqli_error($con);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave a Review</title>
</head>
<body>
    <h2>Leave a Review</h2>
    <?php if ($message) echo "<p><strong>$message</strong></p>"; ?>

    <form method="post">
        <label for="property_id">Property ID:</label>
        <input type="number" name="property_id" required><br><br>

        <label for="review_text">Your Review:</label><br>
        <textarea name="review_text" required></textarea><br><br>

        <label for="rating">Rating (1-5):</label>
        <input type="number" name="rating" min="1" max="5" required><br><br>

        <input type="submit" value="Submit Review">
    </form>

    <h2>All Reviews</h2>
    <?php
    $sql = "SELECT r.review_text, r.rating, u.name, p.description 
            FROM reviews r 
            JOIN users u ON r.user_id = u.user_id 
            JOIN properties p ON r.property_id = p.property_id 
            ORDER BY r.review_id DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            echo "<p><strong>" . htmlspecialchars($row['name']) . "</strong> 
                  reviewed <em>" . htmlspecialchars($row['description']) . "</em>: 
                  " . htmlspecialchars($row['review_text']) . " 
                  (Rating: " . $row['rating'] . "/5)</p>";
        }
    } else {
        echo "No reviews yet.";
    }
    ?>
</body>
</html>