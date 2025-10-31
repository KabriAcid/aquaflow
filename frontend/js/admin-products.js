document.addEventListener('DOMContentLoaded', function() {
    const productsTableBody = document.querySelector('#products-table tbody');
    const addProductBtn = document.getElementById('add-product-btn');
    const productModal = document.getElementById('product-modal');
    const modalTitle = document.getElementById('modal-title');
    const productForm = document.getElementById('product-form');
    const cancelBtn = document.getElementById('cancel-btn');
    const productIdInput = document.getElementById('product-id');

    const apiEndpoint = '../api/ajax.php';

    const renderProducts = (products) => {
        productsTableBody.innerHTML = '';
        if (products.length === 0) {
            productsTableBody.innerHTML = '<tr><td colspan="4" class="p-3 text-center">No products found.</td></tr>';
            return;
        }
        products.forEach(product => {
            const row = document.createElement('tr');
            row.className = 'border-b';
            row.innerHTML = `
                <td class="p-3">${escapeHTML(product.name)}</td>
                <td class="p-3">$${parseFloat(product.price).toFixed(2)}</td>
                <td class="p-3">${product.stock}</td>
                <td class="p-3">
                    <button class="text-blue-600 hover:underline edit-btn" data-id="${product.id}">Edit</button>
                    <button class="text-red-600 hover:underline delete-btn ml-4" data-id="${product.id}">Delete</button>
                </td>
            `;
            productsTableBody.appendChild(row);
        });
    };

    const fetchProducts = () => {
        fetch(`${apiEndpoint}?action=get_products`)
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    renderProducts(data.data);
                } else {
                    productsTableBody.innerHTML = `<tr><td colspan="4" class="p-3 text-center text-red-500">Error: ${data.message || 'Could not fetch products.'}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productsTableBody.innerHTML = '<tr><td colspan="4" class="p-3 text-center text-red-500">An error occurred while fetching data.</td></tr>';
            });
    };

    const openModal = (title, product = {}) => {
        modalTitle.textContent = title;
        productIdInput.value = product.id || '';
        productForm.elements['product-name'].value = product.name || '';
        productForm.elements['product-price'].value = product.price || '';
        productForm.elements['product-stock'].value = product.stock || '';
        productModal.classList.remove('hidden');
        productModal.classList.add('flex');
    };

    const closeModal = () => {
        productModal.classList.add('hidden');
        productModal.classList.remove('flex');
        productForm.reset();
    };

    addProductBtn.addEventListener('click', () => openModal('Add Product'));
    cancelBtn.addEventListener('click', closeModal);

    productForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const id = productIdInput.value;
        const action = id ? 'update_product' : 'add_product';
        const formData = new FormData(productForm);
        formData.append('action', action);
        if (id) {
            formData.append('id', id);
        }

        fetch(apiEndpoint, {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                fetchProducts();
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => console.error('Error saving product:', error));
    });

    productsTableBody.addEventListener('click', (event) => {
        const target = event.target;
        const id = target.dataset.id;
        if (target.classList.contains('edit-btn')) {
            fetch(`${apiEndpoint}?action=get_product&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        openModal('Edit Product', data.data);
                    } else {
                        alert(`Error: ${data.message}`);
                    }
                });
        } else if (target.classList.contains('delete-btn')) {
            if (confirm('Are you sure you want to delete this product?')) {
                fetch(apiEndpoint, {
                    method: 'POST',
                    body: new URLSearchParams({ action: 'delete_product', id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchProducts();
                    } else {
                        alert(`Error: ${data.message}`);
                    }
                });
            }
        }
    });

    // Initial fetch
    fetchProducts();
});

// Utility to prevent XSS
function escapeHTML(str) {
    if (str === null || str === undefined) {
        return '';
    }
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
