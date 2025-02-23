<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $updated = false;
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['id'] == $product_id) {
            $_SESSION['cart'][$index]['quantity'] = $quantity; // Update session
            $updated = true;
            break;
        }
    }

    echo json_encode(["success" => $updated, "message" => $updated ? "Cart updated" : "Item not found"]);
    exit;
}
?>
