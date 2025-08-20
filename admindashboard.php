
<?php
// Start a session to track logged-in users
session_start();

// Include the database connection file
include 'db.php'; 
// --- DASHBOARD COUNTS ---
$counts = [];

// Users
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users");
$counts['users'] = mysqli_fetch_assoc($res)['c'];

// Hosts
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM hosts");
$counts['hosts'] = mysqli_fetch_assoc($res)['c'];

// Properties
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM properties");
$counts['properties'] = mysqli_fetch_assoc($res)['c'];

// Bookings
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM bookings");
$counts['bookings'] = mysqli_fetch_assoc($res)['c'];

// Reviews
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM reviews");
$counts['reviews'] = mysqli_fetch_assoc($res)['c'];


// --- ACCESS CONTROL ---
if(!isset($_SESSION['admin_id']) || $_SESSION['role'] != 'admin'){
    header("Location: adminlogin.php"); // Redirect to login if not admin
    exit();
}
    




// --- ACTION HANDLER ---
// Check if an action (like delete, approve) and an ID is set in the URL
if(isset($_GET['action'], $_GET['id'])){
    $id = $_GET['id']; // Get the record ID from URL
    switch($_GET['action']){
        case 'delete_user': 
            // Delete a user from the database
            mysqli_query($conn, "DELETE FROM users WHERE user_id='$id'"); 
            break;
        case 'approve_host': 
            // Approve a host account
            mysqli_query($conn, "UPDATE users SET status='Approved' WHERE user_id='$id'"); 
            break;
        case 'suspend_host': 
            // Suspend a host account
            mysqli_query($conn, "UPDATE users SET status='Suspended' WHERE user_id='$id'"); 
            break;
        case 'delete_host': 
            // Delete a host account
            mysqli_query($conn, "DELETE FROM users WHERE user_id='$id'"); 
            break;
        case 'delete_property': 
            // Delete a property listing
            mysqli_query($conn, "DELETE FROM properties WHERE property_id='$id'"); 
            break;
        case 'confirm_booking': 
            // Confirm a booking
            mysqli_query($conn, "UPDATE bookings SET status='Confirmed' WHERE booking_id='$id'"); 
            break;
        case 'cancel_booking': 
            // Cancel a booking
            mysqli_query($conn, "UPDATE bookings SET status='Cancelled' WHERE booking_id='$id'"); 
            break;
        case 'approve_review': 
            // Approve a review
            mysqli_query($conn, "UPDATE reviews SET status='Approved' WHERE review_id='$id'"); 
            break;
        case 'delete_review': 
            // Delete a review
            mysqli_query($conn, "DELETE FROM reviews WHERE review_id='$id'"); 
            break;
    }
    // After performing the action, redirect back to the current page
    header("Location: ?page=".$_GET['page']); 
    exit();
}

// --- PAGE SELECTION ---
// Determine which section of the dashboard to show (users, hosts, properties, bookings, reviews)
$page = isset($_GET['page']) ? $_GET['page'] : 'users';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <!-- Load font-awesome for icons if needed later -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        /* Basic CSS styling for dashboard */
        body { font-family: Arial; margin:0; } /* Set font and remove default margin */
        .sidebar { 
            width: 200px; 
            background:#333; 
            color:white; 
            float:left; 
            height:100vh; 
            padding-top:20px; 
        }
        .sidebar a { 
            display:block; 
            color:white; 
            padding:10px; 
            text-decoration:none; 
        }
        .sidebar a:hover { 
            background:#444; 
        }
        .main { 
            margin-left:200px; /* Make space for sidebar */
            padding:20px; 
        }
        table { 
            border-collapse:collapse; 
            width:100%; 
        }
        table, th, td { 
            border:1px solid #ccc; 
        }
        th, td { 
            padding:8px; 
            text-align:left; 
        }
        .action-btn { 
            margin-right:5px; /* Space between action links */
        }
        .status-approved{color:green;font-weight:bold;}
        .status-pending{color:orange;font-weight:bold;}
        .status-suspended{color:red;font-weight:bold;}
    </style>
</head>
<body>

<!-- --- SIDEBAR NAVIGATION --- -->
<div class="sidebar">
    <h2 style="text-align:center;">Admin</h2> <!-- Sidebar title -->
    <a href='admindashboard.php?page=users'>Users</a> <!-- Link to users section -->
    <a href='admindashboard.php?page=hosts'>Hosts</a> <!-- Link to hosts section -->
    <a href='admindashboard.php?page=properties'>Properties</a> <!-- Link to properties section -->
    <a href='admindashboard.php?page=bookings'>Bookings</a> <!-- Link to bookings section -->
    <a href='admindashboard.php?page=reviews'>Reviews</a> <!-- Link to reviews section -->
    <a href='adminlogout.php'>Logout</a> <!-- Logout link -->
</div>

<!-- --- MAIN CONTENT --- -->
<div class="main">

<?php
// --- USERS SECTION ---
if($page=='users'){
    // Fetch all users from the database
    $result = mysqli_query($conn,"SELECT * FROM users"); 
    echo "<h2>Users</h2><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>";
    // Loop through users and display each row
    while($row=mysqli_fetch_assoc($result)){
        echo "<tr>
            <td>{$row['user_id']}</td> <!-- User ID -->
            <td>{$row['name']}</td> <!-- User name -->
            <td>{$row['email']}</td> <!-- User email -->
            <td>{$row['role']}</td> <!-- User role -->
            <td>
                <!-- Delete user link -->
                <a class='action-btn' href='?page=users&action=delete_user&id={$row['user_id']}'>Delete</a>
            </td>
        </tr>";
    }
    echo "<h2> Users (Total : {$counts['users']})</h2>";
    echo "</table>";
}

// --- HOSTS SECTION ---
elseif($page=='hosts'){
    // Fetch all hosts
    $result=mysqli_query($conn,"SELECT * FROM hosts");
    echo "<h2>Hosts</h2><table><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        $status=$row['status'] ?? 'Pending'; // Default status if not set
        echo "<tr>
            <td>{$row['host_id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td class='status-{$status}'>{$status}</td> <!-- Display status with color -->
            <td>
                <!-- Approve, Suspend, Delete links -->
                <a href='?page=hosts&action=approve_host&id={$row['host_id']}'>Approve</a>
                <a href='?page=hosts&action=suspend_host&id={$row['host_id']}'>Suspend</a>
                <a href='?page=hosts&action=delete_host&id={$row['host_id']}'>Delete</a>
            </td>
        </tr>";
    }
     echo "<h2> Hosts (Total : {$counts['hosts']})</h2>";
    echo "</table>";
}

// --- PROPERTIES SECTION ---
elseif($page=='properties'){
    // Fetch all properties with host name
    $result=mysqli_query($conn,"SELECT p.*, h.name AS host_name FROM properties p LEFT JOIN hosts h ON p.owner_id=h.host_id");
    echo "<h2>Properties</h2><table><tr><th>ID</th><th>Host</th><th>Description</th><th>Location</th><th>Price</th><th>Status</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        echo "<tr>
            <td>{$row['property_id']}</td> <!-- Property ID -->
            <td>{$row['host_name']}</td> <!-- Host name -->
            <td>{$row['description']}</td> <!-- Property description -->
            <td>{$row['location']}</td> <!-- Property location -->
            <td>{$row['price']}</td> <!-- Property price -->
            <td>{$row['status']}</td> <!-- Property status -->
            <td>
                <!-- Delete property link -->
                <a href='?page=properties&action=delete_property&id={$row['property_id']}'>Delete</a>
            </td>
        </tr>";
    }
     echo "<h2> Properties (Total : {$counts['properties']})</h2>";
    echo "</table>";
}

// --- BOOKINGS SECTION ---
elseif($page=='bookings'){
    // Fetch bookings with guest name and property description
    $result=mysqli_query($conn,"SELECT b.*, u.name AS guest_name, p.description AS property_name,pay.payment_status AS payment_status,pay.amount FROM bookings b JOIN users u ON b.user_id=u.user_id JOIN properties p ON b.property_id=p.property_id LEFT JOIN payments pay ON b.booking_id=pay.booking_id");
    echo "<h2>Bookings</h2><table><tr><th>ID</th><th>Guest</th><th>Property</th><th>Check-in</th><th>Check-out</th><th>Status</th><th>Payment</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        echo "<tr>
            <td>{$row['booking_id']}</td> <!-- Booking ID -->
            <td>{$row['guest_name']}</td> <!-- Guest name -->
            <td>{$row['property_name']}</td> <!-- Property name -->
            <td>{$row['check_in']}</td> <!-- Check-in date -->
            <td>{$row['check_out']}</td> <!-- Check-out date -->
            <td>{$row['status']}</td> <!-- Booking status -->
            <td>{$row['payment_status']}</td> <!-- Payment status -->
            <td>
                <!-- Confirm or Cancel booking links -->
                <a href='?page=bookings&action=confirm_booking&id={$row['booking_id']}'>Confirm</a>
                <a href='?page=bookings&action=cancel_booking&id={$row['booking_id']}'>Cancel</a>
            </td>
        </tr>";
    }
     echo "<h2> Bookings (Total : {$counts['bookings']})</h2>";
    echo "</table>";
}

// --- REVIEWS SECTION ---
elseif($page=='reviews'){
    // Fetch reviews with reviewer name and property
    $result=mysqli_query($conn,"SELECT r.*, u.name AS reviewer_name, p.description AS property_name FROM reviews r JOIN users u ON r.user_id=u.user_id JOIN properties p ON r.property_id=p.property_id");
    echo "<h2>Reviews</h2><table><tr><th>ID</th><th>Reviewer</th><th>Property</th><th>Rating</th><th>Comment</th><th>Status</th><th>Actions</th></tr>";
    while($row=mysqli_fetch_assoc($result)){
        $status = $row['status'] ?? 'Pending'; // Default to Pending if no status
        echo "<tr>
            <td>{$row['review_id']}</td> <!-- Review ID -->
            <td>{$row['reviewer_name']}</td> <!-- Reviewer name -->
            <td>{$row['property_name']}</td> <!-- Property name -->
            <td>{$row['rating']}</td> <!-- Rating -->
            <td>{$row['review_text']}</td> <!-- Review comment -->
            <td class='status-{$status}'>{$status}</td> <!-- Status with color -->
            <td>
                <!-- Approve or Delete review -->
                <a href='?page=reviews&action=approve_review&id={$row['review_id']}'>Approve</a>
                <a href='?page=reviews&action=delete_review&id={$row['review_id']}'>Delete</a>
            </td>
        </tr>";
    }
     echo "<h2> Reviews (Total : {$counts['reviews']})</h2>";
    echo "</table>";
}
?>
</div>
</body>
</html>