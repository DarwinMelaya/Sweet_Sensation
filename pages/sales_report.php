<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: index.php');
    exit();
}

require_once '../includes/db_connection.php';

// Get date range from query parameters or use defaults
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get period for comparison (previous period of same length)
$date_diff = strtotime($end_date) - strtotime($start_date);
$prev_end = date('Y-m-d', strtotime($start_date) - 1);
$prev_start = date('Y-m-d', strtotime($prev_end) - $date_diff);

// Current period sales query
$query = "SELECT 
    DATE(s.recorded_at) as sale_date,
    COUNT(DISTINCT s.order_id) as orders_count,
    (SELECT SUM(total_amount) 
     FROM orders 
     WHERE id IN (SELECT order_id FROM sales WHERE DATE(recorded_at) = DATE(s.recorded_at))
    ) as total_sales
    FROM sales s
    WHERE DATE(s.recorded_at) BETWEEN ? AND ?
    GROUP BY DATE(s.recorded_at)
    ORDER BY sale_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Calculate summary statistics
$summary_query = "SELECT 
    (SELECT SUM(total_amount) 
     FROM orders 
     WHERE id IN (SELECT order_id FROM sales WHERE DATE(recorded_at) BETWEEN ? AND ?)
    ) as current_period_total,
    (SELECT SUM(total_amount)
     FROM orders
     WHERE id IN (SELECT order_id FROM sales WHERE DATE(recorded_at) BETWEEN ? AND ?)
    ) as previous_period_total,
    (SELECT COUNT(DISTINCT order_id) 
     FROM sales 
     WHERE DATE(recorded_at) BETWEEN ? AND ?
    ) as total_orders,
    (SELECT AVG(daily_total) 
     FROM (
         SELECT DATE(recorded_at) as sale_date, 
                SUM(total_amount) as daily_total
         FROM orders o
         JOIN sales s ON o.id = s.order_id
         WHERE DATE(s.recorded_at) BETWEEN ? AND ?
         GROUP BY DATE(recorded_at)
     ) as daily_totals
    ) as daily_average,
    (SELECT MAX(daily_total)
     FROM (
         SELECT DATE(recorded_at) as sale_date,
                SUM(total_amount) as daily_total
         FROM orders o
         JOIN sales s ON o.id = s.order_id
         WHERE DATE(s.recorded_at) BETWEEN ? AND ?
         GROUP BY DATE(recorded_at)
     ) as peak_sales
    ) as highest_daily_sales";

$stmt = $conn->prepare($summary_query);
$stmt->bind_param(
    "ssssssssss",
    $start_date,
    $end_date,
    $prev_start,
    $prev_end,
    $start_date,
    $end_date,
    $start_date,
    $end_date,
    $start_date,
    $end_date
);
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();

// Calculate growth percentage
$growth_rate = 0;
if ($summary['previous_period_total'] > 0) {
    $growth_rate = (($summary['current_period_total'] - $summary['previous_period_total']) / $summary['previous_period_total']) * 100;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Sweet Sensations</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .report-container {
            padding: 2rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .report-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .date-filter {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .stat-card h3 {
            color: #64748b;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-card p {
            color: #1e293b;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .growth-positive {
            color: #059669;
        }

        .growth-negative {
            color: #dc2626;
        }

        .sales-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 2rem;
        }

        .sales-table th {
            background: #f1f5f9;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }

        .sales-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .sales-table tr:hover {
            background-color: #f8fafc;
        }

        .btn {
            background: #DC143C;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        .btn:hover {
            background: #b91c1c;
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

    <div class="container">
        <div class="report-container">
            <div class="report-header">
                <h1>Sales Report</h1>
                <p>Period: <?php echo date('F d, Y', strtotime($start_date)); ?> - <?php echo date('F d, Y', strtotime($end_date)); ?></p>
            </div>

            <form class="date-filter" method="GET">
                <div class="form-group">
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>" onchange="this.form.submit()">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>" onchange="this.form.submit()">
                </div>
            </form>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Sales</h3>
                    <p>₱<?php echo number_format($summary['current_period_total'], 2); ?></p>
                    <span class="<?php echo $growth_rate >= 0 ? 'growth-positive' : 'growth-negative'; ?>">
                        <?php echo number_format(abs($growth_rate), 1); ?>%
                        <?php echo $growth_rate >= 0 ? '↑' : '↓'; ?>
                    </span>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p><?php echo number_format($summary['total_orders']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Daily Average</h3>
                    <p>₱<?php echo number_format($summary['daily_average'], 2); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Highest Daily Sales</h3>
                    <p>₱<?php echo number_format($summary['highest_daily_sales'], 2); ?></p>
                </div>
            </div>

            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Orders</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('F d, Y', strtotime($row['sale_date'])); ?></td>
                            <td><?php echo $row['orders_count']; ?></td>
                            <td>₱<?php echo number_format($row['total_sales'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>