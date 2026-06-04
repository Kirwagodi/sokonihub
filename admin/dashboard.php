<?php
// =============================================
// Sokoni Hub – Admin Dashboard
// =============================================

require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$pageTitle = 'Admin Dashboard';

// Stats
$totalUsers    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM users WHERE role='customer'"))['c'];
$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$totalOrders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$totalRevenue  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) as c FROM orders WHERE status != 'cancelled'"))['c'];

// Recent orders
$recentOrders = mysqli_query($conn, "
    SELECT o.*, u.username, u.full_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");

// Low stock
$lowStock = mysqli_query($conn, "SELECT * FROM products WHERE stock <= 5 ORDER BY stock ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Sokoni Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="brand">
            <span class="logo-s">S</span>okoni<span class="logo-hub"> Hub</span>
            <small>Admin Panel</small>
        </div>
        <nav class="admin-nav">
            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="products.php">📦 Products</a>
            <a href="orders.php">🧾 Orders</a>
            <a href="users.php">👥 Users</a>
            <a href="add_product.php">➕ Add Product</a>
            <a href="../index.php" style="margin-top:auto;">🏠 View Store</a>
            <a href="../logout.php" style="color:rgba(255,100,80,0.8);">🚪 Logout</a>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">
        <h2>Dashboard Overview</h2>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?= number_format($totalUsers) ?></div>
                <div class="stat-label">Total Customers</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-value"><?= number_format($totalProducts) ?></div>
                <div class="stat-label">Products Listed</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🧾</div>
                <div class="stat-value"><?= number_format($totalOrders) ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card" style="border-left:4px solid var(--primary);">
                <div class="stat-icon">💰</div>
                <div class="stat-value" style="color:var(--primary);font-size:1.4rem;"><?= formatKES($totalRevenue) ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;">

            <!-- RECENT ORDERS -->
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <h3 style="font-family:var(--font-display);font-size:1.1rem;font-weight:800;">Recent Orders</h3>
                    <a href="orders.php" style="color:var(--primary);font-size:0.85rem;font-weight:600;">View All →</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($recentOrders) > 0): ?>
                        <?php while ($o = mysqli_fetch_assoc($recentOrders)): ?>
                        <tr>
                            <td><strong>#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                            <td><?= htmlspecialchars($o['full_name'] ?: $o['username']) ?></td>
                            <td style="font-weight:700;color:var(--primary);"><?= formatKES($o['total_amount']) ?></td>
                            <td><span class="order-status status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;color:var(--text-muted);">No orders yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- LOW STOCK ALERT -->
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <h3 style="font-family:var(--font-display);font-size:1.1rem;font-weight:800;">⚠️ Low Stock Alert</h3>
                    <a href="products.php" style="color:var(--primary);font-size:0.85rem;font-weight:600;">Manage →</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($lowStock) > 0): ?>
                        <?php while ($p = mysqli_fetch_assoc($lowStock)): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= formatKES($p['price']) ?></td>
                            <td>
                                <span style="color:<?= $p['stock'] == 0 ? 'var(--danger)' : 'var(--warning)' ?>;font-weight:700;">
                                    <?= $p['stock'] == 0 ? '❌ Out of Stock' : '⚠️ ' . $p['stock'] . ' left' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr><td colspan="3" style="text-align:center;color:var(--success);">✅ All products well-stocked</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>
