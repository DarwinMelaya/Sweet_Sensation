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
            background-color: #DC143C;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .view-details-btn:hover {
            background-color: #b91c1c;
            transform: scale(1.05);
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

        /* Updated Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
            position: relative;
            max-height: 85vh;
            overflow-y: auto;
        }

        .close {
            position: absolute;
            right: 1.5rem;
            top: 1rem;
            font-size: 1.8rem;
            font-weight: bold;
            color: #DC143C;
            cursor: pointer;
            transition: all 0.2s;
        }

        .close:hover {
            color: #b91c1c;
            transform: scale(1.1);
        }

        .modal h2 {
            color: #DC143C;
            margin-bottom: 1.5rem;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #ffe5e5;
        }

        .order-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .order-info-group {
            background: #fff5f5;
            padding: 1.2rem;
            border-radius: 8px;
            border: 1px solid #ffe5e5;
        }

        .order-info-group h3 {
            color: #DC143C;
            margin-bottom: 0.8rem;
            font-size: 1.1rem;
        }

        .order-info-group p {
            margin: 0.5rem 0;
            color: #4a5568;
        }

        .order-items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1.5rem;
            border: 1px solid #ffe5e5;
            border-radius: 8px;
            overflow: hidden;
        }

        .order-items-table th {
            background-color: #DC143C;
            color: white;
            padding: 1rem;
            text-align: left;
        }

        .order-items-table td {
            padding: 1rem;
            border-bottom: 1px solid #ffe5e5;
        }

        .order-items-table tr:last-child td {
            border-bottom: none;
        }

        .order-items-table tr:hover {
            background-color: #fff5f5;
        }

        @media (max-width: 768px) {
            .order-detail-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 95%;
                margin: 2% auto;
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
                    <li><a href="../pages/sales_report.php">Sales Report</a></li>
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
                                    <input type="hidden" name="total_amount" value="<?php echo $order['total_amount']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $order['status']; ?>">
                                </form>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <button class="view-details-btn" data-order-id="<?php echo $order['id']; ?>">View Details</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Update the modal HTML -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Order Details</h2>
            <div id="orderDetails"></div>
        </div>
    </div>

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

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('orderModal');
            const closeBtn = document.querySelector('.close');
            const orderDetails = document.getElementById('orderDetails');

            // Add click event to all View Details buttons
            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    fetchOrderDetails(orderId);
                });
            });

            // Close modal when clicking (x)
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });

            function fetchOrderDetails(orderId) {
                fetch(`../includes/get_order_details.php?id=${orderId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        displayOrderDetails(data);
                        modal.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error fetching order details: ' + error.message);
                    });
            }

            function displayOrderDetails(data) {
                orderDetails.innerHTML = `
                    <div class="order-detail-grid">
                        <div class="order-info-group">
                            <h3>Order Information</h3>
                            <p><strong>Order ID:</strong> #${data.order.id}</p>
                            <p><strong>Date:</strong> ${data.order.created_at}</p>
                            <p><strong>Status:</strong> ${data.order.status}</p>
                            <p><strong>Total Amount:</strong> ₱${data.order.total_amount}</p>
                        </div>
                        <div class="order-info-group">
                            <h3>Customer Information</h3>
                            <p><strong>Name:</strong> ${data.order.username}</p>
                            <p><strong>Email:</strong> ${data.order.email}</p>
                        </div>
                    </div>
                    <div class="order-items">
                        <h3>Order Items</h3>
                        <table class="order-items-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.items.map(item => `
                                    <tr>
                                        <td>${item.name}</td>
                                        <td>₱${item.price}</td>
                                        <td>${item.quantity}</td>
                                        <td>₱${item.subtotal}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }
        });
    </script>
</body>

</html>