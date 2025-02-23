<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <!-- Container -->
    <div class="container">
        <!-- Logo -->
        <h1 class="logo"><a href="../index.php" style="text-decoration: none; color:white;">Sweet Sensations</a></h1>
        <nav id="menuActive">
            <ul>
                <li><a href="/Sweet Sensations/index.php">Home</a></li>
                <li><a href="/Sweet Sensations/pages/product.php">Products</a></li>
                <li><a href="/Sweet Sensations/pages/about.php">About</a></li>
                <li><a href="/Sweet Sensations/pages/contact.php">Contact</a></li>
                <li class="cart-link">
                    <a href="/Sweet Sensations/pages/cart.php">Cart
                        <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="login-link">
                        <a href="/Sweet Sensations/includes/logout.php">Log out</a>
                        <span class="devider"></span>
                    </li>
                    <script>document.body.classList.add('logged-in');</script>
                <?php else: ?>
                    <li class="login-link">
                        <a href="/Sweet Sensations/pages/login.php">Sign In</a><span style="color: white;"> |</span>
                        <a href="/Sweet Sensations/pages/register.php">Sign Up</a>
                        <span class="devider"></span>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>



