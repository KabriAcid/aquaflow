<?php
session_start();
// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Redirect to login if not an admin
    header("Location: ../login.php");
    exit;
}

$page_title = "Manage Sales Managers";

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
                <h3 class="text-xl font-bold">Sales Managers</h3>
                <button id="add-manager-btn" class="btn-primary">Add Sales Manager</button>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <table class="w-full" id="sales-managers-table">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Name</th>
                            <th class="text-left p-3">Email</th>
                            <th class="text-left p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Sales manager rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <!-- Add Sales Manager Modal -->
    <div id="add-manager-modal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center hidden">
        <div class="bg-card p-8 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6">Add Sales Manager</h2>
            <form id="add-manager-form">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-muted-foreground">Name</label>
                    <input type="text" id="name" name="name" class="form-input mt-1 block w-full" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-muted-foreground">Email</label>
                    <input type="email" id="email" name="email" class="form-input mt-1 block w-full" required>
                </div>
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-muted-foreground">Password</label>
                    <input type="password" id="password" name="password" class="form-input mt-1 block w-full" required>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Add Manager</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/admin-sales-managers.js"></script>

</body>

</html>