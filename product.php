<?php

require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$pid = (int)$_GET['id'];
$result = mysqli_query($conn, "
    SELECT p.*, c.name as cat_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = $pid
");
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: index.php');
    exit();
}

$pageTitle = $product['name'];

$catEmojis = [
    'Electronics' => '📱',
    'Fashion' => '👗',
    'Home & Kitchen' => '🏠',
    'Sports & Fitness' => '🏋️',
    'Books & Education' => '📚'
];

// Related products
$related = mysqli_query($conn, "
    SELECT p.*, c.name as cat_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = {$product['category_id']} AND p.id != $pid
    LIMIT 4
");

include 'includes/header.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb">
    <a href="index.php">Home</a>
    <span>›</span>
    <a href="index.php?cat=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['cat_name']) ?></a>
    <span>›</span>
    <?= htmlspecialchars($product['name']) ?>
</div>

<!-- PRODUCT DETAIL -->
<div class="product-detail">
    <div class="detail-img">
        <?php if (!empty($product['image']) && file_exists('images/' . $product['image'])): ?>
            <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="border-radius:20px;width:100%;height:100%;object-fit:cover;">
        <?php else: ?>
            <?= $catEmojis[$product['cat_name']] ?? '🛍️' ?>
        <?php endif; ?>
    </div>

    <div class="detail-info">
        <div style="font-size:0.8rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">
            <?= htmlspecialchars($product['cat_name']) ?>
        </div>
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <div class="detail-price"><?= formatKES($product['price']) ?></div>

        <span class="stock-badge <?= $product['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
            <?= $product['stock'] > 0 ? '✅ In Stock (' . $product['stock'] . ' available)' : '❌ Out of Stock' ?>
        </span>

        <p class="detail-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

        <?php if ($product['stock'] > 0): ?>
            <?php if (isLoggedIn()): ?>
            <div style="display:flex;gap:12px;align-items:center;margin-bottom:24px;">
                <input type="number" id="qty-input" value="1" min="1" max="<?= $product['stock'] ?>" style="width:80px;padding:12px;border:2px solid var(--border);border-radius:10px;text-align:center;font-size:1rem;font-weight:700;outline:none;">
                <button onclick="addToCart(<?= $product['id'] ?>)" class="btn-primary" style="padding:14px 32px;border:none;cursor:pointer;font-size:1rem;">
                    🛒 Add to Cart
                </button>
            </div>
            <?php else: ?>
            <a href="login.php?redirect=product.php?id=<?= $product['id'] ?>" class="btn-primary" style="display:inline-block;margin-bottom:24px;">
                Login to Buy
            </a>
            <?php endif; ?>
        <?php else: ?>
            <button class="btn-primary" disabled style="opacity:0.5;cursor:not-allowed;padding:14px 32px;border:none;font-size:1rem;margin-bottom:24px;">
                Out of Stock
            </button>
        <?php endif; ?>

        <div style="background:var(--bg);border-radius:14px;padding:20px;">
            <div style="display:flex;gap:16px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:8px;font-size:0.88rem;">
                    <span style="font-size:1.2rem;">🚚</span> Free delivery over KES 5,000
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:0.88rem;">
                    <span style="font-size:1.2rem;">↩️</span> 7-day returns
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:0.88rem;">
                    <span style="font-size:1.2rem;">🔒</span> Secure checkout
                </div>
            </div>
        </div>
    </div>
</div>

<!-- RELATED PRODUCTS -->
<?php if (mysqli_num_rows($related) > 0): ?>
<section class="section" style="background:white;border-top:1px solid var(--border);">
    <div class="section-header">
        <h2>Related Products</h2>
        <a href="index.php?cat=<?= $product['category_id'] ?>">View All →</a>
    </div>
    <div class="product-grid">
        <?php while ($r = mysqli_fetch_assoc($related)): ?>
        <div class="product-card">
            <a href="product.php?id=<?= $r['id'] ?>">
                <div class="product-img"><?= $catEmojis[$r['cat_name']] ?? '🛍️' ?></div>
                <div class="product-info">
                    <div class="product-cat"><?= htmlspecialchars($r['cat_name']) ?></div>
                    <div class="product-name"><?= htmlspecialchars($r['name']) ?></div>
                    <div class="product-footer">
                        <div class="product-price"><?= formatKES($r['price']) ?></div>
                    </div>
                </div>
            </a>
            <?php if ($r['stock'] > 0): ?>
            <div style="padding: 0 18px 18px;">
                <button class="add-to-cart-btn" data-id="<?= $r['id'] ?>" style="width:100%;padding:10px;">🛒 Add to Cart</button>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

<script>
function addToCart(productId) {
    const qty = document.getElementById('qty-input').value;
    const btn = document.querySelector('.detail-info .btn-primary');
    btn.textContent = 'Adding...';
    btn.disabled = true;

    fetch('cart_action.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=add&product_id=${productId}&qty=${qty}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.textContent = '✓ Added to Cart!';
            btn.style.background = 'var(--success)';
            const badge = document.querySelector('.cart-badge');
            if (badge) badge.textContent = data.cart_count;
            showToast('Added to cart successfully!');
        } else {
            btn.textContent = data.message || 'Error';
            btn.style.background = 'var(--danger)';
        }
        setTimeout(() => {
            btn.textContent = '🛒 Add to Cart';
            btn.style.background = '';
            btn.disabled = false;
        }, 2000);
    });
}
</script>

<?php include 'includes/footer.php'; ?>
