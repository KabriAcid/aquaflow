<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Sales Dashboard";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Aquaflow</title>
    <link rel="stylesheet" href="../css/tailwind.css">
</head>
<body class="bg-gray-100 flex">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>
            
            <div id="dashboard-summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Sales -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-600">Total Sales</h2>
                    <p id="totalSales" class="text-3xl font-bold text-blue-600 mt-2">Loading...</p>
                </div>
                <!-- Total Orders -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-600">Total Orders</h2>
                    <p id="totalOrders" class="text-3xl font-bold text-green-600 mt-2">Loading...</p>
                </div>
                <!-- New Customers -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-600">New Customers</h2>
                    <p id="newCustomers" class="text-3xl font-bold text-yellow-600 mt-2">Loading...</p>
                </div>
                <!-- Pending Deliveries -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-600">Pending Deliveries</h2>
                    <p id="pendingDeliveries" class="text-3xl font-bold text-red-600 mt-2">Loading...</p>
                </div>
            </div>

        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('authToken');
            if (!token) {
                window.location.href = '../login.php';
                return;
            }

            // Fetch dashboard summary data
            fetch('../../backend/api/sales/summary.php', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('totalSales').textContent = `₦${parseFloat(data.data.total_sales).toLocaleString()}`;
                    document.getElementById('totalOrders').textContent = data.data.total_orders;
                    document.getElementById('newCustomers').textContent = data.data.new_customers;
                    document.getElementById('pendingDeliveries').textContent = data.data.pending_deliveries;
                } else {
                    alert('Failed to load dashboard data: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching dashboard data:', error);
                alert('An error occurred while fetching dashboard data.');
            });
        });
    </script>

</body>
</html>
