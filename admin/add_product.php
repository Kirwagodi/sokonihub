<?php
// =============================================
// Sokoni Hub – Admin: Add Product
// =============================================

require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['name'] ?? '');
    $desc     = sanitize($_POST['description'] ?? '');
    $price    = floatval($_POST['price'] ?? 0);
    $stock    = intval($_POST['stock'] ?? 0);
    $cat_id   = intval($_POST['category_id'] ?? 0);
    $image    = sanitize($_POST['image'] ?? '');

    if (empty($name) || $price <= 0 || $cat_id <= 0) {
        $error = 'Please fill in all required fields correctly.';
    } else {
        $insert = mysqli_query($conn, "
            INSERT INTO products (category_id, name, description, price, stock, image)
            VALUES ($cat_id, '$name', '$desc', $price, $stock, '$image')
        ");
        if ($insert) {
            $success = "Product \"$name\" added successfully!";
        } else {
            $error = 'Failed to add product. Try again.';
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Admin</title>
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
            <a href="users.php">👥 Users</a>
            <a href="add_product.php" class="active">➕ Add Product</a>
            <a href="../index.php">🏠 View Store</a>
            <a href="../logout.php" style="color:rgba(255,100,80,0.8);">🚪 Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <h2>Add New Product</h2>

        <?php if ($error): ?><div class="alert alert-error">❌ <?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success">✅ <?= $success ?> <a href="products.php">View Products</a></div><?php endif; ?>

        <div style="background:var(--white);border-radius:var(--radius);padding:32px;max-width:700px;box-shadow:var(--shadow);">
            <form method="POST" action="add_product.php">
                <div class="form-group">
                    <label>Product Name <span style="color:var(--danger)">*</span></label>
                    <input type="text" name="name" placeholder="e.g. Samsung Galaxy A54" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Product description..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label>Price (KES) <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="price" placeholder="e.g. 45000" step="0.01" min="0" required value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity <span style="color:var(--danger)">*</span></label>
                        <input type="number" name="stock" placeholder="e.g. 20" min="0" required value="<?= htmlspecialchars($_POST['stock'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Category <span style="color:var(--danger)">*</span></label>
                        <select name="category_id" required>
                            <option value="">Select Category</option>
                            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $cat['id'] ?>" <?= (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Image Filename (optional)</label>
                    <input type="text" name="image" placeholder="e.g. product.jpg (upload to /images/ folder)" value="<?= htmlspecialchars($_POST['image'] ?? '') ?>">
                    <small style="color:var(--text-muted);font-size:0.78rem;">Place image files in the /images/ directory in your project folder.</small>
                </div>
                <div style="display:flex;gap:12px;">
                    <button type="submit" class="btn-submit" style="width:auto;padding:14px 32px;">Add Product</button>
                    <a href="products.php" style="padding:14px 24px;border-radius:10px;background:var(--bg);color:var(--text);font-weight:600;display:inline-flex;align-items:center;">Cancel</a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
