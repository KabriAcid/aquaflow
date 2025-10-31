<?php
// customer/products.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products - Aquaflow</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../favicon.png">

    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-gray-100">

    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

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
            // verify session
            fetch('../../backend/api/auth/me.php', {
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(userData => {
                    if (!userData.success) {
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
                            // determine if product already in cart
                            const cartNow = getCart();
                            const inCart = cartNow.some(i => String(i.id) === String(product.id));
                            const btnId = `add-btn-${product.id}`;
                            const btnText = inCart ? 'In Cart' : 'Add to Cart';
                            const btnClasses = inCart ? 'bg-gray-400 text-white px-4 py-2 rounded-md cursor-not-allowed' : 'bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600';

                            const productCard = `
                            <div class="bg-white rounded-lg shadow-md p-4">
                                 <div class="h-40 bg-gray-200 rounded-md mb-4 flex items-center justify-center">
                                    <img src="${product.image_url || '../../assets/images/default.png'}" alt="${product.name}" class="h-full w-full object-cover rounded-md">
                                </div>
                                <h3 class="font-semibold text-lg">${product.name}</h3>
                                <p class="text-gray-600">${product.size} ${product.volume}</p>
                                <p class="text-blue-500 font-bold mt-2">₦${parseFloat(product.unit_price).toFixed(2)}</p>
                                <p class="text-sm text-gray-500 mt-1">Min. Order: ${product.minimum_order_quantity}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <button id="${btnId}" onclick='addToCart(${JSON.stringify(product)})' class="${btnClasses}" ${inCart ? 'disabled' : ''}>${btnText}</button>
                                </div>
                            </div>
                        `;
                            productsGrid.innerHTML += productCard;
                        });

                        // ensure buttons reflect current cart after render
                        if (typeof updateProductButtons === 'function') {
                            updateProductButtons();
                        }
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
                        fetch('../../backend/api/auth/logout.php', {
                                method: 'POST',
                                credentials: 'same-origin'
                            })
                            .then(() => window.location.href = '../login.php')
                            .catch(() => window.location.href = '../login.php');
                    });
                });

            function addToCart(product) {
                // Immediately update UI to prevent duplicate clicks
                try {
                    const btn = document.getElementById(`add-btn-${product.id}`);
                    if (btn) {
                        btn.textContent = 'In Cart';
                        btn.className = 'bg-gray-400 text-white px-4 py-2 rounded-md cursor-not-allowed';
                        btn.disabled = true;
                    }
                } catch (e) {
                    console.error('Failed to update add button', e);
                }

                // Then perform cart logic (non-blocking)
                if (window.addToCart && typeof window.addToCart === 'function') {
                    try {
                        window.addToCart(product, 1);
                    } catch (err) {
                        console.error('Error adding to cart:', err);
                        // revert button if add failed
                        try {
                            const btn = document.getElementById(`add-btn-${product.id}`);
                            if (btn) {
                                btn.textContent = 'Add to Cart';
                                btn.className = 'bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600';
                                btn.disabled = false;
                            }
                        } catch (e2) {
                            /* ignore */ }
                        alert('Failed to add item to cart. Please try again.');
                        return;
                    }

                    // update badge and other UI
                    if (typeof updateCartBadge === 'function') updateCartBadge();
                    if (typeof updateProductButtons === 'function') updateProductButtons();
                    // lightweight confirmation
                    try {
                        // non-blocking small UI cue could be implemented; keep alert for now
                        alert(`${product.name} has been added to your cart.`);
                    } catch (e) {}
                } else {
                    console.error('cart.js is not loaded or addToCart function is not available.');
                }
            }

            // Refresh all Add-to-Cart buttons to reflect current cart contents
            function updateProductButtons() {
                try {
                    const cartNow = (typeof getCart === 'function') ? getCart() : [];
                    const idsInCart = new Set(cartNow.map(i => String(i.id)));
                    document.querySelectorAll('[id^="add-btn-"]').forEach(btn => {
                        const idAttr = btn.id.replace('add-btn-', '');
                        if (idsInCart.has(String(idAttr))) {
                            btn.textContent = 'In Cart';
                            btn.className = 'bg-gray-400 text-white px-4 py-2 rounded-md cursor-not-allowed';
                            btn.disabled = true;
                        } else {
                            btn.textContent = 'Add to Cart';
                            btn.className = 'bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600';
                            btn.disabled = false;
                        }
                    });
                } catch (e) {
                    console.error('Failed to refresh product buttons', e);
                }
            }
        });
    </script>

</body>

</html>