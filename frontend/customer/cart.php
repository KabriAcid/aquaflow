<?php
// customer/cart.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Aquaflow</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../favicon.png">

    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-gray-100">

    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <!-- Cart Content -->
    <main class="py-10">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Shopping Cart</h1>
            <div id="cartContent" class="bg-white p-8 rounded-lg multi-shadow">
                <!-- Cart items will be populated here -->
            </div>
        </div>
    </main>

    <script src="../js/cart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // detect session
            fetch('../../backend/api/auth/me.php', {
                    credentials: 'same-origin'
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        window.location.href = '../login.php';
                        return;
                    }
                    renderCart();
                })
                .catch(err => {
                    console.error('Auth check failed', err);
                    window.location.href = '../login.php';
                });
        });

        function renderCart() {
            const cart = getCart();
            const cartContent = document.getElementById('cartContent');
            cartContent.innerHTML = '';

            if (cart.length === 0) {
                cartContent.innerHTML = `
                    <p class="text-center text-gray-600">Your cart is empty.</p>
                    <div class="text-center mt-6">
                        <a href="products.php" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">Browse Products</a>
                    </div>`;
                return;
            }

            // Sanitize cart items: ensure minQty exists and quantity is at least minQty
            let sanitized = false;
            cart.forEach(item => {
                // prefer explicit minQty, fallback to minimum_order_quantity or 1
                const min = item.minQty ? Number(item.minQty) : (item.minimum_order_quantity ? Number(item.minimum_order_quantity) : 1);
                if (!item.minQty || item.minQty !== min) item.minQty = min;
                // coerce quantity to number and ensure at least min
                const qty = Number(item.quantity) || 0;
                if (qty < item.minQty) {
                    item.quantity = item.minQty;
                    sanitized = true;
                } else {
                    item.quantity = qty;
                }
            });
            if (sanitized) saveCart(cart);

            let cartTable = `
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b text-left">Product</th>
                            <th class="py-2 px-4 border-b text-left">Price</th>
                            <th class="py-2 px-4 border-b text-left">Quantity</th>
                            <th class="py-2 px-4 border-b text-left">Subtotal</th>
                            <th class="py-2 px-4 border-b text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>`;

            cart.forEach(item => {
                const qty = Number(item.quantity) || item.minQty || 1;
                const subtotal = Number(item.price) * qty;
                cartTable += `
                    <tr>
                        <td class="py-2 px-4 border-b">${item.name}</td>
                        <td class="py-2 px-4 border-b">₦${parseFloat(item.price).toFixed(2)}</td>
                        <td class="py-2 px-4 border-b">
                            <input type="number" value="${qty}" min="${item.minQty}" onchange="updateQuantity(${item.id}, this.value)" class="w-20 text-center border rounded">
                        </td>
                        <td class="py-2 px-4 border-b">₦${subtotal.toFixed(2)}</td>
                        <td class="py-2 px-4 border-b">
                            <button onclick="removeFromCart(${item.id}); renderCart();" class="text-red-500 hover:underline">Remove</button>
                        </td>
                    </tr>`;
            });

            cartTable += `</tbody></table>`;

            const total = calculateTotal();
            const deliveryFee = 500; // Example fee
            const grandTotal = total + deliveryFee;

            let summary = `
                <div class="mt-6 text-right">
                    <p class="text-lg mb-3">Subtotal: <span>₦${total.toFixed(2)}</span></p>
                    <p class="text-lg mb-3">Delivery Fee: <span>₦${deliveryFee.toFixed(2)}</span></p>
                    <p class="text-2xl font-bold">Total: <span class="text-blue-600">₦${grandTotal.toFixed(2)}</span></p>
                    <div class="mt-6 flex justify-end space-x-4">
                        <a href="products.php" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-md hover:bg-gray-300">Continue Shopping</a>
                        <a href="checkout.php" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">Proceed to Checkout</a>
                    </div>
                     <div class="mt-4">
                        <button onclick="clearCart(); renderCart();" class="text-red-500 hover:underline">Clear Cart</button>
                    </div>
                </div>`;

            cartContent.innerHTML = cartTable + summary;
            document.getElementById('logoutBtn').addEventListener('click', function() {
                fetch('../../backend/api/auth/logout.php', {
                        method: 'POST',
                        credentials: 'same-origin'
                    })
                    .then(() => window.location.href = '../login.php')
                    .catch(() => window.location.href = '../login.php');
            });
        }
    </script>

</body>

</html>