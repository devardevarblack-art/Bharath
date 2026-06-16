<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'My Products';
$vendor_id = getVendorId();

// Toggle status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $pid = (int)$_GET['toggle'];
    mysqli_query($conn, "UPDATE products SET status = IF(status='active','inactive','active') WHERE id=$pid AND vendor_id=$vendor_id");
    header("Location: products.php");
    exit();
}

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$pid AND vendor_id=$vendor_id");
    header("Location: products.php");
    exit();
}

$products = mysqli_query($conn, "
    SELECT p.*, c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON c.id = p.category_id
    WHERE p.vendor_id = $vendor_id
    ORDER BY p.created_at DESC
");

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">My Products</h4>
        <p class="page-subtitle">Manage your product listings</p>
    </div>
    <a href="add_product.php" class="btn btn-success rounded-pill px-4">
        <i class="fas fa-plus me-2"></i>Add Product
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table datatable mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Original</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (mysqli_num_rows($products) === 0): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">No products yet. <a href="add_product.php">Add your first product</a></td></tr>
                <?php else: ?>
                <?php $i = 1; while ($p = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td>
                        <?php if ($p['image'] && file_exists('../uploads/products/' . $p['image'])): ?>
                            <img src="../uploads/products/<?php echo htmlspecialchars($p['image']); ?>" style="width:36px;height:36px;border-radius:8px;object-fit:cover;" class="me-2">
                        <?php else: ?>
                            <span class="me-2" style="width:36px;height:36px;border-radius:8px;background:#f1f5f9;display:inline-flex;align-items:center;justify-content:center;"><i class="fas fa-box text-muted small"></i></span>
                        <?php endif; ?>
                        <span class="fw-semibold"><?php echo htmlspecialchars($p['name']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?></td>
                    <td class="fw-semibold text-success">₹<?php echo number_format($p['price'], 2); ?></td>
                    <td class="text-muted"><s>₹<?php echo $p['original_price'] ? number_format($p['original_price'], 2) : '-'; ?></s></td>
                    <td>
                        <?php if ($p['stock'] <= 5): ?>
                            <span class="text-danger fw-semibold"><?php echo $p['stock']; ?></span>
                        <?php else: ?>
                            <?php echo $p['stock']; ?>
                        <?php endif; ?>
                    </td>
                    <td><span class="status-badge badge-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                    <td>
                        <a href="add_product.php?edit=<?php echo $p['id']; ?>" class="action-btn edit me-1" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="products.php?toggle=<?php echo $p['id']; ?>" class="action-btn view me-1" title="Toggle Status"><i class="fas fa-toggle-on"></i></a>
                        <button onclick="confirmDelete('products.php?delete=<?php echo $p['id']; ?>')" class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
