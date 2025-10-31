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
</head>

<body class="bg-gray-100 flex text-gray-800">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">Products</h3>
                <button id="add-product-btn" class="btn-primary">Add Product</button>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <table class="w-full" id="products-table">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Product Name</th>
                            <th class="text-left p-3">Price</th>
                            <th class="text-left p-3">Stock</th>
                            <th class="text-left p-3">Actions</th>
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

    <!-- Add/Edit Product Modal -->
    <div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg multi-shadow w-full max-w-md">
            <h2 id="modal-title" class="text-2xl font-bold mb-6">Add Product</h2>
            <form id="product-form">
                <input type="hidden" id="product-id" name="product-id">
                <div class="mb-4">
                    <label for="product-name" class="block text-sm font-medium text-muted-foreground">Product Name</label>
                    <input type="text" id="product-name" name="product-name" class="form-input mt-1 block w-full" required>
                </div>
                <div class="mb-4">
                    <label for="product-description" class="block text-sm font-medium text-muted-foreground">Description</label>
                    <textarea id="product-description" name="product-description" rows="3" class="form-input mt-1 block w-full" required></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="product-price" class="block text-sm font-medium text-muted-foreground">Price</label>
                        <input type="number" id="product-price" name="product-price" class="form-input mt-1 block w-full" step="0.01" required>
                    </div>
                    <div>
                        <label for="product-stock" class="block text-sm font-medium text-muted-foreground">Stock</label>
                        <input type="number" id="product-stock" name="product-stock" class="form-input mt-1 block w-full" required>
                    </div>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" id="save-btn" class="btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/admin-products.js"></script>

</body>

</html>