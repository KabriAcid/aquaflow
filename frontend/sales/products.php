<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Manage Products";

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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Manage Products</h1>
                <a href="#" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add New Product</a>
            </div>

            <!-- Products Table -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Product ID</th>
                            <th class="py-2 px-4 border-b">Name</th>
                            <th class="py-2 px-4 border-b">Price</th>
                            <th class="py-2 px-4 border-b">Stock</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sample Row 1 -->
                        <tr>
                            <td class="py-2 px-4 border-b">PROD-001</td>
                            <td class="py-2 px-4 border-b">25-Litre Bottle</td>
                            <td class="py-2 px-4 border-b">₦1,500</td>
                            <td class="py-2 px-4 border-b">150</td>
                            <td class="py-2 px-4 border-b">
                                <a href="#" class="text-blue-500 hover:underline mr-2">Edit</a>
                                <a href="#" class="text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                        <!-- Sample Row 2 -->
                        <tr>
                            <td class="py-2 px-4 border-b">PROD-002</td>
                            <td class="py-2 px-4 border-b">50-Litre Bottle</td>
                            <td class="py-2 px-4 border-b">₦2,800</td>
                            <td class="py-2 px-4 border-b">75</td>
                            <td class="py-2 px-4 border-b">
                                <a href="#" class="text-blue-500 hover:underline mr-2">Edit</a>
                                <a href="#" class="text-red-500 hover:underline">Delete</a>
                            </td>
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
