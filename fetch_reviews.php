<?php
$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) die("");

// Fetch reviews (updated for your schema)
$result = $conn->query("
    SELECT 
        r.review_id,
        r.review_text,
        r.rating,
        r.created_at,
        u.name AS user_name,
        p.title AS property_title
    FROM reviews r
    JOIN users u ON r.user_id = u.user_id
    JOIN properties p ON r.property_id = p.property_id
    ORDER BY r.created_at DESC
    LIMIT 5
");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="review">';
        echo '<h3>' . htmlspecialchars($row['property_title']) . '</h3>';
        echo '<p><strong>' . htmlspecialchars($row['user_name']) . '</strong> ';
        echo '<span class="rating">' . str_repeat('★', $row['rating']) . '</span></p>';
        echo '<p>' . nl2br(htmlspecialchars($row['review_text'])) . '</p>';
        echo '<p class="date">' . date('M j, Y', strtotime($row['created_at'])) . '</p>';
        echo '</div>';
    }
} else {
    echo "<p>No reviews yet. Be the first!</p>";
}

$conn->close();
?>