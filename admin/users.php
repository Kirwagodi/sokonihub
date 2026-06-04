<?php

require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$users = mysqli_query($conn, "
    SELECT u.*, COUNT(o.id) as order_count, COALESCE(SUM(o.total_amount),0) as total_spent
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Admin</title>
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
            <a href="orders.php">🧾 Orders</a>
            <a href="users.php" class="active">👥 Users</a>
            <a href="add_product.php">➕ Add Product</a>
            <a href="../index.php">🏠 View Store</a>
            <a href="../logout.php" style="color:rgba(255,100,80,0.8);">🚪 Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <h2>Registered Users</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><strong><?= htmlspecialchars($u['full_name'] ?: '—') ?></strong></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td style="font-size:0.85rem;"><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span style="padding:4px 10px;border-radius:50px;font-size:0.75rem;font-weight:700;background:<?= $u['role']==='admin' ? '#fff0ea' : '#edfaf3' ?>;color:<?= $u['role']==='admin' ? 'var(--primary)' : 'var(--success)' ?>;">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td><?= $u['order_count'] ?></td>
                    <td style="font-weight:700;color:var(--primary);"><?= formatKES($u['total_spent']) ?></td>
                    <td style="font-size:0.82rem;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
