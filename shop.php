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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Check if product already in cart
    $check_sql = "SELECT cart_id FROM cart WHERE user_id = $user_id AND product_id = $product_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update quantity
        $row = $check_result->fetch_assoc();
        $new_quantity = $quantity + $row['quantity'];
        $update_sql = "UPDATE cart SET quantity = $new_quantity WHERE cart_id = {$row['cart_id']}";
        $conn->query($update_sql);
    } else {
        // Insert new cart item
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
        $conn->query($insert_sql);
    }

    $message = "Product added to cart!";
}

// Fetch all products
$products_query = "SELECT * FROM products ORDER BY category";
$products_result = $conn->query($products_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Pastimes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="container">
                <h1 class="logo">Pastimes</h1>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="shop.php" class="active">Shop</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <section class="shop-section">
            <div class="container">
                <h2>Our Collection</h2>

                <?php if (isset($message)): ?>
                    <div class="message success"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="products-grid">
                    <?php
                    if ($products_result->num_rows > 0) {
                        while ($product = $products_result->fetch_assoc()) {
                            ?>
                            <div class="product-card">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($product['category']); ?></p>
                                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                                
                                <?php if ($product['quantity'] > 0): ?>
                                    <form method="POST" class="add-to-cart-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <p class="out-of-stock">Out of Stock</p>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No products available.</p>";
                    }
                    ?>
                </div>
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
