<?php
// =============================================
// Sokoni Hub – My Orders
// =============================================

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();

$pageTitle = 'My Orders';
$uid = (int)$_SESSION['user_id'];

$orders = mysqli_query($conn, "
    SELECT o.*, COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = $uid
    GROUP BY o.id
    ORDER BY o.created_at DESC
");

include 'includes/header.php';
?>

<div class="orders-page">
    <div class="breadcrumb" style="padding:16px 0;background:none;">
        <a href="index.php">Home</a> <span>›</span> My Orders
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        🎉 Order #<?= (int)$_GET['order_id'] ?> placed successfully! We'll process it shortly. Thank you for shopping with Sokoni Hub!
    </div>
    <?php endif; ?>

    <h2>My Orders</h2>

    <?php if (mysqli_num_rows($orders) > 0): ?>
        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-id">Order #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></div>
                    <div style="font-size:0.82rem;color:var(--text-muted);margin-top:4px;">
                        📅 <?= date('D, d M Y H:i', strtotime($order['created_at'])) ?>
                    </div>
                </div>
                <span class="order-status status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
            </div>

            <div style="display:flex;gap:32px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;">Items</div>
                    <div style="font-weight:600;"><?= $order['item_count'] ?> item(s)</div>
                </div>
                <div>
                    <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;">Total</div>
                    <div style="font-weight:800;color:var(--primary);font-family:var(--font-display);"><?= formatKES($order['total_amount']) ?></div>
                </div>
                <div style="flex:1;">
                    <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;">Delivery Address</div>
                    <div style="font-size:0.9rem;"><?= htmlspecialchars($order['shipping_address']) ?></div>
                </div>
            </div>

            <?php
            // Fetch order items
            $orderItems = mysqli_query($conn, "
                SELECT oi.*, p.name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = {$order['id']}
            ");
            ?>
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                <div style="font-size:0.78rem;color:var(--text-muted);text-transform:uppercase;font-weight:600;margin-bottom:8px;">Products</div>
                <?php while ($oi = mysqli_fetch_assoc($orderItems)): ?>
                <div style="display:flex;justify-content:space-between;font-size:0.88rem;padding:4px 0;">
                    <span><?= htmlspecialchars($oi['name']) ?> × <?= $oi['quantity'] ?></span>
                    <span style="font-weight:600;"><?= formatKES($oi['unit_price'] * $oi['quantity']) ?></span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>No orders yet</h3>
        <p>You haven't placed any orders. Start shopping!</p>
        <a href="index.php" class="btn-primary" style="display:inline-block;margin-top:20px;">Shop Now →</a>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
