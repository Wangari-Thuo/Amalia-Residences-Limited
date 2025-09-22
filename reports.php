<?php
// =========================================================
// reports.php
// Description: Admin dashboard showing 7 key system reports
// (Users, Payments, Bookings, Revenue per Property, Pending Payments,
// Completed Bookings, Most Popular Properties)
// with collapsible tabs for easy navigation
// =========================================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start PHP session to track admin login
}

// Check if logged-in user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // If not admin, redirect to login page
    header("Location: login.php");
    exit();
}

// Include database connection file
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Reports - Amalia Residences</title>
<style>
    /* ========================================================= */
    /* PAGE STYLING */
    /* ========================================================= */
    body { 
        font-family: Arial, sans-serif; /* Set font */
        background: #f4f6f9; /* Light gray background */
        margin: 0; padding: 0;
    }
    h2 { 
        text-align: center; 
        color: #333; 
        padding: 20px; 
    }

    /* Tabs navigation container */
    nav { 
        display: flex; 
        justify-content: center; 
        gap: 10px; 
        background: #007bff; 
        padding: 10px; 
        flex-wrap: wrap; 
    }

    /* Style for each tab button */
    nav button { 
        padding: 10px 15px; 
        cursor: pointer; 
        border: none; 
        border-radius: 5px; 
        font-weight: bold; 
        color: #fff; 
        background: #007bff; 
        transition: 0.3s; 
    }

    /* Active tab style */
    nav button.active { 
        background: #ffdd57; 
        color: #000; 
    }

    /* Hover effect for tab buttons */
    nav button:hover { 
        opacity: 0.8; 
    }

    /* ========================================================= */
    /* TAB CONTENT STYLING */
    /* ========================================================= */
    .tab-content { 
        display: none; /* Hidden by default */
        padding: 20px; 
        background: #fff; 
        margin: 20px auto; 
        width: 90%; 
        border-radius: 12px; 
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
    }

    /* Show active tab */
    .tab-content.active { 
        display: block; 
    }

    /* Table styling */
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 10px; 
    }

    th { 
        background: #2c3e50; 
        color: #fff; 
        padding: 10px; 
        text-align: left; 
    }

    td { 
        padding: 8px; 
        border-bottom: 1px solid #ddd; 
    }

    tr:nth-child(even) { 
        background: #f9f9f9; 
    }

    tr:hover { 
        background: #f1f1f1; 
    }
</style>
</head>
<body>

<h2>📊 Admin Dashboard Reports</h2>

<!-- ========================================================= -->
<!-- TABS NAVIGATION -->
<!-- Each button corresponds to a report -->
<!-- ========================================================= -->
<nav>
    <button class="tab-button active" data-tab="users-tab">Users</button>
    <button class="tab-button" data-tab="payments-tab">Payments</button>
    <button class="tab-button" data-tab="bookings-tab">Bookings</button>
    <button class="tab-button" data-tab="revenue-tab">Revenue per Property</button>
    <button class="tab-button" data-tab="pending-tab">Pending Payments</button>
    <button class="tab-button" data-tab="completed-tab">Completed Bookings</button>
    <button class="tab-button" data-tab="popular-tab">Popular Properties</button>
</nav>

<!-- ========================================================= -->
<!-- 1. Users Report -->
<!-- Displays all users in the system -->
<!-- ========================================================= -->
<div id="users-tab" class="tab-content active">
    <h3>1️⃣ Users</h3>
    <table>
        <tr>
            <th>ID</th> <!-- User ID -->
            <th>Name</th> <!-- User name -->
            <th>Email</th> <!-- User email -->
            <th>Role</th> <!-- User role (admin/user) -->
        </tr>
        <?php
        // Query to fetch all users
        $users = mysqli_query($conn, "SELECT * FROM users");
        // Loop through each user record
        while ($row = mysqli_fetch_assoc($users)) {
            echo "<tr>
                    <td>{$row['user_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['role']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- ========================================================= -->
<!-- 2. Payments Report -->
<!-- Displays all payments with related user and property -->
<!-- ========================================================= -->
<div id="payments-tab" class="tab-content">
    <h3>2️⃣ Payments</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Property</th>
            <th>Amount (KES)</th>
            <th>Date</th>
        </tr>
        <?php
        // Query to fetch payments joined with bookings, users, and properties
        $payments = mysqli_query($conn, "
            SELECT pay.payment_id, u.name, p.description AS property, pay.amount, pay.payment_date
            FROM payments pay
            JOIN bookings b ON pay.booking_id = b.booking_id
            JOIN users u ON b.user_id = u.user_id
            JOIN properties p ON b.property_id = p.property_id
        ");
        // Loop through payment records
        while ($row = mysqli_fetch_assoc($payments)) {
            echo "<tr>
                    <td>{$row['payment_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['property']}</td>
                    <td>KES {$row['amount']}</td>
                    <td>{$row['payment_date']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- ========================================================= -->
<!-- 3. Bookings Report -->
<!-- Displays all bookings with user and property info -->
<!-- ========================================================= -->
<div id="bookings-tab" class="tab-content">
    <h3>3️⃣ Bookings</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>User</th>
            <th>Property</th>
            <th>Check In</th>
            <th>Check Out</th>
            <th>Guests</th>
            <th>Status</th>
        </tr>
        <?php
        // Query to fetch bookings joined with users and properties
        $bookings = mysqli_query($conn, "
            SELECT b.booking_id, u.name AS user_name, p.description AS property, b.check_in, b.check_out, b.num_guests, b.status
            FROM bookings b
            JOIN users u ON b.user_id = u.user_id
            JOIN properties p ON b.property_id = p.property_id
        ");
        // Loop through bookings
        while ($row = mysqli_fetch_assoc($bookings)) {
            echo "<tr>
                    <td>{$row['booking_id']}</td>
                    <td>{$row['user_name']}</td>
                    <td>{$row['property']}</td>
                    <td>{$row['check_in']}</td>
                    <td>{$row['check_out']}</td>
                    <td>{$row['num_guests']}</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- ========================================================= -->
<!-- 4. Revenue per Property -->
<!-- Shows total revenue for each property -->
<!-- ========================================================= -->
<div id="revenue-tab" class="tab-content">
    <h3>4️⃣ Revenue per Property</h3>
    <table>
        <tr>
            <th>Property</th>
            <th>Total Revenue (KES)</th>
        </tr>
        <?php
        // Query to calculate total revenue per property
        $revenue = mysqli_query($conn, "
            SELECT p.description AS property, SUM(pay.amount) AS total_revenue
            FROM payments pay
            JOIN bookings b ON pay.booking_id = b.booking_id
            JOIN properties p ON b.property_id = p.property_id
            GROUP BY b.property_id
        ");
        while ($row = mysqli_fetch_assoc($revenue)) {
            echo "<tr>
                    <td>{$row['property']}</td>
                    <td>KES {$row['total_revenue']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- ========================================================= -->
<!-- 5. Pending Payments -->
<!-- Shows bookings that are still pending payment -->
<!-- ========================================================= -->
<div id="pending-tab" class="tab-content">
    <h3>5️⃣ Pending Payments</h3>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>User</th>
            <th>Property</th>
            <th>Total Price</th>
            <th>Status</th>
        </tr>
        <?php
        // Fetch pending bookings
        $pending = mysqli_query($conn, "
            SELECT b.booking_id, u.name AS user_name, p.description AS property, b.totalprice, b.status
            FROM bookings b
            JOIN users u ON b.user_id = u.user_id
            JOIN properties p ON b.property_id = p.property_id
            WHERE b.status = 'pending'
        ");
        while ($row = mysqli_fetch_assoc($pending)) {
            echo "<tr>
                    <td>{$row['booking_id']}</td>
                    <td>{$row['user_name']}</td>
                    <td>{$row['property']}</td>
                    <td>KES {$row['totalprice']}</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- ========================================================= -->
<!-- 6. Completed Bookings -->
<!-- Shows bookings that are completed/paid -->
<!-- ========================================================= -->
<div id="completed-tab" class="tab-content">
    <h3>6️⃣ Completed Bookings</h3>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>User</th>
            <th>Property</th>
            <th>Total Price</th>
            <th>Status</th>
        </tr>
        <?php
        // Fetch completed bookings
        $completed = mysqli_query($conn, "
            SELECT b.booking_id, u.name AS user_name, p.description AS property, b.totalprice, b.status
            FROM bookings b
            JOIN users u ON b.user_id = u.user_id
            JOIN properties p ON b.property_id = p.property_id
            WHERE b.status = 'paid'
        ");
        while ($row = mysqli_fetch_assoc($completed)) {
            echo "<tr>
                    <td>{$row['booking_id']}</td>
                    <td>{$row['user_name']}</td>
                    <td>{$row['property']}</td>
                    <td>KES {$row['totalprice']}</td>
                    <td>{$row['status']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- ========================================================= -->
<!-- 7. Most Popular Properties -->
<!-- Shows top 10 properties with most bookings -->
<!-- ========================================================= -->
<div id="popular-tab" class="tab-content">
    <h3>7️⃣ Most Popular Properties</h3>
    <table>
        <tr>
            <th>Property</th>
            <th>Number of Bookings</th>
        </tr>
        <?php
        // Fetch most booked properties
        $popular = mysqli_query($conn, "
            SELECT p.description AS property, COUNT(b.booking_id) AS num_bookings
            FROM bookings b
            JOIN properties p ON b.property_id = p.property_id
            GROUP BY b.property_id
            ORDER BY num_bookings DESC
            LIMIT 10
        ");
        while ($row = mysqli_fetch_assoc($popular)) {
            echo "<tr>
                    <td>{$row['property']}</td>
                    <td>{$row['num_bookings']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- ========================================================= -->
<!-- JAVASCRIPT FOR TAB FUNCTIONALITY -->
<!-- Switch between tabs when clicked -->
<!-- ========================================================= -->
<script>
const buttons = document.querySelectorAll('.tab-button'); // Select all tab buttons
const contents = document.querySelectorAll('.tab-content'); // Select all tab contents

// Add click event to each button
buttons.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        buttons.forEach(b => b.classList.remove('active'));
        // Hide all tab contents
        contents.forEach(c => c.classList.remove('active'));
        // Add active class to clicked button
        btn.classList.add('active');
        // Show corresponding tab content
        document.getElementById(btn.getAttribute('data-tab')).classList.add('active');
    });
});
</script>

</body>
</html>