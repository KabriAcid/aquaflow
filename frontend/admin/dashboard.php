<?php
session_start();
// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login if not an admin
    header("Location: ../login.php");
    exit;
}

$page_title = "Admin Dashboard";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Aquaflow</title>
    <link rel="shortcut icon" href="../../favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 flex text-gray-800">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-card p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-muted-foreground">Total Sales</h3>
                    <p id="total-sales" class="text-3xl font-bold">$0</p>
                </div>
                <div class="bg-card p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-muted-foreground">Total Customers</h3>
                    <p id="total-customers" class="text-3xl font-bold">0</p>
                </div>
                <div class="bg-card p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-muted-foreground">Total Orders</h3>
                    <p id="total-orders" class="text-3xl font-bold">0</p>
                </div>
                <div class="bg-card p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-muted-foreground">Sales Managers</h3>
                    <p id="total-sales-managers" class="text-3xl font-bold">0</p>
                </div>
            </div>

            <div class="bg-card p-6 rounded-lg shadow-md">
                <h3 class="text-xl font-bold mb-4">Sales Over Time</h3>
                <canvas id="sales-chart"></canvas>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script src="../js/admin-dashboard.js"></script>

</body>
</html>
