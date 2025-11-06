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

<div class="flex-1 flex">
    <?php include './partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 p-6">
        <div class="container-fluid">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-700">Manage Products</h1>
                    <p class="text-gray-500">Manage production items and record output.</p>
                </div>
                <button id="add-product-btn" class="btn-primary inline-flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Product
                </button>
            </div>

            <!-- Products Grid -->
            <div id="products-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Product cards will be dynamically inserted here -->
            </div>
        </div>
    </main>
</div>

<!-- Add/Edit Product Modal -->
<div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6" id="product-modal-title">Add New Product</h2>
        <form id="product-form">
            <input type="hidden" id="product_id" name="product_id">
            <div class="mb-4">
                <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                <input type="text" id="product_name" name="product_name" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                <input type="number" step="0.01" id="price" name="price" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="image_url" class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
                <input type="url" id="image_url" name="image_url" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-4">
                <button type="button" id="cancel-product-btn" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Record Production Modal -->
<div id="record-production-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6">Record Production for <span id="record-product-name"></span></h2>
        <form id="record-production-form">
            <input type="hidden" id="record_product_id" name="product_id">
            <div class="mb-4">
                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity Produced</label>
                <input type="number" id="quantity" name="quantity" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required min="1">
            </div>
            <div class="mb-4">
                <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                <select id="shift" name="shift" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select a shift...</option>
                    <option value="morning">Morning</option>
                    <option value="afternoon">Afternoon</option>
                    <option value="night">Night</option>
                </select>
            </div>
            <div id="record-feedback" class="mt-4 hidden p-3 rounded-md"></div>
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" id="cancel-record-btn" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Record</button>
            </div>
        </form>
    </div>
</div>


<script src="../js/manage-production.js"></script>

<?php include './partials/footer.php'; ?>
