<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
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
    <link rel="stylesheet" href="../css/tailwind.css">
</head>
<body class="bg-gray-100 flex">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Orders</h1>

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
            const token = localStorage.getItem('authToken');
            if (!token) {
                window.location.href = '../login.php';
                return;
            }

            fetch('../../backend/api/orders/get_all.php', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tableBody = document.getElementById('ordersTableBody');
                    tableBody.innerHTML = ''; // Clear existing rows
                    data.data.forEach(order => {
                        const statusClass = getStatusClass(order.status);
                        const row = `
                            <tr>
                                <td class="py-2 px-4 border-b">${order.order_number}</td>
                                <td class="py-2 px-4 border-b">${order.customer_name}</td>
                                <td class="py-2 px-4 border-b">${new Date(order.order_date).toLocaleDateString()}</td>
                                <td class="py-2 px-4 border-b">₦${parseFloat(order.total_amount).toFixed(2)}</td>
                                <td class="py-2 px-4 border-b"><span class="${statusClass} text-white py-1 px-3 rounded-full text-xs">${order.status}</span></td>
                                <td class="py-2 px-4 border-b"><a href="order-details.php?id=${order.id}" class="text-blue-500 hover:underline">View</a></td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    alert('Failed to load orders: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching orders:', error);
                alert('An error occurred while fetching orders.');
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
