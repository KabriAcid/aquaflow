<?php
// frontend/customer/partials/topbar.php
?>
<!-- Topbar (reusable) -->
<nav class="bg-white multi-shadow">
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
                    <a href="cart.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Cart <span id="cart-badge" class="ml-2 inline-block bg-red-500 text-white rounded-full px-2 py-0 text-xs">0</span></a>
                </div>
            </div>
            <div class="hidden md:flex items-center space-x-3 ">
                <a href="profile.php" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">Profile</a>
                <a href="#" id="logoutBtn" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Log Out</a>
            </div>
        </div>
    </div>
</nav>

<script>
    // Attach logout handler so pages don't need to duplicate this code
    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('../../backend/api/auth/logout.php', {
                    method: 'POST',
                    credentials: 'same-origin'
                }).finally(function() {
                    window.location.href = '../login.php';
                });
            });
        }

        // Allow cart.js to update the badge by exposing updateCartBadge if it's defined
        if (typeof updateCartBadge === 'function') {
            updateCartBadge();
        }
    });
</script>