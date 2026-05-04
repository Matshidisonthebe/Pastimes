<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clothingStore";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total = 0;
$cart_items = array();

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $sql = "SELECT * FROM products WHERE id = " . intval($item['product_id']);
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $item_total = $product['price'] * $item['quantity'];
            $total += $item_total;
            $cart_items[] = array(
                'product_id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $item['quantity'],
                'item_total' => $item_total
            );
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Pastimes</title>
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
        <section class="cart">
            <div class="container">
                <h2>Your Shopping Cart</h2>
                <?php
                if (!empty($cart_items)) {
                    echo '<table class="cart-table">';
                    echo '<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Action</th></tr>';
                    foreach ($cart_items as $item) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($item['name']) . '</td>';
                        echo '<td>$' . number_format($item['price'], 2) . '</td>';
                        echo '<td>' . $item['quantity'] . '</td>';
                        echo '<td>$' . number_format($item['item_total'], 2) . '</td>';
                        echo '<td><a href="remove_from_cart.php?product_id=' . $item['product_id'] . '" class="btn btn-danger">Remove</a></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '<div class="cart-summary">';
                    echo '<h3>Cart Total: $' . number_format($total, 2) . '</h3>';
                    echo '<button class="btn btn-primary">Checkout</button>';
                    echo '</div>';
                } else {
                    echo '<p>Your cart is empty. <a href="shop.php">Continue Shopping</a></p>';
                }
                ?>
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
<?php $conn->close(); ?>
