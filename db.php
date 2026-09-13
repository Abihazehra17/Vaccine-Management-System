<?php
/**
 * Database Connection & Global Configuration
 * Vaccine Management System (VMS)
 */

// Start session safely if not already active and headers not sent (ensures smooth merge with Admin & Parent panels)
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'vms';

// Establish MySQL connection
$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Verify database connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Set character set to utf8mb4 for full unicode support
mysqli_set_charset($connection, "utf8mb4");