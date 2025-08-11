<?php
header('Content-Type: text/html'); // Ensure proper HTML output

$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) die("Database error");

$result = $conn->query("SELECT user_id, name FROM users");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['user_id']}'>{$row['name']}</option>";
    }
} else {
    echo "<option value=''>No users found</option>";
}
$conn->close();
?>