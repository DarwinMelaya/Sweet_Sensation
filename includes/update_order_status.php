<?php
session_start();
require_once 'db_connection.php';

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $current_status = $_POST['current_status'];
    $total_amount = $_POST['total_amount'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update order status
        $update_query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();

        // If status is being changed to 'completed', record the sale
        if ($new_status === 'completed' && $current_status !== 'completed') {
            $insert_sale_query = "INSERT INTO sales (order_id) VALUES (?)";
            $stmt = $conn->prepare($insert_sale_query);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
        }

        // Commit transaction
        $conn->commit();

        $_SESSION['success'] = "Order status updated successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error updating order status: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request method";
}

// Redirect back to view orders page
header('Location: ../pages/view_orders.php');
exit();
