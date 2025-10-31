<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales_manager') {
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
    <link rel="shortcut icon" href="../../favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-gray-100 flex">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Manage Products</h1>
                <a href="add-product.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add New Product</a>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <input type="text" id="searchInput" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Search by product name...">
            </div>

            <!-- Products Table -->
            <div class="bg-white p-4 md:p-8 rounded-lg multi-shadow overflow-auto">
                <table class="min-w-full w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="py-3 px-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Price</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody" class="bg-white divide-y divide-gray-100">
                        <!-- Product rows will be inserted here -->
                    </tbody>
                </table>
            </div>

        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let allProducts = [];

            fetchProducts();

            document.getElementById('searchInput').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const filteredProducts = allProducts.filter(product => product.name.toLowerCase().includes(searchTerm));
                renderProducts(filteredProducts);
            });

            function fetchProducts() {
                fetch('../../backend/api/products/get_all.php')
                    .then(response => {
                        if (response.status === 401) {
                            window.location.href = '../login.php';
                            return Promise.reject('Unauthorized');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            allProducts = data.data;
                            renderProducts(allProducts);
                        } else {
                            alert('Failed to load products: ' + data.message);
                        }
                    })
                    .catch(error => {
                        if (error !== 'Unauthorized') {
                            console.error('Error fetching products:', error);
                            alert('An error occurred while fetching products.');
                        }
                    });
            }

            function renderProducts(products) {
                const tableBody = document.getElementById('productsTableBody');
                tableBody.innerHTML = ''; // Clear existing rows
                products.forEach(product => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50';
                    tr.innerHTML = `
                        <td class="py-3 px-4 text-sm text-gray-700">${escapeHtml(String(product.id))}</td>
                        <td class="py-3 px-4 text-sm text-gray-800">${escapeHtml(product.name || '')}</td>
                        <td class="py-3 px-4 text-sm text-gray-700 text-right">₦${(parseFloat(product.unit_price) || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
                        <td class="py-3 px-4 text-sm text-gray-700 text-center">${escapeHtml(String(product.current_stock || 0))}</td>
                        <td class="py-3 px-4 text-sm text-center">
                            <a href="edit-product.php?id=${encodeURIComponent(product.id)}" class="text-blue-600 hover:underline mr-3">Edit</a>
                            <button data-id="${encodeURIComponent(product.id)}" class="text-red-600 hover:underline delete-btn">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });

                // Add event listeners to delete buttons
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.getAttribute('data-id');
                        if (confirm('Are you sure you want to delete this product?')) {
                            deleteProduct(productId);
                        }
                    });
                });
            }

            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text).replace(/[&<>"'`]/g, function(s) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": "&#39;",
                        '`': '&#96;'
                    })[s];
                });
            }

            function deleteProduct(productId) {
                fetch('../../backend/api/products/delete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: productId
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Product deleted successfully!');
                            fetchProducts(); // Refresh the product list
                        } else {
                            alert('Failed to delete product: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting product:', error);
                        alert('An error occurred while deleting the product.');
                    });
            }
        });
    </script>

</body>

</html>