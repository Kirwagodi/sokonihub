<?php
// =============================================
// Sokoni Hub – Cart Page
// =============================================

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'Shopping Cart';
$uid = (int)$_SESSION['user_id'];

// Fetch cart items
$items = mysqli_query($conn, "
    SELECT c.id as cart_id, c.quantity, p.id as product_id, p.name, p.price, p.stock, p.image, cat.name as cat_name
    FROM cart c
    JOIN products p ON c.product_id = p.id
    JOIN categories cat ON p.category_id = cat.id
    WHERE c.user_id = $uid
    ORDER BY c.added_at DESC
");

$total = 0;
$cartEmojis = [
    'Electronics' => '📱', 'Fashion' => '👗',
    'Home & Kitchen' => '🏠', 'Sports & Fitness' => '🏋️', 'Books & Education' => '📚'
];

include 'includes/header.php';
?>

<div class="breadcrumb">
    <a href="index.php">Home</a> <span>›</span> Shopping Cart
</div>

<div class="cart-layout">
    <!-- CART ITEMS -->
    <div>
        <h2 style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;margin-bottom:20px;">
            🛒 Your Cart
        </h2>

        <?php if (mysqli_num_rows($items) > 0): ?>
        <div class="cart-items">
            <?php while ($item = mysqli_fetch_assoc($items)):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <div class="cart-item">
                <div class="cart-item-img">
                    <?= $cartEmojis[$item['cat_name']] ?? '🛍️' ?>
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-name"><?= htmlspecialchars($item['name']) ?></div>
                    <div class="cart-item-price"><?= formatKES($item['price']) ?> each</div>
                    <div class="qty-control">
                        <button class="qty-btn" data-action="decrease" data-cart-id="<?= $item['cart_id'] ?>">−</button>
                        <strong><?= $item['quantity'] ?></strong>
                        <button class="qty-btn" data-action="increase" data-cart-id="<?= $item['cart_id'] ?>">+</button>
                        <button class="remove-btn" data-cart-id="<?= $item['cart_id'] ?>">Remove</button>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:800;color:var(--primary);">
                        <?= formatKES($subtotal) ?>
                    </div>
                    <div style="font-size:0.78rem;color:var(--text-muted);">
                        <?= $item['quantity'] ?> × <?= formatKES($item['price']) ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="empty-state" style="padding:60px 20px;">
            <div class="empty-icon">🛒</div>
            <h3>Your cart is empty</h3>
            <p>Start shopping to add items to your cart.</p>
            <a href="index.php" class="btn-primary" style="display:inline-block;margin-top:20px;">Browse Products</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- ORDER SUMMARY -->
    <?php if (mysqli_num_rows($items) > 0): ?>
    <div>
        <div class="cart-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal</span>
                <span><?= formatKES($total) ?></span>
            </div>
            <div class="summary-row">
                <span>Delivery</span>
                <span><?= $total >= 5000 ? '<span style="color:var(--success)">FREE</span>' : formatKES(300) ?></span>
            </div>
            <?php if ($total < 5000): ?>
            <div style="background:var(--primary-light);padding:12px;border-radius:8px;font-size:0.8rem;color:var(--primary-dark);margin-bottom:8px;">
                Add <?= formatKES(5000 - $total) ?> more for free delivery!
            </div>
            <?php endif; ?>
            <div class="summary-row total">
                <span>Total</span>
                <span><?= formatKES($total < 5000 ? $total + 300 : $total) ?></span>
            </div>

            <form method="POST" action="checkout.php">
                <div class="form-group" style="margin-top:20px;">
                    <label>Delivery Address</label>
                    <textarea name="shipping_address" placeholder="Enter your full delivery address..." required style="background:var(--bg);"></textarea>
                </div>
                <button type="submit" class="btn-submit">
                    Place Order 🎉
                </button>
            </form>

            <div style="display:flex;justify-content:center;gap:16px;margin-top:20px;">
                <span style="font-size:1.5rem;">📱</span>
                <span style="font-size:1.5rem;">💳</span>
                <span style="font-size:1.5rem;">🏦</span>
            </div>
            <p style="text-align:center;font-size:0.75rem;color:var(--text-muted);margin-top:8px;">M-Pesa · Visa · Mastercard · Bank Transfer</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
