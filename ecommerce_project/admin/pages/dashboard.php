<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();
$pageTitle = 'Dashboard';

// Stats queries
$totalVendors   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM vendors"))[0] ?? 0;
$totalProducts  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0] ?? 0;
$totalOrders    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0] ?? 0;
$totalCustomers = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM customers"))[0] ?? 0;
$totalRevenue   = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'cancelled'"))[0] ?? 0;
$pendingVendors = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM vendors WHERE status='pending'"))[0] ?? 0;

// Recent Orders
$recentOrders = mysqli_query($conn, "SELECT o.*, c.name as customer_name FROM orders o LEFT JOIN customers c ON o.customer_id=c.id ORDER BY o.created_at DESC LIMIT 8");

// Monthly revenue chart data
$chartData = [];
for ($m = 6; $m >= 0; $m--) {
    $month = date('Y-m', strtotime("-$m months"));
    $rev = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE DATE_FORMAT(created_at,'%Y-%m')='$month' AND status!='cancelled'"))[0] ?? 0;
    $chartData[] = ['month' => date('M', strtotime("-$m months")), 'revenue' => (float)$rev];
}
$chartLabels  = json_encode(array_column($chartData, 'month'));
$chartRevenue = json_encode(array_column($chartData, 'revenue'));

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Dashboard</h4>
        <p class="page-subtitle">Welcome back! Here's what's happening today.</p>
    </div>
    <span class="text-muted small"><?php echo date('D, d M Y'); ?></span>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-users-cog"></i></div>
            <div>
                <div class="stat-value"><?php echo $totalVendors; ?></div>
                <div class="stat-label">Total Vendors</div>
                <?php if($pendingVendors > 0): ?>
                <div class="stat-change up"><i class="fas fa-clock"></i> <?php echo $pendingVendors; ?> pending</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-box"></i></div>
            <div>
                <div class="stat-value"><?php echo $totalProducts; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-shopping-cart"></i></div>
            <div>
                <div class="stat-value"><?php echo $totalOrders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-rupee-sign"></i></div>
            <div>
                <div class="stat-value">₹<?php echo number_format($totalRevenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts + Recent Orders -->
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6>Revenue Overview (Last 7 Months)</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header"><h6>Quick Stats</h6></div>
            <div class="card-body d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background:#f8faff">
                    <span class="small text-muted">Total Customers</span>
                    <strong><?php echo $totalCustomers; ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background:#f8faff">
                    <span class="small text-muted">Pending Vendors</span>
                    <span class="badge bg-warning text-dark"><?php echo $pendingVendors; ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background:#f8faff">
                    <span class="small text-muted">Avg Order Value</span>
                    <strong>₹<?php echo $totalOrders > 0 ? number_format($totalRevenue/$totalOrders, 0) : 0; ?></strong>
                </div>
                <a href="vendors.php" class="btn btn-sm btn-outline-primary mt-auto">
                    <i class="fas fa-check-circle me-1"></i> Review Pending Vendors
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6>Recent Orders</h6>
        <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($recentOrders && mysqli_num_rows($recentOrders) > 0):
                        while($o = mysqli_fetch_assoc($recentOrders)): ?>
                    <tr>
                        <td><strong>#<?php echo $o['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars($o['customer_name'] ?? 'N/A'); ?></td>
                        <td>₹<?php echo number_format($o['total_amount'], 2); ?></td>
                        <td>
                            <?php
                            $sc = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                            $cls = $sc[$o['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $cls; ?>"><?php echo ucfirst($o['status']); ?></span>
                        </td>
                        <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
                        <td>
                            <a href="orders.php?view=<?php echo $o['id']; ?>" class="action-btn view"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No orders yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$extraScript = "
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: $chartLabels,
        datasets: [{
            label: 'Revenue (₹)',
            data: $chartRevenue,
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,0.08)',
            borderWidth: 2.5,
            pointRadius: 4,
            pointBackgroundColor: '#6366f1',
            fill: true, tension: 0.4
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' },
                 ticks: { callback: v => '₹' + v.toLocaleString() } },
            x: { grid: { display: false } }
        }
    }
});
</script>";
include '../includes/footer.php';
?>
