<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        $error = 'This email is already registered!';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)');
        if ($stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => 'user', 
        ])) {
            $_SESSION['success_message'] = 'Registration successful! Please log in.';
            header('Location: login.php');
            exit();
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>

<?php include('header.php'); ?>

<div class="content">
    <h2>Register</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" required />

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required />

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required />

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>

<?php include('footer.php'); ?>
