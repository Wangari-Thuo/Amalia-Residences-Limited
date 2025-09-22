<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'guest') {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Please submit the booking form first.<br>";
    echo "<a href ='index.php'>Go To Dashboard </a>";
    exit;
}

$user_id = $_SESSION['user_id'];

$property_id = $_POST['property_id'] ?? null;
$price = $_POST['price'] ?? null;
$maxguests = $_POST['maxguests'] ?? null;
$check_in = $_POST['check_in'] ?? '';
$check_out = $_POST['check_out'] ?? '';
$num_guests = (int)($_POST['num_guests'] ?? 0);

//validation to avoid booking past dates
$today=new DateTime();
$today->setTime(0,0,0);
$checkInDate=new DateTime($check_in);
$checkOutDate=new DateTime($check_out);

if($checkInDate<$today)
{
    echo "<script>alert('Check-in date cannot be in the past!'); window.history.back();</script>";
    exit;
}

if($checkOutDate<=$checkInDate)
{
    echo "<script>alert('Check-out date must be after the check-in date!'); window.history.back();</script>";
    exit;
}


if (!$property_id || !$price || !$maxguests || !$check_in || !$check_out || !$num_guests) {
    echo "<script>alert('Please fill in all fields.'); window.history.back();</script>";
    exit;
}

if ($num_guests > $maxguests) {
    echo "<script>alert('Number of guests exceeds maximum allowed.'); window.history.back();</script>";
    exit;
}

$date1 = new DateTime($check_in);
$date2 = new DateTime($check_out);
$interval = $date1->diff($date2);
$days = $interval->days;

if ($days <= 0) {
    echo "<script>alert('Check-out date must be after check-in date.'); window.history.back();</script>";
    exit;
}


$totalprice = $days * $price;

$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$status="confirmed";
$booking_date=date('Y-m-d');
$stmt = $conn->prepare("INSERT INTO bookings (user_id, property_id, booking_date, check_in, check_out, num_guests,status, totalprice) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?)");
if(!$stmt){
    die("prepare failed: " .$conn->error);
}
$stmt->bind_param("iissisd", $user_id, $property_id, $check_in, $check_out, $num_guests, $status, $totalprice);

if ($stmt->execute()) {
    echo "<script>
        alert('Booking successful! Total price: KES $totalprice');
        window.location.href = 'guestdashboard.php';
    </script>";
    exit;
} else {
    echo "<script>
        alert('Booking failed: " . $stmt->error . "');
        window.history.back();
    </script>";
}

$stmt->close();
$conn->close();