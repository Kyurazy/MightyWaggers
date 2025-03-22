<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];

    $stmt = $pdo->prepare('INSERT INTO products (name, price, stock, description) VALUES (:name, :price, :stock, :description)');
    if ($stmt->execute([
        'name' => $name,
        'price' => $price,
        'stock' => $stock,
        'description' => $description
    ])) {
        $product_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action) VALUES (:user_id, "create_product")');
        $stmt->execute(['user_id' => $_SESSION['user_id']]);

        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error = 'Failed to add product.';
    }
}
?>

<?php include('../backend/header.php'); ?>

<div class="content">
    <h2>Add New Product</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="add_product.php">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" required>

        <label for="price">Price</label>
        <input type="number" name="price" id="price" required>

        <label for="stock">Stock</label>
        <input type="number" name="stock" id="stock" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" required></textarea>

        <button type="submit">Add Product</button>
    </form>
</div>

<?php include('../backend/footer.php'); ?>
