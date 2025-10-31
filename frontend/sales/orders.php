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
            <div class="bg-white p-4 md:p-8 rounded-lg multi-shadow overflow-auto">
                <table class="min-w-full w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order ID</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="py-3 px-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody" class="bg-white divide-y divide-gray-100">
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
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50';
                    const dateVal = order.order_date || order.created_at || '';
                    const dateStr = dateVal ? new Date(dateVal).toLocaleDateString() : '';
                    const total = (parseFloat(order.total_amount) || 0).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    const statusLabel = (order.status || '').charAt(0).toUpperCase() + (order.status || '').slice(1);
                    tr.innerHTML = `
                        <td class="py-3 px-4 text-sm text-gray-700">${escapeHtml(String(order.id))}</td>
                        <td class="py-3 px-4 text-sm text-gray-800">${escapeHtml(order.customer_name || 'N/A')}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">${escapeHtml(dateStr)}</td>
                        <td class="py-3 px-4 text-sm text-gray-700 text-right">₦${total}</td>
                        <td class="py-3 px-4 text-sm text-center">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStatusClasses(order.status)}">${escapeHtml(statusLabel)}</span>
                        </td>
                        <td class="py-3 px-4 text-sm text-center">
                            <a href="order-details.php?id=${encodeURIComponent(order.id)}" class="text-blue-600 hover:underline">View Details</a>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
            }

            function getStatusClasses(status) {
                switch (status) {
                    case 'pending':
                        return 'bg-yellow-100 text-yellow-800';
                    case 'shipped':
                        return 'bg-blue-100 text-blue-800';
                    case 'delivered':
                        return 'bg-green-100 text-green-800';
                    case 'cancelled':
                        return 'bg-red-100 text-red-800';
                    default:
                        return 'bg-gray-100 text-gray-800';
                }
            }

            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text).replace(/[&<>"'`]/g, function(s) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": "&#39;",
                        '`': '&#96;'
                    })[s];
                });
            }
        });
    </script>

</body>

</html>