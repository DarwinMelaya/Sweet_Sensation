<?php
session_start();
require_once '../includes/db_connection.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../pages/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    // Update user information
    $sql = "UPDATE users SET username = ?, email = ?, user_type = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $username, $email, $user_type, $id);

    if ($stmt->execute()) {
        header('Location: ../pages/manage_users.php?success=1');
    } else {
        header('Location: ../pages/manage_users.php?error=1');
    }
    exit();
}
