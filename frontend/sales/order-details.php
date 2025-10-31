<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales_manager') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Order Details";
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    header("Location: orders.php");
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
                <h1 class="text-3xl font-bold text-gray-800">Order Details</h1>
                <a href="orders.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Orders</a>
            </div>

            <div id="orderDetails" class="bg-white p-8 rounded-lg shadow-md">
                <!-- Order details will be loaded here -->
                <p>Loading order details...</p>
            </div>
        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderId = <?php echo json_encode($order_id); ?>;

            fetch(`../../backend/api/orders/get_single.php?id=${orderId}`)
            .then(response => {
                if (response.status === 401) {
                    window.location.href = '../login.php';
                    return Promise.reject('Unauthorized');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const order = data.data;
                    const orderDetailsDiv = document.getElementById('orderDetails');
                    let itemsHtml = '';
                    order.items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td class="py-2 px-4 border-b">${item.product_name}</td>
                                <td class="py-2 px-4 border-b">${item.quantity}</td>
                                <td class="py-2 px-4 border-b">₦${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td class="py-2 px-4 border-b">₦${(item.quantity * item.unit_price).toFixed(2)}</td>
                            </tr>
                        `;
                    });

                    orderDetailsDiv.innerHTML = `
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div><strong>Order ID:</strong> ${order.order_number}</div>
                            <div><strong>Order Date:</strong> ${new Date(order.order_date).toLocaleDateString()}</div>
                            <div><strong>Customer:</strong> ${order.customer_name}</div>
                            <div><strong>Status:</strong> <span class="${getStatusClass(order.status)} text-white py-1 px-3 rounded-full text-xs">${order.status}</span></div>
                        </div>
                        <h2 class="text-xl font-bold mb-4">Items</h2>
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Product</th>
                                    <th class="py-2 px-4 border-b">Quantity</th>
                                    <th class="py-2 px-4 border-b">Unit Price</th>
                                    <th class="py-2 px-4 border-b">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                        <div class="text-right mt-4">
                            <h2 class="text-2xl font-bold">Total: ₦${parseFloat(order.total_amount).toFixed(2)}</h2>
                        </div>
                    `;
                } else {
                    alert('Failed to load order details: ' + data.message);
                }
            })
            .catch(error => {
                if (error !== 'Unauthorized') {
                    console.error('Error fetching order details:', error);
                    alert('An error occurred while fetching order details.');
                }
            });

            function getStatusClass(status) {
                 switch (status.toLowerCase()) {
                    case 'pending':
                        return 'bg-yellow-500';
                    case 'delivered':
                        return 'bg-green-500';
                    case 'cancelled':
                        return 'bg-red-500';
                    case 'processing':
                        return 'bg-blue-500';
                    case 'out_for_delivery':
                        return 'bg-purple-500';
                    default:
                        return 'bg-gray-500';
                }
            }
        });
    </script>

</body>
</html>
