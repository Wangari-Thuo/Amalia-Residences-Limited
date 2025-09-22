<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amaliaresidences_db";
//$port=3307;

// Create connection
$conn = new mysqli("localhost" ,"root" ,"" ,"amaliaresidences_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>