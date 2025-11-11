<?php
session_start();
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['sales', 'sales_manager'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Products";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="text-2xl mb-4 text-gray-800 font-semibold">Manage Products</h1>

    <div class="p-6">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-600"></h3>
            <div class="flex space-x-2">
                <input id="searchFilter" type="text" placeholder="Search by name..." class="border rounded px-3 py-2 w-80" />
                <select id="categoryFilter" class="border rounded px-3 py-2">
                    <option value="">All Categories</option>
                    <option value="bottled_water">Bottled Water</option>
                    <option value="beverage">Beverages</option>
                    <option value="package">Packages</option>
                </select>
            </div>
        </div>

        <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <p class="text-gray-500">Loading products...</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productsGrid = document.getElementById('productsGrid');
        const categoryFilter = document.getElementById('categoryFilter');
        const searchFilter = document.getElementById('searchFilter');
        let allProducts = [];

        function API_BASE() {
            const parts = window.location.pathname.split('/');
            const idx = parts.indexOf('frontend');
            if (idx !== -1) return parts.slice(0, idx).join('/') + '/backend/api';
            return '/backend/api';
        }

        function escapeHtml(unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return String(unsafe).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        fetch(API_BASE() + '/products/get_all.php', {
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.success) {
                    allProducts = data.data || [];
                    renderProducts(allProducts);
                } else {
                    productsGrid.innerHTML = '<p class="text-red-600">Failed to load products.</p>';
                }
            }).catch(err => {
                console.error(err);
                productsGrid.innerHTML = '<p class="text-red-600">Error loading products.</p>';
            });

        function renderProducts(products) {
            productsGrid.innerHTML = '';
            if (!products || products.length === 0) {
                productsGrid.innerHTML = '<p>No products found.</p>';
                return;
            }
            products.forEach(product => {
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg multi-shadow p-4';
                card.innerHTML = `
                <div class="h-40 bg-gray-200 rounded-md mb-4 flex items-center justify-center">
                    <img src="${escapeHtml(product.image_url || '../../assets/images/default.png')}" alt="${escapeHtml(product.name)}" class="h-full w-full object-cover rounded-md" />
                </div>
                <h3 class="font-semibold text-lg">${escapeHtml(product.name)}</h3>
                <p class="text-gray-600">${escapeHtml(product.size)} ${escapeHtml(product.volume)}</p>
                <p class="text-blue-500 font-bold mt-2">₦${(parseFloat(product.unit_price)||0).toFixed(2)}</p>
                <p class="p-2 font-semibold text-sm text-gray-500 mt-1">Min. Order: ${escapeHtml(String(product.minimum_order_quantity||1))}</p>
            `;
                productsGrid.appendChild(card);
            });
        }

        function filterAndRender() {
            let filtered = allProducts.slice();
            const cat = categoryFilter.value;
            const q = (searchFilter.value || '').toLowerCase();
            if (cat) filtered = filtered.filter(p => p.category === cat);
            if (q) filtered = filtered.filter(p => (p.name || '').toLowerCase().includes(q));
            renderProducts(filtered);
        }

        categoryFilter.addEventListener('change', filterAndRender);
        searchFilter.addEventListener('input', filterAndRender);
    });
</script>

<?php include 'partials/footer.php'; ?>