<?php
// customer/payment.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Aquaflow</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../favicon.png">

    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-gray-100">

    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <!-- Payment Content -->
    <main class="py-10">
        <div class="max-w-lg mx-auto px-4">
            <div class="bg-white p-8 rounded-lg multi-shadow text-center">
                <h1 class="text-2xl font-bold mb-4">Confirm Your Payment</h1>
                <p class="text-gray-600 mb-6">You will be redirected to our secure payment gateway to complete your purchase.</p>
                <div id="orderDetails" class="text-left mb-6">
                    <!-- Order details will be loaded here -->
                </div>
                <button id="confirmPaymentBtn" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700">Confirm Payment</button>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // session check
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
                    const orderId = urlParams.get('order_id');
                    if (!orderId) {
                        alert('No order specified.');
                        window.location.href = 'orders.php';
                        return;
                    }
                    fetch(`../../backend/api/orders/get_one.php?id=${orderId}`, {
                            credentials: 'same-origin'
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.data) {
                                const order = data.data;
                                document.getElementById('orderDetails').innerHTML = `
                            <p><strong>Order Number:</strong> ${order.order_number}</p>
                            <p><strong>Total Amount:</strong> ₦${parseFloat(order.total_amount).toFixed(2)}</p>
                        `;
                            } else {
                                alert('Could not retrieve order details.');
                            }
                        });


                    document.getElementById('confirmPaymentBtn').addEventListener('click', function() {
                        // In a real application, this would redirect to a payment gateway like Flutterwave or Paystack.
                        // For this example, we will just mark the order as paid.
                        alert('Payment confirmed! Thank you for your order.');
                        window.location.href = `order-details.php?id=${orderId}`;
                    });
                })
                .catch(err => {
                    console.error('Auth check failed', err);
                    window.location.href = '../login.php';
                });
        });
    </script>

</body>

</html>