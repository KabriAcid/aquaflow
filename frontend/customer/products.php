<?php
// customer/products.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products - Aquaflow</title>
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
                    <div class="hidden md:flex items-center space-x-1">
                         <a href="dashboard.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Dashboard</a>
                        <a href="products.php" class="py-4 px-2 text-blue-500 border-b-4 border-blue-500 font-semibold">Products</a>
                        <a href="orders.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">My Orders</a>
                        <a href="cart.php" class="py-4 px-2 text-gray-500 font-semibold hover:text-blue-500 transition duration-300">Cart</a>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-3 ">
                     <a href="profile.php" class="py-2 px-2 font-medium text-gray-500 rounded hover:bg-blue-500 hover:text-white transition duration-300">Profile</a>
                    <a href="#" id="logoutBtn" class="py-2 px-2 font-medium text-white bg-blue-500 rounded hover:bg-blue-400 transition duration-300">Log Out</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Products Content -->
    <main class="py-10">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Browse Products</h1>

            <!-- Filters -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex space-x-4">
                    <select id="categoryFilter" class="border rounded px-3 py-2">
                        <option value="">All Categories</option>
                        <option value="bottled_water">Bottled Water</option>
                        <option value="beverage">Beverages</option>
                        <option value="package">Packages</option>
                    </select>
                    <input type="text" id="searchFilter" placeholder="Search by name..." class="border rounded px-3 py-2">
                </div>
            </div>

            <!-- Products Grid -->
            <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Products will be populated by JavaScript -->
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
            
            const productsGrid = document.getElementById('productsGrid');
            const categoryFilter = document.getElementById('categoryFilter');
            const searchFilter = document.getElementById('searchFilter');
            let allProducts = [];

            fetch('../../backend/api/products/get_all.php')
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data) {
                        allProducts = data.data;
                        renderProducts(allProducts);
                    } else {
                        productsGrid.innerHTML = '<p>Failed to load products.</p>';
                    }
                })
                .catch(err => {
                    productsGrid.innerHTML = '<p>Error loading products. Please try again later.</p>';
                    console.error('Error fetching products:', err);
                });

            function renderProducts(products) {
                productsGrid.innerHTML = '';
                 if (products.length === 0) {
                    productsGrid.innerHTML = '<p>No products found.</p>';
                    return;
                }
                products.forEach(product => {
                    const productCard = `
                        <div class="bg-white rounded-lg shadow-md p-4">
                             <div class="h-40 bg-gray-200 rounded-md mb-4 flex items-center justify-center">
                                <img src="${product.image_url || '../assets/images/product_placeholder.png'}" alt="${product.name}" class="h-full w-full object-cover rounded-md">
                            </div>
                            <h3 class="font-semibold text-lg">${product.name}</h3>
                            <p class="text-gray-600">${product.size} ${product.volume}</p>
                            <p class="text-blue-500 font-bold mt-2">₦${parseFloat(product.unit_price).toFixed(2)}</p>
                            <p class="text-sm text-gray-500 mt-1">Min. Order: ${product.minimum_order_quantity}</p>
                            <div class="mt-4 flex justify-between items-center">
                                <button onclick='addToCart(${JSON.stringify(product)})' class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Add to Cart</button>
                            </div>
                        </div>
                    `;
                    productsGrid.innerHTML += productCard;
                });
            }

            function filterAndRender() {
                let filteredProducts = allProducts;
                const category = categoryFilter.value;
                const searchTerm = searchFilter.value.toLowerCase();

                if (category) {
                    filteredProducts = filteredProducts.filter(p => p.category === category);
                }

                if (searchTerm) {
                    filteredProducts = filteredProducts.filter(p => p.name.toLowerCase().includes(searchTerm));
                }

                renderProducts(filteredProducts);
            }

            categoryFilter.addEventListener('change', filterAndRender);
            searchFilter.addEventListener('input', filterAndRender);

             document.getElementById('logoutBtn').addEventListener('click', function() {
                localStorage.removeItem('authToken');
                localStorage.removeItem('userName');
                window.location.href = '../login.php';
            });
        });

        function addToCart(product) {
            // The actual addToCart function is in cart.js
            if (window.addToCart && typeof window.addToCart === 'function') {
                window.addToCart(product, 1);
                alert(`${product.name} has been added to your cart.`);
            } else {
                console.error('cart.js is not loaded or addToCart function is not available.');
            }
        }
    </script>

</body>

</html>