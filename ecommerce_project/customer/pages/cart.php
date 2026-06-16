<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'My Cart';
$cid = getCustomerId();

$items = mysqli_query($conn, "
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.original_price, p.image, p.stock, v.business_name
    FROM cart c
    JOIN products p ON p.id = c.product_id
    LEFT JOIN vendors v ON v.id = p.vendor_id
    WHERE c.customer_id = $cid
    ORDER BY c.created_at DESC
");

$cartItems = [];
$subtotal  = 0;
while ($row = mysqli_fetch_assoc($items)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['line_total'];
    $cartItems[] = $row;
}

$shipping = $subtotal > 0 ? ($subtotal >= 999 ? 0 : 49) : 0;
$total    = $subtotal + $shipping;

require_once '../includes/header.php';
?>

<h4 class="page-title mb-4"><i class="fas fa-shopping-cart me-2"></i>My Cart</h4>

<?php if (empty($cartItems)): ?>
    <div class="empty-state">
        <i class="fas fa-shopping-cart"></i>
        <p class="mb-0">Your cart is empty.</p>
        <a href="home.php" class="btn-store mt-3 d-inline-block">Start Shopping</a>
    </div>
<?php else: ?>

<div class="row g-4">
    <div class="col-md-8">
        <?php foreach ($cartItems as $item): ?>
            <div class="cart-item">
                <?php if ($item['image'] && file_exists('../../vendor/uploads/products/' . $item['image'])): ?>
                    <img src="../../vendor/uploads/products/<?php echo htmlspecialchars($item['image']); ?>" class="cart-item-img" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <?php else: ?>
                    <div class="cart-item-img d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div>
                <?php endif; ?>

                <div class="cart-item-info">
                    <a href="product.php?id=<?php echo $item['product_id']; ?>" class="text-dark fw-semibold text-decoration-none"><?php echo htmlspecialchars($item['name']); ?></a>
                    <div class="product-vendor"><?php echo htmlspecialchars($item['business_name'] ?? 'MultiVendor'); ?></div>
                    <div class="price-current">₹<?php echo number_format($item['price'], 2); ?></div>
                </div>

                <div class="qty-control">
                    <button type="button" onclick="changeQty(<?php echo $item['cart_id']; ?>, -1, <?php echo $item['stock']; ?>)"><i class="fas fa-minus"></i></button>
                    <input type="number" id="qty_<?php echo $item['cart_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" readonly>
                    <button type="button" onclick="changeQty(<?php echo $item['cart_id']; ?>, 1, <?php echo $item['stock']; ?>)"><i class="fas fa-plus"></i></button>
                </div>

                <div class="fw-bold" style="min-width:90px; text-align:right;">₹<?php echo number_format($item['line_total'], 2); ?></div>

                <button class="action-btn delete" style="width:34px;height:34px;border-radius:8px;border:none;background:#fee2e2;color:#b91c1c;" title="Remove" onclick="confirmRemove('cart_action_remove.php?id=<?php echo $item['cart_id']; ?>', 'Remove this item from your cart?')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="col-md-4">
        <div class="summary-card">
            <h6 class="fw-bold mb-3">Order Summary</h6>
            <div class="summary-row"><span>Subtotal</span><span>₹<?php echo number_format($subtotal, 2); ?></span></div>
            <div class="summary-row">
                <span>Shipping</span>
                <span><?php echo $shipping == 0 ? 'FREE' : '₹' . number_format($shipping, 2); ?></span>
            </div>
            <?php if ($subtotal < 999 && $subtotal > 0): ?>
                <p class="small text-muted">Add ₹<?php echo number_format(999 - $subtotal, 2); ?> more for free shipping!</p>
            <?php endif; ?>
            <div class="summary-row total"><span>Total</span><span>₹<?php echo number_format($total, 2); ?></span></div>
            <a href="checkout.php" class="btn-store w-100 d-block text-center mt-3">
                <i class="fas fa-lock me-2"></i>Proceed to Checkout
            </a>
            <a href="home.php" class="btn-store-outline w-100 d-block text-center mt-2">
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
