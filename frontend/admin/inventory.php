<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Inventory Management';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="flex-1 flex flex-col">
    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Inventory</h1>
                <p class="text-gray-500">Monitor and update product stock levels.</p>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Product Stock</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full" id="inventory-table">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3">Product Name</th>
                                <th class="text-left p-3">Current Stock</th>
                                <th class="text-left p-3">Update Stock</th>
                                <th class="text-left p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-tbody">
                            <!-- Inventory rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <script src="../js/admin-inventory.js" defer></script>
        </div>
    </main>
</div>
</script>
<?php include 'partials/footer.php'; ?>