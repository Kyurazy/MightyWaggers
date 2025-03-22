<?php
session_start();
require_once 'config.php';
?>

<?php include('header.php'); ?>

<div class="content">
    <h1>Welcome to Mighty Waggers</h1>
    <p>Your go-to source for premium, delicious products!</p>
    <button>Explore Products</button>

    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
        <a href="admin_dashboard.php">
            <button>Go to Admin Dashboard</button>
        </a>
    <?php endif; ?>
</div>
<?php include('footer.php'); ?>
