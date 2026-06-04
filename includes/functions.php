<?php
// =============================================
// Sokoni Hub - Session & Auth Functions
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php?error=unauthorized");
        exit();
    }
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

function getCartCount() {
    global $conn;
    if (!isLoggedIn()) return 0;
    $uid = $_SESSION['user_id'];
    $result = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart WHERE user_id = $uid");
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}

function formatKES($amount) {
    return 'KES ' . number_format($amount, 2);
}
?>
