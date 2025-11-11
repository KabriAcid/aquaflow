<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'production_manager') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Inventory";
include './partials/header.php';
?>

<?php include './partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="md:ml-64 bg-gray-100 min-h-screen p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Manage Inventory</h1>
            <p class="text-gray-600 mt-1">Monitor and update product stock levels</p>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Product Stock Levels</h2>
            </div>

            <div id="loading-indicator" class="text-center p-6 hidden">
                <p class="text-gray-500">Loading inventory...</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Product</th>
                            <th class="text-right py-3 px-6 font-semibold text-gray-700">Current Stock</th>
                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Last Updated</th>
                            <th class="text-center py-3 px-6 font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody id="inventory-table-body">
                        <!-- Rows will be injected by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Update Inventory Modal -->
<div id="update-inventory-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 p-4" style="display: none; align-items: center; justify-content: center;">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="border-b border-gray-200 p-6 sticky top-0 bg-white">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Update Stock</h2>
                <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-600 transition p-1 rounded hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="update-inventory-form" class="p-6">
            <input type="hidden" id="update-product-id">

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product</label>
                <p id="modal-product-name" class="px-4 py-2 bg-gray-100 rounded-lg text-gray-700 font-medium"></p>
            </div>

            <div class="mb-6">
                <label for="update-quantity" class="block text-sm font-semibold text-gray-700 mb-2">New Quantity *</label>
                <input type="number" id="update-quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter new quantity" required min="0">
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" id="cancel-modal-btn" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Update Stock</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/manage-inventory.js"></script>

<?php include './partials/footer.php'; ?>