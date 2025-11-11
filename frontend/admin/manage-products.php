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

<div class="md:ml-64">
    <main class="p-6 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Manage Products</h1>
                    <p class="text-gray-600 mt-1">Create, edit, and manage your product catalog.</p>
                </div>
                <button id="add-product-btn" class="btn-primary inline-flex items-center gap-2">
                    <i data-lucide="plus" class="w-5 h-5"></i> Add Product
                </button>
            </div>

            <!-- Filters & Search -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <div class="flex gap-4">
                    <input type="text" id="searchFilter" placeholder="Search by name..." class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <select id="categoryFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">All Categories</option>
                        <option value="bottled_water">Bottled Water</option>
                        <option value="beverage">Beverages</option>
                        <option value="package">Packages</option>
                    </select>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div id="loading-indicator" class="text-center p-8 hidden">
                    <p class="text-gray-500">Loading products...</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Image</th>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Product Name</th>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Category</th>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Size/Volume</th>
                                <th class="text-right py-3 px-6 font-semibold text-gray-700">Price</th>
                                <th class="text-center py-3 px-6 font-semibold text-gray-700">Status</th>
                                <th class="text-center py-3 px-6 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            <!-- Rows will be injected by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div id="no-products-message" class="text-center py-8 text-gray-500 hidden">
                    <i data-lucide="inbox" class="w-12 h-12 inline-block text-gray-300 mb-2"></i>
                    <p class="mt-2">No products found</p>
                </div>
            </div>
        </div>
    </main>

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
    </script>
    <?php include 'partials/footer.php'; ?>