<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'Product Details';
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

$product = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT p.*, v.name AS vendor_name, v.business_name, v.email AS vendor_email,
           c.name AS category_name
    FROM products p
    LEFT JOIN vendors v ON v.id = p.vendor_id
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.id = $id
    LIMIT 1
"));

if (!$product) {
    header("Location: products.php");
    exit();
}

// Sales stats for this product
$stats = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(oi.quantity), 0) AS total_sold, COALESCE(SUM(oi.quantity * oi.price), 0) AS total_revenue
    FROM order_items oi WHERE oi.product_id = $id
"));

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Product Details</h4>
        <p class="page-subtitle">Full information about this product</p>
    </div>
    <a href="products.php" class="btn btn-outline-secondary rounded-pill px-3 small">
        <i class="fas fa-arrow-left me-1"></i> Back to Products
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success py-2 small">
        <?php echo $_GET['msg'] === 'added' ? 'Product added successfully!' : 'Product updated successfully!'; ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="product-detail-img">
            <?php if ($product['image'] && file_exists('../../vendor/uploads/products/' . $product['image'])): ?>
                <img src="../../vendor/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">
            <?php else: ?>
                <i class="fas fa-image text-muted" style="font-size:3rem;"></i>
            <?php endif; ?>
        </div>

        <div class="d-flex gap-2 mt-3">
            <a href="add_product.php?edit=<?php echo $product['id']; ?>" class="btn btn-primary rounded-pill px-4 flex-fill">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <button class="btn btn-outline-danger rounded-pill px-4" onclick="confirmDelete('products.php?delete=<?php echo $product['id']; ?>','Delete this product permanently?')">
                <i class="fas fa-trash me-2"></i>Delete
            </button>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-6">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-shopping-bag"></i></div>
                    <div>
                        <div class="stat-value"><?php echo (int)$stats['total_sold']; ?></div>
                        <div class="stat-label">Units Sold</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fas fa-rupee-sign"></i></div>
                    <div>
                        <div class="stat-value">₹<?php echo number_format($stats['total_revenue'], 0); ?></div>
                        <div class="stat-label">Revenue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($product['name']); ?></h4>
                    <span class="status-badge <?php echo $product['status'] === 'active' ? 'badge-approved' : 'badge-inactive'; ?>">
                        <?php echo ucfirst($product['status']); ?>
                    </span>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="detail-label">Selling Price</div>
                        <div class="detail-value fw-bold text-success">₹<?php echo number_format($product['price'], 2); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Original Price</div>
                        <div class="detail-value">
                            <?php echo $product['original_price'] ? '₹' . number_format($product['original_price'], 2) : '—'; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Stock</div>
                        <div class="detail-value">
                            <span class="badge <?php echo $product['stock'] > 10 ? 'bg-success' : ($product['stock'] > 0 ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                <?php echo $product['stock']; ?> units
                            </span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="detail-label">Category</div>
                        <div class="detail-value"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Vendor</div>
                        <div class="detail-value"><?php echo htmlspecialchars($product['business_name'] ?? $product['vendor_name'] ?? '-'); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Vendor Email</div>
                        <div class="detail-value"><?php echo htmlspecialchars($product['vendor_email'] ?? '-'); ?></div>
                    </div>

                    <div class="col-md-4">
                        <div class="detail-label">Product ID</div>
                        <div class="detail-value">#<?php echo $product['id']; ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="detail-label">Added On</div>
                        <div class="detail-value"><?php echo date('d M Y, h:i A', strtotime($product['created_at'])); ?></div>
                    </div>

                    <div class="col-12">
                        <div class="detail-label">Description</div>
                        <div class="detail-value">
                            <?php echo $product['description'] ? nl2br(htmlspecialchars($product['description'])) : '<span class="text-muted">No description provided.</span>'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
