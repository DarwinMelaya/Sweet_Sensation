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
        <h1>Your Cart</h1>

        <?php if (!empty($_SESSION['cart'])): ?>
          <div class="cart-container">
            <div style="width: 68%;"">
            <table class="cart-table">
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
                <button class="checkout-btn">Checkout</button>
            </div>
            </div>
          </div>
            

            
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</section>

<script>
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
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
