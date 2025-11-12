<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Sales Reports";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="flex-1 flex flex-col md:ml-64">

    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Sales Reports</h1>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-600">Generate Sales Report</h3>
                    <button id="generateReportBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                        <span id="reportSpinner" class="w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin hidden" aria-hidden="true"></span>
                        <span id="reportBtnText">Generate Report</span>
                    </button>
                </div>

                <!-- Loading Progress Bar -->
                <div id="loadingProgress" class="hidden mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Generating Report...</span>
                        <span id="progressPercentage" class="text-sm font-semibold text-blue-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div id="progressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p id="loadingStatus" class="text-xs text-gray-500 mt-2">Initializing...</p>
                </div>

                <div id="reportContent">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-semibold text-gray-600 mb-2">Top Selling Products</h4>
                            <div class="h-64">
                                <canvas id="topProductsChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-md font-semibold text-gray-600 mb-2">Sales Over Time</h4>
                            <div class="h-64">
                                <canvas id="salesOverTimeChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div id="reportTable" class="mt-6"></div>
                </div>
            </div>

            <script src="../js/admin-reports.js"></script>
            <script src="../js/utils.js"></script>
        </div>

        <?php include 'partials/footer.php'; ?>
</div>