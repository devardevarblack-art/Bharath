<?php
session_start();

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Redirect to login if not authenticated
function require_login() {
    if (!is_logged_in()) {
        header("Location: " . SITE_URL . "login.php");
        exit();
    }
}

// Check user role
function check_role($required_role) {
    if ($_SESSION['user_type'] !== $required_role) {
        header("Location: " . SITE_URL . "index.php");
        exit();
    }
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Hash password
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Generate success message
function set_success($message) {
    $_SESSION['success'] = $message;
}

// Generate error message
function set_error($message) {
    $_SESSION['error'] = $message;
}

// Display messages
function display_message() {
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                ' . $_SESSION['success'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                ' . $_SESSION['error'] . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['error']);
    }
}

// Logout user
function logout() {
    session_destroy();
    header("Location: " . SITE_URL . "index.php");
    exit();
}
?>
