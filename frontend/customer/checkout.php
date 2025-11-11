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
            <div id="checkoutGrid" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Delivery Information -->
                <div class="bg-white p-8 rounded-lg multi-shadow">
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
                <div class="bg-white p-8 rounded-lg multi-shadow">
                    <h2 class="text-2xl font-bold mb-6">Order Summary</h2>
                    <div id="orderSummary">
                        <!-- Order summary will be populated here -->
                    </div>
                    <div id="codConfirm" class="mt-6 bg-yellow-50 p-4 rounded hidden">
                        <p class="mb-3">You selected Cash on Delivery. Confirm that you want to place the order and pay on delivery.</p>
                        <div class="flex gap-3">
                            <button id="confirmCodBtn" class="flex-1 bg-green-600 text-white py-3 rounded-md hover:bg-green-700">Confirm & Place Order (COD)</button>
                            <button id="cancelCodBtn" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-md hover:bg-gray-300">Cancel</button>
                        </div>
                    </div>
                    <div id="validationMessage" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-md hidden">
                        <p class="text-red-700 text-sm" id="validationText"></p>
                    </div>
                    <div class="mt-6">
                        <button id="placeOrderBtn" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700">Place Order</button>
                    </div>
                </div>
            </div>

            <!-- Full-page COD confirmation (hidden by default) -->
            <div id="codFullConfirm" class="hidden bg-yellow-50 p-8 rounded-lg multi-shadow mt-6">
                <h3 class="text-lg font-medium mb-3">Confirm Cash on Delivery</h3>
                <p class="mb-4">You will pay on delivery. Click confirm to place the order and log the order and transaction statuses.</p>
                <div class="flex gap-3">
                    <button id="confirmCodFullBtn" class="flex-1 bg-green-600 text-white py-3 rounded-md hover:bg-green-700">Confirm & Place Order (COD)</button>
                    <button id="cancelCodFullBtn" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-md hover:bg-gray-300">Cancel</button>
                </div>
            </div>
        </div>
    </main>

    <script src="../js/cart.js"></script>
    <!-- Flutterwave inline script -->
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="../js/utils.js"></script>
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

                    // Ensure the place order button is disabled until validation passes
                    const placeBtn = document.getElementById('placeOrderBtn');
                    const confirmCodBtn = document.getElementById('confirmCodBtn');
                    const cancelCodBtn = document.getElementById('cancelCodBtn');
                    const codConfirm = document.getElementById('codConfirm');
                    const checkoutGrid = document.getElementById('checkoutGrid');
                    const codFullConfirm = document.getElementById('codFullConfirm');
                    const confirmCodFullBtn = document.getElementById('confirmCodFullBtn');
                    const cancelCodFullBtn = document.getElementById('cancelCodFullBtn');
                    const validationMessage = document.getElementById('validationMessage');
                    const validationText = document.getElementById('validationText');

                    placeBtn.disabled = true;

                    // Form validation: ensure required fields are filled and cart not empty
                    const requiredFields = ['address', 'state', 'city', 'delivery_date'];

                    function formIsValid() {
                        // must have cart
                        const cart = getCart();
                        if (!cart || cart.length === 0) return false;
                        for (const id of requiredFields) {
                            const el = document.getElementById(id);
                            if (!el) return false;
                            if (!el.value || String(el.value).trim() === '') return false;
                        }
                        return true;
                    }

                    function getValidationMessage() {
                        // Check each required field and return specific message for first missing field
                        const cart = getCart();
                        if (!cart || cart.length === 0) {
                            return 'Your cart is empty.';
                        }

                        const address = document.getElementById('address').value.trim();
                        if (!address) {
                            return 'Please enter a delivery address.';
                        }

                        const state = document.getElementById('state').value.trim();
                        if (!state) {
                            return 'Please select a state.';
                        }

                        const city = document.getElementById('city').value.trim();
                        if (!city) {
                            return 'Please select a city.';
                        }

                        const deliveryDate = document.getElementById('delivery_date').value.trim();
                        if (!deliveryDate) {
                            return 'Please select a delivery date.';
                        }

                        return '';
                    }

                    function updateValidationDisplay() {
                        const isValid = formIsValid();
                        if (isValid) {
                            validationMessage.classList.add('hidden');
                            validationText.textContent = '';
                        } else {
                            validationMessage.classList.remove('hidden');
                            validationText.textContent = getValidationMessage();
                        }
                    }

                    function updateUiForPaymentMethod() {
                        const method = document.querySelector('input[name="payment_method"]:checked').value;
                        const isValid = formIsValid();

                        if (method === 'cash_on_delivery') {
                            // For COD: only show COD container if form is valid
                            if (isValid) {
                                // hide full checkout grid and show a single confirm container
                                if (checkoutGrid) checkoutGrid.classList.add('hidden');
                                if (codFullConfirm) codFullConfirm.classList.remove('hidden');
                                // hide inline place button and inline cod confirm
                                placeBtn.classList.add('hidden');
                                if (codConfirm) codConfirm.classList.add('hidden');
                                // set confirm button state based on form validity
                                if (confirmCodBtn) confirmCodBtn.disabled = false;
                                if (confirmCodFullBtn) confirmCodFullBtn.disabled = false;
                            } else {
                                // Form not valid — keep checkout grid visible with validation message
                                if (checkoutGrid) checkoutGrid.classList.remove('hidden');
                                if (codFullConfirm) codFullConfirm.classList.add('hidden');
                                placeBtn.classList.add('hidden');
                                if (codConfirm) codConfirm.classList.add('hidden');
                            }
                        } else {
                            // card payment — show regular checkout grid
                            if (checkoutGrid) checkoutGrid.classList.remove('hidden');
                            if (codFullConfirm) codFullConfirm.classList.add('hidden');
                            placeBtn.classList.remove('hidden');
                            if (codConfirm) codConfirm.classList.add('hidden');
                            placeBtn.disabled = !isValid;
                        }

                        // Update validation message display
                        updateValidationDisplay();
                    }

                    // expose so other functions (renderOrderSummary) can trigger UI update when cart changes
                    window.updateUiForPaymentMethod = updateUiForPaymentMethod;

                    // Watch required fields and payment method changes
                    requiredFields.forEach(id => {
                        const el = document.getElementById(id);
                        if (!el) return;
                        el.addEventListener('input', () => updateUiForPaymentMethod());
                        el.addEventListener('change', () => updateUiForPaymentMethod());
                    });
                    document.querySelectorAll('input[name="payment_method"]').forEach(r => r.addEventListener('change', updateUiForPaymentMethod));

                    // wire place order and COD confirm/cancel (both inline and full)
                    document.getElementById('placeOrderBtn').addEventListener('click', placeOrder);
                    if (confirmCodBtn) confirmCodBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        placeOrder();
                    });
                    if (cancelCodBtn) cancelCodBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.querySelector('input[name="payment_method"][value="card"]').checked = true;
                        updateUiForPaymentMethod();
                    });
                    if (confirmCodFullBtn) confirmCodFullBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        placeOrder();
                    });
                    if (cancelCodFullBtn) cancelCodFullBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.querySelector('input[name="payment_method"][value="card"]').checked = true;
                        updateUiForPaymentMethod();
                    });

                    // Set delivery date default and minimum to today
                    const today = new Date();
                    const isoToday = today.toISOString().split('T')[0];
                    const deliveryEl = document.getElementById('delivery_date');
                    if (deliveryEl) {
                        deliveryEl.setAttribute('min', isoToday);
                        // if no value already set (e.g., from profile), default to today
                        if (!deliveryEl.value) deliveryEl.value = isoToday;
                    }

                    // initialize UI state
                    updateUiForPaymentMethod();
                })
                .catch(err => {
                    console.error('Auth check failed', err);
                    window.location.href = '../login.php';
                });
        });

        // small helper to escape text for insertion into HTML
        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // calculate total robustly from current cart (tolerant of different item shapes)
        function calculateTotal() {
            const cart = getCart();
            return cart.reduce((acc, item) => {
                const qty = (item.quantity !== undefined && item.quantity !== null) ? Number(item.quantity) : (item.qty !== undefined ? Number(item.qty) : 1);
                const price = (item.price !== undefined && item.price !== null) ? Number(item.price) : (item.unit_price !== undefined ? Number(item.unit_price) : 0);
                return acc + (price * qty);
            }, 0);
        }

        function renderOrderSummary() {
            const cart = getCart();
            const orderSummary = document.getElementById('orderSummary');
            let summaryHtml = '';

            if (cart.length === 0) {
                orderSummary.innerHTML = '<p>Your cart is empty.</p>';
                document.getElementById('placeOrderBtn').disabled = true;
                return;
            }

            // render each item — be tolerant of different cart item shapes
            cart.forEach(item => {
                const name = item.name || item.product_name || 'Item';
                const qty = (item.quantity !== undefined && item.quantity !== null) ? Number(item.quantity) : (item.qty !== undefined ? Number(item.qty) : 1);
                const price = (item.price !== undefined && item.price !== null) ? Number(item.price) : (item.unit_price !== undefined ? Number(item.unit_price) : 0);

                summaryHtml += `
                    <div class="flex justify-between items-center mb-2">
                        <span>${escapeHtml(name)} (x${qty})</span>
                        <span>${formatNaira(price * qty)}</span>
                    </div>`;
            });

            const total = calculateTotal();
            const deliveryFee = 500; // Example fee
            const grandTotal = total + deliveryFee;

            summaryHtml += `
                <hr class="my-4">
                <div class="flex justify-between font-semibold">
                    <span>Subtotal</span>
                    <span>${formatNaira(total)}</span>
                </div>
                 <div class="flex justify-between font-semibold">
                    <span>Delivery Fee</span>
                    <span>${formatNaira(deliveryFee)}</span>
                </div>
                <div class="flex justify-between font-bold text-xl mt-2">
                    <span>Total</span>
                    <span>${formatNaira(grandTotal)}</span>
                </div>`;

            orderSummary.innerHTML = summaryHtml;
            // notify UI that cart/summary changed so validation can update (if available)
            if (typeof window.updateUiForPaymentMethod === 'function') window.updateUiForPaymentMethod();
        }

        function populateAddress() {
            fetch('../../backend/api/auth/get_profile.php', {
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        document.getElementById('address').value = data.data.address || '';
                        // store defaults on selects — populateStatesAndCities will apply them when options are available
                        const stateSel = document.getElementById('state');
                        const citySel = document.getElementById('city');
                        if (stateSel && data.data.state) stateSel.dataset.default = data.data.state;
                        if (citySel && data.data.city) citySel.dataset.default = data.data.city;
                        // also attempt to apply immediately if options already present
                        try {
                            if (stateSel && stateSel.options.length > 1 && stateSel.dataset.default) {
                                stateSel.value = stateSel.dataset.default;
                                stateSel.dispatchEvent(new Event('change'));
                                if (citySel && citySel.dataset.default) citySel.value = citySel.dataset.default;
                            }
                        } catch (e) {
                            // ignore
                        }
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

                    // after populating states, if a default was provided by profile, select it
                    if (stateSel.dataset && stateSel.dataset.default) {
                        stateSel.value = stateSel.dataset.default;
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
                            // if a default city was provided, select it
                            if (citySel.dataset && citySel.dataset.default) {
                                citySel.value = citySel.dataset.default;
                            }
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

            // Validate required fields before processing order
            const address = document.getElementById('address').value.trim();
            const state = document.getElementById('state').value.trim();
            const city = document.getElementById('city').value.trim();
            const deliveryDate = document.getElementById('delivery_date').value.trim();

            if (!address) {
                alert('Please enter a delivery address.');
                return;
            }

            if (!state) {
                alert('Please select a state.');
                return;
            }

            if (!city) {
                alert('Please select a city.');
                return;
            }

            if (!deliveryDate) {
                alert('Please select a delivery date.');
                return;
            }

            const deliveryAddress = [address, city, state].filter(Boolean).join(', ');

            // sanitize and normalize cart items before sending to server
            const sanitizedItems = cart.map(it => {
                return {
                    id: it.id ?? it.product_id ?? null,
                    name: it.name ?? it.product_name ?? 'Item',
                    quantity: Number(it.quantity ?? it.qty ?? 1),
                    price: Number(it.price ?? it.unit_price ?? 0)
                };
            }).filter(i => i.id !== null);

            const subtotal = sanitizedItems.reduce((acc, it) => acc + (Number(it.price || 0) * Number(it.quantity || 0)), 0);

            const orderData = {
                delivery_address: deliveryAddress,
                delivery_date: document.getElementById('delivery_date').value,
                special_instructions: document.getElementById('special_instructions').value,
                payment_method: document.querySelector('input[name="payment_method"]:checked').value,
                items: sanitizedItems,
                subtotal: subtotal,
                delivery_fee: 500, // Example fee
                total_amount: subtotal + 500
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

                    // Clear the client-side cart immediately after the order is created server-side
                    // This prevents duplicate submissions and aligns with server-side order logging.
                    try {
                        clearCart(true);
                    } catch (e) {
                        console.warn('clearCart not available', e);
                    }

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
                                // send data to backend to verify and update payment status
                                fetch('../../backend/api/payments/verify.php', {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        tx_ref: res.tx_ref,
                                        transaction_id: res.transaction_id,
                                        order_id: orderId,
                                        payment_status: res.status === 'successful' ? 'completed' : 'failed'
                                    })
                                }).then(r => r.json()).then(v => {
                                    if (v.success) {
                                        // Cart already cleared above
                                        window.location.href = `payment.php?order_id=${orderId}`;
                                    } else {
                                        alert('Payment verification failed: ' + v.message);
                                        // Still navigate to order page so user can see the order
                                        window.location.href = `payment.php?order_id=${orderId}`;
                                    }
                                }).catch(err => {
                                    console.error('Verification error', err);
                                    alert('Payment completed but verification failed. Contact support.');
                                    // Still navigate to order page
                                    window.location.href = `payment.php?order_id=${orderId}`;
                                });
                            },
                            onclose: function() {
                                // user closed the modal
                                console.log('Flutterwave modal closed');
                            }
                        });
                    } else {
                        // Cash on delivery — create a COD transaction and mark order with pending status
                        fetch('../../backend/api/orders/confirm_cod.php', {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    order_id: orderId
                                })
                            })
                            .then(r => r.json())
                            .then(codResp => {
                                if (!codResp.success) {
                                    alert('Order placed but failed to confirm COD: ' + (codResp.message || 'Unknown'));
                                    // still redirect to order page so user can see order
                                    window.location.href = `payment.php?order_id=${orderId}`;
                                    return;
                                }
                                // success — clear cart (skip confirmation) and redirect to order/payment page
                                clearCart(true);
                                window.location.href = `payment.php?order_id=${orderId}`;
                            })
                            .catch(err => {
                                console.error('COD confirm error', err);
                                // fallback: redirect to order page and clear cart (skip confirmation)
                                clearCart(true);
                                window.location.href = `payment.php?order_id=${orderId}`;
                            });
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