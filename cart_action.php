<?php
// =============================================
// Sokoni Hub – Cart Action Handler (AJAX)
// =============================================

require_once 'includes/db.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit();
}

$uid    = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';

switch ($action) {

    case 'add':
        $pid = (int)($_POST['product_id'] ?? 0);
        $qty = max(1, (int)($_POST['qty'] ?? 1));

        // Check product exists and has stock
        $prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$pid AND stock>0"));
        if (!$prod) {
            echo json_encode(['success' => false, 'message' => 'Product not available']);
            exit();
        }

        // Check if already in cart
        $existing = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$uid AND product_id=$pid"));
        if ($existing) {
            $newQty = min($existing['quantity'] + $qty, $prod['stock']);
            mysqli_query($conn, "UPDATE cart SET quantity=$newQty WHERE id={$existing['id']}");
        } else {
            $qty = min($qty, $prod['stock']);
            mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($uid, $pid, $qty)");
        }

        echo json_encode(['success' => true, 'cart_count' => getCartCount()]);
        break;

    case 'increase':
        $cartId = (int)($_POST['cart_id'] ?? 0);
        $item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT c.*, p.stock FROM cart c JOIN products p ON c.product_id=p.id WHERE c.id=$cartId AND c.user_id=$uid"));
        if ($item && $item['quantity'] < $item['stock']) {
            mysqli_query($conn, "UPDATE cart SET quantity=quantity+1 WHERE id=$cartId");
        }
        echo json_encode(['success' => true]);
        break;

    case 'decrease':
        $cartId = (int)($_POST['cart_id'] ?? 0);
        $item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cart WHERE id=$cartId AND user_id=$uid"));
        if ($item) {
            if ($item['quantity'] > 1) {
                mysqli_query($conn, "UPDATE cart SET quantity=quantity-1 WHERE id=$cartId");
            } else {
                mysqli_query($conn, "DELETE FROM cart WHERE id=$cartId");
            }
        }
        echo json_encode(['success' => true]);
        break;

    case 'remove':
        $cartId = (int)($_POST['cart_id'] ?? 0);
        mysqli_query($conn, "DELETE FROM cart WHERE id=$cartId AND user_id=$uid");
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
