<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Home';

// Fetch categories
$cats = mysqli_query($conn, "SELECT * FROM categories");

// Filter by category
$catFilter = '';
$catName = 'All Products';
if (isset($_GET['cat']) && is_numeric($_GET['cat'])) {
    $catId = (int)$_GET['cat'];
    $catFilter = "WHERE p.category_id = $catId";
    $catRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM categories WHERE id = $catId"));
    if ($catRow) $catName = $catRow['name'];
}

// Fetch products
$products = mysqli_query($conn, "
    SELECT p.*, c.name as cat_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    $catFilter
    ORDER BY p.created_at DESC
");

// Featured (limit 8 for hero section)
$featured = mysqli_query($conn, "
    SELECT p.*, c.name as cat_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.stock DESC
    LIMIT 8
");

// Category emoji map
$catEmojis = [
    'Electronics' => '📱',
    'Fashion' => '👗',
    'Home & Kitchen' => '🏠',
    'Sports & Fitness' => '🏋️',
    'Books & Education' => '📚'
];

include 'includes/header.php';
?>

<!-- SUCCESS/ERROR MESSAGES -->
<?php if (isset($_GET['msg'])): ?>
<div style="padding: 16px 40px; background: #edfaf3; border-bottom: 1px solid #b6efd0;">
    <div class="alert alert-success" style="margin:0;max-width:600px;">
        ✅ <?= htmlspecialchars($_GET['msg']) ?>
    </div>
</div>
<?php endif; ?>

<!-- HERO BANNER -->
<section class="hero">
    <div class="hero-content">
        <span class="hero-tag">🇰🇪 Kenya's #1 Smart Store</span>
        <h1>Shop Smart.<br>Live <span>Better.</span></h1>
        <p>Discover thousands of products from electronics to fashion, all delivered to your doorstep across Kenya.</p>
        <div class="hero-btns">
            <a href="#products" class="btn-primary">Shop Now →</a>
            <a href="register.php" class="btn-outline">Join Free</a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <strong>2,400+</strong>
                <span>Products</span>
            </div>
            <div class="hero-stat">
                <strong>50K+</strong>
                <span>Customers</span>
            </div>
            <div class="hero-stat">
                <strong>47</strong>
                <span>Counties</span>
            </div>
        </div>
    </div>
    <div class="hero-visual">
        <div class="hero-placeholder">🛍️</div>
    </div>
</section>

<!-- FEATURES STRIP -->
<div class="features-strip">
    <div class="feature-item">
        <div class="feature-icon">🚚</div>
        <div>
            <strong>Fast Delivery</strong>
            <span>Nairobi same-day</span>
        </div>
    </div>
    <div class="feature-item">
        <div class="feature-icon">🔒</div>
        <div>
            <strong>Secure Payments</strong>
            <span>M-Pesa & Cards</span>
        </div>
    </div>
    <div class="feature-item">
        <div class="feature-icon">↩️</div>
        <div>
            <strong>Easy Returns</strong>
            <span>7-day return policy</span>
        </div>
    </div>
    <div class="feature-item">
        <div class="feature-icon">💬</div>
        <div>
            <strong>24/7 Support</strong>
            <span>Always here to help</span>
        </div>
    </div>
</div>

<!-- SHOP BY CATEGORY -->
<section class="section" style="background: white;">
    <div class="section-header">
        <h2>Shop by Category</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;">
        <?php mysqli_data_seek($cats, 0); while($cat = mysqli_fetch_assoc($cats)): ?>
        <a href="index.php?cat=<?= $cat['id'] ?>" style="text-align:center;padding:28px 16px;background:var(--bg);border-radius:16px;transition:all 0.2s;border:2px solid transparent;" onmouseover="this.style.borderColor='var(--primary)';this.style.background='var(--primary-light)'" onmouseout="this.style.borderColor='transparent';this.style.background='var(--bg)'">
            <div style="font-size:2.5rem;margin-bottom:10px;"><?= $catEmojis[$cat['name']] ?? '🛍️' ?></div>
            <div style="font-family:var(--font-display);font-weight:700;font-size:0.85rem;"><?= htmlspecialchars($cat['name']) ?></div>
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;"><?= htmlspecialchars($cat['description']) ?></div>
        </a>
        <?php endwhile; ?>
    </div>
</section>

<!-- PRODUCTS -->
<section class="section" id="products">
    <div class="section-header">
        <h2><?= htmlspecialchars($catName) ?></h2>
        <?php if (isset($_GET['cat'])): ?>
        <a href="index.php">View All →</a>
        <?php endif; ?>
    </div>

    <?php if (mysqli_num_rows($products) > 0): ?>
    <div class="product-grid">
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
        <div class="product-card">
            <a href="product.php?id=<?= $p['id'] ?>">
                <div class="product-img">
                    <?php if(!empty($p['image']) && file_exists('images/' . $p['image'])): ?>
                        <img src="images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php else: ?>
                        <?= $catEmojis[$p['cat_name']] ?? '🛍️' ?>
                    <?php endif; ?>
                    <?php if ($p['stock'] <= 5 && $p['stock'] > 0): ?>
                        <span class="product-badge">Only <?= $p['stock'] ?> left!</span>
                    <?php elseif ($p['stock'] == 0): ?>
                        <span class="product-badge" style="background:#777;">Out of Stock</span>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="product-cat"><?= htmlspecialchars($p['cat_name']) ?></div>
                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
                    <div class="product-footer">
                        <div class="product-price">
                            <?= formatKES($p['price']) ?>
                            <small><?= $p['stock'] > 0 ? 'In Stock' : 'Unavailable' ?></small>
                        </div>
                    </div>
                </div>
            </a>
            <?php if ($p['stock'] > 0): ?>
            <div style="padding: 0 18px 18px;">
                <button class="add-to-cart-btn" data-id="<?= $p['id'] ?>" style="width:100%;padding:10px;">
                    🛒 Add to Cart
                </button>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <h3>No products found</h3>
        <p>Try a different category or <a href="index.php" style="color:var(--primary)">browse all products</a>.</p>
    </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
