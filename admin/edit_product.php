<?php
// =============================================
// Sokoni Hub – Admin: Edit Product
// =============================================

require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$pid = (int)($_GET['id'] ?? 0);
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$pid"));
if (!$product) {
    header('Location: products.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = sanitize($_POST['name'] ?? '');
    $desc   = sanitize($_POST['description'] ?? '');
    $price  = floatval($_POST['price'] ?? 0);
    $stock  = intval($_POST['stock'] ?? 0);
    $cat_id = intval($_POST['category_id'] ?? 0);
    $image  = sanitize($_POST['image'] ?? '');

    if (empty($name) || $price <= 0) {
        $error = 'Please fill in all required fields.';
    } else {
        mysqli_query($conn, "
            UPDATE products SET
                name='$name', description='$desc', price=$price,
                stock=$stock, category_id=$cat_id, image='$image'
            WHERE id=$pid
        ");
        $success = 'Product updated successfully!';
        $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$pid"));
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Admin</title>
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
        <h2>Edit Product #<?= $pid ?></h2>

        <?php if ($error): ?><div class="alert alert-error">❌ <?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius);padding:32px;max-width:700px;box-shadow:var(--shadow);">
            <form method="POST" action="edit_product.php?id=<?= $pid ?>">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>Price (KES) *</label>
                        <input type="number" name="price" step="0.01" min="0" required value="<?= $product['price'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Stock *</label>
                        <input type="number" name="stock" min="0" required value="<?= $product['stock'] ?>">
                    </div>
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category_id" required>
                            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Image Filename</label>
                    <input type="text" name="image" value="<?= htmlspecialchars($product['image'] ?? '') ?>">
                </div>
                <div style="display:flex;gap:12px;">
                    <button type="submit" class="btn-submit" style="width:auto;padding:14px 32px;">Update Product</button>
                    <a href="products.php" style="padding:14px 24px;border-radius:10px;background:var(--bg);color:var(--text);font-weight:600;display:inline-flex;align-items:center;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
