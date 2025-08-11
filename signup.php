<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $id_number = $_POST['id_number'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';  

    if (empty($name) || empty($email) || empty($id_number) || empty($password) || empty($role)) {
        echo "Please fill in all required fields.";
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
        echo "Signup successful. You can now <a href='login.html'>log in</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();  
} else {
    echo "Invalid request method.";
}
?>