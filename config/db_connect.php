<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = ""; // Change if you have set a MySQL password
$database = "tea_gap_db";

// Create database connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add this to disable query caching if needed
$conn->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
?>
