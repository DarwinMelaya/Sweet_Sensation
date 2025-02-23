<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    exit('Unauthorized');
}

require_once '../includes/db_connection.php';

if (isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];

    try {
        // Get order and user information
        $order_query = "SELECT o.*, u.username, u.email 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ?";

        $stmt = $conn->prepare($order_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order_result = $stmt->get_result();
        $order = $order_result->fetch_assoc();

        if (!$order) {
            throw new Exception('Order not found');
        }

        // Get order items
        $items_query = "SELECT oi.*, p.name, p.price 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?";

        $stmt = $conn->prepare($items_query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $items_result = $stmt->get_result();
        $items = $items_result->fetch_all(MYSQLI_ASSOC);

        // Format the response data
        $response = [
            'order' => [
                'id' => $order['id'],
                'created_at' => date('M d, Y H:i', strtotime($order['created_at'])),
                'status' => ucfirst($order['status']),
                'total_amount' => number_format($order['total_amount'], 2),
                'username' => htmlspecialchars($order['username']),
                'email' => htmlspecialchars($order['email'])
            ],
            'items' => array_map(function ($item) {
                return [
                    'name' => htmlspecialchars($item['name']),
                    'price' => number_format($item['price'], 2),
                    'quantity' => $item['quantity'],
                    'subtotal' => number_format($item['price'] * $item['quantity'], 2)
                ];
            }, $items)
        ];

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID not provided']);
}

$conn->close();
