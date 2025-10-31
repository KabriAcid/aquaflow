<?php
// customer/checkout.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Aquaflow</title>
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
                </div>
            </div>
        </div>
    </nav>

    <!-- Checkout Content -->
    <main class="py-10">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Checkout</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Delivery Information -->
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-6">Delivery Information</h2>
                    <form id="checkoutForm">
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Delivery Address</label>
                            <input type="text" id="address" name="address" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" id="city" name="city" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                            <input type="text" id="state" name="state" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                            <input type="date" id="delivery_date" name="delivery_date" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="special_instructions" class="block text-sm font-medium text-gray-700">Special Instructions</label>
                            <textarea id="special_instructions" name="special_instructions" rows="3" class="mt-1 block w-full border rounded px-3 py-2"></textarea>
                        </div>
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-800">Payment Method</h3>
                            <div class="mt-2 space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="card" class="form-radio" checked>
                                    <span class="ml-2">Credit Card</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="form-radio">
                                    <span class="ml-2">Bank Transfer</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="payment_method" value="cash_on_delivery" class="form-radio">
                                    <span class="ml-2">Cash on Delivery</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-6">Order Summary</h2>
                    <div id="orderSummary">
                        <!-- Order summary will be populated here -->
                    </div>
                    <div class="mt-6">
                        <button id="placeOrderBtn" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700">Place Order</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/cart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // verify session
            fetch('../../backend/api/auth/me.php', {
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(u => {
                    if (!u.success) {
                        window.location.href = '../login.php';
                        return;
                    }
                    renderOrderSummary();
                    populateAddress();

                    document.getElementById('placeOrderBtn').addEventListener('click', placeOrder);
                    // Set minimum delivery date to tomorrow
                    const today = new Date();
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    const minDate = tomorrow.toISOString().split('T')[0];
                    document.getElementById('delivery_date').setAttribute('min', minDate);
                })
                .catch(err => {
                    console.error('Auth check failed', err);
                    window.location.href = '../login.php';
                });
        });

        function renderOrderSummary() {
            const cart = getCart();
            const orderSummary = document.getElementById('orderSummary');
            let summaryHtml = '';

            if (cart.length === 0) {
                orderSummary.innerHTML = '<p>Your cart is empty.</p>';
                document.getElementById('placeOrderBtn').disabled = true;
                return;
            }

            cart.forEach(item => {
                summaryHtml += `
                    <div class="flex justify-between items-center mb-2">
                        <span>${item.name} (x${item.quantity})</span>
                        <span>₦${(item.price * item.quantity).toFixed(2)}</span>
                    </div>`;
            });

            const total = calculateTotal();
            const deliveryFee = 500; // Example fee
            const grandTotal = total + deliveryFee;

            summaryHtml += `
                <hr class="my-4">
                <div class="flex justify-between font-semibold">
                    <span>Subtotal</span>
                    <span>₦${total.toFixed(2)}</span>
                </div>
                 <div class="flex justify-between font-semibold">
                    <span>Delivery Fee</span>
                    <span>₦${deliveryFee.toFixed(2)}</span>
                </div>
                <div class="flex justify-between font-bold text-xl mt-2">
                    <span>Total</span>
                    <span>₦${grandTotal.toFixed(2)}</span>
                </div>`;

            orderSummary.innerHTML = summaryHtml;
        }

        function populateAddress() {
            fetch('../../backend/api/auth/get_profile.php', {
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        document.getElementById('address').value = data.data.address || '';
                        document.getElementById('city').value = data.data.city || '';
                        document.getElementById('state').value = data.data.state || '';
                    }
                });
        }

        function placeOrder() {
            const token = localStorage.getItem('authToken');
            const cart = getCart();
            if (cart.length === 0) {
                alert('Your cart is empty.');
                return;
            }

            const orderData = {
                delivery_address: document.getElementById('address').value + ', ' + document.getElementById('city').value + ', ' + document.getElementById('state').value,
                delivery_date: document.getElementById('delivery_date').value,
                special_instructions: document.getElementById('special_instructions').value,
                payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                items: cart,
                subtotal: calculateTotal(),
                delivery_fee: 500, // Example fee
                total_amount: calculateTotal() + 500
            };

            fetch('../../backend/api/orders/create.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        clearCart();
                        alert('Order placed successfully!');
                        window.location.href = `payment.php?order_id=${data.data.order_id}`;
                    } else {
                        alert('Failed to place order: ' + data.message);
                    }
                })
                .catch(err => {
                    alert('An error occurred while placing your order. Please try again.');
                    console.error(err);
                });
        }
    </script>

</body>

</html>