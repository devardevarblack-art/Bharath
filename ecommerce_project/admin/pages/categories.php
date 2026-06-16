<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'Product Categories';

// ---- Delete ----
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    header("Location: categories.php?msg=deleted");
    exit();
}

// ---- Toggle status ----
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    mysqli_query($conn, "UPDATE categories SET status = IF(status=1,0,1) WHERE id=$id");
    header("Location: categories.php?msg=updated");
    exit();
}

$edit_id  = isset($_GET['edit']) && is_numeric($_GET['edit']) ? (int)$_GET['edit'] : null;
$category = null;
$error    = '';

if ($edit_id) {
    $category = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM categories WHERE id=$edit_id LIMIT 1"));
    if (!$category) { header("Location: categories.php"); exit(); }
}

// ---- Add / Update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $name      = trim($_POST['name']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : 'NULL';
    $status    = isset($_POST['status']) ? 1 : 0;
    $save_id   = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        // Generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-'));
        $slugCheck = $save_id
            ? "SELECT id FROM categories WHERE slug='$slug' AND id != $save_id LIMIT 1"
            : "SELECT id FROM categories WHERE slug='$slug' LIMIT 1";
        if (mysqli_num_rows(mysqli_query($conn, $slugCheck)) > 0) {
            $slug .= '-' . time();
        }

        $name = mysqli_real_escape_string($conn, $name);
        $slug = mysqli_real_escape_string($conn, $slug);

        if ($save_id) {
            if ($parent_id !== 'NULL' && $parent_id == $save_id) {
                $error = 'A category cannot be its own parent.';
            } else {
                mysqli_query($conn, "
                    UPDATE categories SET name='$name', slug='$slug',
                        parent_id=" . ($parent_id === 'NULL' ? 'NULL' : $parent_id) . ", status=$status
                    WHERE id=$save_id
                ");
                header("Location: categories.php?msg=updated");
                exit();
            }
        } else {
            mysqli_query($conn, "
                INSERT INTO categories (name, slug, parent_id, status)
                VALUES ('$name', '$slug', " . ($parent_id === 'NULL' ? 'NULL' : $parent_id) . ", $status)
            ");
            header("Location: categories.php?msg=added");
            exit();
        }
    }
}

$search = isset($_GET['s']) ? mysqli_real_escape_string($conn, trim($_GET['s'])) : '';
$where  = $search ? "WHERE c.name LIKE '%$search%'" : '';

$categories = mysqli_query($conn, "
    SELECT c.*, p.name AS parent_name,
           (SELECT COUNT(*) FROM products pr WHERE pr.category_id = c.id) AS product_count
    FROM categories c
    LEFT JOIN categories p ON p.id = c.parent_id
    $where
    ORDER BY c.name
");

$parentOptions = mysqli_query($conn, "SELECT id, name FROM categories " . ($edit_id ? "WHERE id != $edit_id " : "") . "ORDER BY name");

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Product Categories</h4>
        <p class="page-subtitle">Organize products into categories</p>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success py-2 small">
        <?php
            $m = $_GET['msg'];
            echo $m === 'added' ? 'Category added successfully!' : ($m === 'deleted' ? 'Category deleted.' : 'Category updated.');
        ?>
    </div>
<?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger py-2 small"><?php echo $error; ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6><?php echo $edit_id ? 'Edit Category' : 'Add New Category'; ?></h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if ($edit_id): ?>
                        <input type="hidden" name="category_id" value="<?php echo $edit_id; ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" placeholder="e.g. Electronics">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Parent Category</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- None (Top Level) --</option>
                            <?php while ($p = mysqli_fetch_assoc($parentOptions)): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo (isset($category['parent_id']) && $category['parent_id'] == $p['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="status" id="catStatus" class="form-check-input" <?php echo (!$edit_id || $category['status']) ? 'checked' : ''; ?>>
                        <label for="catStatus" class="form-check-label small">Active (visible to customers)</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="save_category" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save me-2"></i><?php echo $edit_id ? 'Update' : 'Add Category'; ?>
                        </button>
                        <?php if ($edit_id): ?>
                            <a href="categories.php" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body py-3">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="s" class="form-control form-control-sm" placeholder="Search categories..." value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-sm btn-primary"><i class="fas fa-search me-1"></i>Search</button>
                    <?php if ($search): ?><a href="categories.php" class="btn btn-sm btn-outline-secondary">Clear</a><?php endif; ?>
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
                                <th>Category</th>
                                <th>Parent</th>
                                <th>Products</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($categories) === 0): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">No categories found.</td></tr>
                            <?php else: ?>
                                <?php $i = 1; while ($c = mysqli_fetch_assoc($categories)): ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($c['name']); ?></td>
                                    <td class="small text-muted"><?php echo htmlspecialchars($c['parent_name'] ?? '—'); ?></td>
                                    <td><span class="badge bg-light text-dark"><?php echo $c['product_count']; ?></span></td>
                                    <td>
                                        <span class="status-badge <?php echo $c['status'] ? 'badge-approved' : 'badge-inactive'; ?>">
                                            <?php echo $c['status'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="categories.php?edit=<?php echo $c['id']; ?>" class="action-btn edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="categories.php?toggle=<?php echo $c['id']; ?>" class="action-btn <?php echo $c['status'] ? 'reject' : 'approve'; ?>" title="Toggle Status">
                                                <i class="fas <?php echo $c['status'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                            </a>
                                            <button class="action-btn delete" title="Delete" onclick="confirmDelete('categories.php?delete=<?php echo $c['id']; ?>','Delete this category? Products in it will become uncategorized.')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
