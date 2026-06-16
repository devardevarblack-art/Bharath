<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'Sales Overview';
$vendor_id = getVendorId();

// Summary totals
$total_revenue  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(oi.price*oi.quantity),0) AS v FROM order_items oi JOIN products p ON p.id=oi.product_id JOIN orders o ON o.id=oi.order_id WHERE p.vendor_id=$vendor_id AND o.payment_status='paid'"))['v'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT oi.order_id) AS v FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE p.vendor_id=$vendor_id"))['v'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS v FROM products WHERE vendor_id=$vendor_id"))['v'];
$total_items    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(oi.quantity),0) AS v FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE p.vendor_id=$vendor_id"))['v'];

// Monthly sales — last 12 months
$monthly = mysqli_query($conn, "
    SELECT DATE_FORMAT(o.created_at,'%b %Y') AS mon,
           DATE_FORMAT(o.created_at,'%Y-%m') AS sort_key,
           IFNULL(SUM(oi.price*oi.quantity),0) AS rev,
           COUNT(DISTINCT oi.order_id) AS cnt
    FROM order_items oi
    JOIN products p ON p.id=oi.product_id
    JOIN orders o ON o.id=oi.order_id
    WHERE p.vendor_id=$vendor_id AND o.payment_status='paid'
      AND o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(o.created_at,'%Y-%m')
    ORDER BY sort_key ASC
");
$months = []; $revenues = []; $order_counts = [];
while ($r = mysqli_fetch_assoc($monthly)) {
    $months[] = $r['mon']; $revenues[] = (float)$r['rev']; $order_counts[] = (int)$r['cnt'];
}

// Top products
$top_products = mysqli_query($conn, "
    SELECT p.name, SUM(oi.quantity) AS qty_sold, SUM(oi.price*oi.quantity) AS revenue
    FROM order_items oi
    JOIN products p ON p.id=oi.product_id
    WHERE p.vendor_id=$vendor_id
    GROUP BY p.id ORDER BY revenue DESC LIMIT 5
");

// Orders by status
$by_status = mysqli_query($conn, "
    SELECT o.status, COUNT(DISTINCT oi.order_id) AS cnt
    FROM order_items oi
    JOIN products p ON p.id=oi.product_id
    JOIN orders o ON o.id=oi.order_id
    WHERE p.vendor_id=$vendor_id
    GROUP BY o.status
");
$status_labels = []; $status_counts = []; $status_colors = [];
$scolor = ['pending'=>'#f59e0b','processing'=>'#3b82f6','shipped'=>'#8b5cf6','delivered'=>'#10b981','cancelled'=>'#ef4444'];
while ($r = mysqli_fetch_assoc($by_status)) {
    $status_labels[] = ucfirst($r['status']); $status_counts[] = (int)$r['cnt'];
    $status_colors[] = $scolor[$r['status']] ?? '#64748b';
}

require_once '../includes/header.php';
?>

<div class="mb-4">
    <h4 class="page-title">Sales Overview</h4>
    <p class="page-subtitle">Your complete sales performance</p>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-rupee-sign"></i></div>
            <div>
                <div class="stat-value">₹<?php echo number_format($total_revenue,0); ?></div>
                <div class="stat-label">Total Revenue (Paid)</div>
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
            <div class="stat-icon orange"><i class="fas fa-box"></i></div>
            <div>
                <div class="stat-value"><?php echo $total_items; ?></div>
                <div class="stat-label">Items Sold</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-tag"></i></div>
            <div>
                <div class="stat-value"><?php echo $total_products; ?></div>
                <div class="stat-label">Active Listings</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header"><h6><i class="fas fa-chart-line me-2 text-success"></i>Revenue — Last 12 Months</h6></div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Orders by Status -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><h6><i class="fas fa-chart-pie me-2 text-success"></i>Orders by Status</h6></div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <?php if (empty($status_counts)): ?>
                    <p class="text-muted small">No data yet.</p>
                <?php else: ?>
                    <canvas id="statusChart" style="max-height:240px"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="card">
    <div class="card-header"><h6><i class="fas fa-trophy me-2 text-success"></i>Top 5 Products by Revenue</h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>#</th><th>Product</th><th>Qty Sold</th><th>Revenue</th></tr></thead>
                <tbody>
                <?php if (mysqli_num_rows($top_products) === 0): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">No sales yet.</td></tr>
                <?php else: ?>
                <?php $i=1; while ($tp = mysqli_fetch_assoc($top_products)): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td class="fw-semibold"><?php echo htmlspecialchars($tp['name']); ?></td>
                    <td><?php echo $tp['qty_sold']; ?> units</td>
                    <td class="fw-semibold text-success">₹<?php echo number_format($tp['revenue'],2); ?></td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Revenue chart
const ctx1 = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months ?: ['No Data']); ?>,
        datasets: [{
            label: 'Revenue (₹)',
            data: <?php echo json_encode($revenues ?: [0]); ?>,
            borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)',
            fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#10b981'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '₹' + v } } }
    }
});

// Status doughnut
<?php if (!empty($status_counts)): ?>
const ctx2 = document.getElementById('statusChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($status_labels); ?>,
        datasets: [{ data: <?php echo json_encode($status_counts); ?>, backgroundColor: <?php echo json_encode($status_colors); ?>, borderWidth: 2 }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
});
<?php endif; ?>
</script>

<?php require_once '../includes/footer.php'; ?>
