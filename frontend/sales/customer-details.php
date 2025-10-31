<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales_manager') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Customer Details";
$customer_id = $_GET['id'] ?? null;

if (!$customer_id) {
    header("Location: customers.php");
    exit;
}

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
                <h1 class="text-3xl font-bold text-gray-800">Customer Details</h1>
                <a href="customers.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Customers</a>
            </div>

            <div id="customerDetails" class="bg-white p-4 md:p-8 rounded-lg multi-shadow">
                <!-- Customer details will be loaded here -->
                <p>Loading customer details...</p>
            </div>

            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Order History</h2>
                <div class="bg-white p-4 md:p-8 rounded-lg multi-shadow overflow-auto">
                    <table class="min-w-full w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Order ID</th>
                                <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="py-3 px-4 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="orderHistoryTableBody" class="bg-white divide-y divide-gray-100">
                            <!-- Order history will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customerId = <?php echo json_encode($customer_id); ?>;

            // Fetch customer details
            fetch(`../../backend/api/customers/get_single.php?id=${customerId}`)
                .then(response => {
                    if (response.status === 401) {
                        window.location.href = '../login.php';
                        return Promise.reject('Unauthorized');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const customer = data.data;
                        const customerDetailsDiv = document.getElementById('customerDetails');
                        customerDetailsDiv.innerHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div><strong>Name:</strong> ${customer.full_name}</div>
                            <div><strong>Email:</strong> ${customer.email}</div>
                            <div><strong>Phone:</strong> ${customer.phone}</div>
                            <div><strong>Address:</strong> ${customer.address || 'N/A'}</div>
                        </div>
                    `;
                    } else {
                        alert('Failed to load customer details: ' + data.message);
                    }
                })
                .catch(error => {
                    if (error !== 'Unauthorized') {
                        console.error('Error fetching customer details:', error);
                        alert('An error occurred while fetching customer details.');
                    }
                });

            // Fetch order history for this customer
            fetch(`../../backend/api/orders/get_all.php?customer_id=${customerId}`)
                .then(response => {
                    if (response.status === 401) {
                        // Don't redirect here, just show no orders
                        return Promise.reject('Unauthorized');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        const tableBody = document.getElementById('orderHistoryTableBody');
                        tableBody.innerHTML = ''; // Clear existing rows
                        data.data.forEach(order => {
                            const dateVal = order.order_date || order.created_at || '';
                            const dateStr = dateVal ? new Date(dateVal).toLocaleDateString() : '';
                            const total = (parseFloat(order.total_amount) || 0).toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            const tr = document.createElement('tr');
                            tr.className = 'hover:bg-gray-50';
                            tr.innerHTML = `
                                <td class="py-3 px-4 text-sm text-gray-700">${escapeHtml(order.order_number || '')}</td>
                                <td class="py-3 px-4 text-sm text-gray-700">${escapeHtml(dateStr)}</td>
                                <td class="py-3 px-4 text-sm text-gray-700 text-right">₦${total}</td>
                                <td class="py-3 px-4 text-sm text-center"><span class="${getStatusClasses(order.status)} text-xs font-semibold px-2 py-1 rounded-full">${escapeHtml(order.status || '')}</span></td>
                                <td class="py-3 px-4 text-sm text-center"><a href="order-details.php?id=${encodeURIComponent(order.id)}" class="text-blue-600 hover:underline">View</a></td>
                            `;
                            tableBody.appendChild(tr);
                        });
                    } else {
                        document.getElementById('orderHistoryTableBody').innerHTML = '<tr><td colspan="5" class="text-center py-4">No orders found for this customer.</td></tr>';
                    }
                })
                .catch(error => {
                    if (error !== 'Unauthorized') {
                        console.error('Error fetching order history:', error);
                        document.getElementById('orderHistoryTableBody').innerHTML = '<tr><td colspan="5" class="text-center py-4">Error loading order history.</td></tr>';
                    }
                });

            function getStatusClasses(status) {
                switch ((status || '').toLowerCase()) {
                    case 'pending':
                        return 'bg-yellow-100 text-yellow-800';
                    case 'delivered':
                        return 'bg-green-100 text-green-800';
                    case 'cancelled':
                        return 'bg-red-100 text-red-800';
                    case 'processing':
                        return 'bg-blue-100 text-blue-800';
                    case 'out_for_delivery':
                        return 'bg-purple-100 text-purple-800';
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