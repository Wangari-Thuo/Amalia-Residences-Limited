<?php
session_start();
include "db.php"; //database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id']) && isset($_SESSION['user_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $user_id = $_SESSION['user_id'];

    // Update the booking status to 'cancelled' only if it belongs to the logged-in user
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE booking_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Booking cancelled successfully.";
    } else {
        $_SESSION['message'] = "Error cancelling booking.";
    }

    $stmt->close();
    $conn->close();

    header("Location: clientdashboard.php");
    exit;
} else {
    header("Location: login.php");
    exit;
}