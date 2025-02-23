<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sweet Sensations</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            width: 90%;
            margin: 0 auto;
        }

        header {
            background-color: #DC143C;
            padding: 20px 0;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo {
            color: #FFFFFF;
            font-size: 24px;
        }

        header nav ul {
            display: flex;
            gap: 20px;
            list-style-type: none;
        }

        header nav ul a {
            color: #FFFFFF;
            text-decoration: none;
            font-weight: 600;
        }

        header nav ul a:hover {
            text-decoration: underline;
            text-underline-offset: 5px;
        }

        .dashboard {
            padding: 50px 0;
        }

        .dashboard h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
        }

        .dashboard p {
            text-align: center;
            margin-bottom: 40px;
            font-size: 16px;
        }

        .admin-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .admin-actions .btn {
            padding: 10px 20px;
            background-color: #DC143C;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .admin-actions .btn:hover {
            background-color: #a10e2d;
        }

        footer {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #000;
            width: 100%;
            height: 100px;
        }

        footer p {
            text-align: center;
            font-size: 18px;
            color: #888;
        }

        footer small {
            font-style: italic;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1 class="logo">Admin Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="../includes/logout.php">Log out</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <section class="dashboard">
        <div class="container">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <p>Use the navigation menu to manage the website.</p>
            <div class="admin-actions">
                <a href="manage_products.php" class="btn">Manage Products</a>
                <a href="manage_users.php" class="btn">Manage Users</a>
                <a href="view_orders.php" class="btn">View Orders</a>
            </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <p><small>All rights reserved by Sweet Sensations &copy; 2025</small></p>
        </div>
    </footer>
</body>

</html>