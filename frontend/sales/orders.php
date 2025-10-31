<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales_manager') {
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

<body class="bg-gray-100 flex">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Manage Orders</h1>
            </div>

            <!-- Search and Filter -->
            <div class="flex justify-between items-center mb-6">
                <input type="text" id="searchInput" class="w-1/2 p-3 border border-gray-300 rounded-lg" placeholder="Search by Customer Name or Order ID...">
                <select id="statusFilter" class="w-1/4 p-3 border border-gray-300 rounded-lg">
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

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
                    <tbody id="ordersTableBody">
                        <!-- Order rows will be inserted here -->
                    </tbody>
                </table>
            </div>

        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let allOrders = [];

            fetchOrders();

            document.getElementById('searchInput').addEventListener('input', applyFilters);
            document.getElementById('statusFilter').addEventListener('change', applyFilters);

            function applyFilters() {
                const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                const statusFilter = document.getElementById('statusFilter').value;

                const filteredOrders = allOrders.filter(order => {
                    const customerName = order.customer_name ? order.customer_name.toLowerCase() : '';
                    const orderId = order.id.toString();
                    const matchesSearch = customerName.includes(searchTerm) || orderId.includes(searchTerm);
                    const matchesStatus = statusFilter === 'all' || order.status === statusFilter;
                    return matchesSearch && matchesStatus;
                });

                renderOrders(filteredOrders);
            }

            function fetchOrders() {
                fetch('../../backend/api/orders/get_all.php')
                    .then(response => {
                        if (response.status === 401) {
                            window.location.href = '../login.php';
                            return Promise.reject('Unauthorized');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            allOrders = data.data;
                            renderOrders(allOrders);
                        } else {
                            alert('Failed to load orders: ' + data.message);
                        }
                    })
                    .catch(error => {
                        if (error !== 'Unauthorized') {
                            console.error('Error fetching orders:', error);
                            alert('An error occurred while fetching orders.');
                        }
                    });
            }

            function renderOrders(orders) {
                const tableBody = document.getElementById('ordersTableBody');
                tableBody.innerHTML = ''; // Clear existing rows
                orders.forEach(order => {
                    const row = `
                        <tr>
                            <td class="py-2 px-4 border-b">${order.id}</td>
                            <td class="py-2 px-4 border-b">${order.customer_name || 'N/A'}</td>
                            <td class="py-2 px-4 border-b">${new Date(order.created_at).toLocaleDateString()}</td>
                            <td class="py-2 px-4 border-b">₦${parseFloat(order.total_amount).toFixed(2)}</td>
                            <td class="py-2 px-4 border-b">
                                <span class="px-2 py-1 font-semibold leading-tight text-white bg-${getStatusColor(order.status)} rounded-full">
                                    ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <a href="order-details.php?id=${order.id}" class="text-blue-500 hover:underline">View Details</a>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }

            function getStatusColor(status) {
                switch (status) {
                    case 'pending':
                        return 'yellow-500';
                    case 'shipped':
                        return 'blue-500';
                    case 'delivered':
                        return 'green-500';
                    case 'cancelled':
                        return 'red-500';
                    default:
                        return 'gray-500';
                }
            }
        });
    </script>

</body>

</html>