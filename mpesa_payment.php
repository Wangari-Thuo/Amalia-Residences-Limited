<?php
session_start();

$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

$booking_id = $_POST['booking_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$phone = $_POST['phone'] ?? null;

if (!$booking_id || !$amount || !$phone) {
    die("Missing payment details.");
}

// Daraja API credentials — replace these with your actual credentials
$consumerKey = 'T6GGMLKYzwkKWGhAJ0MAkzOXGsA6xwmGh6ANXJEGGdT6g7PX';
$consumerSecret = 'L6BkN6bxJ4LIGNuQKconmKoiLFxS9rxSGGkdfrxoVBGqPGSsFXoj2GBnAvACEVAD';
$businessShortCode = '174379';
$passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'; // Lipa na M-Pesa Online Passkey
$callbackUrl = ' https://f50dc3a9edf5.ngrok-free.app -> http://localhost:80'; // Your callback URL

// Get access token
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);
$tokenUrl = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $tokenUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
curl_close($curl);

if (!$response) {
    die("Failed to get access token");
}

$tokenData = json_decode($response, true);
$accessToken = $tokenData['access_token'] ?? null;

if (!$accessToken) {
    die("Invalid access token response");
}

// Prepare STK Push request
$timestamp = date('YmdHis');
$password = base64_encode($businessShortCode . $passkey . $timestamp);

$stkPushUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$stkPushRequest = [
    'BusinessShortCode' => $businessShortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $businessShortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackUrl,
    'AccountReference' => "Booking $booking_id",
    'TransactionDesc' => "Payment for booking $booking_id"
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $stkPushUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, [
    'Content-Type:application/json',
    'Authorization:Bearer ' . $accessToken
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($stkPushRequest));

$response = curl_exec($curl);
curl_close($curl);

$responseData = json_decode($response, true);

if (isset($responseData['ResponseCode']) && $responseData['ResponseCode'] == '0') {
    $checkoutRequestID = $responseData['CheckoutRequestID'];

    // Save the payment record with status 'pending'
    $stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, method, checkout_request_id, payment_status) VALUES (?, ?, 'Mpesa', ?, 'pending')");
    $stmt->bind_param("ids", $booking_id, $amount, $checkoutRequestID);
    $stmt->execute();
    $stmt->close();

    echo "<h2>STK Push sent successfully!</h2>";
    echo "<p>Please complete the payment on your phone.</p>";
    echo "<p><a href='clientdashboard.php'>Go back to dashboard</a></p>";
} else {
    echo "<h2>Failed to initiate M-Pesa payment</h2>";
    echo "<pre>" . print_r($responseData, true) . "</pre>";
    echo "<p><a href='payment.php?booking_id=$booking_id&total_price=$amount'>Try Again</a></p>";
}

$conn->close();
?>