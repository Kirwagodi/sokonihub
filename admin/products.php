<?php
// =============================================
// Sokoni Hub – Admin: Manage Products
// =============================================

require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$did");
    header('Location: products.php?msg=Product+deleted');
    exit();
}

$products = mysqli_query($conn, "
    SELECT p.*, c.name as cat_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="brand"><span class="logo-s">S</span>okoni<span class="logo-hub"> Hub</span><small>Admin Panel</small></div>
        <nav class="admin-nav">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="products.php" class="active">📦 Products</a>
            <a href="orders.php">🧾 Orders</a>
            <a href="users.php">👥 Users</a>
            <a href="add_product.php">➕ Add Product</a>
            <a href="../index.php">🏠 View Store</a>
            <a href="../logout.php" style="color:rgba(255,100,80,0.8);">🚪 Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;">
            <h2 style="margin:0;">Manage Products</h2>
            <a href="add_product.php" class="btn-primary" style="padding:12px 24px;">+ Add New Product</a>
        </div>

        <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td><?= htmlspecialchars($p['cat_name']) ?></td>
                    <td style="font-weight:700;color:var(--primary);"><?= formatKES($p['price']) ?></td>
                    <td>
                        <span style="color:<?= $p['stock']==0 ? 'var(--danger)' : ($p['stock']<=5 ? 'var(--warning)' : 'var(--success)') ?>;font-weight:600;">
                            <?= $p['stock'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?= $p['id'] ?>" style="color:var(--accent);font-weight:600;font-size:0.85rem;margin-right:12px;">Edit</a>
                        <a href="products.php?delete=<?= $p['id'] ?>" style="color:var(--danger);font-weight:600;font-size:0.85rem;" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
</div>
</body>
</html>
