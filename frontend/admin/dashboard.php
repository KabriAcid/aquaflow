<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Admin Dashboard";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="container-fluid">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-700">Admin Dashboard</h1>
        <p class="text-gray-500">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex items-center">
                <div class="bg-blue-500 rounded-full p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-800">$12,345</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex items-center">
                <div class="bg-green-500 rounded-full p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A5.965 5.965 0 0112 13a5.965 5.965 0 013 5.197"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">New Customers</p>
                    <p class="text-2xl font-bold text-gray-800">123</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex items-center">
                <div class="bg-yellow-500 rounded-full p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pending Orders</p>
                    <p class="text-2xl font-bold text-gray-800">45</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex items-center">
                <div class="bg-red-500 rounded-full p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Revenue</p>
                    <p class="text-2xl font-bold text-gray-800">$9,876</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
        <div class="lg:col-span-3 bg-white p-6 rounded-lg multi-shadow">
            <h3 class="text-lg font-medium text-gray-600 mb-4">Sales Overview</h3>
            <canvas id="salesOverviewChart"></canvas>
        </div>
        <div class="lg:col-span-2 bg-white p-6 rounded-lg multi-shadow">
            <h3 class="text-lg font-medium text-gray-600 mb-4">Top Products</h3>
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../js/admin-dashboard.js"></script>

<?php include 'partials/footer.php'; ?>
