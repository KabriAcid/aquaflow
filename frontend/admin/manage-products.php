<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Manage Products';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="flex-1 flex flex-col">
    <main class="flex-1 p-6 bg-gray-100">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Products</h1>
            <p class="text-gray-500">Manage your product catalog — add, edit or remove products and update stock or pricing information.</p>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">All Products</h3>
            <button id="add-product-btn" class="btn-primary inline-flex items-center gap-2"><i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i> Add Product</button>
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

        <!-- Add/Edit Product Modal (placeholder) -->
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
                        <button type="submit" id="save-btn" class="btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="../js/admin-products.js"></script>

        <?php include 'partials/footer.php'; ?>
    </main>
</div>