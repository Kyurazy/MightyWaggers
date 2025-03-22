<?php
session_start();
require_once '../backend/config.php';  

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');  
    exit();
}

$contacts = $pdo->query('SELECT * FROM contact ORDER BY created_at DESC')->fetchAll();
$products = $pdo->query('SELECT * FROM products')->fetchAll();
$users = $pdo->query('SELECT * FROM users')->fetchAll();

if (isset($_POST['reset_logs'])) {
    $stmt = $pdo->prepare('DELETE FROM audit_logs');
    $stmt->execute();
}

if (isset($_GET['delete_user_id'])) {
    $user_id = $_GET['delete_user_id'];
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute(['id' => $user_id]);
    header('Location: admin_dashboard.php');
    exit();
}

if (isset($_POST['message_user'])) {
    $user_id = $_POST['user_id'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare('INSERT INTO user_messages (user_id, message) VALUES (:user_id, :message)');
    $stmt->execute(['user_id' => $user_id, 'message' => $message]);

    $_SESSION['success_message'] = 'Message sent successfully!';
    header('Location: admin_dashboard.php');
    exit();
}

if (isset($_POST['respond_message'])) {
    $contact_id = $_POST['contact_id'];
    $response = $_POST['response'];

    // Update the response in the contact table
    $stmt = $pdo->prepare('UPDATE contact SET response = :response WHERE id = :id');
    $stmt->execute(['response' => $response, 'id' => $contact_id]);

    $_SESSION['success_message'] = 'Response sent successfully!';
    header('Location: admin_dashboard.php');
    exit();
}

?>

<?php include('../backend/header.php'); ?>

<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>

    <div class="section">
        <h2>Contact Messages</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Response</th>
                    <th>Respond</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?php echo $contact['name']; ?></td>
                        <td><?php echo $contact['email']; ?></td>
                        <td><?php echo $contact['message']; ?></td>
                        <td><?php echo $contact['created_at']; ?></td>
                        <td><?php echo $contact['response'] ? $contact['response'] : 'No response yet'; ?></td>
                        <td>
                            <?php if (!$contact['response']): ?>
                                <form method="POST" action="admin_dashboard.php">
                                    <textarea name="response" placeholder="Type your response here..." required></textarea>
                                    <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>" />
                                    <button type="submit" name="respond_message">Respond</button>
                                </form>
                            <?php else: ?>
                                <button disabled>Response Sent</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Manage Products</h2>
        <a href="add_product.php"><button>Add New Product</button></a>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['price']; ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a> |
                            <a href="delete_product.php?id=<?php echo $product['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Registered Users</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <a href="?delete_user_id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a> |
                            <form method="POST" action="admin_dashboard.php" style="display:inline;">
                                <select name="user_id" required>
                                    <option value="">Select User</option>
                                    <?php foreach ($users as $user_option): ?>
                                        <option value="<?php echo $user_option['id']; ?>"><?php echo $user_option['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <textarea name="message" placeholder="Enter message to send" required></textarea>
                                <button type="submit" name="message_user">Send Message</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Audit Logs</h2>
        <form method="POST" action="admin_dashboard.php">
            <button type="submit" name="reset_logs" class="reset-logs-btn">Reset Logs</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query('SELECT audit_logs.*, users.name AS user_name FROM audit_logs JOIN users ON audit_logs.user_id = users.id ORDER BY timestamp DESC');
                $logs = $stmt->fetchAll();
                foreach ($logs as $log): ?>
                    <tr>
                        <td><?php echo $log['user_name']; ?></td>
                        <td><?php echo $log['action']; ?></td>
                        <td><?php echo $log['timestamp']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('footer.php'); ?>
