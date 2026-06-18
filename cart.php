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
$checkout_success = false;
$order_id = null;

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    if (!empty($_SESSION['cart'])) {
        // Calculate totals for order
        foreach ($_SESSION['cart'] as $item) {
            $sql = "SELECT * FROM products WHERE id = " . intval($item['product_id']);
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                $item_total = $product['price'] * $item['quantity'];
                $total += $item_total;
            }
        }

        // Insert order into database
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $order_date = date('Y-m-d H:i:s');
        $item_count = count($_SESSION['cart']);

        $order_sql = "INSERT INTO orders (user_id, order_date, total_amount, item_count, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($order_sql);
        $stmt->bind_param('isdi', $user_id, $order_date, $total, $item_count);

        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;

            // Insert order items
            foreach ($_SESSION['cart'] as $item) {
                $sql = "SELECT * FROM products WHERE id = " . intval($item['product_id']);
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();
                    $item_total = $product['price'] * $item['quantity'];

                    $order_item_sql = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity, item_total) VALUES (?, ?, ?, ?, ?, ?)";
                    $item_stmt = $conn->prepare($order_item_sql);
                    $item_stmt->bind_param('iisidi', $order_id, $product['id'], $product['name'], $product['price'], $item['quantity'], $item_total);
                    $item_stmt->execute();
                    $item_stmt->close();
                }
            }

            $checkout_success = true;
            // Clear the cart after successful checkout
            unset($_SESSION['cart']);
        }

        $stmt->close();
    }
}

// Load cart items for display (if not checked out)
if (!$checkout_success && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
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
    <style>
        /* Checkout Popup Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
            text-align: center;
            animation: slideUp 0.3s ease-in-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-success-icon {
            width: 80px;
            height: 80px;
            background-color: #27ae60;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
        }

        .modal-title {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .modal-subtitle {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .order-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: bold;
            color: #2c3e50;
        }

        .detail-value {
            color: #555;
        }

        .order-total {
            font-size: 20px;
            font-weight: bold;
            color: #27ae60;
            padding-top: 10px;
            margin-top: 10px;
            border-top: 2px solid #27ae60;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }

        .modal-btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            flex: 1;
        }

        .btn-primary-modal {
            background-color: #3498db;
            color: white;
        }

        .btn-primary-modal:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(52, 152, 219, 0.3);
        }

        .btn-secondary-modal {
            background-color: #95a5a6;
            color: white;
        }

        .btn-secondary-modal:hover {
            background-color: #7f8c8d;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(149, 165, 166, 0.3);
        }

        .order-id {
            background-color: #e8f4f8;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
            font-size: 14px;
            color: #2c3e50;
        }

        @media (max-width: 600px) {
            .modal-content {
                padding: 30px 20px;
            }

            .modal-title {
                font-size: 24px;
            }

            .modal-buttons {
                flex-direction: column;
            }

            .modal-btn {
                width: 100%;
            }
        }
    </style>
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
                if ($checkout_success) {
                    // Show success state
                    echo '<div class="order-success-message">';
                    echo '<p style="text-align: center; color: #27ae60; font-weight: bold; font-size: 18px;">✓ Order placed successfully!</p>';
                    echo '<p style="text-align: center; margin-top: 10px;"><a href="shop.php" class="btn btn-primary">Back to Shop</a></p>';
                    echo '</div>';
                } else if (!empty($cart_items)) {
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
                    echo '<form method="POST" style="display: inline;">';
                    echo '<input type="hidden" name="action" value="checkout">';
                    echo '<button type="submit" class="btn btn-primary" onclick="return confirmCheckout();">Checkout</button>';
                    echo '</form>';
                    echo '</div>';
                } else {
                    echo '<p>Your cart is empty. <a href="shop.php">Continue Shopping</a></p>';
                }
                ?>
            </div>
        </section>
    </main>

    <!-- Checkout Success Popup Modal -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <div class="modal-success-icon">✓</div>
            <h2 class="modal-title">Order Confirmed!</h2>
            <p class="modal-subtitle">Thank you for your purchase</p>
            
            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#<?php echo isset($order_id) ? str_pad($order_id, 6, '0', STR_PAD_LEFT) : 'N/A'; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Items:</span>
                    <span class="detail-value"><?php echo isset($order_id) ? count($_POST['action'] ?? []) : 'N/A'; ?> items</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Date:</span>
                    <span class="detail-value"><?php echo date('M d, Y'); ?></span>
                </div>
                <div class="detail-row order-total">
                    <span>Order Total:</span>
                    <span>$<?php echo isset($order_id) ? number_format($total, 2) : '0.00'; ?></span>
                </div>
            </div>

            <p style="color: #7f8c8d; font-size: 14px;">A confirmation email has been sent to your inbox.</p>

            <div class="modal-buttons">
                <a href="shop.php" class="modal-btn btn-primary-modal">Back to Shop</a>
                <a href="orders.php?order_id=<?php echo isset($order_id) ? $order_id : ''; ?>" class="modal-btn btn-secondary-modal">View Receipt</a>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 Pastimes Clothing Store. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Show checkout modal if order was successful
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($checkout_success): ?>
                const modal = document.getElementById('checkoutModal');
                modal.classList.add('show');
            <?php endif; ?>
        });

        // Confirmation before checkout
        function confirmCheckout() {
            return confirm('Proceed with checkout? Your cart will be cleared after confirming.');
        }

        // Close modal when clicking outside
        const modal = document.getElementById('checkoutModal');
        if (modal) {
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    // Don't close on background click for order confirmation
                    // Users must use the buttons
                }
            });
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
