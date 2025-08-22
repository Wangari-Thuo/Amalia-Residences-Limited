<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $id_number = $_POST['id_number'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';  

    if (empty($name) || empty($email) || empty($id_number) || empty($password) || empty($confirm_password) || empty($role)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit;
    }

    if (strlen($id_number) < 7) {
        echo "<script>alert('ID number must be at least 7 digits long.'); window.history.back();</script>";
        exit;
    }
    if (strlen($password) < 7) {
    echo "<script>alert('Password must be at least 7 characters long.'); window.history.back();</script>";
    exit;
}

// Require at least one uppercase, one lowercase, one digit, and one special character
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{7,}$/', $password)) {
    echo "<script>alert('Password must include uppercase, lowercase, a number, and a special character.'); window.history.back();</script>";
    exit;
}


    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO users (name, email, id_number, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $id_number, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful! You can now log in.'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();  
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
}
?>
