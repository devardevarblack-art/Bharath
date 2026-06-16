<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();
$pageTitle = 'Vendor Approval';

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $action = $_GET['action'];
    $status = '';
    if ($action === 'approve') $status = 'approved';
    elseif ($action === 'reject') $status = 'rejected';
    elseif ($action === 'delete') {
        mysqli_query($conn, "DELETE FROM vendors WHERE id=$id");
        header("Location: vendors.php?msg=deleted"); exit();
    }
    if ($status) {
        mysqli_query($conn, "UPDATE vendors SET status='$status' WHERE id=$id");
        header("Location: vendors.php?msg=$status"); exit();
    }
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where  = $filter !== 'all' ? "WHERE v.status='$filter'" : '';
$vendors = mysqli_query($conn, "SELECT v.*, COUNT(p.id) as product_count FROM vendors v LEFT JOIN products p ON v.id=p.vendor_id $where GROUP BY v.id ORDER BY v.created_at DESC");

include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Vendor Approval</h4>
        <p class="page-subtitle">Manage vendor registrations and approvals</p>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
<div class="alert alert-success">
    <?php
    $msgs = ['approved'=>'Vendor approved successfully!','rejected'=>'Vendor rejected.','deleted'=>'Vendor deleted.'];
    echo $msgs[$_GET['msg']] ?? 'Action completed.';
    ?>
</div>
<?php endif; ?>

<!-- Filter Tabs -->
<ul class="nav nav-pills mb-3">
    <?php foreach(['all'=>'All','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $k=>$v): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo $filter===$k?'active':''; ?>" href="?filter=<?php echo $k; ?>"><?php echo $v; ?></a>
    </li>
    <?php endforeach; ?>
</ul>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Vendor</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Business</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($vendors && mysqli_num_rows($vendors) > 0):
                        $i=1; while($v = mysqli_fetch_assoc($vendors)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($v['name']); ?>&background=6366f1&color=fff&size=36" class="avatar-sm">
                                <div>
                                    <div class="fw-semibold small"><?php echo htmlspecialchars($v['name']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="small"><?php echo htmlspecialchars($v['email']); ?></td>
                        <td class="small"><?php echo htmlspecialchars($v['phone'] ?? '-'); ?></td>
                        <td class="small"><?php echo htmlspecialchars($v['business_name'] ?? '-'); ?></td>
                        <td><span class="badge bg-light text-dark"><?php echo $v['product_count']; ?></span></td>
                        <td>
                            <?php
                            $sc = ['pending'=>'badge-pending','approved'=>'badge-approved','rejected'=>'badge-rejected'];
                            $cls = $sc[$v['status']] ?? 'badge-inactive';
                            ?>
                            <span class="status-badge <?php echo $cls; ?>"><?php echo ucfirst($v['status']); ?></span>
                        </td>
                        <td class="small"><?php echo date('d M Y', strtotime($v['created_at'])); ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if($v['status']==='pending'): ?>
                                <button class="action-btn approve" title="Approve" onclick="confirmAction('?action=approve&id=<?php echo $v['id']; ?>','Approve this vendor?')"><i class="fas fa-check"></i></button>
                                <button class="action-btn reject"  title="Reject"  onclick="confirmAction('?action=reject&id=<?php echo $v['id']; ?>','Reject this vendor?')"><i class="fas fa-times"></i></button>
                                <?php elseif($v['status']==='approved'): ?>
                                <button class="action-btn reject"  title="Suspend" onclick="confirmAction('?action=reject&id=<?php echo $v['id']; ?>','Suspend this vendor?')"><i class="fas fa-ban"></i></button>
                                <?php elseif($v['status']==='rejected'): ?>
                                <button class="action-btn approve" title="Re-approve" onclick="confirmAction('?action=approve&id=<?php echo $v['id']; ?>','Re-approve this vendor?')"><i class="fas fa-redo"></i></button>
                                <?php endif; ?>
                                <button class="action-btn delete" title="Delete" onclick="confirmDelete('?action=delete&id=<?php echo $v['id']; ?>','Permanently delete this vendor?')"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="9" class="text-center py-4 text-muted">No vendors found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
