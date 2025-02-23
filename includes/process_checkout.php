<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

require_once '../includes/db_connection.php';

try {
    // Calculate total amount
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Start transaction
    $conn->begin_transaction();

    // Create order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->bind_param("id", $_SESSION['user_id'], $total_amount);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Create order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    // Clear cart
    $_SESSION['cart'] = [];

    // Redirect with success message
    $_SESSION['message'] = "Order placed successfully!";
    header('Location: cart.php');
    exit();
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    $_SESSION['error'] = "Error processing order. Please try again.";
    header('Location: cart.php');
    exit();
}
