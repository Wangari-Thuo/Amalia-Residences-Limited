<?php
include "db.php"; // your DB connection

// Fetch all hosts whose passwords are not hashed yet
$stmt = $conn->prepare("SELECT user_id, password FROM users WHERE role='host'");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];
    $plain_password = $row['password'];

    // Hash the password
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    // Update the user record
    $update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $update->bind_param("si", $hashed_password, $user_id);
    $update->execute();
}

echo "All host passwords are now hashed successfully.";

$conn->close();
?>