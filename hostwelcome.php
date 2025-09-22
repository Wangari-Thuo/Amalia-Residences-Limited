<?php
/**
 * hostwelcome.php
 * ----------------
 * Host welcome widget for the Host Dashboard.
 * - Greets the logged-in host by name (from session)
 * - Displays quick stats:
 *     * number of properties owned
 *     * number of bookings for those properties
 *     * number of pending payments for those properties
 *
 * Notes:
 * - Uses prepared statements and basic error checks so mysqli_fetch_assoc() won't
 *   receive a boolean if a query fails.
 */

session_start();                 // resume session to access login info
include 'db.php';                // include DB connection (must set $conn)

// -------------------------
// Authentication / safety
// -------------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'host') {
    // not logged in as host — send them to login
    header("Location: login.php");
    exit();
}

// host id from session
$host_id = (int) $_SESSION['user_id'];

// host display name: prefer 'username' but fall back to 'name' if present
$host_name = '';
if (!empty($_SESSION['username'])) {
    $host_name = $_SESSION['username'];
} elseif (!empty($_SESSION['name'])) {
    $host_name = $_SESSION['name'];
} else {
    $host_name = 'Host';
}

// -------------------------
// Helpers: safe query -> count
// -------------------------
/**
 * Run a prepared COUNT query that takes a single integer parameter (owner/host id).
 * Returns integer count (0 on error).
 */
function prepared_count($conn, $sql, $param) {
    // prepare statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // for debugging on local/dev: you can uncomment the next line
        // error_log("Prepare failed: " . $conn->error);
        return 0;
    }
    $stmt->bind_param("i", $param); // bind the single integer param
    if (!$stmt->execute()) {
        // error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        return 0;
    }
    $res = $stmt->get_result();
    if (!$res) {
        // get_result can be false if driver doesn't support it; handle gracefully
        $stmt->close();
        return 0;
    }
    $row = $res->fetch_assoc();
    $stmt->close();
    return isset($row[array_key_first($row)]) ? (int) $row[array_key_first($row)] : 0;
}

// -------------------------
// Fetch counts (using safe helpers)
// -------------------------

// 1) total properties owned by host
$sql_props = "SELECT COUNT(*) AS total_properties FROM properties WHERE owner_id = ?";
$totalProperties = prepared_count($conn, $sql_props, $host_id);

// 2) total bookings for host's properties
$sql_bookings = "
    SELECT COUNT(*) AS total_bookings
    FROM bookings b
    JOIN properties p ON b.property_id = p.property_id
    WHERE p.owner_id = ?
";
$totalBookings = prepared_count($conn, $sql_bookings, $host_id);

// 3) pending payments for host's properties
// Note: We join payments -> bookings -> properties to be robust to schema differences
// and we filter by pay.payment_status (this matches your admin code naming).
$sql_pending_payments = "
    SELECT COUNT(*) AS pending_payments
    FROM payments pay
    JOIN bookings b ON pay.booking_id = b.booking_id
    JOIN properties p ON b.property_id = p.property_id
    WHERE p.owner_id = ? 
      AND pay.payment_status = 'pending'
";
$pendingPayments = prepared_count($conn, $sql_pending_payments, $host_id);

// (Optional) If you prefer the older simpler query that relies on payments having property_id:
// $sql_pay_simple = "SELECT COUNT(*) AS pending_payments FROM payments pay JOIN properties p ON pay.property_id = p.property_id WHERE p.owner_id = ? AND pay.payment_status = 'pending'";
// $pendingPayments = prepared_count($conn, $sql_pay_simple, $host_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Host Dashboard - Welcome</title>
    <style>
        /* minimal styles for the cards */
        body { font-family: Arial, sans-serif; background: #f9f9f9; margin:0; }
        .container { padding: 20px; }
        h2 { color: #333; margin: 0 0 6px 0; }
        p.lead { color:#555; margin-top:8px; }
        .stats { display:flex; gap:20px; margin-top:20px; }
        .card { flex:1; background:#fff; padding:20px; border-radius:12px; box-shadow:0 3px 6px rgba(0,0,0,0.08); text-align:center; }
        .card h3 { margin:0; font-size:16px; color:#666; }
        .card p { font-size:26px; margin:10px 0 0; color:#2c3e50; font-weight:700; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Print host name safely -->
        <h2>Welcome, <?php echo htmlspecialchars($host_name, ENT_QUOTES, 'UTF-8'); ?> 👋</h2>
        <p class="lead">Here’s a quick overview of your hosting activity:</p>

        <!-- Stats Cards -->
        <div class="stats" role="region" aria-label="Host quick statistics">
            <div class="card" title="Number of properties you listed">
                <h3>Your Properties</h3>
                <p><?php echo (int)$totalProperties; ?></p>
            </div>

            <div class="card" title="Total bookings made for your properties">
                <h3>Total Bookings</h3>
                <p><?php echo (int)$totalBookings; ?></p>
            </div>

            <div class="card" title="Payments pending for your properties">
                <h3>Pending Payments</h3>
                <p><?php echo (int)$pendingPayments; ?></p>
            </div>
        </div>
    </div>
</body>
</html>