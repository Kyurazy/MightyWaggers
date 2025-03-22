<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['id'];

$stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
if ($stmt->execute(['id' => $product_id])) {
    header('Location: admin_dashboard.php');
    exit();
} else {
    echo 'Failed to delete product.';
}
?>
