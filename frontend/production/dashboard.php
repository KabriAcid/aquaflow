<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has the correct role
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'production_manager') {
    // Redirect to the login page if not authenticated
    header('Location: ../login.php');
    exit;
}

$page_title = "Production Dashboard";

?>

<?php include './partials/header.php'; ?>

<div class="flex-1 flex">
    <!-- Sidebar -->
    <?php include './partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 p-6 md:p-10">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Welcome, Production Manager!</h1>
                <span class="text-lg text-gray-500">Today is <?php echo date('F j, Y'); ?></span>
            </div>

            <!-- Main dashboard content will be loaded here by JavaScript -->
            <div id="production-dashboard-content">
                <!-- Daily Output Metrics -->
                <div id="daily-output-metrics" class="mb-8"></div>

                <!-- Production Trends Chart -->
                <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
                    <h3 class="text-xl font-bold mb-4">Weekly Production Trends</h3>
                    <div class="h-64 md:h-96">
                        <canvas id="production-trends-chart"></canvas>
                    </div>
                </div>

                <!-- Stock Levels Summary -->
                <div id="stock-levels-summary"></div>
            </div>

        </div>
    </main>
</div>

<!-- Include the dashboard-specific JavaScript -->
<script src="../js/production-dashboard.js"></script>

<?php include './partials/footer.php'; ?>