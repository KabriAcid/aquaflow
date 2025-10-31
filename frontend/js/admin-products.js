document.addEventListener('DOMContentLoaded', function() {
    const addProductBtn = document.getElementById('add-product-btn');
    const productModal = document.getElementById('product-modal');
    const cancelBtn = document.getElementById('cancel-btn');
    const productForm = document.getElementById('product-form');
    const modalTitle = document.getElementById('modal-title');
    const saveBtn = document.getElementById('save-btn');
    const productsTable = document.getElementById('products-table').querySelector('tbody');

    const showModal = (title, buttonText) => {
        modalTitle.textContent = title;
        saveBtn.textContent = buttonText;
        productModal.classList.remove('hidden');
    };

    const hideModal = () => {
        productModal.classList.add('hidden');
        productForm.reset();
        document.getElementById('product-id').value = '';
    };

    addProductBtn.addEventListener('click', () => showModal('Add Product', 'Add Product'));
    cancelBtn.addEventListener('click', hideModal);

    // Handle form submission (Add/Edit)
    productForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const productId = document.getElementById('product-id').value;
        const isEditing = !!productId;
        const endpoint = isEditing ? `../backend/api/products/update.php?id=${productId}` : '../backend/api/products/create.php';
        const method = isEditing ? 'PUT' : 'POST';

        const formData = new FormData(productForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const res = await fetch(endpoint, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (res.ok) {
                hideModal();
                fetchProducts();
            } else {
                const error = await res.json();
                alert(`Error: ${error.message}`);
            }
        } catch (error) {
            console.error('Form submission error:', error);
            alert('An unexpected error occurred.');
        }
    });

    // Event delegation for Edit and Delete buttons
    productsTable.addEventListener('click', (e) => {
        const target = e.target;
        const productId = target.dataset.id;

        if (target.classList.contains('edit-btn')) {
            handleEdit(productId);
        }
        if (target.classList.contains('delete-btn')) {
            handleDelete(productId);
        }
    });

    const handleEdit = async (id) => {
        try {
            const res = await fetch(`../backend/api/products/get_single.php?id=${id}`);
            const product = await res.json();
            if (product.success) {
                document.getElementById('product-id').value = product.data.id;
                document.getElementById('product-name').value = product.data.name;
                document.getElementById('product-description').value = product.data.description;
                document.getElementById('product-price').value = product.data.price;
                document.getElementById('product-stock').value = product.data.stock;
                showModal('Edit Product', 'Save Changes');
            } else {
                alert(product.message);
            }
        } catch (error) {
            console.error('Error fetching product:', error);
        }
    };

    const handleDelete = async (id) => {
        if (confirm('Are you sure you want to delete this product?')) {
            try {
                const res = await fetch(`../backend/api/products/delete.php?id=${id}`, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    fetchProducts();
                } else {
                    const error = await res.json();
                    alert(`Error: ${error.message}`);
                }
            } catch (error) {
                console.error('Error deleting product:', error);
                alert('An unexpected error occurred.');
            }
        }
    };

    // Fetch and display products
    async function fetchProducts() {
        try {
            const res = await fetch('../backend/api/products/get_all.php');
            const products = await res.json();

            productsTable.innerHTML = ''; // Clear existing rows

            if (products.success && products.data.length > 0) {
                products.data.forEach(product => {
                    const row = document.createElement('tr');
                    row.classList.add('border-b');
                    row.innerHTML = `
                        <td class="p-3">${product.name}</td>
                        <td class="p-3">$${product.price}</td>
                        <td class="p-3">${product.stock}</td>
                        <td class="p-3">
                            <button class="edit-btn text-blue-500 hover:underline" data-id="${product.id}">Edit</button>
                            <button class="delete-btn text-red-500 hover:underline ml-4" data-id="${product.id}">Delete</button>
                        </td>
                    `;
                    productsTable.appendChild(row);
                });
            } else {
                productsTable.innerHTML = '<tr><td colspan="4" class="p-3 text-center">No products found.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching products:', error);
        }
    }

    fetchProducts();
});
