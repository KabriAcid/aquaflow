<?php
// customer/orders.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Aquaflow</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../favicon.png">

    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-gray-100">

    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="../index.php" class="flex items-center py-4 px-2">
                            <span class="font-bold text-gray-700 text-lg">Aquaflow</span>
                        </a>
                    </div>
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="dashboard.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Dashboard</a>
                        <a href="products.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Products</a>
                        <a href="orders.php" class="py-4 px-2 text-blue-500 border-b-4 border-blue-500 font-semibold">My Orders</a>
                        <a href="cart.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Cart</a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3 ">
                    <a href="profile.php" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">Profile</a>
                    <a href="#" id="logoutBtn" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Log Out</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Orders Content -->
    <main class="py-10">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">My Orders</h1>

            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" data-tab="all">All</a>
                    </li>
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active" data-tab="pending">Pending</a>
                    </li>
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" data-tab="processing">Processing</a>
                    </li>
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" data-tab="delivered">Delivered</a>
                    </li>
                    <li class="mr-2">
                        <a href="#" class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" data-tab="cancelled">Cancelled</a>
                    </li>
                </ul>
            </div>

            <div class="bg-white p-8 rounded-lg shadow-md">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Order ID</th>
                            <th class="py-2 px-4 border-b">Date</th>
                            <th class="py-2 px-4 border-b">Total</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        <!-- Orders will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // detect session via server-side endpoint
            fetch('../../backend/api/auth/me.php', {
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(userData => {
                    if (!userData.success) {
                        window.location.href = '../login.php';
                        return Promise.reject('Not authenticated');
                    }
                    // fetch orders using session
                    return fetch('../../backend/api/orders/get_all.php', {
                        credentials: 'same-origin'
                    });
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        allOrders = data.data;
                        renderOrders('all'); // Initially show all orders
                    } else {
                        document.getElementById('ordersTable').innerHTML = '<tr><td colspan="5" class="text-center py-4">Could not load orders.</td></tr>';
                    }
                })
                .catch(err => {
                    if (typeof err === 'string') return; // handled redirect
                    console.error('Failed to load orders:', err);
                });
            let allOrders = [];

            const tabs = document.querySelectorAll('[data-tab]');
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    tabs.forEach(t => t.classList.remove('text-blue-600', 'border-blue-600', 'active'));
                    this.classList.add('text-blue-600', 'border-blue-600', 'active');
                    renderOrders(this.dataset.tab);
                });
            });

            function renderOrders(statusFilter) {
                const ordersTable = document.getElementById('ordersTable');
                ordersTable.innerHTML = '';
                const filteredOrders = statusFilter === 'all' ? allOrders : allOrders.filter(o => o.status === statusFilter);

                if (filteredOrders.length === 0) {
                    ordersTable.innerHTML = `<tr><td colspan="5" class="text-center py-4">No orders found for this status.</td></tr>`;
                    return;
                }

                filteredOrders.forEach(order => {
                    const row = `
                        <tr>
                            <td class="py-2 px-4 border-b text-center">${order.order_number}</td>
                            <td class="py-2 px-4 border-b text-center">${new Date(order.order_date).toLocaleDateString()}</td>
                            <td class="py-2 px-4 border-b text-center">₦${parseFloat(order.total_amount).toFixed(2)}</td>
                            <td class="py-2 px-4 border-b text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(order.status)}">
                                    ${order.status}
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b text-center">
                                <a href="order-details.php?id=${order.id}" class="text-blue-500 hover:underline">View Details</a>
                                ${order.status === 'pending' ? `<button onclick="cancelOrder(${order.id})" class="ml-2 text-red-500 hover:underline">Cancel</button>` : ''}
                            </td>
                        </tr>
                    `;
                    ordersTable.innerHTML += row;
                });
            }

            function getStatusColor(status) {
                switch (status) {
                    case 'pending':
                        return 'bg-yellow-200 text-yellow-800';
                    case 'processing':
                        return 'bg-blue-200 text-blue-800';
                    case 'delivered':
                        return 'bg-green-200 text-green-800';
                    case 'cancelled':
                        return 'bg-red-200 text-red-800';
                    default:
                        return 'bg-gray-200 text-gray-800';
                }
            }

            document.getElementById('logoutBtn').addEventListener('click', function() {
                fetch('../../backend/api/auth/logout.php', {
                        method: 'POST',
                        credentials: 'same-origin'
                    })
                    .then(res => res.json())
                    .then(() => {
                        window.location.href = '../login.php';
                    })
                    .catch(() => {
                        window.location.href = '../login.php';
                    });
            });
        });

        function cancelOrder(orderId) {
            if (!confirm('Are you sure you want to cancel this order?')) return;

            const token = localStorage.getItem('authToken');
            fetch(`../../backend/api/orders/delete.php?id=${orderId}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Order cancelled successfully.');
                        window.location.reload();
                    } else {
                        alert('Failed to cancel order: ' + data.message);
                    }
                })
                .catch(err => {
                    alert('An error occurred. Please try again.');
                    console.error(err);
                });
        }
    </script>

</body>

</html>