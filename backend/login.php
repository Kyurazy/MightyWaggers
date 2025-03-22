<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']); 
}

$error = '';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['name']; 

        $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action) VALUES (:user_id, "login")');
        $stmt->execute(['user_id' => $user['id']]);

        header('Location: index.php'); 
        exit();
    } else {
        $error = 'Invalid email or password!';
    }
}
?>

<?php include('header.php'); ?>

<div class="content">
    <h2>Login</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required />

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required />

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include('footer.php'); ?>
