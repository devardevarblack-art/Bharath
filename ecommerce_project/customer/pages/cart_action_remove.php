<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$cid     = getCustomerId();
$cart_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($cart_id > 0) {
    mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND customer_id=$cid");
}

header("Location: cart.php");
exit();
