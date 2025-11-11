<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'production_manager' || !isset($_SESSION['production_manager_id'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Products";
include './partials/header.php';
?>

<?php include './partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="md:ml-64 bg-gray-100 min-h-screen p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Manage Production</h1>
                <p class="text-gray-600 mt-1">Manage production items and record daily output</p>
            </div>
            <button id="add-product-btn" class="btn-primary inline-flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="plus" class="inline w-5 h-5"></i>
                Add Product
            </button>
        </div>

        <!-- Products Grid -->
        <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Product cards will be dynamically inserted here -->
        </div>
    </div>
</main>

<!-- Add/Edit Product Modal -->
<div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md max-h-screen overflow-y-auto">
        <!-- Modal Header -->
        <div class="border-b border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800" id="product-modal-title">Add New Product</h2>
                <button type="button" id="close-product-modal" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="product-form" class="p-6">
            <input type="hidden" id="product_id" name="product_id">

            <!-- Form Fields Grid - 2 columns responsive -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div>
                    <label for="product_name" class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                    <input type="text" id="product_name" name="product_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter product name" required>
                </div>

                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Unit Price (₦) *</label>
                    <input type="number" step="0.01" id="price" name="price" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="0.00" required>
                </div>
            </div>

            <div class="mb-5">
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter product description"></textarea>
            </div>

            <!-- Image Upload Section -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 hover:border-blue-400 transition">
                <label for="image_upload" class="block text-sm font-semibold text-gray-700 mb-3">Product Image</label>
                <div class="flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2 cursor-pointer">
                        <i data-lucide="cloud-upload-2" class="w-8 h-8 text-gray-400"></i>
                        <p class="text-xs text-gray-500">Click to upload or drag and drop</p>
                        <input type="file" id="image_upload" name="image_upload" accept="image/*" class="hidden" />
                    </div>
                </div>
                <div id="image-preview" class="mt-3 hidden text-center">
                    <img id="preview-img" src="" alt="Preview" class="w-full h-24 object-cover rounded-lg">
                    <button type="button" id="clear-image" class="mt-2 text-xs text-red-500 hover:text-red-700">Remove Image</button>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" id="cancel-product-btn" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Record Production Modal -->
<div id="record-production-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md">
        <!-- Modal Header -->
        <div class="border-b border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Record Production</h2>
                <button type="button" id="close-record-modal" class="text-gray-400 hover:text-gray-600 transition">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <p class="text-gray-600 text-sm mt-2">Product: <span id="record-product-name" class="font-semibold text-gray-800"></span></p>
        </div>

        <!-- Modal Body -->
        <form id="record-production-form" class="p-6">
            <input type="hidden" id="record_product_id" name="product_id">

            <!-- Form Fields Grid - 2 columns responsive -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Product Name (Read-only) -->
                <div class="md:col-span-2">
                    <label for="record_product_display" class="block text-sm font-semibold text-gray-700 mb-2">Product *</label>
                    <input type="text" id="record_product_display" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600" placeholder="Product will appear here">
                </div>

                <!-- Production Date -->
                <div>
                    <label for="production_date" class="block text-sm font-semibold text-gray-700 mb-2">Production Date *</label>
                    <input type="date" id="production_date" name="production_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                </div>

                <!-- Shift -->
                <div>
                    <label for="shift" class="block text-sm font-semibold text-gray-700 mb-2">Shift *</label>
                    <select id="shift" name="shift" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        <option value="">Select a shift...</option>
                        <option value="morning">🌅 Morning (6 AM - 2 PM)</option>
                        <option value="afternoon">☀️ Afternoon (2 PM - 10 PM)</option>
                        <option value="night">🌙 Night (10 PM - 6 AM)</option>
                    </select>
                </div>

                <!-- Quantity Produced -->
                <div>
                    <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity Produced *</label>
                    <input type="number" id="quantity" name="quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter quantity" required min="1">
                </div>

                <!-- Equipment Used -->
                <div>
                    <label for="equipment_used" class="block text-sm font-semibold text-gray-700 mb-2">Equipment Used</label>
                    <input type="text" id="equipment_used" name="equipment_used" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="e.g., Machine A, Line 1">
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        <option value="">Select status...</option>
                        <option value="completed">✓ Completed</option>
                        <option value="in_progress">⟳ In Progress</option>
                        <option value="failed">✗ Failed</option>
                    </select>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Add any additional notes or observations"></textarea>
                </div>
            </div>

            <!-- Feedback Message -->
            <div id="record-feedback" class="mt-4 hidden p-4 rounded-lg text-sm font-medium"></div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 mt-6">
                <button type="button" id="cancel-record-btn" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Record Production</button>
            </div>
        </form>
    </div>
</div>


<script src="../js/manage-production.js"></script>

<?php include './partials/footer.php'; ?>