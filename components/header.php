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
        <div class="burger-menu">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
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
                    <script>
                        document.body.classList.add('logged-in');
                    </script>
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

<style>
    /* Add these styles at the end of your existing styles */
    .burger-menu {
        display: none;
        cursor: pointer;
        padding: 10px;
    }

    .burger-menu .bar {
        width: 25px;
        height: 3px;
        background-color: white;
        margin: 5px 0;
        transition: 0.3s;
    }

    @media screen and (max-width: 768px) {
        .burger-menu {
            display: block;
        }

        #menuActive {
            display: none;
            width: 100%;
            position: absolute;
            top: 80px;
            left: 0;
            background-color: #DC143C;
            padding: 20px 0;
        }

        #menuActive.active {
            display: block;
        }

        #menuActive ul {
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .container {
            position: relative;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const burgerMenu = document.querySelector('.burger-menu');
        const nav = document.querySelector('#menuActive');

        burgerMenu.addEventListener('click', function() {
            nav.classList.toggle('active');
        });
    });
</script>