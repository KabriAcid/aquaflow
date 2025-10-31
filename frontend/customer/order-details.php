<?php
// customer/order-details.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Aquaflow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-gray-100">

    <!-- Navigation -->
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <!-- Order Details Content -->
    <main class="py-10">
        <div class="max-w-4xl mx-auto px-4">
            <div id="orderDetailsContent" class="bg-white p-8 rounded-lg multi-shadow">
                <!-- Order details will be populated here -->
                <p class="text-center">Loading order details...</p>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('../../backend/api/auth/me.php', {
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(u => {
                    if (!u.success) {
                        window.location.href = '../login.php';
                        return;
                    }

                    const urlParams = new URLSearchParams(window.location.search);
                    const orderId = urlParams.get('id');
                    if (!orderId) {
                        document.getElementById('orderDetailsContent').innerHTML = '<p class="text-center text-red-500">No order ID specified.</p>';
                        return;
                    }

                    fetch(`../../backend/api/orders/get_one.php?id=${orderId}`, {
                            credentials: 'same-origin'
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.data) {
                                renderOrderDetails(data.data);
                            } else {
                                document.getElementById('orderDetailsContent').innerHTML = `<p class="text-center text-red-500">Error: ${data.message}</p>`;
                            }
                        })
                        .catch(err => {
                            document.getElementById('orderDetailsContent').innerHTML = '<p class="text-center text-red-500">An error occurred while fetching order details.</p>';
                            console.error(err);
                        });
                })
                .catch(err => {
                    console.error('Auth check failed', err);
                    window.location.href = '../login.php';
                });
        });

        function renderOrderDetails(order) {
            const orderDetailsContent = document.getElementById('orderDetailsContent');
            let itemsHtml = '';
            order.items.forEach(item => {
                itemsHtml += `
                    <tr>
                        <td class="py-2 px-4 border-b">${item.product_name}</td>
                        <td class="py-2 px-4 border-b text-center">${item.quantity}</td>
                        <td class="py-2 px-4 border-b text-right">₦${parseFloat(item.unit_price).toFixed(2)}</td>
                        <td class="py-2 px-4 border-b text-right">₦${parseFloat(item.subtotal).toFixed(2)}</td>
                    </tr>`;
            });

            const detailsHtml = `
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Order #${order.order_number}</h1>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full ${getStatusColor(order.status)}">${order.status}</span>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-8">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${getProgressBarWidth(order.status)}"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                        <p><strong>Order Date:</strong> ${new Date(order.order_date).toLocaleDateString()}</p>
                        <p><strong>Delivery Date:</strong> ${new Date(order.delivery_date).toLocaleDateString()}</p>
                        <p><strong>Delivery Address:</strong> ${order.delivery_address}</p>
                         <p><strong>Payment Method:</strong> ${order.payment_method}</p>
                        <p><strong>Payment Status:</strong> ${order.payment_status}</p>
                    </div>
                     <div>
                        <h2 class="text-xl font-semibold mb-4">Financials</h2>
                        <p><strong>Subtotal:</strong> ₦${parseFloat(order.subtotal).toFixed(2)}</p>
                        <p><strong>Delivery Fee:</strong> ₦${parseFloat(order.delivery_fee).toFixed(2)}</p>
                        <p class="font-bold"><strong>Total:</strong> ₦${parseFloat(order.total_amount).toFixed(2)}</p>
                    </div>
                </div>

                <div class="mt-8">
                     <h2 class="text-xl font-semibold mb-4">Items Ordered</h2>
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b text-left">Product</th>
                                <th class="py-2 px-4 border-b text-center">Quantity</th>
                                <th class="py-2 px-4 border-b text-right">Unit Price</th>
                                <th class="py-2 px-4 border-b text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>${itemsHtml}</tbody>
                    </table>
                </div>

                 <div class="mt-8 text-right">
                    <button class="bg-gray-700 text-white px-6 py-2 rounded-md hover:bg-gray-800">Download Invoice</button>
                </div>
            `;

            orderDetailsContent.innerHTML = detailsHtml;
        }

        function getStatusColor(status) {
            switch (status) {
                case 'pending':
                    return 'bg-yellow-200 text-yellow-800';
                case 'processing':
                    return 'bg-blue-200 text-blue-800';
                case 'out_for_delivery':
                    return 'bg-indigo-200 text-indigo-800';
                case 'delivered':
                    return 'bg-green-200 text-green-800';
                case 'cancelled':
                    return 'bg-red-200 text-red-800';
                default:
                    return 'bg-gray-200 text-gray-800';
            }
        }

        function getProgressBarWidth(status) {
            switch (status) {
                case 'pending':
                    return '25%';
                case 'processing':
                    return '50%';
                case 'out_for_delivery':
                    return '75%';
                case 'delivered':
                    return '100%';
                default:
                    return '0%';
            }
        }

        document.getElementById('logoutBtn').addEventListener('click', function() {
            localStorage.removeItem('authToken');
            localStorage.removeItem('userName');
            window.location.href = '../login.php';
        });
    </script>

</body>

</html>