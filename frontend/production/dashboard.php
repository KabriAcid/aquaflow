<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is a production manager
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'production_manager') {
    header('Location: ../login.php');
    exit;
}

$userName = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Production Manager';
$page_title = "Production Dashboard";

include 'partials/header.php';
?>

<div class="flex-1 flex">
    <?php include './partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Production Dashboard</h1>
                <p class="text-gray-500">Welcome, <?php echo htmlspecialchars($userName); ?>!</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Production Today -->
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <div class="flex items-center">
                        <div class="bg-blue-500 rounded-full p-3">
                            <i data-lucide="package-check" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Production Today</p>
                            <p id="total-production" class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
                <!-- Bottled Water Production -->
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <div class="flex items-center">
                        <div class="bg-green-500 rounded-full p-3">
                            <i data-lucide="glass-water" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Bottled Water Output</p>
                            <p id="bottled-water-output" class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
                <!-- Sparkling Beverages Production -->
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <div class="flex items-center">
                        <div class="bg-yellow-500 rounded-full p-3">
                            <i data-lucide="sparkles" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Sparkling Beverages</p>
                            <p id="sparkling-beverages-output" class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
                <!-- Low Stock Items -->
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <div class="flex items-center">
                        <div class="bg-red-500 rounded-full p-3">
                            <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Low Stock Items</p>
                            <p id="low-stock-items" class="text-2xl font-bold text-gray-800">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts & Summaries -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
                <!-- Production Trends Chart -->
                <div class="lg:col-span-3 bg-white p-6 rounded-lg multi-shadow">
                    <h3 class="text-lg font-medium text-gray-600 mb-4">Daily Production Trends</h3>
                    <div class="h-64">
                        <canvas id="production-trends-chart"></canvas>
                    </div>
                </div>
                <!-- Inventory Summary -->
                <div class="lg:col-span-2 bg-white p-6 rounded-lg multi-shadow">
                    <h3 class="text-lg font-medium text-gray-600 mb-4">Inventory Summary</h3>
                    <div class="h-64 overflow-y-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-2">Product</th>
                                    <th class="text-right p-2">Quantity</th>
                                    <th class="text-center p-2">Status</th>
                                </tr>
                            </thead>
                            <tbody id="inventory-summary-tbody">
                                <!-- JS will populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/production-dashboard.js"></script>

    <?php include 'partials/footer.php'; ?>