<?php
/**
 * Session Management
 * Organ Donate System
 */

session_start();

// Check if session exists and is valid
if (isset($_SESSION['user_id'])) {
    // Update session timestamp
    $_SESSION['last_activity'] = time();
} else if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT * 60)) {
    // Session expired
    session_destroy();
    header('Location: ' . BASE_URL . 'index.php?session_expired=true');
    exit();
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
}

// Function to redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . 'index.php?login_required=true');
        exit();
    }
}

// Function to check role
function check_role($required_role) {
    if (!is_logged_in()) {
        return false;
    }
    return $_SESSION['user_role'] === $required_role;
}

?>