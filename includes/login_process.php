<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type']; // Ensure user_type is set in the session
            if ($user['user_type'] == 'admin') {
                header("Location: ../pages/admin_dashboard.php");
            } else {
                header("Location: ../pages/product.php");
            }
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: ../pages/login.php");
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
        header("Location: ../pages/login.php");
    }

    $conn->close();
}
?>
