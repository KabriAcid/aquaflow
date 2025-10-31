<?php
session_start();
// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login if not an admin
    header("Location: ../login.php");
    exit;
}

$page_title = "Manage Orders";

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
</head>

<body class="bg-gray-100 flex text-gray-800">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">Customer Orders</h3>
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
                    <tbody>
                        <!-- Order rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <!-- Order Details Modal -->
    <div id="order-details-modal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-white p-8 rounded-lg multi-shadow w-full max-w-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Order Details</h2>
                <button id="close-modal-btn" class="text-gray-500 hover:text-gray-800">&times;</button>
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

    <script src="../js/admin-orders.js"></script>

</body>

</html>