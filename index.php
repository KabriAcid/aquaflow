<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Aquaflow — Water & Beverage Factory Management</title>
    <meta name="description" content="Aquaflow — Manage products, orders, production and inventory for a water & beverage factory">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="favicon.png">

    <!-- Local styles (tailwind.css optional prebuilt + custom style) -->
    <link rel="stylesheet" href="frontend/css/tailwind.css">
    <link rel="stylesheet" href="frontend/css/style.css">
</head>

<body class="bg-gray-50 text-gray-800">
    <nav class="bg-white shadow-md">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <!-- AAdd the favicon brand icon  -->
                        <div class="flex items-center">
                            <img src="favicon.png" alt="Aquaflow" class="h-8">
                            <a href="index.php" class="flex items-center py-4 px-2">
                                <span class="font-bold text-gray-700 text-lg">Aquaflow</span>
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="index.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Home</a>
                        <a href="frontend/products.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Products</a>
                        <a href="frontend/contact.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Contact</a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3 ">
                    <a href="frontend/login.php" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">Log In</a>
                    <a href="frontend/register.php" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Register</a>
                </div>
                <div class="md:hidden flex items-center">
                    <button class="outline-none mobile-menu-button">
                        <svg class=" w-6 h-6 text-gray-500 hover:text-blue-500 "
                            x-show="!showMenu"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="hidden mobile-menu">
            <ul class="">
                <li><a href="index.php" class="block text-sm px-2 py-4 text-white bg-blue-500 font-semibold">Home</a></li>
                <li><a href="frontend/products.php" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Products</a></li>
                <li><a href="frontend/contact.php" class="block text-sm px-2 py-4 hover:bg-blue-500 transition duration-300">Contact</a></li>
            </ul>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <!-- Section 1: Hero -->
        <section id="hero" class="text-center py-12">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Aquaflow — Smart Factory Management</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">Manage products, production, inventory and orders with a lightweight web interface built for water and beverage factories.</p>
            <div class="space-x-3">
                <a href="frontend/products.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded">Browse Products</a>
                <a href="frontend/register.php" class="inline-block border border-blue-600 text-blue-600 px-6 py-2 rounded">Create Account</a>
            </div>
        </section>

        <!-- Section 2: Featured Products -->
        <section id="featured" class="py-12">
            <h2 class="text-2xl font-semibold mb-6">Featured Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <article class="bg-white rounded shadow p-4">
                    <div class="h-40 bg-gray-100 rounded mb-4 flex items-center justify-center">Image</div>
                    <h3 class="font-semibold">Natural Spring Water — 1L</h3>
                    <p class="text-sm text-gray-600">₦120</p>
                    <div class="mt-3"><a href="frontend/products.php" class="text-blue-600">View</a></div>
                </article>

                <article class="bg-white rounded shadow p-4">
                    <div class="h-40 bg-gray-100 rounded mb-4 flex items-center justify-center">Image</div>
                    <h3 class="font-semibold">Sparkling Beverage — 330ml</h3>
                    <p class="text-sm text-gray-600">₦200</p>
                    <div class="mt-3"><a href="frontend/products.php" class="text-blue-600">View</a></div>
                </article>

                <article class="bg-white rounded shadow p-4">
                    <div class="h-40 bg-gray-100 rounded mb-4 flex items-center justify-center">Image</div>
                    <h3 class="font-semibold">Bulk Water Package — 24 x 500ml</h3>
                    <p class="text-sm text-gray-600">₦2,500</p>
                    <div class="mt-3"><a href="frontend/products.php" class="text-blue-600">View</a></div>
                </article>
            </div>
        </section>

        <!-- Section 3: About / How it helps -->
        <section id="about" class="py-12">
            <h2 class="text-2xl font-semibold mb-4">Why Aquaflow?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded shadow p-4">
                    <h4 class="font-medium mb-2">Inventory Control</h4>
                    <p class="text-sm text-gray-600">Track stock levels, set reorder points and avoid production delays.</p>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h4 class="font-medium mb-2">Production Scheduling</h4>
                    <p class="text-sm text-gray-600">Plan shifts, log output and monitor material usage by shift.</p>
                </div>
                <div class="bg-white rounded shadow p-4">
                    <h4 class="font-medium mb-2">Orders & Payments</h4>
                    <p class="text-sm text-gray-600">Receive orders, manage deliveries and verify payments with integrated workflows.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white border-t py-6 mt-10">
        <div class="max-w-6xl mx-auto px-4 text-sm text-gray-600">
            <div class="flex items-center justify-between">
                <div>© 2025 Aquaflow. All rights reserved.</div>
                <div class="space-x-4">
                    <a href="#">Privacy</a>
                    <a href="frontend/contact.php">Contact</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>