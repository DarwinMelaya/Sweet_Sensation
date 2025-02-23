<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sweet Sensations</title>
    <!-- CSS FILE -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/product.css">
    <!-- BOX ICON LINKS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Header -->
    <header>
    <!-- Container -->
      <div class="container">
          <!-- Logo -->
          <h1 class="logo"><a href="../index.php" style="text-decoration: none; color:white;">Sweet Sensations</a></h1><h1 style="color: white; font-size:20px;">Register Account</h1>

      </div>
    </header>
    <!-- Register Form -->
    <section class="register">
        <div class="container">
            <h1>Register</h1>
            <form id="registerForm" action="../includes/register_process.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                    <span class="error" id="usernameError"></span>
                </div>
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
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <span class="error" id="confirmPasswordError"></span>
                </div>
                <?php
            if (isset($_SESSION['error'])) {
                echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<p class="success">' . htmlspecialchars($_SESSION['success']) . '</p>';
                unset($_SESSION['success']);
            }
            ?>
                <button type="submit">Register</button>
                <span style="font-size: 14px;">Already have an account? <a href="../pages/login.php" style="text-decoration: none;">Log in</a></span>
            </form>
            


        </div>
    </section>
    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <p><small>All rights reserved by Sweet Sensations &copy; 2025</small></p>
        </div>
    </footer>
    <script src="../assets/js/register.js"></script>
</body>
</html>
