<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$userName = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
$page_title = "Admin Dashboard";

include 'partials/header.php';
include 'partials/sidebar.php';
// Page layout wrapper (kept in page so partials only render their component)
?>
<div class="flex-1 flex flex-col md:ml-64">
    <main class="flex-1 p-6 bg-gray-100">
        <?php
        ?>

        <div class="container-fluid">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Admin Dashboard</h1>
                <p class="text-gray-500">Welcome, <?php echo htmlspecialchars($userName); ?>!</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <div class="flex items-center">
                        <div class="bg-blue-500 rounded-full p-3">
                            <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Sales</p>
                            <p id="total-sales" class="text-2xl font-bold text-gray-800">₦0.00</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <div class="flex items-center">
                        <div class="bg-green-500 rounded-full p-3">
                            <i data-lucide="users" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">New Customers</p>
                            <p id="total-customers" class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <div class="flex items-center">
                        <div class="bg-yellow-500 rounded-full p-3">
                            <i data-lucide="clipboard" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Pending Orders</p>
                            <p id="total-orders" class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <div class="flex items-center">
                        <div class="bg-red-500 rounded-full p-3">
                            <i data-lucide="user-check" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Sales Managers</p>
                            <p id="total-sales-managers" class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
                <div class="lg:col-span-3 bg-white p-6 rounded-lg multi-shadow">
                    <h3 class="text-lg font-medium text-gray-600 mb-4">Sales Overview</h3>
                    <div class="h-64">
                        <canvas id="salesOverviewChart"></canvas>
                    </div>
                </div>
                <div class="lg:col-span-2 bg-white p-6 rounded-lg multi-shadow">
                    <h3 class="text-lg font-medium text-gray-600 mb-4">Top Products</h3>
                    <div class="h-64">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/admin-dashboard.js"></script>

    <?php include 'partials/footer.php'; ?>