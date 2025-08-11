<?php
session_start();

// Database connection
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "amaliaresidences_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    die("We're experiencing technical difficulties. Please try again later.");
}

$user_id = $_SESSION['user_id'] ?? null;

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $category = trim($_POST['category'] ?? 'General Inquiry');
    $message = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($category) || empty($message)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Sanitize
        $name = htmlspecialchars($name);
        $email = htmlspecialchars($email);
        $category = htmlspecialchars($category);
        $message = htmlspecialchars($message);

        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO contact_us (user_id, name, email, category, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $name, $email, $category, $message);

        if ($stmt->execute()) {
            $success = "Your message has been sent successfully!";
            // Clear form values on success
            $name = $email = $message = '';
            $category = 'General Inquiry';
        } else {
            error_log("DB Insert error: " . $stmt->error);
            $error = "Failed to send your message. Please try again.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Contact Us - Amalia Residences</title>
  <link rel="stylesheet" href="main.css"/>
  <style>
    /* Basic styling for demo, customize in your main.css */
    body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
    .container { max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 6px; box-shadow: 0 0 10px #ccc;}
    label { display: block; margin-top: 15px; }
    input[type="text"], input[type="email"], select, textarea {
      width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px;
    }
    button { margin-top: 20px; padding: 10px 15px; background: #007bff; border: none; color: white; border-radius: 4px; cursor: pointer; }
    button:hover { background: #0056b3; }
    .error { color: red; margin-top: 10px; }
    .success { color: green; margin-top: 10px; }
    .view-messages-link { margin-top: 20px; display: block; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Contact Us</h1>

    <?php if ($error): ?>
      <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
      <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>

    <form action="contact_us.php" method="POST">
      <label for="name">Your Name:</label>
      <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required />

      <label for="email">Your Email:</label>
      <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required />

      <label for="category">Category:</label>
      <select name="category" id="category" required>
        <?php
          $categories = ['General Inquiry', 'Support Request', 'Complaint'];
          foreach ($categories as $cat) {
              $selected = ($cat === ($category ?? 'General Inquiry')) ? 'selected' : '';
              echo "<option value=\"$cat\" $selected>$cat</option>";
          }
        ?>
      </select>

      <label for="message">Message:</label>
      <textarea name="message" id="message" rows="6" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>

      <button type="submit">Send</button>
    </form>

    
<div class="social-links">
    <a href="https://facebook.com/https://www.facebook.com/share/17UqepWgRy/" target="_blank" class="social-btn facebook">
      <i class="fab fa-facebook-f"></i> Facebook
    </a>
    <a href="https://twitter.com/https://www.instagram.com/thuo_rey?igsh=MWVibmtqZmM2a2Y5MA==" target="_blank" class="social-btn twitter">
      <i class="fab fa-twitter"></i> Twitter
    </a>
    <a href="https://instagram.com/https://www.instagram.com/thuo_rey?igsh=MWVibmtqZmM2a2Y5MA==" target="_blank" class="social-btn instagram">
      <i class="fab fa-instagram"></i> Instagram
    </a>
    <a href="https://wa.me/+254708746900" target="_blank" class="social-btn whatsapp">
      <i class="fab fa-whatsapp"></i> WhatsApp
    </a>
  </div> 

    <?php if ($user_id): ?>
      <a href="messages.php" class="view-messages-link">View My Previous Messages</a>
    <?php endif; ?>
  </div>
</body>
</html>