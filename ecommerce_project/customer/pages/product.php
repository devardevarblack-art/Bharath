<?php
require_once '../includes/db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

$product = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT p.*, c.name AS category_name, v.business_name, v.name AS vendor_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    LEFT JOIN vendors v ON v.id = p.vendor_id
    WHERE p.id = $id AND p.status = 'active'
    LIMIT 1
"));

if (!$product) {
    header("Location: home.php");
    exit();
}

$pageTitle = $product['name'];

$inWishlist = false;
if (isCustomerLoggedIn()) {
    $cid = getCustomerId();
    $w = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM wishlist WHERE customer_id=$cid AND product_id=$id LIMIT 1"));
    $inWishlist = (bool)$w;
}

$hasDiscount = $product['original_price'] && $product['original_price'] > $product['price'];
$discountPct = $hasDiscount ? round((($product['original_price'] - $product['price']) / $product['original_price']) * 100) : 0;

// Related products from same category
$related = mysqli_query($conn, "
    SELECT p.*, v.business_name
    FROM products p
    LEFT JOIN vendors v ON v.id = p.vendor_id
    WHERE p.category_id = " . ($product['category_id'] ?? 0) . "
      AND p.id != $id AND p.status = 'active'
    LIMIT 4
");

require_once '../includes/header.php';
?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="home.php">Shop</a></li>
        <?php if ($product['category_name']): ?>
            <li class="breadcrumb-item"><a href="home.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
        <?php endif; ?>
        <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
    </ol>
</nav>

<div class="row g-4">
    <div class="col-md-5">
        <div class="product-img-wrap" style="height: 360px; border-radius: 14px;">
            <?php if ($product['image'] && file_exists('../../vendor/uploads/products/' . $product['image'])): ?>
                <img src="../../vendor/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php else: ?>
                <i class="fas fa-image no-img" style="font-size: 4rem;"></i>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-7">
        <div class="product-vendor mb-1"><?php echo htmlspecialchars($product['business_name'] ?? $product['vendor_name'] ?? 'MultiVendor'); ?></div>
        <h3 class="fw-bold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>

        <div class="product-price mb-2">
            <span class="price-current fs-3">₹<?php echo number_format($product['price'], 2); ?></span>
            <?php if ($hasDiscount): ?>
                <span class="price-original fs-5">₹<?php echo number_format($product['original_price'], 2); ?></span>
                <span class="discount-badge position-relative" style="top:0; left:0;"><?php echo $discountPct; ?>% OFF</span>
            <?php endif; ?>
        </div>

        <?php if ($product['stock'] > 0): ?>
            <p class="product-stock mb-3"><i class="fas fa-check-circle me-1"></i><?php echo $product['stock']; ?> in stock</p>
        <?php else: ?>
            <p class="product-stock out mb-3"><i class="fas fa-times-circle me-1"></i>Out of stock</p>
        <?php endif; ?>

        <p class="text-muted"><?php echo $product['description'] ? nl2br(htmlspecialchars($product['description'])) : 'No description available for this product.'; ?></p>

        <?php if ($product['category_name']): ?>
            <p class="small text-muted">Category: <span class="fw-semibold text-dark"><?php echo htmlspecialchars($product['category_name']); ?></span></p>
        <?php endif; ?>

        <div class="d-flex align-items-center gap-3 mt-4">
            <div class="qty-control">
                <button type="button" onclick="document.getElementById('qtyInput').stepDown()"><i class="fas fa-minus"></i></button>
                <input type="number" id="qtyInput" value="1" min="1" max="<?php echo max(1,$product['stock']); ?>">
                <button type="button" onclick="document.getElementById('qtyInput').stepUp()"><i class="fas fa-plus"></i></button>
            </div>
            <button class="btn-add-cart" style="width:auto; padding: 10px 28px;" <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>
                onclick="addToCart(<?php echo $product['id']; ?>, document.getElementById('qtyInput').value, this, true)">
                <i class="fas fa-cart-plus me-2"></i>Add to Cart
            </button>
            <button class="wishlist-toggle <?php echo $inWishlist ? 'active' : ''; ?>" style="position: static;" onclick="toggleWishlist(<?php echo $product['id']; ?>, this)" title="Add to wishlist">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </div>
</div>

<?php if (mysqli_num_rows($related) > 0): ?>
<h5 class="fw-bold mt-5 mb-3">You may also like</h5>
<div class="product-grid">
    <?php while ($r = mysqli_fetch_assoc($related)): ?>
        <?php $rDiscount = $r['original_price'] && $r['original_price'] > $r['price']; ?>
        <div class="product-card">
            <a href="product.php?id=<?php echo $r['id']; ?>" class="text-decoration-none">
                <div class="product-img-wrap">
                    <?php if ($r['image'] && file_exists('../../vendor/uploads/products/' . $r['image'])): ?>
                        <img src="../../vendor/uploads/products/<?php echo htmlspecialchars($r['image']); ?>" alt="<?php echo htmlspecialchars($r['name']); ?>">
                    <?php else: ?>
                        <i class="fas fa-image no-img"></i>
                    <?php endif; ?>
                </div>
                <div class="product-body">
                    <div class="product-vendor"><?php echo htmlspecialchars($r['business_name'] ?? 'MultiVendor'); ?></div>
                    <div class="product-title text-dark"><?php echo htmlspecialchars($r['name']); ?></div>
                    <div class="product-price">
                        <span class="price-current">₹<?php echo number_format($r['price'], 2); ?></span>
                        <?php if ($rDiscount): ?><span class="price-original">₹<?php echo number_format($r['original_price'], 2); ?></span><?php endif; ?>
                    </div>
                </div>
            </a>
        </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
