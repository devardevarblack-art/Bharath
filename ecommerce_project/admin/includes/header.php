<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>MultiVendor Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-store me-2"></i>
        <span>MultiVendor</span>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="sidebar-heading">Vendor</li>
        <li>
            <a href="vendors.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'vendors.php' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i> Vendor Approval
            </a>
        </li>
        <li class="sidebar-heading">Catalog</li>
        <li>
            <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' || basename($_SERVER['PHP_SELF']) == 'add_product.php' || basename($_SERVER['PHP_SELF']) == 'product_view.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> Products
            </a>
        </li>
        <li>
            <a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Categories
            </a>
        </li>
        <li class="sidebar-heading">Sales</li>
        <li>
            <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
        </li>
        <li class="sidebar-heading">Users</li>
        <li>
            <a href="customers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Customers
            </a>
        </li>
        <li class="sidebar-heading">Reports</li>
        <li>
            <a href="reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i> Reports & Analytics
            </a>
        </li>
        <li class="mt-3">
            <a href="../../customer/index.php" target="_blank">
                <i class="fas fa-shopping-bag"></i> View Storefront
            </a>
        </li>
        <li>
            <a href="logout.php" class="text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small">Welcome, <strong><?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin'; ?></strong></span>
            <img src="https://ui-avatars.com/api/?name=Admin&background=6366f1&color=fff&size=36" class="rounded-circle" alt="Admin">
        </div>
    </nav>

    <!-- Page Content -->
    <div class="page-content">
