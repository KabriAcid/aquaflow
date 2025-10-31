<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Manage Products";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Aquaflow</title>
    <link rel="stylesheet" href="../css/tailwind.css">
</head>
<body class="bg-gray-100 flex">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Manage Products</h1>
                <a href="#" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add New Product</a>
            </div>

            <!-- Products Table -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Product ID</th>
                            <th class="py-2 px-4 border-b">Name</th>
                            <th class="py-2 px-4 border-b">Price</th>
                            <th class="py-2 px-4 border-b">Stock</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        <!-- Product rows will be inserted here -->
                    </tbody>
                </table>
            </div>

        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('authToken');
            if (!token) {
                window.location.href = '../login.php';
                return;
            }

            fetch('../../backend/api/products/get_all.php', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tableBody = document.getElementById('productsTableBody');
                    tableBody.innerHTML = ''; // Clear existing rows
                    data.data.forEach(product => {
                        const row = `
                            <tr>
                                <td class="py-2 px-4 border-b">${product.id}</td>
                                <td class="py-2 px-4 border-b">${product.name}</td>
                                <td class="py-2 px-4 border-b">₦${parseFloat(product.price).toFixed(2)}</td>
                                <td class="py-2 px-4 border-b">${product.stock_quantity}</td>
                                <td class="py-2 px-4 border-b">
                                    <a href="#" class="text-blue-500 hover:underline mr-2">Edit</a>
                                    <a href="#" class="text-red-500 hover:underline">Delete</a>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    alert('Failed to load products: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                alert('An error occurred while fetching products.');
            });
        });
    </script>

</body>
</html>
