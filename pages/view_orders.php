<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../includes/db_connection.php';

// Get all orders with user information
$query = "SELECT o.*, u.username, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Sensations - View Orders</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        /* Add these new header styles */
        header {
            background-color: #DC143C;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo {
            color: #FFFFFF;
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

        /* Update the existing admin-orders section */
        .admin-orders {
            padding: 2rem 0;
            background-color: #f4f4f4;
        }

        /* Add description text styling */
        .description-text {
            text-align: center;
            font-weight: 500;
            margin: -10px 90px 10px 90px;
            border-bottom: 1px solid rgb(0, 0, 0, 0.2);
            padding-bottom: 10px;
        }

        /* Update the existing h1 style */
        h1 {
            text-align: center;
            font-weight: 600;
            font-size: 32px;
            margin-bottom: 20px;
            color: #1e293b;
            border-bottom: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .error {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .orders-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 0.875rem;
        }

        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }

        .orders-table tr:hover {
            background-color: #f8fafc;
        }

        .status-form select {
            padding: 0.5rem 2rem 0.5rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 6px;
            background-color: white;
            cursor: pointer;
            font-weight: 500;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 12px;
            transition: all 0.2s;
        }

        .status-form select:hover {
            border-color: #cbd5e1;
        }

        .status-form select.pending {
            background-color: #fffbeb;
            color: #92400e;
            border-color: #fcd34d;
        }

        .status-form select.processing {
            background-color: #eff6ff;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .status-form select.completed {
            background-color: #f0fdf4;
            color: #166534;
            border-color: #86efac;
        }

        .status-form select.cancelled {
            background-color: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .view-details-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .view-details-btn:hover {
            background-color: #2563eb;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .orders-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }

        @media (max-width: 640px) {
            h1 {
                font-size: 1.5rem;
            }

            .orders-table th,
            .orders-table td {
                padding: 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1 class="logo">Sweet Sensations</h1>
            <nav>
                <ul>
                    <li><a href="../pages/admin_dashboard.php">Dashboard</a></li>
                    <li><a href="../pages/manage_users.php">Manage Users</a></li>
                    <li><a href="../pages/manage_products.php">Manage Products</a></li>
                    <span style="color: white;">|</span>
                    <li><a href="../includes/logout.php">Log out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="admin-orders">
        <div class="container">
            <h1>Order Management</h1>
            <h5 class="description-text">View and manage all customer orders below.</h5>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="message success">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['username']); ?></td>
                            <td><?php echo htmlspecialchars($order['email']); ?></td>
                            <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <form method="POST" action="../includes/update_order_status.php" class="status-form">
                                    <select name="status" onchange="this.form.submit()" class="<?php echo $order['status']; ?>">
                                        <?php
                                        $statuses = ['pending', 'processing', 'completed', 'cancelled'];
                                        foreach ($statuses as $status) {
                                            $selected = ($status === $order['status']) ? 'selected' : '';
                                            echo "<option value=\"$status\" $selected>" . ucfirst($status) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                </form>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <a href="view_order_details.php?id=<?php echo $order['id']; ?>" class="view-details-btn">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <script>
        // Auto-hide messages after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const messages = document.querySelectorAll('.message');
                messages.forEach(function(message) {
                    message.style.display = 'none';
                });
            }, 3000);
        });
    </script>
</body>

</html>