<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include '../includes/db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $image = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($image);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $name, $description, $price, $image);

            if ($stmt->execute()) {
                $message = "Product added successfully!";
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    } elseif (isset($_POST['edit_product'])) {
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $price = isset($_POST['price']) ? $_POST['price'] : '';
        $image = isset($_FILES['image']['name']) ? $_FILES['image']['name'] : '';
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($image);

        if ($image) {
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
            $stmt->bind_param("ssdsi", $name, $description, $price, $image, $id);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=? WHERE id=?");
            $stmt->bind_param("ssdi", $name, $description, $price, $id);
        }

        if ($stmt->execute()) {
            $message = "Product updated successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['delete_product'])) {
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $message = "Product deleted successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all products
$sql = "SELECT * FROM products";
$products = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Sweet Sensations</title>
    <style>
        /* Internal CSS similar to product.css */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            padding: 0;
            margin: 0;
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

        h1 {
            text-align: center;
            font-weight: 600;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .form-main {
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-width: 500px;
            margin: 0 auto 40px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;
        }

        input,
        textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            padding: 10px;
            background-color: #DC143C;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background-color: #a10e2d;
        }

        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            color: green;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 40px;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
        }

        table,
        th,
        td {
            border: 1px solid #e0e0e0;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
        }

        th {
            background-color: #DC143C;
            color: white;
            font-weight: bold;
        }

        tbody tr:hover {
            background-color: #f5f5f5;
            transition: background 0.3s ease;
        }

        td img {
            border-radius: 6px;
            transition: transform 0.3s ease;
        }

        td img:hover {
            transform: scale(1.1);
        }

        .action-btns {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .action-btns button {
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 10px 0;
        }

        .edit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 20px;
        }

        .edit-btn:hover {
            background-color: #45a049;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
            padding: 8px 12px;

        }

        .delete-btn:hover {
            background-color: #e53935;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            th,
            td {
                padding: 10px;
                font-size: 14px;
            }

            .action-btns {
                flex-direction: column;
                gap: 4px;
            }
        }


        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            /* Slightly darker background */
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            /* For Safari support */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 0;
            padding: 30px;
            border: none;
            /* Remove border */
            width: 80%;
            max-width: 500px;
            border-radius: 12px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            /* Enhanced shadow */
        }

        /* Optional: Add animation for smooth appearance */
        .modal.show {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <div></div>
    <h1>Manage Products</h1>
    <h5 style="text-align: center; font-weight:500; margin:-10px 90px 10px 90px; border-bottom: 1px solid rgb(0, 0, 0, 0.2); padding-bottom:10px;"></h5>
    </div>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
            <h1 style="margin: 0;">Products</h1>
            <button onclick="openAddModal()" style="font-size: 16px; padding: 12px 24px;">Add New Product</button>
        </div>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>₱<?php echo number_format($row['price'], 2); ?></td>
                        <td>
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="50" height="50">
                        </td>
                        <td class="action-btns">
                            <button class="edit-btn" onclick="openModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>', '<?php echo htmlspecialchars($row['description']); ?>', <?php echo $row['price']; ?>)">Edit</button>

                            <form action="manage_products.php" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_product" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Add New Product Modal -->
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h2>Add New Product</h2>
                <form class="form-main" action="manage_products.php" method="POST" enctype="multipart/form-data">
                    <input type="text" name="name" placeholder="Product Name" required>
                    <textarea name="description" placeholder="Product Description" rows="4" required></textarea>
                    <input type="number" name="price" placeholder="Product Price" step="0.01" required>
                    <input type="file" name="image" accept="image/*" required>
                    <button type="submit" name="add_product">Add Product</button>
                </form>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Edit Product</h2>
                <form class="form-main" action="manage_products.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="text" name="name" id="edit-name" placeholder="Product Name" required>
                    <textarea name="description" id="edit-description" placeholder="Product Description" rows="4" required></textarea>
                    <input type="number" name="price" id="edit-price" placeholder="Product Price" step="0.01" required>
                    <input type="file" name="image" accept="image/*">
                    <button type="submit" name="edit_product">Update Product</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        function openAddModal() {
            const modal = document.getElementById('addModal');
            modal.style.display = "block";
            // Optional: Add class for animation
            modal.classList.add('show');
            // Add blur to background content
            document.querySelector('.container').style.filter = 'blur(5px)';
        }

        function closeAddModal() {
            const modal = document.getElementById('addModal');
            modal.style.display = "none";
            // Remove blur from background content
            document.querySelector('.container').style.filter = 'none';
        }

        function openModal(id, name, description, price) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-price').value = price;
            document.getElementById('editModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('editModal').style.display = "none";
        }

        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const addModal = document.getElementById('addModal');
            if (event.target == editModal) {
                closeModal();
            }
            if (event.target == addModal) {
                closeAddModal();
            }
        }
    </script>
    <?php $conn->close(); ?>
</body>

</html>