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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .graph-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 30px 0;
            height: 400px;
        }

        .graph-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 8px 16px;
            border: none;
            background: #f4f4f4;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: #DC143C;
            color: white;
        }

        .tab-btn:hover {
            background: #a10e2d;
            color: white;
        }
    </style>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <?php
    // Add database connection
    require_once '../includes/db_connection.php';

    // Fetch quick statistics
    $totalSales = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status='completed'")->fetch_assoc()['total'];
    $totalOrders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
    $totalProducts = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
    $totalCustomers = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type='user'")->fetch_assoc()['count'];

    // Fetch daily sales data (last 7 days)
    $dailySales = $conn->query("
        WITH RECURSIVE dates AS (
            SELECT CURDATE() as date
            UNION ALL
            SELECT DATE_SUB(date, INTERVAL 1 DAY)
            FROM dates
            WHERE DATE_SUB(date, INTERVAL 1 DAY) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        )
        SELECT 
            d.date,
            COALESCE(SUM(o.total_amount), 0) as total
        FROM dates d
        LEFT JOIN orders o ON DATE(o.created_at) = d.date 
            AND o.status = 'completed'
        GROUP BY d.date
        ORDER BY d.date ASC
    ")->fetch_all(MYSQLI_ASSOC);

    // Fetch weekly sales data (last 8 weeks)
    $weeklySales = $conn->query("
        WITH RECURSIVE weeks AS (
            SELECT 
                DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) as week_start,
                DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) - 6) DAY) as week_end
            UNION ALL
            SELECT 
                DATE_SUB(week_start, INTERVAL 1 WEEK),
                DATE_SUB(week_end, INTERVAL 1 WEEK)
            FROM weeks
            WHERE DATE_SUB(week_start, INTERVAL 1 WEEK) >= DATE_SUB(CURDATE(), INTERVAL 7 WEEK)
        )
        SELECT 
            w.week_start,
            COALESCE(SUM(o.total_amount), 0) as total
        FROM weeks w
        LEFT JOIN orders o ON DATE(o.created_at) BETWEEN w.week_start AND w.week_end
            AND o.status = 'completed'
        GROUP BY w.week_start
        ORDER BY w.week_start ASC
    ")->fetch_all(MYSQLI_ASSOC);

    // Fetch monthly sales data for the graph
    $monthlySales = $conn->query("
        WITH RECURSIVE dates AS (
            SELECT DATE_FORMAT(NOW(), '%Y-%m') as month
            UNION ALL
            SELECT DATE_FORMAT(DATE_SUB(STR_TO_DATE(month, '%Y-%m'), INTERVAL 1 MONTH), '%Y-%m')
            FROM dates
            WHERE month > DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 11 MONTH), '%Y-%m')
        )
        SELECT 
            d.month,
            COALESCE(SUM(o.total_amount), 0) as total
        FROM dates d
        LEFT JOIN orders o ON DATE_FORMAT(o.created_at, '%Y-%m') = d.month 
            AND o.status = 'completed'
        GROUP BY d.month
        ORDER BY d.month ASC
    ")->fetch_all(MYSQLI_ASSOC);
    ?>

    <section class="dashboard">
        <div class="container">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Sales</h3>
                    <p>₱<?php echo number_format($totalSales, 2); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Products</h3>
                    <p><?php echo $totalProducts; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Customers</h3>
                    <p><?php echo $totalCustomers; ?></p>
                </div>
            </div>

            <!-- Add graph container with tabs -->
            <div class="graph-container">
                <div class="graph-tabs">
                    <button class="tab-btn active" data-period="daily">Daily</button>
                    <button class="tab-btn" data-period="weekly">Weekly</button>
                    <button class="tab-btn" data-period="monthly">Monthly</button>
                </div>
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p><small>All rights reserved by Sweet Sensations &copy; 2025</small></p>
        </div>
    </footer>

    <script>
        // Initialize chart with no data
        let salesChart = null;

        // Prepare the data
        const chartData = {
            daily: {
                labels: <?php echo json_encode(array_map(function ($row) {
                            return date('M d', strtotime($row['date']));
                        }, $dailySales)); ?>,
                data: <?php echo json_encode(array_map(function ($row) {
                            return $row['total'];
                        }, $dailySales)); ?>
            },
            weekly: {
                labels: <?php echo json_encode(array_map(function ($row) {
                            return date('M d', strtotime($row['week_start'])) . ' - ' . date('M d', strtotime($row['week_start'] . ' +6 days'));
                        }, $weeklySales)); ?>,
                data: <?php echo json_encode(array_map(function ($row) {
                            return $row['total'];
                        }, $weeklySales)); ?>
            },
            monthly: {
                labels: <?php echo json_encode(array_map(function ($row) {
                            return date('M Y', strtotime($row['month']));
                        }, $monthlySales)); ?>,
                data: <?php echo json_encode(array_map(function ($row) {
                            return $row['total'];
                        }, $monthlySales)); ?>
            }
        };

        // Function to update chart
        function updateChart(period) {
            if (salesChart) {
                salesChart.destroy();
            }

            const ctx = document.getElementById('salesChart').getContext('2d');
            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData[period].labels,
                    datasets: [{
                        label: period.charAt(0).toUpperCase() + period.slice(1) + ' Sales (₱)',
                        data: chartData[period].data,
                        borderColor: '#DC143C',
                        backgroundColor: 'rgba(220, 20, 60, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#DC143C',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointHoverBackgroundColor: '#DC143C',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: period.charAt(0).toUpperCase() + period.slice(1) + ' Sales Overview',
                            font: {
                                size: 16,
                                family: 'Poppins',
                                weight: '600'
                            },
                            padding: 20
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                font: {
                                    family: 'Poppins'
                                },
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.1)'
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins'
                                },
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        }
                    }
                }
            });
        }

        // Add click handlers for tabs
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Update active state
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                // Update chart
                updateChart(button.dataset.period);
            });
        });

        // Initialize with daily view
        updateChart('daily');
    </script>
</body>

</html>