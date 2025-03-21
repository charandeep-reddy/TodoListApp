<?php
/**
 * Database Configuration
 * This file contains database connection parameters
 */

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // Default XAMPP password. For MAMP it's often 'root'
define('DB_NAME', 'todo_list_db');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false) {
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}

// Set character set
mysqli_set_charset($conn, "utf8mb4");

// Session start
session_start();
?>
