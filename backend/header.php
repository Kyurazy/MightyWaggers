<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mighty Waggers</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&family=Nunito+Sans:wght@400&family=Bubblegum+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="about.php" class="nav-link">About</a></li>
            <li><a href="products.php" class="nav-link">Products</a></li>
            <li><a href="contact.php" class="nav-link">Contact</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
                <li>Welcome, <?php echo $_SESSION['username']; ?>!</li> 
            <?php else: ?>
                <li><a href="login.php" class="nav-link">Login</a></li> 
            <?php endif; ?>
        </ul>
    </nav>
</header>
