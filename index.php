<?php
session_start();

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clothingStore";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastimes - Clothing Store</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <h1 class="logo">Pastimes</h1>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        if ($_SESSION['role'] == 'admin') {
                            echo '<li><a href="admin/dashboard.php">Admin Dashboard</a></li>';
                        }
                        echo '<li><a href="logout.php">Logout</a></li>';
                    } else {
                        echo '<li><a href="login.php">Login</a></li>';
                        echo '<li><a href="register.php">Register</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h2>Welcome to Pastimes</h2>
                <p>Your premium clothing destination</p>
                <a href="shop.php" class="btn btn-primary">Start Shopping</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Pastimes Clothing Store. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
