<?php
// customer/checkout.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Aquaflow</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../favicon.png">

    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-gray-100">

    <!-- Navigation -->
    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

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
                            <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                            <select id="state" name="state" class="mt-1 block w-full border rounded px-3 py-2" required>
                                <option value="">Select state</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <select id="city" name="city" class="mt-1 block w-full border rounded px-3 py-2" required>
                                <option value="">Select city</option>
                            </select>
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
                                    <span class="ml-2">Credit/Debit Card (Flutterwave)</span>
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
    <!-- Flutterwave inline script -->
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script>
        // Flutterwave public key from env (set FLW_PUBLIC_KEY in your environment or replace below)
        const FLW_PUBLIC_KEY = '<?= htmlspecialchars("FLWPUBK_TEST-14973597272a7c26e4c3dc1f63affa33-X") ?>';
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
                    populateStatesAndCities();

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
                        // select state and city once options are populated
                        const setStateCity = () => {
                            const stateSel = document.getElementById('state');
                            const citySel = document.getElementById('city');
                            if (stateSel.options.length > 1) {
                                if (data.data.state) stateSel.value = data.data.state;
                                // trigger change to populate cities
                                const evt = new Event('change');
                                stateSel.dispatchEvent(evt);
                                if (data.data.city) citySel.value = data.data.city;
                            } else {
                                // try again shortly
                                setTimeout(setStateCity, 150);
                            }
                        };
                        setStateCity();
                    }
                });
        }

        function populateStatesAndCities() {
            fetch('../../backend/api/location/states_cities.php')
                .then(r => r.json())
                .then(data => {
                    // data may be raw JSON object (not wrapped) depending on API — handle both
                    const mapping = data && data.data ? data.data : data;
                    const stateSel = document.getElementById('state');
                    const citySel = document.getElementById('city');
                    // clear existing options except first
                    stateSel.innerHTML = '<option value="">Select state</option>';
                    citySel.innerHTML = '<option value="">Select city</option>';
                    for (const key in mapping) {
                        if (!mapping.hasOwnProperty(key)) continue;
                        const stateObj = mapping[key];
                        const opt = document.createElement('option');
                        opt.value = stateObj.name || key;
                        opt.textContent = stateObj.name || key;
                        stateSel.appendChild(opt);
                    }

                    stateSel.addEventListener('change', function() {
                        const s = stateSel.value;
                        citySel.innerHTML = '<option value="">Select city</option>';
                        // find state object
                        let found = null;
                        for (const k in mapping) {
                            if (!mapping.hasOwnProperty(k)) continue;
                            if ((mapping[k].name || k) === s) {
                                found = mapping[k];
                                break;
                            }
                        }
                        if (found && Array.isArray(found.cities)) {
                            found.cities.forEach(c => {
                                const o = document.createElement('option');
                                o.value = c.name || c.id;
                                o.textContent = c.name || c.id;
                                citySel.appendChild(o);
                            });
                        }
                    });
                })
                .catch(err => {
                    console.error('Failed to load states/cities mapping', err);
                });
        }

        function placeOrder() {
            const token = localStorage.getItem('authToken');
            const cart = getCart();
            if (cart.length === 0) {
                alert('Your cart is empty.');
                return;
            }
            const deliveryAddress = [document.getElementById('address').value, document.getElementById('city').value, document.getElementById('state').value].filter(Boolean).join(', ');
            const orderData = {
                delivery_address: deliveryAddress,
                delivery_date: document.getElementById('delivery_date').value,
                special_instructions: document.getElementById('special_instructions').value,
                payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                items: cart,
                subtotal: calculateTotal(),
                delivery_fee: 500, // Example fee
                total_amount: calculateTotal() + 500
            };

            // create order on server first
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
                    if (!data.success) {
                        alert('Failed to create order: ' + data.message);
                        return;
                    }
                    // payload structure: { order: {order_id,...}, user: {email,..} }
                    const payload = data.data || {};
                    const order = payload.order || payload;
                    const user = payload.user || {};
                    const orderId = order.order_id;
                    const amount = parseFloat(order.total_amount || order.total_amount || 0);

                    if (orderData.payment_method === 'card') {
                        if (!FLW_PUBLIC_KEY || FLW_PUBLIC_KEY.includes('REPLACE_ME')) {
                            alert('Flutterwave public key is not configured. Set FLW_PUBLIC_KEY in your environment variables.');
                            return;
                        }

                        // open Flutterwave checkout
                        FlutterwaveCheckout({
                            public_key: FLW_PUBLIC_KEY,
                            tx_ref: `order-${orderId}-${Date.now()}`,
                            amount: amount,
                            currency: 'NGN',
                            payment_options: 'card,ussd,qr',
                            customer: {
                                email: user.email || '',
                                phonenumber: user.phone || '',
                                name: user.full_name || ''
                            },
                            customizations: {
                                title: 'Aquaflow Order',
                                description: `Payment for order #${orderId}`
                            },
                            callback: function(res) {
                                // res.status === 'successful' normally
                                // send data to backend to verify
                                fetch('../../backend/api/payments/verify.php', {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        tx_ref: res.tx_ref,
                                        transaction_id: res.transaction_id,
                                        order_id: orderId
                                    })
                                }).then(r => r.json()).then(v => {
                                    if (v.success) {
                                        clearCart();
                                        window.location.href = `payment.php?order_id=${orderId}`;
                                    } else {
                                        alert('Payment verification failed: ' + v.message);
                                    }
                                }).catch(err => {
                                    console.error('Verification error', err);
                                    alert('Payment completed but verification failed. Contact support.');
                                });
                            },
                            onclose: function() {
                                // user closed the modal
                                console.log('Flutterwave modal closed');
                            }
                        });
                    } else {
                        // Cash on delivery — order already created
                        clearCart();
                        alert('Order placed successfully!');
                        window.location.href = `payment.php?order_id=${orderId}`;
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