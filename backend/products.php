<?php
session_start();
require_once 'config.php';

$products = $pdo->query('SELECT * FROM products')->fetchAll();
?>

<?php include('header.php'); ?>

<div class="content">
    <h2>Our Products</h2>
    <p>Explore our range of premium products, crafted with care and quality in mind.</p>

    <div class="product-list">
        <?php foreach ($products as $product): ?>
            <div class="product-item">
                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['name']; ?>">
                <h3><?php echo $product['name']; ?></h3>
                <p><?php echo $product['description']; ?></p>
                <p>Price: $<?php echo $product['price']; ?></p>
                <p>Stock: <?php echo $product['stock']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include('footer.php'); ?>
