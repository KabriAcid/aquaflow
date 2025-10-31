<?php
session_start();
// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login if not an admin
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 flex text-gray-800">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Products</h1>
                <button id="add-product-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 multi-shadow-lift">Add Product</button>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <table class="w-full" id="products-table">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold text-gray-600">Product</th>
                            <th class="text-left p-3 font-semibold text-gray-600">Price</th>
                            <th class="text-left p-3 font-semibold text-gray-600">Stock</th>
                            <th class="text-left p-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Product rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="bg-white rounded-lg p-8 w-full max-w-md mx-auto multi-shadow-lift">
            <h2 id="modal-title" class="text-2xl font-bold mb-6">Add Product</h2>
            <form id="product-form">
                <input type="hidden" id="product-id">
                <div class="mb-4">
                    <label for="product-name" class="block text-gray-700 font-semibold mb-2">Product Name</label>
                    <input type="text" id="product-name" class="form-input w-full" required>
                </div>
                <div class="mb-4">
                    <label for="product-price" class="block text-gray-700 font-semibold mb-2">Price</label>
                    <input type="number" id="product-price" class="form-input w-full" step="0.01" required>
                </div>
                <div class="mb-6">
                    <label for="product-stock" class="block text-gray-700 font-semibold mb-2">Stock</label>
                    <input type="number" id="product-stock" class="form-input w-full" required>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancel-btn" class="text-gray-600 hover:underline">Cancel</button>
                    <button type="submit" id="save-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/admin-products.js"></script>

</body>

</html>