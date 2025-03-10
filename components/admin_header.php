<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="container">
        <h1 class="logo"><a href="../index.php" style="text-decoration: none; color:white;">Sweet Sensations</a></h1>
        <div class="burger-menu">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a></li>
                <li><a href="manage_products.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_products.php' ? 'class="active"' : ''; ?>>Manage Products</a></li>
                <li><a href="manage_users.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'class="active"' : ''; ?>>Manage Users</a></li>
                <li><a href="view_orders.php" <?php echo basename($_SERVER['PHP_SELF']) == 'view_orders.php' ? 'class="active"' : ''; ?>>View Orders</a></li>
                <li><a href="sales_report.php" <?php echo basename($_SERVER['PHP_SELF']) == 'sales_report.php' ? 'class="active"' : ''; ?>>Sales Report</a></li>
                <span style="color: white;">|</span>
                <li><a href="../includes/logout.php">Log out</a></li>
            </ul>
        </nav>
    </div>
</header>

<style>
    /* Use !important to ensure these styles take precedence */
    header {
        background-color: #DC143C !important;
        padding: 20px 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }

    header .container {
        width: 90% !important;
        margin: 0 auto !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 0 !important;
    }

    header .logo {
        color: #FFFFFF !important;
        font-size: 24px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    header .logo a {
        text-decoration: none !important;
        color: white !important;
    }

    header nav ul {
        display: flex !important;
        gap: 20px !important;
        list-style-type: none !important;
        margin: 0 !important;
        padding: 0 !important;
        align-items: center !important;
    }

    header nav ul li {
        margin: 0 !important;
        padding: 0 !important;
    }

    header nav ul a {
        color: #FFFFFF !important;
        text-decoration: none !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        padding: 5px 0 !important;
    }

    header nav ul a:hover {
        text-decoration: underline !important;
        text-underline-offset: 5px !important;
    }

    header nav ul a.active {
        text-decoration: underline !important;
        text-underline-offset: 5px !important;
    }

    /* Add these new styles for mobile responsiveness */
    .burger-menu {
        display: none !important;
        cursor: pointer !important;
        padding: 10px !important;
    }

    .burger-menu .bar {
        width: 25px !important;
        height: 3px !important;
        background-color: white !important;
        margin: 5px 0 !important;
        transition: 0.3s !important;
    }

    @media screen and (max-width: 768px) {
        .burger-menu {
            display: block !important;
        }

        header nav {
            display: none !important;
            width: 100% !important;
            position: absolute !important;
            top: 80px !important;
            left: 0 !important;
            background-color: #DC143C !important;
            padding: 20px 0 !important;
        }

        header nav.active {
            display: block !important;
        }

        header nav ul {
            flex-direction: column !important;
            align-items: center !important;
            gap: 15px !important;
        }

        header .container {
            position: relative !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const burgerMenu = document.querySelector('.burger-menu');
        const nav = document.querySelector('nav');

        burgerMenu.addEventListener('click', function() {
            nav.classList.toggle('active');
        });
    });
</script>