<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();
$pageTitle = 'Order Management';

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $oid    = (int)$_POST['order_id'];
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$oid");
    header("Location: orders.php?msg=updated"); exit();
}

$filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$where  = $filter !== 'all' ? "WHERE o.status='$filter'" : '';

$orders = mysqli_query($conn, "SELECT o.*, c.name as customer_name, c.email as customer_email FROM orders o LEFT JOIN customers c ON o.customer_id=c.id $where ORDER BY o.created_at DESC");

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Order Management</h4>
        <p class="page-subtitle">Track and manage all customer orders</p>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success">Order status updated successfully!</div>
<?php endif; ?>

<!-- Filter -->
<ul class="nav nav-pills mb-3">
    <?php foreach(['all'=>'All','pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $k=>$v): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo $filter===$k?'active':''; ?>" href="?status=<?php echo $k; ?>"><?php echo $v; ?></a>
    </li>
    <?php endforeach; ?>
</ul>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($orders && mysqli_num_rows($orders) > 0):
                        while($o = mysqli_fetch_assoc($orders)): ?>
                    <tr>
                        <td><strong>#<?php echo $o['id']; ?></strong></td>
                        <td>
                            <div class="small fw-semibold"><?php echo htmlspecialchars($o['customer_name'] ?? 'N/A'); ?></div>
                            <div class="text-muted" style="font-size:0.75rem"><?php echo htmlspecialchars($o['customer_email'] ?? ''); ?></div>
                        </td>
                        <td class="fw-semibold">₹<?php echo number_format($o['total_amount'], 2); ?></td>
                        <td>
                            <?php
                            $pm = $o['payment_method'] ?? 'cod';
                            $pmLabels = ['cod'=>'COD','online'=>'Online','upi'=>'UPI','card'=>'Card'];
                            ?>
                            <span class="badge bg-light text-dark"><?php echo $pmLabels[$pm] ?? strtoupper($pm); ?></span>
                        </td>
                        <td>
                            <?php
                            $sc = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                            $cls = $sc[$o['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $cls; ?>"><?php echo ucfirst($o['status']); ?></span>
                        </td>
                        <td class="small"><?php echo date('d M Y h:i A', strtotime($o['created_at'])); ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                <select name="status" class="form-select form-select-sm" style="width:130px">
                                    <?php foreach(['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $o['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No orders found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
