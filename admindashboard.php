<?php
/**
 * Admin Dashboard
 * ----------------
 * This page acts as the central control panel for administrators of Amalia Residences Limited.
 * Admins can:
 *  - Manage users and hosts
 *  - Approve/suspend/delete hosts
 *  - Manage properties, bookings, reviews, and payments
 *  - View reports
 *  - See quick stats about platform activity
 * 
 * Access is restricted to logged-in admins only.
 */

session_start();                // Start/resume a session (for login state)
include 'db.php';               // Include database connection file

// ----------------------
// DASHBOARD STAT COUNTS
// ----------------------
// We fetch counts of major entities for quick stats cards
$counts = [];
$counts['users'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users"))['c'];              // total users
$counts['hosts'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE role='host'"))['c']; // total hosts
$counts['guests'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE role='guest'"))['c']; // total guests
$counts['properties'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM properties"))['c'];    // total properties
$counts['bookings'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM bookings"))['c'];        // total bookings
$counts['reviews'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM reviews"))['c'];          // total reviews

// ----------------------
// ACCESS CONTROL
// ----------------------
// Only admins are allowed here. If no admin session or role mismatch, redirect to login.
if(!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin'){
    header("Location: adminlogin.php"); // bounce to login
    exit();
}

// ----------------------
// ACTION HANDLER (CRUD)
// ----------------------
// If an action is triggered via GET (e.g. ?action=delete_user&id=3)
if(isset($_GET['action'], $_GET['id'])){
    $id = $_GET['id']; // ID of record to act on

    // Handle different actions based on type
    switch($_GET['action']){
        case 'delete_user':
        case 'delete_host':
            mysqli_query($conn, "DELETE FROM users WHERE user_id='$id'");
            break;
        case 'approve_host':
            mysqli_query($conn, "UPDATE users SET status='Approved' WHERE user_id='$id'");
            break;
        case 'suspend_host':
            mysqli_query($conn, "UPDATE users SET status='Suspended' WHERE user_id='$id'");
            break;
        case 'delete_property':
            mysqli_query($conn, "DELETE FROM properties WHERE property_id='$id'");
            break;
        case 'confirm_booking':
            mysqli_query($conn, "UPDATE bookings SET status='Confirmed' WHERE booking_id='$id'");
            break;
        case 'cancel_booking':
            mysqli_query($conn, "UPDATE bookings SET status='Cancelled' WHERE booking_id='$id'");
            break;
        case 'approve_review':
            mysqli_query($conn, "UPDATE reviews SET status='Approved' WHERE review_id='$id'");
            break;
        case 'delete_review':
            mysqli_query($conn, "DELETE FROM reviews WHERE review_id='$id'");
            break;
    }

    // After action, refresh the same section to show updates
    header("Location: ?page=".$_GET['page']);
    exit();
}

// ----------------------
// PAGE SELECTION
// ----------------------
// Default section is "users" if ?page is not set
$page = isset($_GET['page']) ? $_GET['page'] : 'users';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        body { margin:0; font-family: 'Segoe UI', Arial, sans-serif; background:#f4f8fb; }

        /* Sidebar Styling */
        .sidebar {
            width: 220px;
            background:#2c3e50;
            color:white;
            float:left;
            height:100vh;             /* full height */
            padding-top:20px;
        }
        .sidebar h2 {
            text-align:center;
            color:#ecf0f1;
            font-size:20px;
            margin-bottom:20px;
        }
        .sidebar a {
            display:block;
            color:#ddd;
            padding:12px 20px;
            text-decoration:none;
            transition:0.3s;
        }
        .sidebar a:hover {
            background:#1abc9c;
            color:#fff;
        }

        /* Main Section */
        .main {
            margin-left:220px; /* make room for sidebar */
            padding:20px;
        }

        /* Stats Cards */
        .stats {
            display:flex;
            gap:20px;
            margin-bottom:20px;
        }
        .card {
            flex:1;
            background:#fff;
            padding:20px;
            border-radius:8px;
            box-shadow:0 2px 6px rgba(0,0,0,0.1);
            text-align:center;
        }
        .card h3 { margin:0; color:#2c3e50; }
        .card p { font-size:24px; margin:10px 0 0; color:#1abc9c; font-weight:bold; }

        /* Tables */
        table { border-collapse:collapse; width:100%; background:#fff; border-radius:6px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
        th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
        th { background:#2c3e50; color:white; }
        tr:hover { background:#f5f5f5; }

        /* Status styling */
        .status-approved{color:green;font-weight:bold;}
        .status-pending{color:orange;font-weight:bold;}
        .status-suspended{color:red;font-weight:bold;}

        /* Header */
        .header {
            background:#fff;
            padding:15px 20px;
            border-radius:8px;
            margin-bottom:20px;
            box-shadow:0 2px 6px rgba(0,0,0,0.1);
        }
        .header h1 {
            margin:0;
            color:#2c3e50;
        }
    </style>
</head>
<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <h2><i class="fa fa-user-shield"></i> Admin Panel</h2>
    <!-- Each link reloads the page with ?page=... -->
    <a href='admindashboard.php?page=users'><i class="fa fa-users"></i> Total Users</a>
    <a href='admindashboard.php?page=hosts'><i class="fa fa-user-tie"></i> Hosts</a>
    <a href='admindashboard.php?page=guests'><i class="fa fa-user-tie"></i> Guests</a>
    <a href='admindashboard.php?page=properties'><i class="fa fa-building"></i> Properties</a>
    <a href='admindashboard.php?page=bookings'><i class="fa fa-calendar-check"></i> Bookings</a>
    <a href='admindashboard.php?page=reviews'><i class="fa fa-star"></i> Reviews</a>
    <a href='admindashboard.php?page=payments'><i class="fa fa-credit-card"></i> Payments</a>
    <a href='admindashboard.php?page=reports'><i class="fa fa-chart-line"></i> Reports</a>
    <a href='adminlogout.php'><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
    <!-- Top Header -->
    <div class="header">
        <h1>Welcome back, Super Admin 👋</h1>
        <p>Here’s what’s happening on Amalia Residences right about now.</p>
    </div>

    <!-- Quick Stats Cards -->
    <div class="stats">
        <div class="card"><h3>Total Users</h3><p><?php echo $counts['users']; ?></p></div>
        <div class="card"><h3>Hosts</h3><p><?php echo $counts['hosts']; ?></p></div>
        <div class="card"><h3>Guests</h3><p><?php echo $counts['guests']; ?></p></div>
        <div class="card"><h3>Properties</h3><p><?php echo $counts['properties']; ?></p></div>
        <div class="card"><h3>Bookings</h3><p><?php echo $counts['bookings']; ?></p></div>
        <div class="card"><h3>Reviews</h3><p><?php echo $counts['reviews']; ?></p></div>
    </div>

<?php
// =========================
// SECTION CONTENT HANDLING
// =========================
// Based on ?page value, we load and display the correct table

// ----- USERS -----
if($page=='users'){
    $result = mysqli_query($conn,"SELECT * FROM users");
    echo "<h2>Users</h2><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        echo "<tr>
            <td>{$row['user_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['role']}</td>
            <td><a href='?page=users&action=delete_user&id={$row['user_id']}'>Delete</a></td>
        </tr>";
    }
    echo "</table><h2>Users (Total: {$counts['users']})</h2>";
}

// ----- HOSTS -----
elseif($page=='hosts'){
    $result=mysqli_query($conn,"SELECT * FROM users WHERE role='host'");
    echo "<h2>Hosts</h2><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        $status=$row['status'] ?? 'Pending';
        echo "<tr>
            <td>{$row['user_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td class='status-{$status}'>{$status}</td>
            <td>
                <a href='?page=hosts&action=approve_host&id={$row['user_id']}'>Approve</a>
                <a href='?page=hosts&action=suspend_host&id={$row['user_id']}'>Suspend</a>
                <a href='?page=hosts&action=delete_host&id={$row['user_id']}'>Delete</a>
            </td>
        </tr>";
    }
    echo "</table><h2>Hosts (Total: {$counts['hosts']})</h2>";
}

// ----- GUESTS -----
elseif($page=='guests'){
    $result=mysqli_query($conn,"SELECT * FROM users WHERE role='guest'");
    echo "<h2>Guests</h2><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        $status=$row['status'] ?? 'Pending';
        echo "<tr>
            <td>{$row['user_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td class='status-{$status}'>{$status}</td>
            <td>
                <a href='?page=guests&action=approve_guest&id={$row['user_id']}'>Approve</a>
                <a href='?page=guests&action=suspend_guest&id={$row['user_id']}'>Suspend</a>
                <a href='?page=guests&action=delete_guest&id={$row['user_id']}'>Delete</a>
            </td>
        </tr>";
    }
    echo "</table><h2>Guests (Total: {$counts['guests']})</h2>";
}



// ----- PROPERTIES -----
elseif($page=='properties'){
    $result=mysqli_query($conn,"
        SELECT p.*, u.name AS host_name
        FROM properties p
        LEFT JOIN users u ON p.owner_id = u.user_id
    ");
    echo "<h2>Properties</h2><table><tr><th>ID</th><th>Host</th><th>Description</th><th>Location</th><th>Price</th><th>Status</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        echo "<tr>
            <td>{$row['property_id']}</td>
            <td>{$row['host_name']}</td>
            <td>{$row['description']}</td>
            <td>{$row['location']}</td>
            <td>{$row['price']}</td>
            <td>{$row['status']}</td>
            <td><a href='?page=properties&action=delete_property&id={$row['property_id']}'>Delete</a></td>
        </tr>";
    }
    echo "</table><h2>Properties (Total: {$counts['properties']})</h2>";
}

// ----- BOOKINGS -----
elseif($page=='bookings'){
    $result=mysqli_query($conn,"
        SELECT b.*, u.name AS guest_name, p.description AS property_name, pay.payment_status
        FROM bookings b
        JOIN users u ON b.user_id=u.user_id
        JOIN properties p ON b.property_id=p.property_id
        LEFT JOIN payments pay ON b.booking_id=pay.booking_id
    ");
    echo "<h2>Bookings</h2><table><tr><th>ID</th><th>Guest</th><th>Property</th><th>Check-in</th><th>Check-out</th><th>Status</th><th>Payment</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        echo "<tr>
            <td>{$row['booking_id']}</td>
            <td>{$row['guest_name']}</td>
            <td>{$row['property_name']}</td>
            <td>{$row['check_in']}</td>
            <td>{$row['check_out']}</td>
            <td>{$row['status']}</td>
            <td>{$row['payment_status']}</td>
            <td>
                <a href='?page=bookings&action=confirm_booking&id={$row['booking_id']}'>Confirm</a>
                <a href='?page=bookings&action=cancel_booking&id={$row['booking_id']}'>Cancel</a>
            </td>
        </tr>";
    }
    echo "</table><h2>Bookings (Total: {$counts['bookings']})</h2>";
}

// ----- REVIEWS -----
elseif($page=='reviews'){
    $result=mysqli_query($conn,"
        SELECT r.*, u.name AS reviewer_name, p.description AS property_name
        FROM reviews r
        JOIN users u ON r.user_id=u.user_id
        JOIN properties p ON r.property_id=p.property_id
    ");
    echo "<h2>Reviews</h2><table><tr><th>ID</th><th>Reviewer</th><th>Property</th><th>Rating</th><th>Comment</th><th>Status</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        $status = $row['status'] ?? 'Pending';
        echo "<tr>
            <td>{$row['review_id']}</td>
            <td>{$row['reviewer_name']}</td>
            <td>{$row['property_name']}</td>
            <td>{$row['rating']}</td>
            <td>{$row['review_text']}</td>
            <td class='status-{$status}'>{$status}</td>
            <td>
                <a href='?page=reviews&action=approve_review&id={$row['review_id']}'>Approve</a>
                <a href='?page=reviews&action=delete_review&id={$row['review_id']}'>Delete</a>
            </td>
        </tr>";
    }
    echo "</table><h2>Reviews (Total: {$counts['reviews']})</h2>";
}

// ----- PAYMENTS -----
elseif($page=='payments'){
    $result = mysqli_query($conn,"
        SELECT pay.*, b.booking_id, u.name AS guest_name, p.description AS property_name
        FROM payments pay
        JOIN bookings b ON pay.booking_id = b.booking_id
        JOIN users u ON b.user_id = u.user_id
        JOIN properties p ON b.property_id = p.property_id
    ");
    echo "<h2>Payments</h2><table>
        <tr>
            <th>Payment ID</th>
            <th>Booking ID</th>
            <th>Guest</th>
            <th>Property</th>
            <th>Amount (KES)</th>
            <th>Method</th>
            <th>Mpesa Receipt</th>
            <th>Checkout Request ID</th>
            <th>Status</th>
            <th>Payment Date</th>
        </tr>";
    while($row=mysqli_fetch_assoc($result)){
        $statusClass = strtolower($row['payment_status']) == 'pending' ? 'status-pending' : 'status-approved';
        echo "<tr>
            <td>{$row['payment_id']}</td>
            <td>{$row['booking_id']}</td>
            <td>{$row['guest_name']}</td>
            <td>{$row['property_name']}</td>
            <td>" . number_format($row['amount']) . "</td>
            <td>{$row['method']}</td>
            <td>{$row['mpesa_receipt']}</td>
            <td>{$row['checkout_request_id']}</td>
            <td class='{$statusClass}'>{$row['payment_status']}</td>
            <td>{$row['payment_date']}</td>
        </tr>";
    }
    echo "</table><h2>Payments (Total: ".mysqli_num_rows($result).")</h2>";
}

// ----- REPORTS -----
elseif($page=='reports'){
    include 'reports.php'; // Load external reports page
}
?>
</div>
</body>
</html>