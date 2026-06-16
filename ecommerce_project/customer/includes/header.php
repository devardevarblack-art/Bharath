<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?>MultiVendor Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/customer.css">
</head>
<body>

<?php
$cartCount     = getCartCount($conn);
$wishlistCount = getWishlistCount($conn);
?>

<!-- Store Navbar -->
<nav class="store-navbar">
    <div class="container-fluid container-narrow">
        <a href="home.php" class="store-brand">
            <i class="fas fa-store"></i>
            <span>MultiVendor<small>Shop with confidence</small></span>
        </a>

        <form class="search-form d-flex" method="GET" action="home.php">
            <input type="text" name="search" class="form-control" placeholder="Search for products..."
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn"><i class="fas fa-search"></i></button>
        </form>

        <div class="nav-icons">
            <a href="wishlist.php" class="nav-icon-link" title="Wishlist">
                <i class="fas fa-heart"></i>
                <span class="nav-badge" id="wishlistBadge" style="<?php echo $wishlistCount > 0 ? '' : 'display:none;'; ?>"><?php echo $wishlistCount; ?></span>
            </a>
            <a href="cart.php" class="nav-icon-link" title="Cart">
                <i class="fas fa-shopping-cart"></i>
                <span class="nav-badge" id="cartBadge" style="<?php echo $cartCount > 0 ? '' : 'display:none;'; ?>"><?php echo $cartCount; ?></span>
            </a>

            <?php if (isCustomerLoggedIn()): ?>
                <div class="dropdown nav-user-dropdown">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>
                        <?php echo htmlspecialchars(explode(' ', $_SESSION['customer_name'])[0]); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-card me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box me-2"></i>My Orders</a></li>
                        <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart me-2"></i>Wishlist</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="../index.php" class="btn-store-outline">Login</a>
                <a href="../register.php" class="btn-store">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Page Content -->
<div class="store-content">
    <div class="container-narrow">
