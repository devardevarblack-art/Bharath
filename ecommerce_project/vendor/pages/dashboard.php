<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle  = 'Dashboard';
$vendor_id  = getVendorId();
$status     = $_SESSION['vendor_status'];

// Stats
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM products WHERE vendor_id=$vendor_id"))['c'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT oi.order_id) AS c FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE p.vendor_id=$vendor_id"))['c'];
$total_revenue  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(oi.price * oi.quantity),0) AS c FROM order_items oi JOIN products p ON p.id=oi.product_id JOIN orders o ON o.id=oi.order_id WHERE p.vendor_id=$vendor_id AND o.payment_status='paid'"))['c'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT oi.order_id) AS c FROM order_items oi JOIN products p ON p.id=oi.product_id JOIN orders o ON o.id=oi.order_id WHERE p.vendor_id=$vendor_id AND o.status='pending'"))['c'];

// Recent orders
$recent_orders = mysqli_query($conn, "
    SELECT o.id, o.status, o.payment_status, o.created_at, o.total_amount,
           c.name AS customer_name, GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN products p ON p.id = oi.product_id
    LEFT JOIN customers c ON c.id = o.customer_id
    WHERE p.vendor_id = $vendor_id
    GROUP BY o.id ORDER BY o.created_at DESC LIMIT 5
");

// Monthly revenue (last 6 months)
$monthly = mysqli_query($conn, "
    SELECT DATE_FORMAT(o.created_at,'%b') AS mon,
           IFNULL(SUM(oi.price * oi.quantity),0) AS rev
    FROM order_items oi
    JOIN products p ON p.id=oi.product_id
    JOIN orders o ON o.id=oi.order_id
    WHERE p.vendor_id=$vendor_id AND o.payment_status='paid'
      AND o.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(o.created_at,'%Y-%m')
    ORDER BY o.created_at ASC
");
$months = []; $revenues = [];
while ($row = mysqli_fetch_assoc($monthly)) { $months[] = $row['mon']; $revenues[] = $row['rev']; }

require_once '../includes/header.php';
?>

<?php if ($status !== 'approved'): ?>
<div class="alert-info-box mb-4">
    <i class="fas fa-clock me-2"></i>
    <strong>Account <?php echo ucfirst($status); ?>:</strong>
    <?php if ($status === 'pending'): ?>
        Your vendor account is pending admin approval. You can view your dashboard but product/order features will be available once approved.
    <?php else: ?>
        Your account has been rejected. Please contact admin at admin@example.com for assistance.
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Dashboard</h4>
        <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['vendor_name']); ?>!</p>
    </div>
    <?php if ($status === 'approved'): ?>
    <a href="add_product.php" class="btn btn-sm btn-success rounded-pill px-3">
        <i class="fas fa-plus me-1"></i> Add Product
    </a>
    <?php endif; ?>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-box"></i></div>
            <div>
                <div class="stat-value"><?php echo $total_products; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
            <div>
                <div class="stat-value"><?php echo $total_orders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-rupee-sign"></i></div>
            <div>
                <div class="stat-value">₹<?php echo number_format($total_revenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-clock"></i></div>
            <div>
                <div class="stat-value"><?php echo $pending_orders; ?></div>
                <div class="stat-label">Pending Orders</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Revenue Chart -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-chart-line me-2 text-success"></i>Monthly Revenue</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6><i class="fas fa-shopping-cart me-2 text-success"></i>Recent Orders</h6>
                <a href="orders.php" class="btn btn-sm btn-outline-success rounded-pill px-3 small">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>#Order</th><th>Customer</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php if (mysqli_num_rows($recent_orders) === 0): ?>
                            <tr><td colspan="3" class="text-center text-muted py-3 small">No orders yet</td></tr>
                        <?php else: ?>
                        <?php while ($o = mysqli_fetch_assoc($recent_orders)): ?>
                        <tr>
                            <td><span class="fw-semibold">#<?php echo $o['id']; ?></span><br><span class="text-muted" style="font-size:0.75rem">₹<?php echo number_format($o['total_amount'],0); ?></span></td>
                            <td><?php echo htmlspecialchars($o['customer_name'] ?? 'Guest'); ?></td>
                            <td><span class="status-badge badge-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months ?: ['No Data']); ?>,
        datasets: [{
            label: 'Revenue (₹)',
            data: <?php echo json_encode($revenues ?: [0]); ?>,
            backgroundColor: 'rgba(16,185,129,0.2)',
            borderColor: '#10b981',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '₹' + v } } }
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
