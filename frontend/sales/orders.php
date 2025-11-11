<?php
session_start();
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['sales', 'sales_manager'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Orders";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="text-2xl mb-4 text-gray-800 font-semibold">Manage Orders</h1>

    <div class="bg-white p-6 rounded-lg multi-shadow">
        <div class="overflow-x-auto">
            <table id="ordersTable" class="w-full text-left table-auto">
                <thead>
                    <tr class="text-sm text-gray-600 border-b">
                        <th class="py-2 px-3">Order #</th>
                        <th class="py-2 px-3">Customer</th>
                        <th class="py-2 px-3">Amount</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Payment</th>
                        <th class="py-2 px-3">Date</th>
                        <th class="py-2 px-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../js/utils.js"></script>
<script src="../js/sales-orders.js"></script>

<?php include 'partials/footer.php'; ?>