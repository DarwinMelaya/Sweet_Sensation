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
                <div class="cart-section">
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
                        <p class="empty-cart">Your cart is empty. Start shopping to add items to your cart!</p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="orders-content" class="tab-content">
                <div class="orders-section">
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
        </div>
    </section>

    <style>
        /* Add header styles to match header.php */
        header {
            background-color: #DC143C;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo {
            font-size: 1.5rem;
            margin: 0;
        }

        header nav ul {
            display: flex;
            gap: 2rem;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        header nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }

        /* Existing cart styles */
        .cart {
            padding-top: 2rem;
            /* Adjusted to account for sticky header */
            padding: 2rem 0;
            background-color: #f8fafc;
            min-height: 80vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        h1 {
            color: #1e293b;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        /* Tabs Styling */
        .cart-tabs {
            margin-bottom: 2rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            gap: 1rem;
        }

        .tab-btn {
            padding: 1rem 2rem;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .tab-btn:hover {
            color: #DC143C;
        }

        .tab-btn.active {
            color: #DC143C;
            position: relative;
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #DC143C;
        }

        /* Updated Cart Table Styling */
        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.1);
            margin-bottom: 2rem;
        }

        .cart-table th {
            background-color: #DC143C;
            color: white;
            font-weight: 600;
            padding: 1.2rem;
            text-align: left;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .cart-table td {
            padding: 1.2rem;
            border-bottom: 1px solid #ffe5e5;
            vertical-align: middle;
            color: #333;
        }

        .cart-table tr:hover {
            background-color: #fff5f5;
        }

        .cart-table img {
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #ffe5e5;
            transition: transform 0.2s;
        }

        .cart-table img:hover {
            transform: scale(1.1);
        }

        /* Updated Quantity Adjuster Styling */
        .quantity-adjuster {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 2px solid #DC143C;
            border-radius: 8px;
            padding: 0.25rem;
            background: white;
            max-width: 120px;
        }

        .quantity-btn {
            background-color: #DC143C;
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s;
        }

        .quantity-btn:hover {
            background-color: #b91c1c;
            transform: scale(1.05);
        }

        .quantity-input {
            width: 40px;
            text-align: center;
            border: none;
            font-size: 1rem;
            padding: 0.25rem;
            color: #DC143C;
            font-weight: 600;
        }

        /* Updated Remove Button Styling */
        .remove-item-btn {
            background-color: #fff;
            color: #DC143C;
            border: 2px solid #DC143C;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .remove-item-btn:hover {
            background-color: #DC143C;
            color: white;
            transform: scale(1.05);
        }

        /* Updated Cart Summary Styling */
        .cart-summary {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.1);
            border: 2px solid #ffe5e5;
        }

        .cart-summary h2 {
            color: #DC143C;
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #ffe5e5;
            text-align: center;
        }

        .cart-summary p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.2rem;
            color: #333;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .cart-summary p span {
            color: #DC143C;
            font-weight: 600;
        }

        .checkout-btn {
            width: 100%;
            background-color: #DC143C;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .checkout-btn:hover {
            background-color: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.2);
        }

        /* Updated Empty Cart Message */
        .cart-section p.empty-cart {
            text-align: center;
            color: #DC143C;
            font-size: 1.2rem;
            padding: 3rem;
            background: #fff5f5;
            border-radius: 12px;
            border: 2px dashed #ffe5e5;
            margin: 2rem 0;
        }

        /* Price and Total Styling */
        .item-price,
        .item-total {
            color: #DC143C;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Cart Container Layout */
        .cart-container {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }

        /* Orders Table Styling */
        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(220, 20, 60, 0.1);
            margin-top: 1.5rem;
        }

        .orders-table th {
            background-color: #DC143C;
            color: white;
            font-weight: 600;
            padding: 1.2rem;
            text-align: left;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .orders-table td {
            padding: 1.2rem;
            border-bottom: 1px solid #ffe5e5;
            color: #333;
            font-size: 0.95rem;
        }

        .orders-table tr:hover {
            background-color: #fff5f5;
        }

        /* Order Status Badges */
        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-status.pending {
            background-color: #fff7ed;
            color: #c2410c;
            border: 1px solid #fdba74;
        }

        .order-status.processing {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        .order-status.completed {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #86efac;
        }

        .order-status.cancelled {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        /* Orders Section Empty State */
        .orders-section p {
            text-align: center;
            color: #DC143C;
            font-size: 1.2rem;
            padding: 3rem;
            background: #fff5f5;
            border-radius: 12px;
            border: 2px dashed #ffe5e5;
            margin: 2rem 0;
        }

        /* Order ID Column */
        .orders-table td:first-child {
            font-weight: 600;
            color: #DC143C;
        }

        /* Total Amount Column */
        .orders-table td:nth-child(3) {
            font-weight: 600;
            color: #DC143C;
        }

        /* Date Column */
        .orders-table td:last-child {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .cart-container {
                flex-direction: column;
            }

            .cart-container>div {
                width: 100% !important;
            }

            .cart-summary {
                margin-top: 1.5rem;
            }

            .cart-table {
                display: block;
                overflow-x: auto;
            }
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .cart-section,
        .orders-section {
            background-color: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Empty state styling */
        .cart-section p,
        .orders-section p {
            text-align: center;
            color: #64748b;
            font-size: 1.1rem;
            padding: 2rem;
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

            // Update page title based on active tab
            const title = document.querySelector('h1');
            if (tabId === 'cart-content') {
                title.textContent = 'Your Cart';
            } else {
                title.textContent = 'My Orders';
            }
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