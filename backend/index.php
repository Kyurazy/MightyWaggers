<?php
session_start();
require_once 'config.php';


$messages = [];
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user') {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare('SELECT * FROM user_messages WHERE user_id = :user_id ORDER BY timestamp DESC');
    $stmt->execute(['user_id' => $user_id]);
    $messages = $stmt->fetchAll();
}
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


    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
        <h2>Your Messages</h2>
        <?php if (count($messages) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Message</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?php echo nl2br(htmlspecialchars($message['message'])); ?></td>
                            <td><?php echo $message['timestamp']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no messages.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>
