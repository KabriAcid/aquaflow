<?php
// customer/cart.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Aquaflow</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="dashboard.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Dashboard</a>
                        <a href="products.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Products</a>
                        <a href="orders.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">My Orders</a>
                        <a href="cart.php" class="py-4 px-2 text-blue-500 border-b-4 border-blue-500 font-semibold">Cart</a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3 ">
                    <a href="profile.php" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">Profile</a>
                    <a href="#" id="logoutBtn" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Log Out</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Cart Content -->
    <main class="py-10">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Shopping Cart</h1>
            <div id="cartContent" class="bg-white p-8 rounded-lg shadow-md">
                <!-- Cart items will be populated here -->
            </div>
        </div>
    </main>

    <script src="../js/cart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('authToken');
            if (!token) {
                window.location.href = '../login.php';
                return;
            }
            renderCart();
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

            let cartTable = `
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Product</th>
                            <th class="py-2 px-4 border-b">Price</th>
                            <th class="py-2 px-4 border-b">Quantity</th>
                            <th class="py-2 px-4 border-b">Subtotal</th>
                            <th class="py-2 px-4 border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody>`;

            cart.forEach(item => {
                cartTable += `
                    <tr>
                        <td class="py-2 px-4 border-b">${item.name}</td>
                        <td class="py-2 px-4 border-b">₦${parseFloat(item.price).toFixed(2)}</td>
                        <td class="py-2 px-4 border-b">
                            <input type="number" value="${item.quantity}" min="${item.minQty}" onchange="updateQuantity(${item.id}, this.value)" class="w-20 text-center border rounded">
                        </td>
                        <td class="py-2 px-4 border-b">₦${(item.price * item.quantity).toFixed(2)}</td>
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
                    <p class="text-lg">Subtotal: <span class="font-semibold">₦${total.toFixed(2)}</span></p>
                    <p class="text-lg">Delivery Fee: <span class="font-semibold">₦${deliveryFee.toFixed(2)}</span></p>
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
                localStorage.removeItem('authToken');
                localStorage.removeItem('userName');
                window.location.href = '../login.php';
            });
        }
    </script>

</body>

</html>