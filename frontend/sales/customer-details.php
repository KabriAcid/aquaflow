<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
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
    <link rel="stylesheet" href="../css/tailwind.css">
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

            <div id="customerDetails" class="bg-white p-8 rounded-lg shadow-md">
                <!-- Customer details will be loaded here -->
                <p>Loading customer details...</p>
            </div>

            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Order History</h2>
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
                        <tbody id="orderHistoryTableBody">
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
            const token = localStorage.getItem('authToken');
            if (!token) {
                window.location.href = '../login.php';
                return;
            }

            const customerId = <?php echo json_encode($customer_id); ?>;

            // Fetch customer details
            fetch(`../../backend/api/customers/get_by_id.php?id=${customerId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const customer = data.data;
                    const customerDetailsDiv = document.getElementById('customerDetails');
                    customerDetailsDiv.innerHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div><strong>Name:</strong> ${customer.name}</div>
                            <div><strong>Email:</strong> ${customer.email}</div>
                            <div><strong>Phone:</strong> ${customer.phone}</div>
                            <div><strong>Address:</strong> ${customer.address}</div>
                        </div>
                    `;
                } else {
                    alert('Failed to load customer details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching customer details:', error);
                alert('An error occurred while fetching customer details.');
            });

            // Fetch order history
            fetch(`../../backend/api/orders/get_by_customer.php?customer_id=${customerId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tableBody = document.getElementById('orderHistoryTableBody');
                    tableBody.innerHTML = ''; // Clear existing rows
                    data.data.forEach(order => {
                        const statusClass = getStatusClass(order.status);
                        const row = `
                            <tr>
                                <td class="py-2 px-4 border-b">${order.order_number}</td>
                                <td class="py-2 px-4 border-b">${new Date(order.order_date).toLocaleDateString()}</td>
                                <td class="py-2 px-4 border-b">₦${parseFloat(order.total_amount).toFixed(2)}</td>
                                <td class="py-2 px-4 border-b"><span class="${statusClass} text-white py-1 px-3 rounded-full text-xs">${order.status}</span></td>
                                <td class="py-2 px-4 border-b"><a href="order-details.php?id=${order.id}" class="text-blue-500 hover:underline">View</a></td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    // No need to alert, as it might be that the customer has no orders
                    document.getElementById('orderHistoryTableBody').innerHTML = '<tr><td colspan="5" class="text-center py-4">No orders found for this customer.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching order history:', error);
                alert('An error occurred while fetching order history.');
            });

            function getStatusClass(status) {
                switch (status.toLowerCase()) {
                    case 'pending':
                        return 'bg-yellow-500';
                    case 'delivered':
                        return 'bg-green-500';
                    case 'cancelled':
                        return 'bg-red-500';
                    default:
                        return 'bg-gray-500';
                }
            }
        });
    </script>

</body>
</html>
