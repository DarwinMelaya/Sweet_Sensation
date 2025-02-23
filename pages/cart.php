<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure cart is set in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart updates and removals
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['id'] == $product_id) {
                $_SESSION['cart'][$index]['quantity'] = (int)$quantity; // Update quantity
                break;
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];

        // Corrected removal method: Find index and remove it
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['id'] == $product_id) {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
                break;
            }
        }
    }
}

// Add this near the top of the file to get user's orders
$orders = [];
if (isset($_SESSION['user_id'])) {
    require_once '../includes/db_connection.php';
    $stmt = $conn->prepare("SELECT o.*, GROUP_CONCAT(p.name SEPARATOR ', ') as products 
                           FROM orders o 
                           LEFT JOIN order_items oi ON o.id = oi.order_id 
                           LEFT JOIN products p ON oi.product_id = p.id 
                           WHERE o.user_id = ? 
                           GROUP BY o.id 
                           ORDER BY o.created_at DESC");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Sensations - Cart</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product.css">
    <link rel="stylesheet" href="../assets/css/cart.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

    <?php include '../components/header.php'; ?>

    <section class="cart">
        <div class="container">
            <!-- Add tabs for Cart and Orders -->
            <div class="cart-tabs">
                <button class="tab-btn active" onclick="showTab('cart-content')">Shopping Cart</button>
                <button class="tab-btn" onclick="showTab('orders-content')">My Orders</button>
            </div>

            <div id="cart-content" class="tab-content active">
                <h1>Your Cart</h1>

                <?php if (!empty($_SESSION['cart'])): ?>
                    <div class="cart-container">
                        <div style="width: 68%;"">
                <table class=" cart-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_price = 0;
                                $total_quantity = 0;
                                foreach ($_SESSION['cart'] as $item):
                                    $total_price += $item['price'] * $item['quantity'];
                                    $total_quantity += $item['quantity'];
                                ?>
                                    <tr>
                                        <td><img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="50" height="50"></td>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td>₱<span class="item-price"><?php echo number_format($item['price'], 2); ?></span></td>
                                        <td>
                                            <!-- Update Form -->
                                            <form method="POST" action="update_cart.php" class="update-cart-form">
                                                <div class="quantity-adjuster">
                                                    <button type="button" class="quantity-btn" onclick="adjustQuantity(this, -1)">-</button>
                                                    <input type="number" name="quantity" value="<?php echo (int)$item['quantity']; ?>" min="1" class="quantity-input" data-price="<?php echo $item['price']; ?>" data-id="<?php echo $item['id']; ?>">
                                                    <button type="button" class="quantity-btn" onclick="adjustQuantity(this, 1)">+</button>
                                                </div>
                                                <input type="hidden" name="product_id" value="<?php echo (int)$item['id']; ?>">
                                            </form>
                                        </td>
                                        <td>₱<span class="item-total"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span></td>
                                        <td>
                                            <!-- Remove Form -->
                                            <form method="POST" action="">
                                                <input type="hidden" name="product_id" value="<?php echo (int)$item['id']; ?>">
                                                <button type="submit" name="remove_item" class="remove-item-btn">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            </table>
                        </div>
                        <div style="width: 30%;">
                            <div class="cart-summary">
                                <h2>Cart Summary</h2>
                                <p>Total Quantity: <span id="total-quantity"><?php echo $total_quantity; ?></span></p>
                                <p>Total Price: ₱<span id="total-price"><?php echo number_format($total_price, 2); ?></span></p>
                                <form method="POST" action="../includes/process_checkout.php">
                                    <button type="submit" name="checkout" class="checkout-btn">Checkout</button>
                                </form>
                            </div>
                        </div>
                    </div>



                <?php else: ?>
                    <p>Your cart is empty.</p>
                <?php endif; ?>
            </div>

            <div id="orders-content" class="tab-content">
                <h1>My Orders</h1>
                <?php if (!empty($orders)): ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Products</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['products']); ?></td>
                                    <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="order-status <?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>You haven't placed any orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <style>
        .cart-tabs {
            margin-bottom: 2rem;
            border-bottom: 1px solid #ddd;
        }

        .tab-btn {
            padding: 1rem 2rem;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1.1rem;
            position: relative;
        }

        .tab-btn.active {
            color: #007bff;
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #007bff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .orders-table th,
        .orders-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .orders-table th {
            background-color: #f5f5f5;
        }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .order-status.pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .order-status.processing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .order-status.completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .order-status.cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab content
            document.getElementById(tabId).classList.add('active');

            // Add active class to clicked tab
            event.currentTarget.classList.add('active');
        }

        function adjustQuantity(button, amount) {
            const input = button.parentElement.querySelector('.quantity-input');
            let quantity = parseInt(input.value) + amount;
            if (quantity < 1) quantity = 1;
            input.value = quantity;
            updateItemTotal(input);
            updateCartSummary();
            updateCart(input);
        }

        function updateItemTotal(input) {
            const price = parseFloat(input.dataset.price);
            const quantity = parseInt(input.value);
            const total = price * quantity;
            input.closest('tr').querySelector('.item-total').textContent = total.toFixed(2);
        }

        function updateCartSummary() {
            let totalQuantity = 0;
            let totalPrice = 0;

            document.querySelectorAll('.quantity-input').forEach(input => {
                const quantity = parseInt(input.value);
                const price = parseFloat(input.dataset.price);

                totalQuantity += quantity;
                totalPrice += price * quantity;
            });

            document.getElementById('total-quantity').textContent = totalQuantity;
            document.getElementById('total-price').textContent = totalPrice.toFixed(2);
        }

        function updateCart(input) {
            const productID = input.dataset.id;
            const quantity = input.value;

            fetch('update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `product_id=${productID}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log("Cart updated successfully");
                    } else {
                        console.error("Error updating cart:", data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', function() {
                updateItemTotal(this);
                updateCartSummary();
                updateCart(this);
            });
        });
    </script>

</body>

</html>