<?php
session_start();
// Only allow admin users to access this page
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Sales Reports";
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
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Sales Reports</h1>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-600">Generate Sales Report</h3>
                    <button id="generateReportBtn" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                        <span id="reportSpinner" class="w-4 h-4 mr-2 border-2 border-white border-t-transparent rounded-full animate-spin hidden" aria-hidden="true"></span>
                        <span>Generate Report</span>
                    </button>
                </div>

                <div id="reportContent"></div>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <!-- Page scripts -->
    <script src="../js/admin-reports.js"></script>

</body>

</html>