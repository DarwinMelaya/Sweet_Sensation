<?php
session_start();
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check for duplicate username
    $check_username_sql = "SELECT * FROM users WHERE username='$username'";
    $username_result = $conn->query($check_username_sql);

    if ($username_result->num_rows > 0) {
        $_SESSION['error'] = "Username already exists.";
        header("Location: ../pages/register.php");
        exit();
    }

    // Check for duplicate email
    $check_email_sql = "SELECT * FROM users WHERE email='$email'";
    $email_result = $conn->query($check_email_sql);

    if ($email_result->num_rows > 0) {
        $_SESSION['error'] = "Email already exists.";
        header("Location: ../pages/register.php");
        exit();
    }

    // Insert new user
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Registration successful. Please log in.";
        header("Location: ../pages/login.php");
    } else {
        $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
        header("Location: ../pages/register.php");
    }

    $conn->close();
}
?>
