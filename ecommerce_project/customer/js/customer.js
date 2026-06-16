// Password show/hide toggle
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

// Confirm before destructive actions
function confirmRemove(url, message) {
    if (confirm(message || 'Remove this item?')) {
        window.location.href = url;
    }
}

// ===== Add to cart (AJAX) =====
function addToCart(productId, qty, btn, redirectCheckout = false) {
    qty = qty || 1;
    const original = btn ? btn.innerHTML : null;
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }

    fetch('cart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=add&product_id=${encodeURIComponent(productId)}&qty=${encodeURIComponent(qty)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'login_required') {
            if (redirectCheckout) {
                window.location.href = '../index.php?redirect=pages/checkout.php';
            } else {
                window.location.href = '../index.php';
            }
            return;
        }
        if (data.status === 'ok') {
            updateBadge('cartBadge', data.cart_count);
            if (redirectCheckout) {
                window.location.href = 'checkout.php';
                return;
            }
            if (btn) { btn.innerHTML = '<i class="fas fa-check"></i> Added'; }
            setTimeout(() => { if (btn) { btn.disabled = false; btn.innerHTML = original; } }, 1200);
        } else {
            alert(data.message || 'Could not add product to cart.');
            if (btn) { btn.disabled = false; btn.innerHTML = original; }
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.innerHTML = original; }
        alert('Something went wrong. Please try again.');
    });
}

// ===== Toggle wishlist (AJAX) =====
function toggleWishlist(productId, btn) {
    fetch('wishlist_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=toggle&product_id=${encodeURIComponent(productId)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'login_required') {
            window.location.href = '../index.php';
            return;
        }
        if (data.status === 'ok') {
            updateBadge('wishlistBadge', data.wishlist_count);
            if (btn) btn.classList.toggle('active', data.added);
        }
    });
}

function updateBadge(id, count) {
    const el = document.getElementById(id);
    if (!el) return;
    if (count > 0) {
        el.textContent = count;
        el.style.display = 'flex';
    } else {
        el.style.display = 'none';
    }
}

// ===== Cart quantity controls =====
function changeQty(cartId, delta, max) {
    const input = document.getElementById('qty_' + cartId);
    if (!input) return;
    let val = parseInt(input.value || '1') + delta;
    if (val < 1) val = 1;
    if (max && val > max) val = max;
    input.value = val;
    updateCartQty(cartId, val);
}

function updateCartQty(cartId, qty) {
    fetch('cart_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=update&cart_id=${encodeURIComponent(cartId)}&qty=${encodeURIComponent(qty)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'ok') {
            window.location.reload();
        }
    });
}
