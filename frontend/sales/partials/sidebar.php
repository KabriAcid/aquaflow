<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-gray-800 text-white min-h-screen p-4 flex flex-col">
    <div class="mb-8 flex items-center gap-3">
        <img src="../../favicon.png" alt="Aquaflow" class="h-10">
        <a href="dashboard.php" class="text-2xl font-bold">Aquaflow</a>
    </div>
    <nav class="flex-1">
        <ul class="space-y-2">
            <li>
                <a href="dashboard.php" class="flex items-center p-3 rounded-md hover:bg-gray-700 transition-colors <?php echo ($current_page == 'dashboard.php') ? 'bg-gray-700' : ''; ?>">
                    <span class="mr-3 w-6 h-6 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="orders.php" class="flex items-center p-3 rounded-md hover:bg-gray-700 transition-colors <?php echo ($current_page == 'orders.php') ? 'bg-gray-700' : ''; ?>">
                    <span class="mr-3 w-6 h-6 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </span>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="customers.php" class="flex items-center p-3 rounded-md hover:bg-gray-700 transition-colors <?php echo ($current_page == 'customers.php') ? 'bg-gray-700' : ''; ?>">
                    <span class="mr-3 w-6 h-6 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.25-1.264-.69-1.724M7 16c-3.314 0-6-2.686-6-6s2.686-6 6-6 6 2.686 6 6-2.686 6-6 6z"></path>
                        </svg>
                    </span>
                    <span>Customers</span>
                </a>
            </li>
            <li>
                <a href="products.php" class="flex items-center p-3 rounded-md hover:bg-gray-700 transition-colors <?php echo ($current_page == 'products.php') ? 'bg-gray-700' : ''; ?>">
                    <span class="mr-3 w-6 h-6 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </span>
                    <span>Products</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>