<?php
session_start();
// Check if the user is a sales manager
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales_manager') {
    // Redirect to login if not a sales manager
    header("Location: ../login.php");
    exit;
}

$page_title = "Sales Dashboard";

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
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Dashboard</h1>

            <div id="dashboard-summary" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Sales -->
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <h2 class="text-lg font-semibold text-gray-600">Total Sales</h2>
                    <p id="totalSales" class="text-3xl font-bold text-blue-600 mt-2">Loading...</p>
                </div>
                <!-- Total Orders -->
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <h2 class="text-lg font-semibold text-gray-600">Total Orders</h2>
                    <p id="totalOrders" class="text-3xl font-bold text-green-600 mt-2">Loading...</p>
                </div>
                <!-- New Customers -->
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <h2 class="text-lg font-semibold text-gray-600">New Customers</h2>
                    <p id="newCustomers" class="text-3xl font-bold text-yellow-600 mt-2">Loading...</p>
                </div>
                <!-- Pending Deliveries -->
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <h2 class="text-lg font-semibold text-gray-600">Pending Deliveries</h2>
                    <p id="pendingDeliveries" class="text-3xl font-bold text-red-600 mt-2">Loading...</p>
                </div>
            </div>
            <!-- Additional Highlights: Products & Customers -->
            <div id="dashboard-highlights" class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <h3 class="text-sm font-medium text-gray-600">Total Products</h3>
                    <p id="totalProducts" class="text-2xl font-bold text-gray-900 mt-2">Loading...</p>
                </div>

                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <h3 class="text-sm font-medium text-gray-600">Total Customers</h3>
                    <p id="totalCustomers" class="text-2xl font-bold text-gray-900 mt-2">Loading...</p>
                </div>

                <div class="bg-white p-6 rounded-lg multi-shadow">
                    <h3 class="text-sm font-medium text-gray-600">Inventory Alerts</h3>
                    <p id="lowStockCount" class="text-2xl font-bold text-red-600 mt-2">Loading...</p>
                </div>
            </div>

            <!-- Recent tables: Orders, Customers, Products -->
            <div id="recent-tables" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                <div class="bg-white p-4 rounded-lg multi-shadow overflow-auto">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Recent Orders</h3>
                    <table class="min-w-full text-sm" id="recentOrdersTable">
                        <thead>
                            <tr class="text-left text-xs text-gray-500">
                                <th class="px-2 py-1">#</th>
                                <th class="px-2 py-1">Order #</th>
                                <th class="px-2 py-1">Customer</th>
                                <th class="px-2 py-1">Total</th>
                                <th class="px-2 py-1">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800" id="recentOrdersBody">
                            <tr>
                                <td colspan="5" class="p-2">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white p-4 rounded-lg multi-shadow overflow-auto">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Recent Customers</h3>
                    <table class="min-w-full text-sm" id="recentCustomersTable">
                        <thead>
                            <tr class="text-left text-xs text-gray-500">
                                <th class="px-2 py-1">#</th>
                                <th class="px-2 py-1">Name</th>
                                <th class="px-2 py-1">Email</th>
                                <th class="px-2 py-1">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800" id="recentCustomersBody">
                            <tr>
                                <td colspan="4" class="p-2">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-white p-4 rounded-lg multi-shadow overflow-auto">
                    <h3 class="text-md font-semibold text-gray-700 mb-2">Recent Products</h3>
                    <table class="min-w-full text-sm" id="recentProductsTable">
                        <thead>
                            <tr class="text-left text-xs text-gray-500">
                                <th class="px-2 py-1">#</th>
                                <th class="px-2 py-1">Name</th>
                                <th class="px-2 py-1">SKU</th>
                                <th class="px-2 py-1">Stock</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800" id="recentProductsBody">
                            <tr>
                                <td colspan="4" class="p-2">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Summary fetch
            fetch('../../backend/api/sales/summary.php')
                .then(response => {
                    if (response.status === 401) {
                        window.location.href = '../login.php';
                        return Promise.reject('Unauthorized');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalSales').textContent = `₦${parseFloat(data.data.total_sales).toLocaleString()}`;
                        document.getElementById('totalOrders').textContent = data.data.total_orders;
                        document.getElementById('newCustomers').textContent = data.data.new_customers;
                        document.getElementById('pendingDeliveries').textContent = data.data.pending_deliveries;
                    } else {
                        console.warn('Failed to load sales summary:', data.message);
                    }
                })
                .catch(err => console.error('Error fetching sales summary:', err));

            // Fetch totals and inventory alerts for highlights
            Promise.all([
                fetch('../../backend/api/products/get_all.php', {
                    credentials: 'same-origin'
                }).then(r => r.json()),
                fetch('../../backend/api/customers/get_all.php', {
                    credentials: 'same-origin'
                }).then(r => r.json()),
                fetch('../../backend/api/inventory/get_alerts.php', {
                    credentials: 'same-origin'
                }).then(r => r.json())
            ]).then(([prodRes, custRes, alertsRes]) => {
                if (prodRes && prodRes.success && Array.isArray(prodRes.data)) {
                    document.getElementById('totalProducts').textContent = prodRes.data.length;
                } else {
                    document.getElementById('totalProducts').textContent = 'N/A';
                }

                if (custRes && custRes.success && Array.isArray(custRes.data)) {
                    document.getElementById('totalCustomers').textContent = custRes.data.length;
                } else {
                    document.getElementById('totalCustomers').textContent = 'N/A';
                }

                if (alertsRes && alertsRes.success && Array.isArray(alertsRes.data)) {
                    document.getElementById('lowStockCount').textContent = alertsRes.data.length;
                } else {
                    document.getElementById('lowStockCount').textContent = '0';
                }
            }).catch(err => {
                console.error('Error fetching highlights data:', err);
                document.getElementById('totalProducts').textContent = 'N/A';
                document.getElementById('totalCustomers').textContent = 'N/A';
                document.getElementById('lowStockCount').textContent = 'N/A';
            });

            // Fetch recent lists (limit=5) and render tables
            Promise.all([
                fetch('../../backend/api/orders/get_all.php?limit=5', {
                    credentials: 'same-origin'
                }).then(r => r.json()),
                fetch('../../backend/api/customers/get_all.php?limit=5', {
                    credentials: 'same-origin'
                }).then(r => r.json()),
                fetch('../../backend/api/products/get_all.php?limit=5', {
                    credentials: 'same-origin'
                }).then(r => r.json())
            ]).then(([ordersRes, recentCustRes, recentProdRes]) => {
                if (ordersRes && ordersRes.success && Array.isArray(ordersRes.data)) {
                    renderOrdersTable(ordersRes.data);
                } else {
                    document.getElementById('recentOrdersBody').innerHTML = '<tr><td colspan="5" class="p-2">No recent orders.</td></tr>';
                }

                if (recentCustRes && recentCustRes.success && Array.isArray(recentCustRes.data)) {
                    renderCustomersTable(recentCustRes.data);
                } else {
                    document.getElementById('recentCustomersBody').innerHTML = '<tr><td colspan="4" class="p-2">No recent customers.</td></tr>';
                }

                if (recentProdRes && recentProdRes.success && Array.isArray(recentProdRes.data)) {
                    renderProductsTable(recentProdRes.data);
                } else {
                    document.getElementById('recentProductsBody').innerHTML = '<tr><td colspan="4" class="p-2">No recent products.</td></tr>';
                }
            }).catch(err => {
                console.error('Error fetching recent lists:', err);
                document.getElementById('recentOrdersBody').innerHTML = '<tr><td colspan="5" class="p-2">Error loading orders.</td></tr>';
                document.getElementById('recentCustomersBody').innerHTML = '<tr><td colspan="4" class="p-2">Error loading customers.</td></tr>';
                document.getElementById('recentProductsBody').innerHTML = '<tr><td colspan="4" class="p-2">Error loading products.</td></tr>';
            });

            function renderOrdersTable(orders) {
                const tbody = document.getElementById('recentOrdersBody');
                tbody.innerHTML = '';
                orders.forEach((o, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-2 py-1">${idx + 1}</td>
                        <td class="px-2 py-1">${escapeHtml(o.order_number || o.id)}</td>
                        <td class="px-2 py-1">${escapeHtml(o.customer_id || '')}</td>
                        <td class="px-2 py-1">₦${(parseFloat(o.total_amount) || 0).toLocaleString()}</td>
                        <td class="px-2 py-1">${escapeHtml(o.status || o.payment_status || '')}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            function renderCustomersTable(customers) {
                const tbody = document.getElementById('recentCustomersBody');
                tbody.innerHTML = '';
                customers.forEach((c, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-2 py-1">${idx + 1}</td>
                        <td class="px-2 py-1">${escapeHtml(c.full_name || '')}</td>
                        <td class="px-2 py-1">${escapeHtml(c.email || '')}</td>
                        <td class="px-2 py-1">${escapeHtml(c.created_at ? new Date(c.created_at).toLocaleDateString() : '')}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            function renderProductsTable(products) {
                const tbody = document.getElementById('recentProductsBody');
                tbody.innerHTML = '';
                products.forEach((p, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="px-2 py-1">${idx + 1}</td>
                        <td class="px-2 py-1">${escapeHtml(p.name || '')}</td>
                        <td class="px-2 py-1">${escapeHtml(p.sku || '')}</td>
                        <td class="px-2 py-1">${escapeHtml(String(p.current_stock || 0))}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            // small helper to avoid inserting raw HTML
            function escapeHtml(text) {
                if (!text) return '';
                return String(text).replace(/[&<>"'`]/g, function(s) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;',
                        '`': '&#96;'
                    })[s];
                });
            }

        });
    </script>

</body>

</html>