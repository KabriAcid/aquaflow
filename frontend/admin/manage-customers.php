<?php
session_start();
// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login if not an admin
    header("Location: ../login.php");
    exit;
}

$page_title = "Manage Customers";

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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Customers</h1>
                <input type="text" id="search-input" placeholder="Search by name or email" class="form-input w-64">
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <table class="w-full" id="customers-table">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold text-gray-600">Name</th>
                            <th class="text-left p-3 font-semibold text-gray-600">Email</th>
                            <th class="text-left p-3 font-semibold text-gray-600">Registered On</th>
                            <th class="text-left p-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Customer rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script src="../js/admin-customers.js"></script>

</body>

</html>