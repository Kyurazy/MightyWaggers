<?php
session_start();
require_once 'config.php';

if ($_SESSION['role'] !== 'admin') {
    die('Access Denied');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$image");

    $stmt = $pdo->prepare('INSERT INTO products (name, description, price, stock, image) VALUES (:name, :description, :price, :stock, :image)');
    if ($stmt->execute(['name' => $name, 'description' => $description, 'price' => $price, 'stock' => $stock, 'image' => $image])) {
        echo 'Product added successfully';
    } else {
        echo 'Error adding product';
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Name</label><input type="text" name="name" required />
    <label>Description</label><textarea name="description" required></textarea>
    <label>Price</label><input type="number" step="0.01" name="price" required />
    <label>Stock</label><input type="number" name="stock" required />
    <label>Image</label><input type="file" name="image" required />
    <button type="submit">Add Product</button>
</form>
