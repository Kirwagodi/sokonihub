<?php
// =============================================
// Sokoni Hub – Checkout (Place Order)
// =============================================

require_once 'includes/db.php';
require_once 'includes/functions.php';

requireLogin();
$uid = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit();
}

$address = sanitize($_POST['shipping_address'] ?? '');
if (empty($address)) {
    header('Location: cart.php?error=Please+provide+a+delivery+address');
    exit();
}

// Fetch cart items
$items = mysqli_query($conn, "
    SELECT c.quantity, p.id as product_id, p.price, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $uid
");

if (mysqli_num_rows($items) == 0) {
    header('Location: cart.php?error=Your+cart+is+empty');
    exit();
}

// Calculate total
$total = 0;
$cartItems = [];
while ($item = mysqli_fetch_assoc($items)) {
    $total += $item['price'] * $item['quantity'];
    $cartItems[] = $item;
}
$delivery = $total >= 5000 ? 0 : 300;
$grandTotal = $total + $delivery;

// Create order
mysqli_begin_transaction($conn);
try {
    mysqli_query($conn, "
        INSERT INTO orders (user_id, total_amount, shipping_address)
        VALUES ($uid, $grandTotal, '$address')
    ");
    $orderId = mysqli_insert_id($conn);

    foreach ($cartItems as $item) {
        $pid   = $item['product_id'];
        $qty   = $item['quantity'];
        $price = $item['price'];
        mysqli_query($conn, "
            INSERT INTO order_items (order_id, product_id, quantity, unit_price)
            VALUES ($orderId, $pid, $qty, $price)
        ");
        mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id = $pid AND stock >= $qty");
    }

    // Clear cart
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $uid");
    mysqli_commit($conn);

    header("Location: orders.php?success=1&order_id=$orderId");
    exit();

} catch (Exception $e) {
    mysqli_rollback($conn);
    header('Location: cart.php?error=Order+failed,+please+try+again');
    exit();
}
