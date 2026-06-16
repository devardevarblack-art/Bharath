<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'multivendor_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();

function isCustomerLoggedIn() {
    return isset($_SESSION['customer_id']);
}

function redirectIfNotLoggedIn() {
    if (!isCustomerLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: ../index.php");
        exit();
    }
}

function getCustomerId() {
    return $_SESSION['customer_id'] ?? null;
}

// Returns total quantity of items in the logged-in customer's cart
function getCartCount($conn) {
    if (!isCustomerLoggedIn()) return 0;
    $cid = getCustomerId();
    $res = mysqli_query($conn, "SELECT COALESCE(SUM(quantity),0) AS cnt FROM cart WHERE customer_id=$cid");
    $row = mysqli_fetch_assoc($res);
    return (int)$row['cnt'];
}

// Returns number of items in the logged-in customer's wishlist
function getWishlistCount($conn) {
    if (!isCustomerLoggedIn()) return 0;
    $cid = getCustomerId();
    $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM wishlist WHERE customer_id=$cid");
    $row = mysqli_fetch_assoc($res);
    return (int)$row['cnt'];
}
?>
