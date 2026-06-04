<?php
// =============================================
// Sokoni Hub - Database Configuration
// BIT3208 Capstone Project
// =============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sokonihub');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("<div style='font-family:sans-serif;color:red;padding:20px;'>
        ❌ Database Connection Failed: " . mysqli_connect_error() . "
        <br><small>Make sure XAMPP MySQL is running and you have imported sokonihub.sql</small>
    </div>");
}

mysqli_set_charset($conn, "utf8");
?>
