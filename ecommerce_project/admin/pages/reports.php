<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();
$pageTitle = 'Reports & Analytics';

// Date range
$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to   = isset($_GET['to'])   ? $_GET['to']   : date('Y-m-d');

// Summary stats
$totalRev   = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status!='cancelled' AND DATE(created_at) BETWEEN '$from' AND '$to'"))[0] ?? 0;
$totalOrd   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE DATE(created_at) BETWEEN '$from' AND '$to'"))[0] ?? 0;
$newCust    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM customers WHERE DATE(created_at) BETWEEN '$from' AND '$to'"))[0] ?? 0;
$newVendors = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM vendors WHERE DATE(created_at) BETWEEN '$from' AND '$to'"))[0] ?? 0;

// Daily revenue for chart
$dailyRevResult = mysqli_query($conn, "SELECT DATE(created_at) as d, SUM(total_amount) as rev FROM orders WHERE status!='cancelled' AND DATE(created_at) BETWEEN '$from' AND '$to' GROUP BY DATE(created_at) ORDER BY d");
$dailyLabels = []; $dailyRevs = [];
while($dr = mysqli_fetch_assoc($dailyRevResult)) {
    $dailyLabels[] = date('d M', strtotime($dr['d']));
    $dailyRevs[]   = (float)$dr['rev'];
}

// Top products
$topProducts = mysqli_query($conn, "SELECT p.name, SUM(oi.quantity) as sold, SUM(oi.quantity * oi.price) as revenue FROM order_items oi LEFT JOIN products p ON oi.product_id=p.id LEFT JOIN orders o ON oi.order_id=o.id WHERE o.status!='cancelled' AND DATE(o.created_at) BETWEEN '$from' AND '$to' GROUP BY oi.product_id ORDER BY sold DESC LIMIT 5");

// Top vendors
$topVendors = mysqli_query($conn, "SELECT v.name, COUNT(DISTINCT o.id) as orders, SUM(o.total_amount) as revenue FROM orders o LEFT JOIN order_items oi ON o.id=oi.order_id LEFT JOIN products p ON oi.product_id=p.id LEFT JOIN vendors v ON p.vendor_id=v.id WHERE o.status!='cancelled' AND DATE(o.created_at) BETWEEN '$from' AND '$to' GROUP BY v.id ORDER BY revenue DESC LIMIT 5");

// Order status distribution
$statusDist = mysqli_query($conn, "SELECT status, COUNT(*) as cnt FROM orders WHERE DATE(created_at) BETWEEN '$from' AND '$to' GROUP BY status");
$statusLabels = []; $statusCounts = []; $statusColors = [];
$colorMap = ['pending'=>'#f59e0b','processing'=>'#3b82f6','shipped'=>'#8b5cf6','delivered'=>'#10b981','cancelled'=>'#ef4444'];
while($sd = mysqli_fetch_assoc($statusDist)) {
    $statusLabels[] = ucfirst($sd['status']);
    $statusCounts[] = (int)$sd['cnt'];
    $statusColors[] = $colorMap[$sd['status']] ?? '#94a3b8';
}

$chartLabels  = json_encode($dailyLabels);
$chartRevs    = json_encode($dailyRevs);
$jStatusLabels= json_encode($statusLabels);
$jStatusCounts= json_encode($statusCounts);
$jStatusColors= json_encode($statusColors);

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Reports & Analytics</h4>
        <p class="page-subtitle">Track performance metrics and business insights</p>
    </div>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-end">
            <div>
                <label class="form-label small mb-1 fw-semibold">From Date</label>
                <input type="date" name="from" class="form-control form-control-sm" value="<?php echo $from; ?>">
            </div>
            <div>
                <label class="form-label small mb-1 fw-semibold">To Date</label>
                <input type="date" name="to" class="form-control form-control-sm" value="<?php echo $to; ?>">
            </div>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Apply Filter</button>
            <!-- Quick filters -->
            <a href="?from=<?php echo date('Y-m-d'); ?>&to=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary btn-sm">Today</a>
            <a href="?from=<?php echo date('Y-m-01'); ?>&to=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary btn-sm">This Month</a>
            <a href="?from=<?php echo date('Y-01-01'); ?>&to=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary btn-sm">This Year</a>
        </form>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-rupee-sign"></i></div>
            <div>
                <div class="stat-value">₹<?php echo number_format($totalRev, 0); ?></div>
                <div class="stat-label">Revenue</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
            <div>
                <div class="stat-value"><?php echo $totalOrd; ?></div>
                <div class="stat-label">Orders</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-user-plus"></i></div>
            <div>
                <div class="stat-value"><?php echo $newCust; ?></div>
                <div class="stat-label">New Customers</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-store"></i></div>
            <div>
                <div class="stat-value"><?php echo $newVendors; ?></div>
                <div class="stat-label">New Vendors</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header"><h6>Daily Revenue</h6></div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h6>Order Status Distribution</h6></div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Products & Vendors -->
<div class="row g-3">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header"><h6>Top Products</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Units Sold</th><th>Revenue</th></tr>
                    </thead>
                    <tbody>
                        <?php if($topProducts && mysqli_num_rows($topProducts)>0):
                            while($tp = mysqli_fetch_assoc($topProducts)): ?>
                        <tr>
                            <td class="small"><?php echo htmlspecialchars($tp['name'] ?? 'N/A'); ?></td>
                            <td><span class="badge bg-primary"><?php echo $tp['sold']; ?></span></td>
                            <td class="small fw-semibold">₹<?php echo number_format($tp['revenue'],0); ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="3" class="text-center py-3 text-muted small">No data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header"><h6>Top Vendors</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Vendor</th><th>Orders</th><th>Revenue</th></tr>
                    </thead>
                    <tbody>
                        <?php if($topVendors && mysqli_num_rows($topVendors)>0):
                            while($tv = mysqli_fetch_assoc($topVendors)): ?>
                        <tr>
                            <td class="small"><?php echo htmlspecialchars($tv['name'] ?? 'N/A'); ?></td>
                            <td><span class="badge bg-success"><?php echo $tv['orders']; ?></span></td>
                            <td class="small fw-semibold">₹<?php echo number_format($tv['revenue'],0); ?></td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="3" class="text-center py-3 text-muted small">No data</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$extraScript = "
<script>
// Daily Revenue Chart
new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: $chartLabels,
        datasets: [{
            label: 'Revenue',
            data: $chartRevs,
            backgroundColor: 'rgba(99,102,241,0.7)',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => '₹'+v.toLocaleString() } },
            x: { grid: { display: false } }
        }
    }
});

// Status Doughnut Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: $jStatusLabels,
        datasets: [{ data: $jStatusCounts, backgroundColor: $jStatusColors, borderWidth: 2 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
        cutout: '65%'
    }
});
</script>";
include '../includes/footer.php';
?>
