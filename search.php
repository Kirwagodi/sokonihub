<?php
// =============================================
// Sokoni Hub – Search
// =============================================

require_once 'includes/db.php';
require_once 'includes/functions.php';

$pageTitle = 'Search Results';
$q = trim($_GET['q'] ?? '');
$safeQ = mysqli_real_escape_string($conn, htmlspecialchars($q));

$results = null;
if (!empty($q)) {
    $results = mysqli_query($conn, "
        SELECT p.*, c.name as cat_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.name LIKE '%$safeQ%' OR p.description LIKE '%$safeQ%' OR c.name LIKE '%$safeQ%'
        ORDER BY p.name ASC
    ");
}

$catEmojis = [
    'Electronics' => '📱', 'Fashion' => '👗',
    'Home & Kitchen' => '🏠', 'Sports & Fitness' => '🏋️', 'Books & Education' => '📚'
];

include 'includes/header.php';
?>

<div class="breadcrumb">
    <a href="index.php">Home</a> <span>›</span> Search: <?= htmlspecialchars($q) ?>
</div>

<section class="section">
    <div class="section-header">
        <h2>
            <?php if ($results): ?>
                <?= mysqli_num_rows($results) ?> result<?= mysqli_num_rows($results) !== 1 ? 's' : '' ?> for "<?= htmlspecialchars($q) ?>"
            <?php else: ?>
                Search Products
            <?php endif; ?>
        </h2>
    </div>

    <?php if (empty($q)): ?>
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <h3>What are you looking for?</h3>
        <p>Enter a search term above to find products.</p>
    </div>

    <?php elseif ($results && mysqli_num_rows($results) > 0): ?>
    <div class="product-grid">
        <?php while ($p = mysqli_fetch_assoc($results)): ?>
        <div class="product-card">
            <a href="product.php?id=<?= $p['id'] ?>">
                <div class="product-img"><?= $catEmojis[$p['cat_name']] ?? '🛍️' ?></div>
                <div class="product-info">
                    <div class="product-cat"><?= htmlspecialchars($p['cat_name']) ?></div>
                    <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
                    <div class="product-footer">
                        <div class="product-price"><?= formatKES($p['price']) ?></div>
                    </div>
                </div>
            </a>
            <?php if ($p['stock'] > 0): ?>
            <div style="padding: 0 18px 18px;">
                <button class="add-to-cart-btn" data-id="<?= $p['id'] ?>" style="width:100%;padding:10px;">🛒 Add to Cart</button>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>

    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">😕</div>
        <h3>No results found</h3>
        <p>We couldn't find anything matching "<?= htmlspecialchars($q) ?>". Try different keywords.</p>
        <a href="index.php" class="btn-primary" style="display:inline-block;margin-top:20px;">Browse All Products</a>
    </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
