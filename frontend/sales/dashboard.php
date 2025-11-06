<?php
session_start();
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['sales', 'sales_manager'])) {
    header('Location: ../login.php');
    exit;
}
$page_title = "Sales Dashboard";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="container-fluid">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Sales Dashboard</h1>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex items-center">
                <div class="bg-blue-500 rounded-full p-3">
                    <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">My Pending Orders</p>
                    <p id="pendingCount" class="text-2xl font-bold text-gray-800">0</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex items-center">
                <div class="bg-green-500 rounded-full p-3">
                    <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">My Sales</p>
                    <p id="mySalesTotal" class="text-2xl font-bold text-gray-800">₦0.00</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex items-center">
                <div class="bg-yellow-500 rounded-full p-3">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">New Customers</p>
                    <p id="newCustomersCount" class="text-2xl font-bold text-gray-800">0</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg multi-shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-600">Recent Orders</h3>
            <a href="orders.php" class="text-sm text-blue-600">View all orders</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left table-auto" id="recentOrdersTable">
                <thead>
                    <tr class="text-sm text-gray-600 border-b">
                        <th class="py-2 px-3">Order #</th>
                        <th class="py-2 px-3">Customer</th>
                        <th class="py-2 px-3">Amount</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Payment</th>
                        <th class="py-2 px-3">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg multi-shadow mt-6">
        <h3 class="text-lg font-medium text-gray-600 mb-4">My Recent Activity</h3>
        <div class="h-64">
            <canvas id="salesActivityChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/sales-dashboard.js"></script>


<?php include 'partials/footer.php'; ?>

<!-- Order details modal -->
<div id="orderDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-11/12 md:w-2/3 lg:w-1/2 p-6 relative">
        <button id="orderModalClose" class="absolute top-3 right-3 text-gray-600">&times;</button>
        <h3 class="text-lg font-medium mb-4">Order Details</h3>
        <div id="orderModalContent" class="text-sm text-gray-700 max-h-96 overflow-auto">
            <!-- populated by JS -->
            <p class="text-gray-500">Loading...</p>
        </div>
    </div>
</div>