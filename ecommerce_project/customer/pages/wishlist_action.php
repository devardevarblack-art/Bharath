<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isCustomerLoggedIn()) {
    echo json_encode(['status' => 'login_required']);
    exit();
}

$cid        = getCustomerId();
$action     = $_POST['action'] ?? '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($action === 'toggle') {
    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM wishlist WHERE customer_id=$cid AND product_id=$product_id LIMIT 1"));

    if ($existing) {
        mysqli_query($conn, "DELETE FROM wishlist WHERE id={$existing['id']}");
        $added = false;
    } else {
        mysqli_query($conn, "INSERT INTO wishlist (customer_id, product_id) VALUES ($cid, $product_id)");
        $added = true;
    }

    echo json_encode(['status' => 'ok', 'added' => $added, 'wishlist_count' => getWishlistCount($conn)]);
    exit();
}

if ($action === 'remove') {
    mysqli_query($conn, "DELETE FROM wishlist WHERE customer_id=$cid AND product_id=$product_id");
    echo json_encode(['status' => 'ok', 'wishlist_count' => getWishlistCount($conn)]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
