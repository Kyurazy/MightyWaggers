<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$product_id = $_GET['id'];
$error = '';

// Fetch product details from the database
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$stmt->execute(['id' => $product_id]);
$product_data = $stmt->fetch();

if (!$product_data) {
    $error = 'Product not found!';
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'];

        // Update the product in the database
        $stmt = $pdo->prepare('UPDATE products SET name = :name, price = :price, stock = :stock, description = :description WHERE id = :id');
        if ($stmt->execute([
            'id' => $product_id,
            'name' => $name,
            'price' => $price,
            'stock' => $stock,
            'description' => $description
        ])) {
            // Log action in audit logs
            $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action) VALUES (:user_id, "update_product")');
            $stmt->execute(['user_id' => $_SESSION['user_id']]);

            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error = 'Failed to update product.';
        }
    }
}

?>

<?php include('header.php'); ?>

<div class="content">
    <h2>Edit Product</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_product.php?id=<?php echo $product_id; ?>">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" value="<?php echo $product_data['name']; ?>" required>

        <label for="price">Price</label>
        <input type="number" name="price" id="price" value="<?php echo $product_data['price']; ?>" required>

        <label for="stock">Stock</label>
        <input type="number" name="stock" id="stock" value="<?php echo $product_data['stock']; ?>" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" required><?php echo $product_data['description']; ?></textarea>

        <label for="image">Product Image</label>
        <input type="file" name="image" id="image">
        <?php if ($product_data['image_url']): ?>
            <p>Current Image: <img src="<?php echo $product_data['image_url']; ?>" alt="Product Image" style="max-width: 200px; max-height: 200px;"></p>
        <?php endif; ?>

        <button type="submit">Update Product</button>
    </form>
</div>

<?php include('footer.php'); ?>
