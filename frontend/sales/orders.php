<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
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
    <link rel="stylesheet" href="../css/tailwind.css">
</head>
<body class="bg-gray-100 flex">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Orders</h1>

            <!-- Orders Table -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Order ID</th>
                            <th class="py-2 px-4 border-b">Customer</th>
                            <th class="py-2 px-4 border-b">Date</th>
                            <th class="py-2 px-4 border-b">Total</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample Row 1 -->
                        <tr>
                            <td class="py-2 px-4 border-b">1001</td>
                            <td class="py-2 px-4 border-b">John Doe</td>
                            <td class="py-2 px-4 border-b">2024-07-28</td>
                            <td class="py-2 px-4 border-b">₦15,000</td>
                            <td class="py-2 px-4 border-b"><span class="bg-yellow-500 text-white py-1 px-3 rounded-full text-xs">Pending</span></td>
                            <td class="py-2 px-4 border-b"><a href="#" class="text-blue-500 hover:underline">View</a></td>
                        </tr>
                        <!-- Sample Row 2 -->
                        <tr>
                            <td class="py-2 px-4 border-b">1002</td>
                            <td class="py-2 px-4 border-b">Jane Smith</td>
                            <td class="py-2 px-4 border-b">2024-07-27</td>
                            <td class="py-2 px-4 border-b">₦25,500</td>
                            <td class="py-2 px-4 border-b"><span class="bg-green-500 text-white py-1 px-3 rounded-full text-xs">Delivered</span></td>
                             <td class="py-2 px-4 border-b"><a href="#" class="text-blue-500 hover:underline">View</a></td>
                        </tr>
                        <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </div>

        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

</body>
</html>
