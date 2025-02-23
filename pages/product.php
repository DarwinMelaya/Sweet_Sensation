<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sweet_sensations";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    session_start();
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $item_exists = false;
    foreach ($_SESSION['cart'] as $item) {
        if ($item['id'] == $product_id) {
            $item_exists = true;
            break;
        }
    }

    if ($item_exists) {
        $message = "Item is already in the cart.";
    } else {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'image' => $product_image,
            'quantity' => 1
        ];
        header('Location: cart.php');
        exit();
    }
}

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Sensations - Products</title>
    <!-- CSS FILE -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product.css">
    <!-- BOX ICON LINKS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/Sweet Sensations/components/header.php'; ?>
    <!-- ...existing code... -->

    <section class="products">
        <div class="container">
            <h1>Delicious Cakes & Pastries</h1>
            <p>Order Your Perfect Cake and Pastry For Any Special Occasion</p>
            <?php if (isset($message)): ?>
                <p class="message"><?php echo $message; ?></p>
            <?php endif; ?>
            <div class="product-container">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="product">
                        <div class="image">
                            <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                        </div>
                        <div class="pduct-info">
                            <h4><?php echo $row['name']; ?></h4>
                            <p style="font-size: 12px;"><?php echo $row['description']; ?></p>
                            <p class="product-price">₱<?php echo $row['price']; ?></p>
                        </div>
                        <div class="addcart-btn">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
                                    <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                                    <input type="hidden" name="product_image" value="<?php echo $row['image']; ?>">
                                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <button class="add-to-cart" onclick="alert('Please log in to add items to the cart.');">Add to Cart</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer>
        <div class="container">
            <p><small>All rights reserved by Sweet Sensations &copy; 2025</small></p>
        </div>
    </footer>
</body>
</html>
