<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'Orders';
$vendor_id = getVendorId();

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $oid    = (int)$_POST['order_id'];
    $nstatus = mysqli_real_escape_string($conn, $_POST['new_status']);
    $allowed = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($nstatus, $allowed)) {
        // Only update if this order has vendor's products
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$oid AND p.vendor_id=$vendor_id"));
        if ($check['c'] > 0) {
            mysqli_query($conn, "UPDATE orders SET status='$nstatus' WHERE id=$oid");
        }
    }
    header("Location: orders.php");
    exit();
}

// Filter
$filter = isset($_GET['status']) && $_GET['status'] !== '' ? mysqli_real_escape_string($conn, $_GET['status']) : '';

$where = "p.vendor_id=$vendor_id";
if ($filter) $where .= " AND o.status='$filter'";

$orders = mysqli_query($conn, "
    SELECT o.id, o.status, o.payment_method, o.payment_status, o.shipping_address,
           o.total_amount, o.created_at,
           c.name AS customer_name, c.phone AS customer_phone,
           GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names,
           SUM(oi.quantity) AS total_qty
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN products p ON p.id = oi.product_id
    LEFT JOIN customers c ON c.id = o.customer_id
    WHERE $where
    GROUP BY o.id ORDER BY o.created_at DESC
");

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Orders</h4>
        <p class="page-subtitle">Orders containing your products</p>
    </div>
    <div class="d-flex gap-2">
        <?php foreach (['','pending','processing','shipped','delivered','cancelled'] as $s): ?>
        <a href="orders.php<?php echo $s ? '?status='.$s : ''; ?>" class="btn btn-sm <?php echo $filter===$s ? 'btn-success' : 'btn-outline-secondary'; ?> rounded-pill">
            <?php echo $s ? ucfirst($s) : 'All'; ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table datatable mb-0">
                <thead>
                    <tr>
                        <th>#Order</th>
                        <th>Customer</th>
                        <th>Products</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($orders) === 0): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No orders found.</td></tr>
                <?php else: ?>
                <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                <tr>
                    <td class="fw-bold">#<?php echo $o['id']; ?></td>
                    <td>
                        <span class="fw-semibold"><?php echo htmlspecialchars($o['customer_name'] ?? 'Guest'); ?></span><br>
                        <small class="text-muted"><?php echo htmlspecialchars($o['customer_phone'] ?? ''); ?></small>
                    </td>
                    <td style="max-width:180px"><small><?php echo htmlspecialchars($o['product_names']); ?></small></td>
                    <td class="fw-semibold text-success">₹<?php echo number_format($o['total_amount'],2); ?></td>
                    <td>
                        <span class="status-badge badge-<?php echo $o['payment_status']==='paid'?'approved':($o['payment_status']==='failed'?'rejected':'pending'); ?>">
                            <?php echo strtoupper($o['payment_method']); ?> / <?php echo ucfirst($o['payment_status']); ?>
                        </span>
                    </td>
                    <td><span class="status-badge badge-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                    <td><small class="text-muted"><?php echo date('d M Y', strtotime($o['created_at'])); ?></small></td>
                    <td>
                        <form method="POST" class="d-flex gap-1">
                            <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                            <select name="new_status" class="form-select form-select-sm" style="width:120px">
                                <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $o['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
