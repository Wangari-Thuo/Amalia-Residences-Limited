<?php
// mpesa_callback.php

$callbackJSONData = file_get_contents('php://input');
$callbackData = json_decode($callbackJSONData, true);

file_put_contents('mpesa_callback_log.txt', date('Y-m-d H:i:s') . " - " . $callbackJSONData . "\n", FILE_APPEND);

$conn = new mysqli("localhost", "root", "", "amaliaresidences_db");
if ($conn->connect_error) {
    http_response_code(500);
    exit("Database connection failed");
}

if (!isset($callbackData['Body']['stkCallback'])) {
    http_response_code(400);
    exit("Invalid callback data");
}

$stkCallback = $callbackData['Body']['stkCallback'];
$checkoutRequestID = $stkCallback['CheckoutRequestID'] ?? null;
$resultCode = $stkCallback['ResultCode'] ?? 1;  // 0 = success
$resultDesc = $stkCallback['ResultDesc'] ?? 'Unknown result';

if (!$checkoutRequestID) {
    http_response_code(400);
    exit("Missing CheckoutRequestID");
}

if ($resultCode === 0) {
    $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];

    $amount = 0;
    $mpesaReceiptNumber = '';
    $phoneNumber = '';

    foreach ($callbackMetadata as $item) {
        switch ($item['Name']) {
            case 'Amount':
                $amount = $item['Value'];
                break;
            case 'MpesaReceiptNumber':
                $mpesaReceiptNumber = $item['Value'];
                break;
            case 'PhoneNumber':
                $phoneNumber = $item['Value'];
                break;
        }
    }

    // Update payment record to success
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'success', mpesa_receipt = ? WHERE checkout_request_id = ?");
    $stmt->bind_param("ss", $mpesaReceiptNumber, $checkoutRequestID);
    $stmt->execute();

    // Get booking_id related to this payment
    $stmt2 = $conn->prepare("SELECT booking_id FROM payments WHERE checkout_request_id = ?");
    $stmt2->bind_param("s", $checkoutRequestID);
    $stmt2->execute();
    $stmt2->bind_result($booking_id);
    $stmt2->fetch();
    $stmt2->close();

    if ($booking_id) {
        // Update booking status to 'paid'
        $stmt3 = $conn->prepare("UPDATE bookings SET status = 'paid' WHERE booking_id = ?");
        $stmt3->bind_param("i", $booking_id);
        $stmt3->execute();
        $stmt3->close();
    }

    $stmt->close();

    // Return success to Safaricom
    http_response_code(200);
    echo json_encode([
        "ResultCode" => 0,
        "ResultDesc" => "The service was accepted successfully"
    ]);
} else {
    // Payment failed or cancelled: update payment_status
    $stmt = $conn->prepare("UPDATE payments SET payment_status = 'failed' WHERE checkout_request_id = ?");
    $stmt->bind_param("s", $checkoutRequestID);
    $stmt->execute();
    $stmt->close();

    http_response_code(200);
    echo json_encode([
        "ResultCode" => 0,
        "ResultDesc" => "The service was accepted successfully"
    ]);
}

$conn->close();
?>