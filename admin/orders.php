<?php

require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $oid    = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);
    $allowed = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$oid");
    }
    header('Location: orders.php?msg=Order+updated');
    exit();
}

$orders = mysqli_query($conn, "
    SELECT o.*, u.username, u.full_name, u.email, COUNT(oi.id) as item_count
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="brand"><span class="logo-s">S</span>okoni<span class="logo-hub"> Hub</span><small>Admin Panel</small></div>
        <nav class="admin-nav">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="products.php">📦 Products</a>
            <a href="orders.php" class="active">🧾 Orders</a>
            <a href="users.php">👥 Users</a>
            <a href="add_product.php">➕ Add Product</a>
            <a href="../index.php">🏠 View Store</a>
            <a href="../logout.php" style="color:rgba(255,100,80,0.8);">🚪 Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <h2>Manage Orders</h2>

        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($orders) > 0): ?>
                <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                <tr>
                    <td><strong>#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                    <td>
                        <strong><?= htmlspecialchars($o['full_name'] ?: $o['username']) ?></strong><br>
                        <small style="color:var(--text-muted);"><?= htmlspecialchars($o['email']) ?></small>
                    </td>
                    <td><?= $o['item_count'] ?></td>
                    <td style="font-weight:700;color:var(--primary);"><?= formatKES($o['total_amount']) ?></td>
                    <td style="font-size:0.82rem;"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                    <td><span class="order-status status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                    <td>
                        <form method="POST" action="orders.php" style="display:flex;gap:8px;align-items:center;">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <select name="status" style="padding:6px 10px;border:2px solid var(--border);border-radius:8px;font-size:0.82rem;font-family:var(--font-body);cursor:pointer;outline:none;">
                                <option value="pending"     <?= $o['status']=='pending'     ? 'selected' : '' ?>>Pending</option>
                                <option value="processing"  <?= $o['status']=='processing'  ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped"     <?= $o['status']=='shipped'     ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered"   <?= $o['status']=='delivered'   ? 'selected' : '' ?>>Delivered</option>
                                <option value="cancelled"   <?= $o['status']=='cancelled'   ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" style="padding:6px 14px;background:var(--primary);color:white;border:none;border-radius:8px;cursor:pointer;font-weight:600;font-size:0.82rem;">Save</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:40px;">No orders yet</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
