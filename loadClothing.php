<?php
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

// Check if products already exist
$check_products = "SELECT COUNT(*) FROM products";
$result = $conn->query($check_products);
$row = $result->fetch_row();

if ($row[0] > 0) {
    echo "Products already loaded in database<br>";
} else {
    // Sample clothing products
    $products = array(
        array('T-Shirt', 'Classic comfortable cotton t-shirt', 29.99, 50, 'Tops', 'images/tshirt.jpg'),
        array('Jeans', 'Durable blue denim jeans', 79.99, 30, 'Bottoms', 'images/jeans.jpg'),
        array('Hoodie', 'Warm and cozy hooded sweatshirt', 59.99, 25, 'Outerwear', 'images/hoodie.jpg'),
        array('Polo Shirt', 'Classic polo shirt for casual wear', 49.99, 35, 'Tops', 'images/polo.jpg'),
        array('Shorts', 'Comfortable shorts for summer', 39.99, 40, 'Bottoms', 'images/shorts.jpg'),
        array('Jacket', 'Stylish winter jacket', 99.99, 20, 'Outerwear', 'images/jacket.jpg'),
        array('Dress', 'Elegant dress for special occasions', 89.99, 15, 'Dresses', 'images/dress.jpg'),
        array('Sweater', 'Cozy knit sweater', 69.99, 28, 'Tops', 'images/sweater.jpg'),
        array('Leggings', 'Comfortable athletic leggings', 44.99, 32, 'Bottoms', 'images/leggings.jpg'),
        array('Cardigan', 'Stylish layering piece', 74.99, 22, 'Outerwear', 'images/cardigan.jpg')
    );

    $insert_sql = "INSERT INTO products (product_name, description, price, quantity, category, image_url) VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert_sql);
    
    foreach ($products as $product) {
        $stmt->bind_param("ssdiss", $product[0], $product[1], $product[2], $product[3], $product[4], $product[5]);
        if ($stmt->execute()) {
            echo "Product '{$product[0]}' inserted successfully<br>";
        } else {
            echo "Error inserting product: " . $stmt->error . "<br>";
        }
    }
    
    $stmt->close();
    echo "<br><strong>All clothing products loaded successfully!</strong>";
}

$conn->close();
?>
