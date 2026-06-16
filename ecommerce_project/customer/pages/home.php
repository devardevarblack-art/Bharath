<?php
require_once '../includes/db.php';

$pageTitle = 'Shop';

$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : 0;

// Build product query
$where = ["p.status = 'active'"];
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where[] = "(p.name LIKE '%$s%' OR p.description LIKE '%$s%')";
}
if ($category > 0) {
    $where[] = "p.category_id = $category";
}
$whereSql = implode(' AND ', $where);

$products = mysqli_query($conn, "
    SELECT p.*, c.name AS category_name, v.business_name, v.name AS vendor_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    LEFT JOIN vendors v ON v.id = p.vendor_id
    WHERE $whereSql
    ORDER BY p.created_at DESC
");

$categories = mysqli_query($conn, "SELECT * FROM categories WHERE status = 1 ORDER BY name");

// Fetch wishlist product IDs for current customer (for heart highlight)
$wishlistIds = [];
if (isCustomerLoggedIn()) {
    $cid = getCustomerId();
    $wres = mysqli_query($conn, "SELECT product_id FROM wishlist WHERE customer_id = $cid");
    while ($w = mysqli_fetch_assoc($wres)) {
        $wishlistIds[] = (int)$w['product_id'];
    }
}

require_once '../includes/header.php';
?>

<?php if ($search === '' && $category === 0): ?>
<div class="store-hero">
    <h2><i class="fas fa-bolt me-2"></i>Big Deals, Every Day</h2>
    <p>Shop products from hundreds of trusted vendors — electronics, fashion, home & more, all in one place.</p>
</div>
<?php endif; ?>

<!-- Category chips -->
<div class="category-chips">
    <a href="home.php" class="category-chip <?php echo $category === 0 ? 'active' : ''; ?>">All Categories</a>
    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
        <a href="home.php?category=<?php echo $cat['id']; ?>" class="category-chip <?php echo $category === (int)$cat['id'] ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($cat['name']); ?>
        </a>
    <?php endwhile; ?>
</div>

<?php if ($search !== ''): ?>
    <p class="text-muted small mb-3">Showing results for "<strong><?php echo htmlspecialchars($search); ?></strong>"</p>
<?php endif; ?>

<?php if (mysqli_num_rows($products) === 0): ?>
    <div class="empty-state">
        <i class="fas fa-box-open"></i>
        <p class="mb-0">No products found<?php echo $search !== '' ? ' matching your search' : ''; ?>.</p>
        <a href="home.php" class="btn-store mt-3 d-inline-block">Browse All Products</a>
    </div>
<?php else: ?>

<div class="product-grid">
    <?php while ($p = mysqli_fetch_assoc($products)): ?>
        <?php
            $hasDiscount = $p['original_price'] && $p['original_price'] > $p['price'];
            $discountPct = $hasDiscount ? round((($p['original_price'] - $p['price']) / $p['original_price']) * 100) : 0;
            $inWishlist  = in_array((int)$p['id'], $wishlistIds);
        ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                <div class="product-img-wrap">
                    <?php if ($p['image'] && file_exists('../../vendor/uploads/products/' . $p['image'])): ?>
                        <img src="../../vendor/uploads/products/<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                    <?php else: ?>
                        <i class="fas fa-image no-img"></i>
                    <?php endif; ?>
                    <?php if ($hasDiscount): ?>
                        <span class="discount-badge"><?php echo $discountPct; ?>% OFF</span>
                    <?php endif; ?>
                </div>
            </a>
            <button class="wishlist-toggle <?php echo $inWishlist ? 'active' : ''; ?>" onclick="toggleWishlist(<?php echo $p['id']; ?>, this)" title="Add to wishlist">
                <i class="fas fa-heart"></i>
            </button>
            <div class="product-body">
                <div class="product-vendor"><?php echo htmlspecialchars($p['business_name'] ?? $p['vendor_name'] ?? 'MultiVendor'); ?></div>
                <a href="product.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                    <div class="product-title text-dark"><?php echo htmlspecialchars($p['name']); ?></div>
                </a>
                <div class="product-price">
                    <span class="price-current">₹<?php echo number_format($p['price'], 2); ?></span>
                    <?php if ($hasDiscount): ?>
                        <span class="price-original">₹<?php echo number_format($p['original_price'], 2); ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($p['stock'] > 0): ?>
                    <div class="product-stock"><i class="fas fa-check-circle me-1"></i>In Stock</div>
                <?php else: ?>
                    <div class="product-stock out"><i class="fas fa-times-circle me-1"></i>Out of Stock</div>
                <?php endif; ?>
                <div class="product-actions">
                    <button class="btn-add-cart" onclick="addToCart(<?php echo $p['id']; ?>, 1, this, true)" <?php echo $p['stock'] <= 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-cart-plus me-1"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
