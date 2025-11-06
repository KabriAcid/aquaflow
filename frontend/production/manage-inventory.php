<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'production_manager') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Inventory";
?>

<?php include './partials/header.php'; ?>

<div class="flex-1 flex">
    <!-- Sidebar -->
    <?php include './partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 p-6 md:p-10">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Manage Inventory</h1>

            <!-- Inventory Table -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Product Stock Levels</h2>
                <div id="loading-indicator" class="text-center hidden">
                    <p class="text-gray-500">Loading inventory...</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left py-3 px-4 border-b-2 font-bold">Product</th>
                                <th class="text-right py-3 px-4 border-b-2 font-bold">Quantity</th>
                                <th class="text-left py-3 px-4 border-b-2 font-bold">Last Updated</th>
                                <th class="text-center py-3 px-4 border-b-2 font-bold">Actions</th>
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
</div>

<!-- Update Inventory Modal -->
<div id="update-inventory-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md">
        <h2 class="text-2xl font-bold mb-4">Update Inventory</h2>
        <p class="mb-4">Updating stock for: <strong id="modal-product-name"></strong></p>

        <form id="update-inventory-form">
            <input type="hidden" id="update-product-id">
            <div class="mb-4">
                <label for="update-quantity" class="block text-sm font-medium text-gray-700">New Quantity</label>
                <input type="number" id="update-quantity" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeUpdateModal()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="../js/manage-inventory.js"></script>

<?php include './partials/footer.php'; ?>