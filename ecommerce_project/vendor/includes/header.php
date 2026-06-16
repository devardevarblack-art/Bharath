<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>Vendor Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../css/vendor.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-store-alt"></i>
        <div>
            <div>MultiVendor</div>
            <small><?php echo isset($_SESSION['vendor_business']) ? htmlspecialchars($_SESSION['vendor_business']) : 'Vendor Panel'; ?></small>
        </div>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li class="sidebar-heading">My Shop</li>
        <li>
            <a href="shop.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'active' : ''; ?>">
                <i class="fas fa-store"></i> Shop Profile
            </a>
        </li>
        <li class="sidebar-heading">Products</li>
        <li>
            <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i> My Products
            </a>
        </li>
        <li>
            <a href="add_product.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> Add Product
            </a>
        </li>
        <li class="sidebar-heading">Sales</li>
        <li>
            <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
        </li>
        <li>
            <a href="sales.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Sales Overview
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
            <?php if (!isVendorApproved()): ?>
                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending Approval</span>
            <?php endif; ?>
            <span class="text-muted small">Welcome, <strong><?php echo isset($_SESSION['vendor_name']) ? htmlspecialchars($_SESSION['vendor_name']) : 'Vendor'; ?></strong></span>
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['vendor_name'] ?? 'V'); ?>&background=10b981&color=fff&size=36" class="rounded-circle" alt="Vendor">
        </div>
    </nav>

    <!-- Page Content -->
    <div class="page-content">
