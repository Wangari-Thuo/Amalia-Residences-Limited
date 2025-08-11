<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables to avoid undefined errors
$user_id = $property_id = $rating = $review_text = '';

// --- HANDLE FORM SUBMISSION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    // Get and validate form data
    $user_id = $_POST['user_id'] ?? '';
    $property_id = $_POST['property_id'] ?? '';
    $rating = $_POST['rating'] ?? '';
    $review_text = $_POST['review_text'] ?? '';
    
    if (!empty($user_id) && !empty($property_id) && !empty($rating) && !empty($review_text)) {
        // Insert review
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, property_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())");
        
        if ($stmt) {
            $stmt->bind_param("iiis", $user_id, $property_id, $rating, $review_text);
            
            if ($stmt->execute()) {
                $success = "Review submitted successfully!";
            } else {
                $error = "Error submitting review: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = "Please fill all fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave a Review</title>
    <link rel="stylesheet" href="main.css">
    <style>
        .star-rating { direction: rtl; display: inline-block; }
        .star-rating input { display: none; }
        .star-rating label { 
            font-size: 2em; 
            color: #ddd; 
            cursor: pointer; 
            padding: 0 3px;
        }
        .star-rating input:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label { color: #ffc107; }
        textarea { width: 100%; min-height: 100px; padding: 10px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Leave a Review</h1>
    
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <!-- User Dropdown -->
        <label>User:</label>
        <select name="user_id" required>
            <option value="">Select User</option>
            <?php
            $users = $conn->query("SELECT user_id, name FROM users");
            while ($user = $users->fetch_assoc()) {
                $selected = ($user['user_id'] == $user_id) ? 'selected' : '';
                echo "<option value='{$user['user_id']}' $selected>{$user['name']}</option>";
            }
            ?>
        </select><br><br>
        
       <label>Property:</label>
<select name="property_id" required>
    <option value="">Select Property</option>
    <?php
    // Debugging: Check connection first
    if ($conn->connect_error) {
        echo '<option value="">Database connection failed</option>';
    } else {
        // Run query with error handling
        $propQuery = $conn->query("SELECT property_id, description FROM properties");
        
        if ($propQuery === false) {
            // Show SQL error (remove in production)
            echo '<option value="">Query error: ' . htmlspecialchars($conn->error) . '</option>';
        } elseif ($propQuery->num_rows > 0) {
            // Display properties
            while ($property = $propQuery->fetch_assoc()) {
                $selected = (isset($_POST['property_id']) && $_POST['property_id'] == $property['property_id']) ? ' selected' : '';
                echo "<option value='{$property['property_id']}'$selected>{$property['description']}</option>";
            }
        } else {
            echo '<option value="">No properties available</option>';
        }
    }
    ?>

</select>
        </select><br><br>
        
        <!-- Star Rating -->
        <label>Rating:</label>
        <div class="star-rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                    <?php echo ($rating == $i) ? 'checked' : ''; ?> required>
                <label for="star<?php echo $i; ?>">★</label>
            <?php endfor; ?>
        </div><br>
        
        <!-- Review Text -->
        <label>Your Review:</label><br>
        <textarea name="review_text" required><?php echo htmlspecialchars($review_text); ?></textarea><br><br>
        
        <button type="submit">Submit Review</button>
    </form>
    
    <!-- Display Existing Reviews -->
    <h2>Recent Reviews</h2>
    <?php
    $reviews = $conn->query("SELECT r.*, u.name as user_name, p.description as description 
                            FROM reviews r
                            JOIN users u ON r.user_id = u.user_id
                            JOIN properties p ON r.property_id = p.property_id
                            ORDER BY created_at DESC LIMIT 5");
    if(!$reviews)
    {
        die("Query Failed: " .$conn->error);
    }
    
    if ($reviews->num_rows > 0) {
        while ($review = $reviews->fetch_assoc()) {
            echo "<div style='border:1px solid #ddd; padding:10px; margin-bottom:10px;'>";
            echo "<h3>{$review['description']}</h3>";
            echo "<p>Rating: " . str_repeat('★', $review['rating']) . "</p>";
            echo "<p>{$review['review_text']}</p>";
            echo "<small>By {$review['user_name']} on {$review['created_at']}</small>";
            echo "</div>";
        }
    } else {
        echo "<p>No reviews yet. Be the first!</p>";
    }
    
    $conn->close();
    ?>
</body>
<footer class="site-footer">
  <div class="footer-content">
    <p&copy;> 2025 Amalia Residences Limited. All rights reserved.</p>
    <p>Designed with Love by Susan</p>
  </div>
</footer>

</html>