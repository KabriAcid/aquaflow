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
    <div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden p-4" style="display: none; align-items: center; justify-content: center;">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="border-b border-gray-200 p-6 sticky top-0 bg-white">
                <div class="flex items-center justify-between">
                    <h2 id="modal-title" class="text-2xl font-bold text-gray-800">Add Product</h2>
                    <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-600 transition p-1 rounded hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="product-form" class="p-6">
                <input type="hidden" id="product-id" name="product_id">

                <!-- Name -->
                <div class="mb-4">
                    <label for="product-name" class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                    <input type="text" id="product-name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="e.g., Pure Life Water" required>
                </div>

                <!-- Category & Product Type -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="product-category" class="block text-sm font-semibold text-gray-700 mb-2">Category *</label>
                        <select id="product-category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                            <option value="">Select category</option>
                            <option value="bottled_water">Bottled Water</option>
                            <option value="beverage">Beverages</option>
                            <option value="package">Packages</option>
                        </select>
                    </div>
                    <div>
                        <label for="product-type" class="block text-sm font-semibold text-gray-700 mb-2">Product Type *</label>
                        <select id="product-type" name="product_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                            <option value="">Select type</option>
                            <option value="bottled_water">Bottled Water</option>
                            <option value="sparkling_beverages">Sparkling Beverages</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <!-- Size & Volume -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="product-size" class="block text-sm font-semibold text-gray-700 mb-2">Size *</label>
                        <input type="text" id="product-size" name="size" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="e.g., Small, Large" required>
                    </div>
                    <div>
                        <label for="product-volume" class="block text-sm font-semibold text-gray-700 mb-2">Volume *</label>
                        <input type="text" id="product-volume" name="volume" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="e.g., 500ml, 1.5L" required>
                    </div>
                </div>

                <!-- Price & Min Order -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="product-price" class="block text-sm font-semibold text-gray-700 mb-2">Unit Price (₦) *</label>
                        <input type="number" id="product-price" name="unit_price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div>
                        <label for="product-min-order" class="block text-sm font-semibold text-gray-700 mb-2">Min. Order Qty *</label>
                        <input type="number" id="product-min-order" name="minimum_order_quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" min="1" placeholder="1" required>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="product-description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="product-description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter product description..."></textarea>
                </div>

                <!-- Image Upload -->
                <div class="mb-6">
                    <label for="product-image" class="block text-sm font-semibold text-gray-700 mb-2">Product Image</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" id="product-image" name="image_url" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="text-xs text-gray-500 mt-1">Recommended: 500x500px (JPG, PNG)</p>
                        </div>
                        <div id="image-preview" class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-300">
                            <img id="preview-img" src="" alt="Preview" class="w-full h-full object-cover rounded-lg hidden">
                            <i data-lucide="image" class="w-8 h-8 text-gray-400" id="preview-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" id="save-btn" class="btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden p-4" style="display: none; align-items: center; justify-content: center;">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-sm">
            <!-- Modal Header -->
            <div class="border-b border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800">Delete Product</h2>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-gray-600 mb-2">Are you sure you want to delete this product?</p>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>

            <!-- Modal Footer -->
            <form id="delete-product-form" class="border-t border-gray-200 p-6">
                <input type="hidden" id="delete-product-id" name="product_id">
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancel-delete-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-danger">Delete Product</button>
                </div>
            </form>
        </div>
    </div>