<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();
$pageTitle = 'Customer Management';

// Toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    mysqli_query($conn, "UPDATE customers SET status = IF(status='active','inactive','active') WHERE id=$id");
    header("Location: customers.php?msg=updated"); exit();
}
// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM customers WHERE id=$id");
    header("Location: customers.php?msg=deleted"); exit();
}

$customers = mysqli_query($conn, "SELECT c.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount),0) as total_spent FROM customers c LEFT JOIN orders o ON c.id=o.customer_id GROUP BY c.id ORDER BY c.created_at DESC");

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Customer Management</h4>
        <p class="page-subtitle">View and manage all registered customers</p>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success"><?php echo $_GET['msg']==='deleted'?'Customer deleted.':'Customer updated.'; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($customers && mysqli_num_rows($customers) > 0):
                        $i=1; while($c = mysqli_fetch_assoc($customers)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($c['name']); ?>&background=3b82f6&color=fff&size=36" class="avatar-sm">
                                <span class="small fw-semibold"><?php echo htmlspecialchars($c['name']); ?></span>
                            </div>
                        </td>
                        <td class="small"><?php echo htmlspecialchars($c['email']); ?></td>
                        <td class="small"><?php echo htmlspecialchars($c['phone'] ?? '-'); ?></td>
                        <td><span class="badge bg-light text-dark"><?php echo $c['order_count']; ?></span></td>
                        <td class="small fw-semibold">₹<?php echo number_format($c['total_spent'], 2); ?></td>
                        <td>
                            <span class="status-badge <?php echo $c['status']==='active'?'badge-approved':'badge-inactive'; ?>">
                                <?php echo ucfirst($c['status'] ?? 'active'); ?>
                            </span>
                        </td>
                        <td class="small"><?php echo date('d M Y', strtotime($c['created_at'])); ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="?toggle=<?php echo $c['id']; ?>" class="action-btn <?php echo ($c['status']??'active')==='active'?'reject':'approve'; ?>" title="Toggle">
                                    <i class="fas <?php echo ($c['status']??'active')==='active'?'fa-ban':'fa-check'; ?>"></i>
                                </a>
                                <button class="action-btn delete" onclick="confirmDelete('?delete=<?php echo $c['id']; ?>','Delete this customer?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="9" class="text-center py-4 text-muted">No customers found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
