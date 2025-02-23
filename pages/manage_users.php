<?php
session_start();
require_once '../includes/db_connection.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch all users from database using mysqli
$sql = "SELECT id, username, email, user_type, created_at FROM users";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Sweet Sensations</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Header styles */
        header {
            background-color: #DC143C;
            padding: 20px 0;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            margin: 0 auto;
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

        /* Container styles */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        /* Table styles */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .user-table thead {
            background-color: #DC143C;
            color: white;
        }

        .user-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }

        .user-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .user-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* User type badge */
        .user-type {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .user-type.admin {
            background-color: #DC143C;
            color: white;
        }

        .user-type.user {
            background-color: #e9ecef;
            color: #495057;
        }

        /* Action buttons */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 500;
            text-decoration: none;
            margin-right: 0.5rem;
            display: inline-block;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background-color: #007bff;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Page title */
        h1 {
            color: #1a1a1a;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        /* Date formatting */
        .date-cell {
            color: #6c757d;
            font-size: 0.875rem;
        }

        /* ID column */
        .id-column {
            font-weight: 600;
            color: #495057;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 50%;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn-save {
            background-color: #28a745;
            color: white;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <div class="container">
        <h1>Manage Users</h1>

        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="id-column">#<?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="user-type <?php echo strtolower($user['user_type']); ?>">
                                <?php echo htmlspecialchars($user['user_type']); ?>
                            </span>
                        </td>
                        <td class="date-cell">
                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td>
                            <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                class="btn btn-edit">Edit</button>
                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                                    class="btn btn-delete"
                                    onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit User</h2>
            <form id="editUserForm" method="POST" action="../includes/update_user.php">
                <input type="hidden" id="userId" name="id">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="user_type">User Type</label>
                    <select id="user_type" name="user_type" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-save">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(user) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('userId').value = user.id;
            document.getElementById('username').value = user.username;
            document.getElementById('email').value = user.email;
            document.getElementById('user_type').value = user.user_type;
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeModal();
            }
        }
    </script>
</body>

</html>