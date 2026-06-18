<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "clothingStore";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $inquiry_message = $conn->real_escape_string($_POST['message']);
    $product_description = $conn->real_escape_string($_POST['product_description']);
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Create uploads directory if it doesn't exist
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        $allowed = array('jpg', 'jpeg', 'gif', 'png');
        $filename = $_FILES['image']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array($ext, $allowed)) {
            $new_filename = 'uploads/' . time() . '_' . $user_id . '.' . $ext;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $new_filename)) {
                $image_path = $new_filename;
            } else {
                $message = "Error uploading image.";
                $message_type = "error";
            }
        } else {
            $message = "Invalid file type. Please upload JPG, JPEG, GIF, or PNG.";
            $message_type = "error";
        }
    }
    
    // Insert inquiry into database
    if (empty($message)) {
        $sql = "INSERT INTO product_inquiries (user_id, message, product_description, price, image_path) 
                VALUES ($user_id, '$inquiry_message', '$product_description', $price, '$image_path')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "Your inquiry has been submitted successfully! Admin will review it soon.";
            $message_type = "success";
            
            // Clear form
            $_POST = array();
        } else {
            $message = "Error submitting inquiry: " . $conn->error;
            $message_type = "error";
        }
    }
}

// Get user's inquiries
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM product_inquiries WHERE user_id = $user_id ORDER BY created_at DESC";
$inquiries_result = $conn->query($sql);
$inquiries = [];
if ($inquiries_result->num_rows > 0) {
    while ($row = $inquiries_result->fetch_assoc()) {
        $inquiries[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Inquiry - Pastimes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .inquiry-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }

        .inquiry-form {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .inquiry-form h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group textarea,
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            font-family: Arial, sans-serif;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .form-group textarea:focus,
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-group input[type="file"] {
            padding: 8px;
            cursor: pointer;
        }

        .form-group small {
            display: block;
            color: #666;
            margin-top: 5px;
            font-size: 0.9em;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .submit-btn {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: #764ba2;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            animation: slideDown 0.3s ease-out;
        }

        .message.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .message.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .inquiries-section h2 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-top: 40px;
        }

        .inquiry-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .inquiry-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .inquiry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .inquiry-date {
            font-size: 0.9em;
            color: #666;
        }

        .inquiry-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-reviewed {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .inquiry-content {
            margin: 15px 0;
        }

        .inquiry-content h4 {
            margin: 0 0 5px 0;
            color: #667eea;
        }

        .inquiry-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: 5px;
            margin: 10px 0;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .inquiry-image:hover {
            transform: scale(1.05);
        }

        .inquiry-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }

        .detail-item {
            font-size: 0.95em;
        }

        .detail-item strong {
            color: #667eea;
        }

        .no-inquiries {
            text-align: center;
            padding: 40px 20px;
            background: #f9f9f9;
            border-radius: 8px;
            color: #666;
        }

        .no-inquiries p {
            font-size: 1.1em;
            margin: 10px 0;
        }

        .admin-notes {
            background: #f0f7ff;
            padding: 15px;
            border-left: 4px solid #667eea;
            border-radius: 3px;
            margin-top: 15px;
        }

        .admin-notes h5 {
            margin: 0 0 10px 0;
            color: #0c5460;
        }

        .admin-notes p {
            margin: 0;
            color: #333;
            line-height: 1.6;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .inquiry-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .inquiry-details {
                grid-template-columns: 1fr;
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
                    <li><a href="product_inquiry.php">Send Inquiry</a></li>
                    <?php
                    if (isset($_SESSION['user_id'])) {
                        if ($_SESSION['role'] == 'admin') {
                            echo '<li><a href="admin/dashboard.php">Admin Dashboard</a></li>';
                        }
                        echo '<li><a href="logout.php">Logout</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="inquiry-container">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="inquiry-form">
                <h2>📸 Send Product Inquiry</h2>
                <p>Have a product you'd like to sell? Send us your details and pictures!</p>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="message">Your Message/Scenario *</label>
                        <textarea id="message" name="message" placeholder="Describe your situation, the product scenario, or why you want to sell this item..." required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="product_description">Product Description *</label>
                            <textarea id="product_description" name="product_description" placeholder="Describe the product details, condition, material, size, etc..." style="min-height: 100px;" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="price">Suggested Price ($) *</label>
                            <input type="number" id="price" name="price" step="0.01" placeholder="Enter price" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image">Upload Picture *</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                        <small>Accepted formats: JPG, JPEG, PNG, GIF (Max 5MB)</small>
                    </div>

                    <button type="submit" class="submit-btn">Submit Inquiry</button>
                </form>
            </div>

            <div class="inquiries-section">
                <h2>📋 Your Inquiries</h2>
                
                <?php if (!empty($inquiries)): ?>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <div class="inquiry-card">
                            <div class="inquiry-header">
                                <div>
                                    <strong>Inquiry #<?php echo $inquiry['id']; ?></strong>
                                    <div class="inquiry-date"><?php echo date('M d, Y H:i', strtotime($inquiry['created_at'])); ?></div>
                                </div>
                                <span class="inquiry-status status-<?php echo $inquiry['status']; ?>">
                                    <?php echo ucfirst($inquiry['status']); ?>
                                </span>
                            </div>

                            <div class="inquiry-content">
                                <h4>Your Message:</h4>
                                <p><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                            </div>

                            <div class="inquiry-details">
                                <div class="detail-item">
                                    <strong>Product Description:</strong>
                                    <p><?php echo nl2br(htmlspecialchars($inquiry['product_description'])); ?></p>
                                </div>
                                <div class="detail-item">
                                    <strong>Suggested Price:</strong>
                                    <p>$<?php echo number_format($inquiry['price'], 2); ?></p>
                                </div>
                            </div>

                            <?php if ($inquiry['image_path']): ?>
                                <div class="inquiry-content">
                                    <h4>Product Image:</h4>
                                    <img src="<?php echo htmlspecialchars($inquiry['image_path']); ?>" alt="Product" class="inquiry-image" onclick="openImageModal(this.src)">
                                </div>
                            <?php endif; ?>

                            <?php if ($inquiry['admin_notes']): ?>
                                <div class="admin-notes">
                                    <h5>📝 Admin Notes:</h5>
                                    <p><?php echo nl2br(htmlspecialchars($inquiry['admin_notes'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-inquiries">
                        <p>📭 No inquiries yet</p>
                        <p>Submit your first inquiry above to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Image Modal -->
    <div id="imageModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); align-items: center; justify-content: center;">
        <img id="modalImage" src="" alt="Product" style="max-width: 90%; max-height: 90%; border-radius: 10px;" onclick="closeImageModal()">
    </div>

    <footer>
        <div class="container">
            <p>&copy; 2026 Pastimes Clothing Store. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function openImageModal(src) {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('modalImage');
            img.src = src;
            modal.style.display = 'flex';
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target == modal) {
                closeImageModal();
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
