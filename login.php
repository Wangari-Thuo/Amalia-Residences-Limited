<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $id_number = $_POST['id_number'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($id_number) || empty($password)) {
        echo "<script>alert('Please provide email, ID number, and password.'); window.history.back();</script>";
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
                echo "<script>alert('Login successful! Welcome, Host.'); window.location.href='hostdashboard.php';</script>";
            } else {
                echo "<script>alert('Login successful! Welcome, Guest.'); window.location.href='guestdashboard.php';</script>";
            }
            exit;
        } else {
            echo "<script>alert('Incorrect password.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User not found or ID number mismatch.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
}

$conn->close();
?>
