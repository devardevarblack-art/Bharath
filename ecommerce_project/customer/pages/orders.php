<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'My Orders';
$cid = getCustomerId();

$orders = mysqli_query($conn, "
    SELECT o.*, (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
    FROM orders o
    WHERE o.customer_id = $cid
    ORDER BY o.created_at DESC
");

require_once '../includes/header.php';
?>

<h4 class="page-title mb-4"><i class="fas fa-box me-2"></i>My Orders</h4>

<?php if (mysqli_num_rows($orders) === 0): ?>
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <p class="mb-0">You haven't placed any orders yet.</p>
        <a href="home.php" class="btn-store mt-3 d-inline-block">Start Shopping</a>
    </div>
<?php else: ?>

<?php while ($o = mysqli_fetch_assoc($orders)): ?>
    <div class="order-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-1">Order #<?php echo $o['id']; ?></h6>
                <p class="text-muted small mb-1"><?php echo date('d M Y, h:i A', strtotime($o['created_at'])); ?> · <?php echo $o['item_count']; ?> item(s)</p>
                <p class="text-muted small mb-0">Payment: <?php echo strtoupper($o['payment_method']); ?> · <span class="status-badge badge-<?php echo $o['payment_status']; ?>"><?php echo ucfirst($o['payment_status']); ?></span></p>
            </div>
            <div class="text-end">
                <span class="status-badge badge-<?php echo $o['status']; ?> mb-2 d-inline-block"><?php echo ucfirst($o['status']); ?></span>
                <div class="fw-bold fs-5">₹<?php echo number_format($o['total_amount'], 2); ?></div>
                <a href="order_detail.php?id=<?php echo $o['id']; ?>" class="btn-store-outline mt-2 d-inline-block">View Details</a>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
