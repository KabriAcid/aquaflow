<?php
// customer/dashboard.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Aquaflow</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../favicon.png">

    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-gray-100">

    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <!-- Dashboard Content -->
    <main class="py-10">
        <div class="container-fluid">
            <div class="max-w-6xl mx-auto px-4">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-700">Customer Dashboard</h1>
                    <p class="text-gray-500">Welcome back, <span id="userName"></span>!</p>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg multi-shadow">
                        <div class="flex items-center">
                            <div class="bg-blue-500 rounded-full p-3">
                                <i data-lucide="shopping-bag" class="w-6 h-6 text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Total Orders</p>
                                <p id="totalOrders" class="text-2xl font-bold text-gray-800">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg multi-shadow">
                        <div class="flex items-center">
                            <div class="bg-yellow-500 rounded-full p-3">
                                <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Pending Orders</p>
                                <p id="pendingOrders" class="text-2xl font-bold text-gray-800">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg multi-shadow">
                        <div class="flex items-center">
                            <div class="bg-green-500 rounded-full p-3">
                                <i data-lucide="naira-sign" class="w-6 h-6 text-white"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Total Spent</p>
                                <p id="totalSpent" class="text-2xl font-bold text-gray-800">₦0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white p-6 rounded-lg multi-shadow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-600">Recent Orders</h3>
                    <a href="orders.php" class="text-blue-500 hover:text-blue-700 flex items-center gap-1">
                        <span>View All</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3">Order Number</th>
                                <th class="text-left p-3">Date</th>
                                <th class="text-left p-3">Total</th>
                                <th class="text-left p-3">Status</th>
                                <th class="text-left p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recentOrdersTable">
                            <!-- Orders will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Detect session by calling the server-side me endpoint. This uses the PHP session cookie.
            fetch('../../backend/api/auth/me.php', {
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(userData => {
                    if (!userData.success) {
                        // not authenticated -> redirect to login
                        window.location.href = '../login.php';
                        return Promise.reject('Not authenticated');
                    }

                    // show user's name
                    const profile = userData.data || {};
                    if (profile.full_name) document.getElementById('userName').textContent = profile.full_name;

                    // now fetch dashboard data (server reads session to determine user)
                    return fetch('../../backend/api/dashboard/customer.php', {
                        credentials: 'same-origin'
                    });
                })
                .then(res => res.json())
                .then(data => {
                    if (!data) return;
                    if (data.success && data.data) {
                        // data.data contains { orders: [...], stats: { total_orders, pending_orders, total_spent } }
                        const orders = data.data.orders || [];
                        const stats = data.data.stats || {};
                        // populate numeric cards from stats
                        document.getElementById('totalOrders').textContent = stats.total_orders ?? orders.length;
                        document.getElementById('pendingOrders').textContent = stats.pending_orders ?? orders.filter(o => o.status === 'pending').length;
                        document.getElementById('totalSpent').textContent = `₦${(stats.total_spent ?? orders.reduce((a,o)=>a+parseFloat(o.total_amount||0),0)).toFixed(2)}`;
                        populateDashboard(orders);
                    } else {
                        console.error('Failed to fetch dashboard data:', data.message);
                    }
                })
                .catch(err => {
                    if (typeof err === 'string') return; // handled earlier
                    console.error('Error fetching dashboard data:', err);
                });

            function populateDashboard(orders) {
                const totalOrders = orders.length;
                const pendingOrders = orders.filter(o => o.status === 'pending').length;
                const totalSpent = orders.reduce((acc, o) => acc + parseFloat(o.total_amount), 0);

                document.getElementById('totalOrders').textContent = totalOrders;
                document.getElementById('pendingOrders').textContent = pendingOrders;
                document.getElementById('totalSpent').textContent = `₦${totalSpent.toFixed(2)}`;

                const recentOrdersTable = document.getElementById('recentOrdersTable');
                recentOrdersTable.innerHTML = '';
                const recentOrders = orders.slice(0, 5);

                if (recentOrders.length === 0) {
                    recentOrdersTable.innerHTML = '<tr><td colspan="5" class="text-center py-4">No recent orders found.</td></tr>';
                    return;
                }

                recentOrders.forEach(order => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="p-3 border-b">${order.order_number}</td>
                        <td class="p-3 border-b">${new Date(order.order_date).toLocaleDateString()}</td>
                        <td class="p-3 border-b">₦${parseFloat(order.total_amount).toFixed(2)}</td>
                        <td class="p-3 border-b">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(order.status)}">
                                ${order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                            </span>
                        </td>
                        <td class="p-3 border-b">
                            <a href="order-details.php?id=${order.id}" class="text-blue-500 hover:text-blue-700 p-1 rounded inline-flex items-center" title="View Order Details">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span class="ml-1">View</span>
                            </a>
                        </td>
                    `;
                    recentOrdersTable.appendChild(tr);
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
                localStorage.removeItem('authToken');
                localStorage.removeItem('userName');
                window.location.href = '../login.php';
            });

            // Initialize Lucide icons
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>

</body>

</html>