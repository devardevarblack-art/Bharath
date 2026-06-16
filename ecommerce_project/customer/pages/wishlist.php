<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'My Wishlist';
$cid = getCustomerId();

// Move to cart
if (isset($_GET['move']) && is_numeric($_GET['move'])) {
    $product_id = (int)$_GET['move'];
    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM products WHERE id=$product_id LIMIT 1"));
    if ($product && $product['stock'] > 0) {
        $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cart WHERE customer_id=$cid AND product_id=$product_id LIMIT 1"));
        if ($existing) {
            $newQty = min($existing['quantity'] + 1, $product['stock']);
            mysqli_query($conn, "UPDATE cart SET quantity=$newQty WHERE id={$existing['id']}");
        } else {
            mysqli_query($conn, "INSERT INTO cart (customer_id, product_id, quantity) VALUES ($cid, $product_id, 1)");
        }
        mysqli_query($conn, "DELETE FROM wishlist WHERE customer_id=$cid AND product_id=$product_id");
    }
    header("Location: wishlist.php");
    exit();
}

// Remove from wishlist
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM wishlist WHERE customer_id=$cid AND product_id=$product_id");
    header("Location: wishlist.php");
    exit();
}

$items = mysqli_query($conn, "
    SELECT w.id AS wishlist_id, p.id AS product_id, p.name, p.price, p.original_price, p.image, p.stock, v.business_name
    FROM wishlist w
    JOIN products p ON p.id = w.product_id
    LEFT JOIN vendors v ON v.id = p.vendor_id
    WHERE w.customer_id = $cid
    ORDER BY w.created_at DESC
");

require_once '../includes/header.php';
?>

<h4 class="page-title mb-4"><i class="fas fa-heart me-2"></i>My Wishlist</h4>

<?php if (mysqli_num_rows($items) === 0): ?>
    <div class="empty-state">
        <i class="fas fa-heart"></i>
        <p class="mb-0">Your wishlist is empty.</p>
        <a href="home.php" class="btn-store mt-3 d-inline-block">Browse Products</a>
    </div>
<?php else: ?>

<?php while ($item = mysqli_fetch_assoc($items)): ?>
    <?php $hasDiscount = $item['original_price'] && $item['original_price'] > $item['price']; ?>
    <div class="wishlist-item">
        <?php if ($item['image'] && file_exists('../../vendor/uploads/products/' . $item['image'])): ?>
            <img src="../../vendor/uploads/products/<?php echo htmlspecialchars($item['image']); ?>" class="wishlist-item-img" alt="<?php echo htmlspecialchars($item['name']); ?>">
        <?php else: ?>
            <div class="wishlist-item-img d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div>
        <?php endif; ?>

        <div class="cart-item-info">
            <a href="product.php?id=<?php echo $item['product_id']; ?>" class="text-dark fw-semibold text-decoration-none"><?php echo htmlspecialchars($item['name']); ?></a>
            <div class="product-vendor"><?php echo htmlspecialchars($item['business_name'] ?? 'MultiVendor'); ?></div>
            <div class="product-price">
                <span class="price-current">₹<?php echo number_format($item['price'], 2); ?></span>
                <?php if ($hasDiscount): ?><span class="price-original">₹<?php echo number_format($item['original_price'], 2); ?></span><?php endif; ?>
            </div>
            <?php if ($item['stock'] <= 0): ?><div class="product-stock out">Out of Stock</div><?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <a href="wishlist.php?move=<?php echo $item['product_id']; ?>" class="btn-store <?php echo $item['stock'] <= 0 ? 'disabled' : ''; ?>" style="white-space:nowrap;">
                <i class="fas fa-cart-plus me-1"></i> Move to Cart
            </a>
            <button class="action-btn delete" style="width:38px;height:38px;border-radius:8px;border:none;background:#fee2e2;color:#b91c1c;" title="Remove" onclick="confirmRemove('wishlist.php?remove=<?php echo $item['product_id']; ?>', 'Remove this item from your wishlist?')">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
<?php endwhile; ?>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
