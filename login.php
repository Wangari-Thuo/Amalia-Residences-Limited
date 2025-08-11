<?php
session_start();

$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $id_number = $_POST['id_number'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($id_number) || empty($password)) {
        echo "Please provide email, ID number, and password.";
        exit;
    }

    // Use prepared statement to find user by email and id_number
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND id_number = ?");
    $stmt->bind_param("ss", $email, $id_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'host') {
                header("Location: hostdashboard.php");
            } else {
                header("Location: clientdashboard.php");
            }
            exit;
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found or ID number mismatch.";
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>