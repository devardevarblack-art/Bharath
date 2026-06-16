<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$edit_id   = isset($_GET['edit']) && is_numeric($_GET['edit']) ? (int)$_GET['edit'] : null;
$product   = null;
$pageTitle = $edit_id ? 'Edit Product' : 'Add Product';

if ($edit_id) {
    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$edit_id LIMIT 1"));
    if (!$product) { header("Location: products.php"); exit(); }
}

$vendors    = mysqli_query($conn, "SELECT id, name, business_name FROM vendors ORDER BY business_name, name");
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE status=1 ORDER BY name");

$msg   = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id    = (int)$_POST['vendor_id'];
    $name         = mysqli_real_escape_string($conn, trim($_POST['name']));
    $desc         = mysqli_real_escape_string($conn, trim($_POST['description']));
    $price        = (float)$_POST['price'];
    $orig_price   = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : 'NULL';
    $stock        = (int)$_POST['stock'];
    $category_id  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : 'NULL';
    $status       = $_POST['status'] === 'inactive' ? 'inactive' : 'active';

    if ($vendor_id <= 0) {
        $error = 'Please select a vendor.';
    } elseif ($name === '') {
        $error = 'Product name is required.';
    } else {
        $image_val = $product['image'] ?? '';
        if (!empty($_FILES['image']['name'])) {
            $allowed = ['jpg','jpeg','png','webp','gif'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $fname = 'prod_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $dest  = '../../vendor/uploads/products/' . $fname;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image_val = $fname;
                }
            } else {
                $error = 'Invalid image format. Use JPG, PNG, WEBP or GIF.';
            }
        }

        if (!$error) {
            if ($edit_id) {
                mysqli_query($conn, "
                    UPDATE products SET
                        vendor_id=$vendor_id,
                        category_id=" . ($category_id === 'NULL' ? 'NULL' : $category_id) . ",
                        name='$name',
                        description='$desc',
                        price=$price,
                        original_price=" . ($orig_price === 'NULL' ? 'NULL' : $orig_price) . ",
                        stock=$stock,
                        status='$status',
                        image='" . mysqli_real_escape_string($conn, $image_val) . "'
                    WHERE id=$edit_id
                ");
                header("Location: product_view.php?id=$edit_id&msg=updated");
                exit();
            } else {
                mysqli_query($conn, "
                    INSERT INTO products (vendor_id, category_id, name, description, price, original_price, stock, image, status)
                    VALUES ($vendor_id, " . ($category_id === 'NULL' ? 'NULL' : $category_id) . ", '$name', '$desc', $price, " . ($orig_price === 'NULL' ? 'NULL' : $orig_price) . ", $stock, '" . mysqli_real_escape_string($conn, $image_val) . "', '$status')
                ");
                $new_id = mysqli_insert_id($conn);
                header("Location: product_view.php?id=$new_id&msg=added");
                exit();
            }
        }
    }
}

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title"><?php echo $pageTitle; ?></h4>
        <p class="page-subtitle"><?php echo $edit_id ? 'Update product details' : 'Add a new product to the catalog'; ?></p>
    </div>
    <a href="products.php" class="btn btn-outline-secondary rounded-pill px-3 small">
        <i class="fas fa-arrow-left me-1"></i> Back to Products
    </a>
</div>

<?php if ($error): ?><div class="alert alert-danger py-2 small"><?php echo $error; ?></div><?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Vendor <span class="text-danger">*</span></label>
                    <select name="vendor_id" class="form-select" required>
                        <option value="">-- Select Vendor --</option>
                        <?php while ($v = mysqli_fetch_assoc($vendors)): ?>
                            <option value="<?php echo $v['id']; ?>" <?php echo (isset($product['vendor_id']) && $product['vendor_id'] == $v['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($v['business_name'] ?: $v['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Select Category --</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div class="form-text">Need a new category? <a href="categories.php">Manage categories</a></div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the product..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Selling Price (₹) <span class="text-danger">*</span></label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0" required value="<?php echo $product['price'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Original Price (₹)</label>
                    <input type="number" name="original_price" class="form-control" step="0.01" min="0" placeholder="Optional MRP" value="<?php echo $product['original_price'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                    <input type="number" name="stock" class="form-control" min="0" required value="<?php echo $product['stock'] ?? 0; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?php echo (!$edit_id || $product['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo (isset($product['status']) && $product['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Product Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                    <div class="mt-2">
                        <?php if ($edit_id && !empty($product['image'])): ?>
                            <img src="../../vendor/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" id="imgPreview" class="show" alt="Product">
                        <?php else: ?>
                            <img id="imgPreview" src="" alt="Preview">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-save me-2"></i><?php echo $edit_id ? 'Update Product' : 'Add Product'; ?>
                </button>
                <a href="products.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
