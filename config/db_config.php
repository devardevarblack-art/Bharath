<?php
/**
 * Database Configuration File
 * Organ Donate System - Database Connection
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'organ_donate_db');
define('DB_PORT', 3306);

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Database error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>