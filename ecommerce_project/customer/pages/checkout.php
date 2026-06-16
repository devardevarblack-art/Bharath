<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'Checkout';
$cid = getCustomerId();

$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE id=$cid LIMIT 1"));

$items = mysqli_query($conn, "
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.stock, p.image
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.customer_id = $cid
");

$cartItems = [];
$subtotal  = 0;
while ($row = mysqli_fetch_assoc($items)) {
    $row['line_total'] = $row['price'] * $row['quantity'];
    $subtotal += $row['line_total'];
    $cartItems[] = $row;
}

if (empty($cartItems)) {
    header("Location: cart.php");
    exit();
}

$shipping = $subtotal >= 999 ? 0 : 49;
$total    = $subtotal + $shipping;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = mysqli_real_escape_string($conn, trim($_POST['shipping_address']));
    $payment = $_POST['payment_method'];
    $notes   = mysqli_real_escape_string($conn, trim($_POST['notes']));

    $allowedPayments = ['cod', 'online', 'upi', 'card'];
    if ($address === '') {
        $error = 'Please enter a shipping address.';
    } elseif (!in_array($payment, $allowedPayments)) {
        $error = 'Please select a valid payment method.';
    } else {
        // Re-check stock availability before placing order
        $stockOk = true;
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock']) {
                $stockOk = false;
                break;
            }
        }

        if (!$stockOk) {
            $error = 'One or more items in your cart exceed available stock. Please update your cart.';
        } else {
            $paymentStatus = ($payment === 'cod') ? 'pending' : 'paid';

            mysqli_query($conn, "
                INSERT INTO orders (customer_id, total_amount, payment_method, payment_status, status, shipping_address, notes)
                VALUES ($cid, $total, '$payment', '$paymentStatus', 'pending', '$address', '$notes')
            ");
            $order_id = mysqli_insert_id($conn);

            foreach ($cartItems as $item) {
                mysqli_query($conn, "
                    INSERT INTO order_items (order_id, product_id, quantity, price)
                    VALUES ($order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']})
                ");
                // Reduce stock
                mysqli_query($conn, "UPDATE products SET stock = stock - {$item['quantity']} WHERE id = {$item['product_id']}");
            }

            // Clear cart
            mysqli_query($conn, "DELETE FROM cart WHERE customer_id=$cid");

            header("Location: order_success.php?id=$order_id");
            exit();
        }
    }
}

require_once '../includes/header.php';
?>

<h4 class="page-title mb-4"><i class="fas fa-lock me-2"></i>Checkout</h4>

<?php if ($error): ?>
    <div class="alert alert-danger small"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
<div class="row g-4">
    <div class="col-md-8">
        <div class="summary-card mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-truck me-2"></i>Shipping Address</h6>
            <textarea name="shipping_address" class="form-control" rows="3" required placeholder="Full delivery address with pincode"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
        </div>

        <div class="summary-card mb-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-credit-card me-2"></i>Payment Method</h6>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="cod" checked>
                <label class="form-check-label" for="pay_cod"><i class="fas fa-money-bill-wave me-2 text-success"></i>Cash on Delivery</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_upi" value="upi">
                <label class="form-check-label" for="pay_upi"><i class="fas fa-mobile-alt me-2 text-primary"></i>UPI</label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_card" value="card">
                <label class="form-check-label" for="pay_card"><i class="fas fa-credit-card me-2 text-info"></i>Debit / Credit Card</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="pay_online" value="online">
                <label class="form-check-label" for="pay_online"><i class="fas fa-globe me-2 text-warning"></i>Net Banking</label>
            </div>
        </div>

        <div class="summary-card">
            <h6 class="fw-bold mb-3"><i class="fas fa-sticky-note me-2"></i>Order Notes (optional)</h6>
            <textarea name="notes" class="form-control" rows="2" placeholder="Any special instructions for delivery..."></textarea>
        </div>
    </div>

    <div class="col-md-4">
        <div class="summary-card">
            <h6 class="fw-bold mb-3">Order Summary</h6>
            <?php foreach ($cartItems as $item): ?>
                <div class="summary-row">
                    <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                    <span>₹<?php echo number_format($item['line_total'], 2); ?></span>
                </div>
            <?php endforeach; ?>
            <hr>
            <div class="summary-row"><span>Subtotal</span><span>₹<?php echo number_format($subtotal, 2); ?></span></div>
            <div class="summary-row"><span>Shipping</span><span><?php echo $shipping == 0 ? 'FREE' : '₹' . number_format($shipping, 2); ?></span></div>
            <div class="summary-row total"><span>Total</span><span>₹<?php echo number_format($total, 2); ?></span></div>
            <button type="submit" class="btn-store w-100 mt-3">
                <i class="fas fa-check-circle me-2"></i>Place Order
            </button>
            <a href="cart.php" class="btn-store-outline w-100 d-block text-center mt-2">Back to Cart</a>
        </div>
    </div>
</div>
</form>

<?php require_once '../includes/footer.php'; ?>
