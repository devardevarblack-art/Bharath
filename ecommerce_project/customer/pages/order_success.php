<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'Order Confirmed';
$cid = getCustomerId();
$id  = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id=$id AND customer_id=$cid LIMIT 1"));

if (!$order) {
    header("Location: home.php");
    exit();
}

require_once '../includes/header.php';
?>

<div class="empty-state">
    <i class="fas fa-check-circle" style="color:#10b981;"></i>
    <h4 class="fw-bold text-dark">Thank you! Your order has been placed.</h4>
    <p class="mb-1">Order #<?php echo $order['id']; ?> · Total: <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></p>
    <p class="mb-4">Payment Method: <strong><?php echo strtoupper($order['payment_method']); ?></strong></p>
    <div class="d-flex justify-content-center gap-2">
        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-store">View Order Details</a>
        <a href="home.php" class="btn-store-outline">Continue Shopping</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
