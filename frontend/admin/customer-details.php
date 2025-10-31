<?php
session_start();
// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login if not an admin
    header("Location: ../login.php");
    exit;
}

$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($customer_id === 0) {
    header("Location: manage-customers.php");
    exit;
}

$page_title = "Customer Details";

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 flex text-gray-800">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <a href="manage-customers.php" class="text-blue-600 hover:underline mb-6 inline-block">&larr; Back to Customers</a>
            <div id="customer-details-container" class="bg-white p-8 rounded-lg multi-shadow">
                <!-- Customer details will be loaded here -->
            </div>

            <div class="mt-8">
                <h2 class="text-2xl font-bold mb-4">Order History</h2>
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <table class="w-full" id="orders-table">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3 font-semibold text-gray-600">Order ID</th>
                                <th class="text-left p-3 font-semibold text-gray-600">Date</th>
                                <th class="text-left p-3 font-semibold text-gray-600">Total</th>
                                <th class="text-left p-3 font-semibold text-gray-600">Status</th>
                                <th class="text-left p-3 font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Order rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script src="../js/admin-customer-details.js"></script>

</body>

</html>