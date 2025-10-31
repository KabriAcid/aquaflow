document.addEventListener('DOMContentLoaded', () => {
    const recentProductsBody = document.getElementById('recentProductsBody');

    // Fetch recent products
    if (recentProductsBody) {
        fetch('../../backend/api/products/get_all.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    recentProductsBody.innerHTML = ''; // Clear loading message
                    const products = data.data.slice(0, 5); // Get latest 5 products
                    if (products.length > 0) {
                        products.forEach((product, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="p-2">${index + 1}</td>
                                <td class="p-2">${product.name}</td>
                                <td class="p-2">${product.sku}</td>
                                <td class="p-2">${product.quantity}</td>
                            `;
                            recentProductsBody.appendChild(row);
                        });
                    } else {
                        recentProductsBody.innerHTML = '<tr><td colspan="4" class="p-2 text-center">No products found</td></tr>';
                    }
                } else {
                    recentProductsBody.innerHTML = '<tr><td colspan="4" class="p-2 text-center">Error loading products</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching recent products:', error);
                recentProductsBody.innerHTML = '<tr><td colspan="4" class="p-2 text-center">Error loading products</td></tr>';
            });
    }
});
