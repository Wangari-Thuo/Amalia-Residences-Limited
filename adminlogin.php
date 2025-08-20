<?php
session_start();
include 'db.php';

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check admins table
    $query = "SELECT * FROM admins WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result)>0) {
        $admin = mysqli_fetch_assoc($result);
        
        if(password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['role'] ='admin';
            header("Location: admindashboard.php");
            exit();
        }
    }
    
    $error = "Invalid credentials!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 50px auto; }
        .login-form { border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; }
        button { padding: 8px 15px; background: #333; color: white; border: none; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Admin Login</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email:</label>
                <input type="text" name="email" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>