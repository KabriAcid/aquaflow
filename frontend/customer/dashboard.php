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
                        <a href="dashboard.php" class="py-4 px-2 text-blue-500 border-b-4 border-blue-500 font-semibold">Dashboard</a>
                        <a href="products.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Products</a>
                        <a href="orders.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">My Orders</a>
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

    <!-- Dashboard Content -->
    <main class="py-10">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Welcome back, <span id="userName"></span>!</h1>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Orders</h2>
                    <p id="totalOrders" class="text-3xl font-bold text-blue-500">0</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Pending Orders</h2>
                    <p id="pendingOrders" class="text-3xl font-bold text-yellow-500">0</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Total Spent</h2>
                    <p id="totalSpent" class="text-3xl font-bold text-green-500">₦0.00</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Recent Orders</h2>
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Order ID</th>
                            <th class="py-2 px-4 border-b">Date</th>
                            <th class="py-2 px-4 border-b">Total</th>
                            <th class="py-2 px-4 border-b">Status</th>
                            <th class="py-2 px-4 border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody id="recentOrdersTable">
                        <!-- Orders will be populated by JavaScript -->
                    </tbody>
                </table>
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

                    // now fetch orders (server endpoints should read session to determine user)
                    return fetch('../../backend/api/orders/get_all.php', {
                        credentials: 'same-origin'
                    });
                })
                .then(res => res.json())
                .then(data => {
                    if (!data) return;
                    if (data.success && data.data) {
                        populateDashboard(data.data);
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
                                <a href="order-details.php?id=${order.id}" class="text-blue-500 hover:underline">View</a>
                            </td>
                        </tr>
                    `;
                    recentOrdersTable.innerHTML += row;
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
        });
    </script>

</body>

</html>