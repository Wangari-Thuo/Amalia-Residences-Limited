<?php
// ============================
// CONTACT FORM WITH PHP + HTML
// ============================


$recipient_email = "swangari388@gmail.com";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation
    $errors = [];
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (empty($subject)) $errors[] = "Subject is required.";
    if (empty($message)) $errors[] = "Message is required.";

    // If no errors, send email
    if (empty($errors)) {
        $headers  = "From: $name <$email>\r\n";
        $headers .= "Reply-To: $email\r\n";
        $mailBody = "Name: $name\nEmail: $email\n\nMessage:\n$message";

        if (mail($recipient_email, $subject, $mailBody, $headers)) {
            $success = "Your message has been sent successfully!";
        } else {
            $errors[] = "Failed to send your message. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Us</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f4f4;
        padding: 20px;
    }
    .container {
        max-width: 500px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
    }
    label {
        display: block;
        margin: 10px 0 5px;
        color: #555;
    }
    input, textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    button {
        background: #28a745;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
    }
    button:hover {
        background: #218838;
    }
    .success {
        background: #d4edda;
        color: #155724;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
    }
    .error {
        background: #f8d7da;
        color: #721c24;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
    }
</style>
</head>
<body>

<div class="container">
    <h2>Contact Us</h2>

    <?php if (!empty($success)): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Your Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>

        <label for="email">Your Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($subject ?? '') ?>" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="5" required><?= htmlspecialchars($message ?? '') ?></textarea>

        <button type="submit">Send Message</button>
    </form>
</div>

</body>
</html>