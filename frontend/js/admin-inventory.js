document.addEventListener('DOMContentLoaded', () => {
    const inventoryTbody = document.getElementById('inventory-tbody');

    const API_URL = '../../backend/api/products/get_all.php';
    const UPDATE_URL = '../../backend/api/products/update.php';

    const fetchInventory = async () => {
        try {
            const response = await fetch(API_URL);
            const { data } = await response.json();
            renderInventory(data);
        } catch (error) {
            console.error('Error fetching inventory:', error);
        }
    };

    const renderInventory = (products) => {
        inventoryTbody.innerHTML = '';
        if (!products || products.length === 0) {
            inventoryTbody.innerHTML = '<tr><td colspan="4" class="p-3 text-center text-gray-500">No products found.</td></tr>';
            return;
        }

        products.forEach(product => {
            const tr = document.createElement('tr');
            tr.dataset.productId = product.product_id;
            tr.innerHTML = `
                <td class="p-3 border-b">${product.name}</td>
                <td class="p-3 border-b">${product.stock_quantity}</td>
                <td class="p-3 border-b">
                    <input type="number" min="0" class="stock-input form-input w-24" value="${product.stock_quantity}">
                </td>
                <td class="p-3 border-b">
                    <button class="update-stock-btn btn-primary" data-id="${product.product_id}">Update</button>
                </td>
            `;
            inventoryTbody.appendChild(tr);
        });
    };

    inventoryTbody.addEventListener('click', async (e) => {
        if (e.target.classList.contains('update-stock-btn')) {
            const button = e.target;
            const productId = button.dataset.id;
            const tr = button.closest('tr');
            const stockInput = tr.querySelector('.stock-input');
            const newStock = parseInt(stockInput.value, 10);

            if (isNaN(newStock) || newStock < 0) {
                alert('Please enter a valid stock quantity.');
                return;
            }

            button.disabled = true;
            button.textContent = 'Updating...';

            try {
                const response = await fetch(UPDATE_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        product_id: productId, 
                        stock_quantity: newStock 
                    })
                });
                const result = await response.json();

                if (result.status === 'success') {
                    // Re-fetch to show the updated state
                    fetchInventory();
                } else {
                    console.error('Update failed:', result.message);
                    alert('Failed to update stock. Please check the console for details.');
                    button.disabled = false;
                    button.textContent = 'Update';
                }
            } catch (error) {
                console.error('Error updating stock:', error);
                alert('An error occurred while updating stock.');
                button.disabled = false;
                button.textContent = 'Update';
            }
        }
    });

    fetchInventory();
});
