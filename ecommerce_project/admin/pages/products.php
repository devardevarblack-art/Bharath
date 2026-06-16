<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();
$pageTitle = 'Product Management';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header("Location: products.php?msg=deleted"); exit();
}
// Toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    mysqli_query($conn, "UPDATE products SET status = IF(status='active','inactive','active') WHERE id=$id");
    header("Location: products.php?msg=updated"); exit();
}

$search   = isset($_GET['s']) ? mysqli_real_escape_string($conn, $_GET['s']) : '';
$catFilter = isset($_GET['cat']) && is_numeric($_GET['cat']) ? (int)$_GET['cat'] : 0;

$conditions = [];
if ($search)   $conditions[] = "(p.name LIKE '%$search%' OR v.name LIKE '%$search%' OR v.business_name LIKE '%$search%')";
if ($catFilter) $conditions[] = "p.category_id = $catFilter";
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$products = mysqli_query($conn, "SELECT p.*, v.name as vendor_name, c.name as category_name FROM products p LEFT JOIN vendors v ON p.vendor_id=v.id LEFT JOIN categories c ON p.category_id=c.id $where ORDER BY p.created_at DESC");
$categoryList = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Product Management</h4>
        <p class="page-subtitle">View and manage all vendor products</p>
    </div>
    <a href="add_product.php" class="btn btn-primary rounded-pill px-4">
        <i class="fas fa-plus me-2"></i>Add Product
    </a>
</div>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success"><?php echo $_GET['msg']==='deleted'?'Product deleted.':'Product updated.'; ?></div>
<?php endif; ?>

<!-- Search -->
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="d-flex gap-2 flex-wrap">
            <input type="text" name="s" class="form-control form-control-sm" style="max-width:240px;" placeholder="Search by product or vendor..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="cat" class="form-select form-select-sm" style="max-width:200px;">
                <option value="">All Categories</option>
                <?php while ($c = mysqli_fetch_assoc($categoryList)): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo $catFilter == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                <?php endwhile; ?>
            </select>
            <button class="btn btn-sm btn-primary"><i class="fas fa-search me-1"></i>Search</button>
            <?php if ($search || $catFilter): ?><a href="products.php" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Vendor</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($products && mysqli_num_rows($products) > 0):
                        $i=1; while($p = mysqli_fetch_assoc($products)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if($p['image'] && file_exists('../../vendor/uploads/products/' . $p['image'])): ?>
                                <img src="../../vendor/uploads/products/<?php echo $p['image']; ?>" class="vendor-logo" alt="">
                                <?php else: ?>
                                <div class="vendor-logo d-flex align-items-center justify-content-center text-muted"><i class="fas fa-box"></i></div>
                                <?php endif; ?>
                                <span class="small fw-semibold"><?php echo htmlspecialchars($p['name']); ?></span>
                            </div>
                        </td>
                        <td class="small"><?php echo htmlspecialchars($p['vendor_name'] ?? '-'); ?></td>
                        <td class="small"><?php echo htmlspecialchars($p['category_name'] ?? '-'); ?></td>
                        <td class="small fw-semibold">₹<?php echo number_format($p['price'], 2); ?></td>
                        <td>
                            <span class="badge <?php echo $p['stock'] > 10 ? 'bg-success' : ($p['stock'] > 0 ? 'bg-warning text-dark' : 'bg-danger'); ?>">
                                <?php echo $p['stock']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $p['status']==='active'?'badge-approved':'badge-inactive'; ?>">
                                <?php echo ucfirst($p['status']); ?>
                            </span>
                        </td>
                        <td class="small"><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="product_view.php?id=<?php echo $p['id']; ?>" class="action-btn view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add_product.php?edit=<?php echo $p['id']; ?>" class="action-btn edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?toggle=<?php echo $p['id']; ?>" class="action-btn <?php echo $p['status']==='active'?'reject':'approve'; ?>" title="Toggle Status">
                                    <i class="fas <?php echo $p['status']==='active'?'fa-eye-slash':'fa-eye'; ?>"></i>
                                </a>
                                <button class="action-btn delete" onclick="confirmDelete('?delete=<?php echo $p['id']; ?>','Delete this product?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="9" class="text-center py-4 text-muted">No products found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
