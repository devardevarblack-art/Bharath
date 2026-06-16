<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'Order Details';
$cid = getCustomerId();
$id  = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id=$id AND customer_id=$cid LIMIT 1"));

if (!$order) {
    header("Location: orders.php");
    exit();
}

$items = mysqli_query($conn, "
    SELECT oi.*, p.name, p.image, v.business_name
    FROM order_items oi
    LEFT JOIN products p ON p.id = oi.product_id
    LEFT JOIN vendors v ON v.id = p.vendor_id
    WHERE oi.order_id = $id
");

$statusSteps = ['pending', 'processing', 'shipped', 'delivered'];
$currentStep = array_search($order['status'], $statusSteps);

require_once '../includes/header.php';
?>

<a href="orders.php" class="text-decoration-none small text-muted mb-3 d-inline-block"><i class="fas fa-arrow-left me-1"></i> Back to Orders</a>

<div class="order-card mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1">Order #<?php echo $order['id']; ?></h5>
            <p class="text-muted small mb-0">Placed on <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
        </div>
        <span class="status-badge badge-<?php echo $order['status']; ?> fs-6"><?php echo ucfirst($order['status']); ?></span>
    </div>

    <?php if ($order['status'] !== 'cancelled'): ?>
    <div class="d-flex justify-content-between mb-4 px-2">
        <?php foreach ($statusSteps as $i => $step): ?>
            <div class="text-center flex-fill">
                <div style="width:30px;height:30px;border-radius:50%;margin:0 auto;display:flex;align-items:center;justify-content:center;
                    background:<?php echo $i <= $currentStep ? '#2563eb' : '#e2e8f0'; ?>; color:#fff;">
                    <i class="fas fa-check"></i>
                </div>
                <div class="small mt-1 <?php echo $i <= $currentStep ? 'fw-semibold text-dark' : 'text-muted'; ?>"><?php echo ucfirst($step); ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div class="alert alert-danger small mb-4">This order has been cancelled.</div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <h6 class="fw-bold small text-uppercase text-muted">Shipping Address</h6>
            <p class="small"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold small text-uppercase text-muted">Payment</h6>
            <p class="small mb-1">Method: <strong><?php echo strtoupper($order['payment_method']); ?></strong></p>
            <p class="small">Status: <span class="status-badge badge-<?php echo $order['payment_status']; ?>"><?php echo ucfirst($order['payment_status']); ?></span></p>
        </div>
        <?php if (!empty($order['notes'])): ?>
        <div class="col-12">
            <h6 class="fw-bold small text-uppercase text-muted">Order Notes</h6>
            <p class="small"><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="order-card">
    <h6 class="fw-bold mb-3">Items</h6>
    <?php while ($it = mysqli_fetch_assoc($items)): ?>
        <div class="cart-item">
            <?php if ($it['image'] && file_exists('../../vendor/uploads/products/' . $it['image'])): ?>
                <img src="../../vendor/uploads/products/<?php echo htmlspecialchars($it['image']); ?>" class="cart-item-img" alt="<?php echo htmlspecialchars($it['name'] ?? ''); ?>">
            <?php else: ?>
                <div class="cart-item-img d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div>
            <?php endif; ?>
            <div class="cart-item-info">
                <div class="fw-semibold"><?php echo htmlspecialchars($it['name'] ?? 'Product unavailable'); ?></div>
                <div class="product-vendor"><?php echo htmlspecialchars($it['business_name'] ?? 'MultiVendor'); ?></div>
                <div class="text-muted small">Qty: <?php echo $it['quantity']; ?> × ₹<?php echo number_format($it['price'], 2); ?></div>
            </div>
            <div class="fw-bold">₹<?php echo number_format($it['price'] * $it['quantity'], 2); ?></div>
        </div>
    <?php endwhile; ?>

    <div class="summary-row total mt-3">
        <span>Total</span>
        <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
