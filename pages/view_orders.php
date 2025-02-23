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
        .message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #166534;
        }

        .error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #991b1b;
        }

        .status-form select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
        }

        .status-form select.pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-form select.processing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-form select.completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-form select.cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; ?>

    <section class="admin-orders">
        <div class="container">
            <h1>Order Management</h1>

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