<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MultiVendor E-Commerce Platform</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            display: flex; align-items: center; justify-content: center;
            margin: 0; padding: 20px;
        }
        .hub-wrapper { max-width: 1000px; width: 100%; text-align: center; }
        .hub-title { color: #fff; font-weight: 800; font-size: 2.2rem; margin-bottom: 6px; }
        .hub-subtitle { color: #94a3b8; margin-bottom: 40px; }
        .portal-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; }
        .portal-card {
            background: #fff; border-radius: 18px; padding: 36px 24px; text-decoration: none;
            color: #1e293b; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.2s, box-shadow 0.2s; display: block;
        }
        .portal-card:hover { transform: translateY(-6px); box-shadow: 0 16px 40px rgba(0,0,0,0.3); color: #1e293b; }
        .portal-icon {
            width: 70px; height: 70px; border-radius: 18px; margin: 0 auto 18px;
            display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #fff;
        }
        .portal-icon.customer { background: linear-gradient(135deg, #2563eb, #60a5fa); }
        .portal-icon.vendor   { background: linear-gradient(135deg, #10b981, #34d399); }
        .portal-icon.admin    { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .portal-card h5 { font-weight: 700; margin-bottom: 6px; }
        .portal-card p { color: #64748b; font-size: 0.9rem; margin-bottom: 0; }
        .hub-footer { color: #64748b; font-size: 0.85rem; margin-top: 40px; }
    </style>
</head>
<body>
<div class="hub-wrapper">
    <div class="hub-title"><i class="fas fa-store me-2"></i>MultiVendor E-Commerce</div>
    <p class="hub-subtitle">Choose a portal to continue</p>

    <div class="portal-grid">
        <a href="customer/index.php" class="portal-card">
            <div class="portal-icon customer"><i class="fas fa-shopping-bag"></i></div>
            <h5>Customer</h5>
            <p>Browse products, manage your cart, wishlist & orders</p>
        </a>
        <a href="vendor/index.php" class="portal-card">
            <div class="portal-icon vendor"><i class="fas fa-store-alt"></i></div>
            <h5>Vendor</h5>
            <p>Manage your shop, products & sales</p>
        </a>
        <a href="admin/index.php" class="portal-card">
            <div class="portal-icon admin"><i class="fas fa-user-shield"></i></div>
            <h5>Admin</h5>
            <p>Manage vendors, products, orders & reports</p>
        </a>
    </div>

    <p class="hub-footer">© <?php echo date('Y'); ?> MultiVendor E-Commerce System</p>
</div>
</body>
</html>
