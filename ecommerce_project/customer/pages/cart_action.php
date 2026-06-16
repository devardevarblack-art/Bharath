<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isCustomerLoggedIn()) {
    echo json_encode(['status' => 'login_required']);
    exit();
}

$cid    = getCustomerId();
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $qty        = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, stock, status FROM products WHERE id=$product_id LIMIT 1"));
    if (!$product || $product['status'] !== 'active') {
        echo json_encode(['status' => 'error', 'message' => 'Product not available.']);
        exit();
    }
    if ($product['stock'] <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Product is out of stock.']);
        exit();
    }

    $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cart WHERE customer_id=$cid AND product_id=$product_id LIMIT 1"));
    if ($existing) {
        $newQty = min($existing['quantity'] + $qty, $product['stock']);
        mysqli_query($conn, "UPDATE cart SET quantity=$newQty WHERE id={$existing['id']}");
    } else {
        $qty = min($qty, $product['stock']);
        mysqli_query($conn, "INSERT INTO cart (customer_id, product_id, quantity) VALUES ($cid, $product_id, $qty)");
    }

    echo json_encode(['status' => 'ok', 'cart_count' => getCartCount($conn)]);
    exit();
}

if ($action === 'update') {
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    $qty     = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

    $item = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT c.*, p.stock FROM cart c
        JOIN products p ON p.id = c.product_id
        WHERE c.id=$cart_id AND c.customer_id=$cid LIMIT 1
    "));

    if (!$item) {
        echo json_encode(['status' => 'error', 'message' => 'Item not found.']);
        exit();
    }

    if ($qty <= 0) {
        mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND customer_id=$cid");
    } else {
        $qty = min($qty, $item['stock']);
        mysqli_query($conn, "UPDATE cart SET quantity=$qty WHERE id=$cart_id AND customer_id=$cid");
    }

    echo json_encode(['status' => 'ok', 'cart_count' => getCartCount($conn)]);
    exit();
}

if ($action === 'remove') {
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND customer_id=$cid");
    echo json_encode(['status' => 'ok', 'cart_count' => getCartCount($conn)]);
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
