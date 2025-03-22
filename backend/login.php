<?php
session_start();
require_once 'config.php';  // Include your database connection settings

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php'); 
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from DB
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['name']; 

        // Capture user details for analytics
        $ip_address = $_SERVER['REMOTE_ADDR']; // Real user IP
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR']; // Use the forwarded IP if available
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT']; // User's browser and OS
        $os_version = "unknown"; // Default value
        $browser = "Default Browser"; // Default value
        $processor = "unknown"; // Default value

        // Parsing the user-agent string for OS and browser
        if (preg_match('/(Linux|Windows|Mac|iPhone|Android|iPad)/', $user_agent, $matches)) {
            $os_version = $matches[0];
        }

        if (preg_match('/(Chrome|Safari|Firefox|Edge|Opera|MSIE|Trident)/', $user_agent, $matches)) {
            $browser = $matches[0];
        }

        // Capture processor (basic example)
        if (preg_match('/x86_64/', $user_agent)) {
            $processor = "AMD64";
        }

        // Insert analytics data (IP, Browser, OS, etc.)
        $stmt = $pdo->prepare('INSERT INTO analytics (user_id, ip_address, browser, os_version, processor) 
                               VALUES (:user_id, :ip_address, :browser, :os_version, :processor)');
        $stmt->execute([
            'user_id' => $user['id'],
            'ip_address' => $ip_address,
            'browser' => $browser,
            'os_version' => $os_version,
            'processor' => $processor
        ]);

        // Log the login action in the audit logs
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
