<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Manage Orders';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="flex-1 flex flex-col md:ml-64">
    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Customer Orders</h1>
                <p class="text-gray-500">View, search and manage customer orders.</p>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-4">
                    <input type="text" id="search-input" placeholder="Search by Order ID or Customer" class="form-input w-64">
                    <select id="status-filter" class="form-input">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <div class="overflow-x-auto">
                    <table class="w-full" id="orders-table">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3">Order ID</th>
                                <th class="text-left p-3">Customer</th>
                                <th class="text-left p-3">Date</th>
                                <th class="text-left p-3">Total</th>
                                <th class="text-left p-3">Status</th>
                                <th class="text-left p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orders-tbody">
                            <!-- Order rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <script src="../js/util.js"></script>
            <script src="../js/admin-orders.js" defer></script>
        </div>
    </main>

    <!-- Order Details Modal -->
    <div id="order-details-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Order Details</h2>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <div id="modal-content">
                <!-- Order details will be loaded here -->
            </div>
            <div class="mt-6 flex justify-end gap-4">
                <select id="update-status-select" class="form-input">
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <button id="update-status-btn" class="btn-primary">Update Status</button>
            </div>
        </div>
    </div>
    </script>
    <?php include 'partials/footer.php'; ?>