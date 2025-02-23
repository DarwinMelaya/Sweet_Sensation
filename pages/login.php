<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sweet Sensations</title>
    <!-- CSS FILE -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product.css">
    <!-- BOX ICON LINKS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .login {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)), url('../assets/img/background.jpg');
            background-size: cover;
            background-position: center;
        }

        .login .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #666;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: none;
            margin: 1rem 0;
            transition: transform 0.2s ease;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
        }

        .error,
        .success {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <!-- Container -->
        <div class="container">
            <!-- Logo -->
            <h1 class="logo"><a href="../index.php" style="text-decoration: none; color:white;">Sweet Sensations</a></h1>
            <h1 style="color: white; font-size:20px;">Login Account</h1>

        </div>
    </header>
    <!-- Login Form -->
    <section class="login">
        <div class="container">
            <?php
            if (isset($_SESSION['success'])) {
                echo '<p class="success">' . htmlspecialchars($_SESSION['success']) . '</p>';
                unset($_SESSION['success']);
            }
            ?>
            <h1>Login</h1>
            <form id="loginForm" action="../includes/login_process.php" method="POST">
                <input type="hidden" name="user_type" value="user">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error" id="emailError"></span>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error" id="passwordError"></span>
                </div>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
                    unset($_SESSION['error']);
                }

                ?>
                <button type="submit">Login</button>
                <span style="font-size: 14px;">Don't have an account? <a href="../pages/register.php" style="text-decoration: none;">Register</a></span>
            </form>

        </div>
    </section>
    <!-- Footer -->
    <footer>
        <div class="container">
            <p><small>All rights reserved by Sweet Sensations &copy; 2025</small></p>
        </div>
    </footer>
    <script src="../assets/js/login.js"></script>
</body>

</html>