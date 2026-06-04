<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' | Sokoni Hub' : 'Sokoni Hub – Shop Smart, Live Better' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <span>🇰🇪 Free delivery on orders above KES 5,000 | Call: 0700 123 456</span>
    <div class="topbar-links">
        <?php if(isLoggedIn()): ?>
            Welcome, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
            <?php if(isAdmin()): ?> | <a href="admin/dashboard.php">Admin Panel</a><?php endif; ?>
            | <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a> | <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="index.php" class="logo">
        <span class="logo-s">S</span>okoni<span class="logo-hub"> Hub</span>
    </a>

    <form class="search-bar" action="search.php" method="GET">
        <input type="text" name="q" placeholder="Search products, categories..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit">🔍</button>
    </form>

    <div class="nav-actions">
        <a href="cart.php" class="cart-btn">
            🛒 Cart
            <?php if($cartCount > 0): ?>
                <span class="cart-badge"><?= $cartCount ?></span>
            <?php endif; ?>
        </a>
        <?php if(isLoggedIn()): ?>
            <a href="orders.php" class="nav-link">My Orders</a>
        <?php endif; ?>
    </div>
</nav>

<!-- CATEGORY NAV -->
<div class="cat-nav">
    <a href="index.php">All Products</a>
    <a href="index.php?cat=1">Electronics</a>
    <a href="index.php?cat=2">Fashion</a>
    <a href="index.php?cat=3">Home & Kitchen</a>
    <a href="index.php?cat=4">Sports & Fitness</a>
    <a href="index.php?cat=5">Books & Education</a>
</div>
