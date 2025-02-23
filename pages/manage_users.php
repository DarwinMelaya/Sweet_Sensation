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
            padding: 0 2rem;
        }

        /* Table styles - updated */
        .user-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .user-table thead {
            background-color: #DC143C;
        }

        .user-table th {
            padding: 1.25rem 1rem;
            text-align: left;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .user-table td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #eee;
            font-size: 0.95rem;
        }

        /* User type badge - updated */
        .user-type {
            padding: 0.4rem 1rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: capitalize;
            display: inline-block;
        }

        .user-type.admin {
            background-color: #DC143C;
            color: white;
        }

        .user-type.user {
            background-color: #f0f0f0;
            color: #666;
        }

        /* Action buttons - updated */
        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            width: 80px;
            text-align: center;
        }

        /* New styles for actions column */
        .actions-cell {
            display: flex;
            justify-content: flex-start;
            gap: 0.5rem;
        }

        .btn-edit {
            background-color: #0066ff;
            color: white;
        }

        .btn-delete {
            background-color: #ff3366;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Page title - updated */
        h1 {
            color: #333;
            font-size: 2.25rem;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
        }

        /* Date and ID formatting - updated */
        .date-cell {
            color: #666;
            font-size: 0.9rem;
        }

        .id-column {
            font-weight: 600;
            color: #333;
        }

        /* Modal styles - updated */
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
            margin: 8% auto;
            padding: 2rem;
            width: 40%;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 28px;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0066ff;
        }

        .btn-save {
            background-color: #00cc66;
            color: white;
            margin-top: 1.5rem;
            width: 100%;
            padding: 0.75rem;
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
                            <div class="actions-cell">
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)"
                                    class="btn btn-edit">Edit</button>
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                                        class="btn btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                <?php endif; ?>
                            </div>
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