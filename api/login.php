<?php
// Backend API Handler
// This file connects frontend to backend

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

// Get the requested API endpoint
$request = $_GET['request'] ?? $_POST['request'] ?? '';
$user_type = $_POST['user_type'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Simulate authentication (Replace with actual database logic)
if($user_type && $email && $password) {
    session_start();
    $_SESSION['user_type'] = $user_type;
    $_SESSION['user_email'] = $email;
    
    // Redirect to dashboard based on user type
    header("Location: dashboard.php?type=" . $user_type);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
