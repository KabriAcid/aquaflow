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
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-700">Products</h1>
                <p class="text-gray-500">Manage your product catalog.</p>
            </div>
            <button id="add-product-btn" class="btn-primary inline-flex items-center gap-2"><i data-lucide="plus" class="w-4 h-4" aria-hidden="true"></i> Add Product</button>
        </div>

        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">All Products</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" id="products-table">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Product Name</th>
                            <th class="text-left p-3">Price</th>
                            <th class="text-left p-3">Stock</th>
                            <th class="text-left p-3">Date Added</th>
                            <th class="text-left p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products-tbody">
                        <!-- Product rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Add/Edit Product Modal -->
<div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 id="modal-title" class="text-2xl font-bold mb-6">Add Product</h2>
        <form id="product-form">
            <input type="hidden" id="product-id" name="product_id">
            <div class="mb-4">
                <label for="product-name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                <input type="text" id="product-name" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="product-description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="product-description" name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="product-price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <input type="number" id="product-price" name="price" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" step="0.01" required>
                </div>
                <div>
                    <label for="product-stock" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                    <input type="number" id="product-stock" name="stock_quantity" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                <button type="submit" id="save-btn" class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-4">Confirm Deletion</h2>
        <p class="text-gray-600 mb-6">Are you sure you want to delete this product? This action cannot be undone.</p>
        <form id="delete-product-form">
            <input type="hidden" id="delete-product-id" name="product_id">
            <div class="flex justify-end gap-4">
                <button type="button" id="cancel-delete-btn" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/admin-products.js" defer></script>
<?php include 'partials/footer.php'; ?>
