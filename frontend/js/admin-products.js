document.addEventListener('DOMContentLoaded', () => {
    const addProductBtn = document.getElementById('add-product-btn');
    const productModal = document.getElementById('product-modal');
    const cancelBtn = document.getElementById('cancel-btn');
    const productForm = document.getElementById('product-form');
    const modalTitle = document.getElementById('modal-title');
    const saveBtn = document.getElementById('save-btn');

    const deleteProductModal = document.getElementById('delete-product-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const deleteProductForm = document.getElementById('delete-product-form');

    const productsTbody = document.getElementById('products-tbody');

    const API_URL = '../../backend/api/products/get_all.php';
    const CREATE_URL = '../../backend/api/products/create.php';
    const UPDATE_URL = '../../backend/api/products/update.php';
    const DELETE_URL = '../../backend/api/products/delete.php';

    const openModal = (modal) => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = (modal) => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    addProductBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Add Product';
        saveBtn.textContent = 'Save';
        productForm.reset();
        openModal(productModal);
    });

    cancelBtn.addEventListener('click', () => closeModal(productModal));
    cancelDeleteBtn.addEventListener('click', () => closeModal(deleteProductModal));

    const fetchProducts = async () => {
        try {
            const response = await fetch(API_URL);
            const { data } = await response.json();
            
            productsTbody.innerHTML = '';
            data.forEach(product => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="p-3 border-b">${product.name}</td>
                    <td class="p-3 border-b">${product.price}</td>
                    <td class="p-3 border-b">${product.stock_quantity}</td>
                    <td class="p-3 border-b">${new Date(product.created_at).toLocaleDateString()}</td>
                    <td class="p-3 border-b">
                        <button class="edit-btn text-blue-500 hover:text-blue-700" data-id="${product.product_id}"><i class="fas fa-edit"></i></button>
                        <button class="delete-btn text-red-500 hover:text-red-700 ml-2" data-id="${product.product_id}"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                productsTbody.appendChild(tr);
            });
        } catch (error) {
            console.error('Error fetching products:', error);
        }
    };

    productForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(productForm);
        const data = Object.fromEntries(formData.entries());
        const url = data.product_id ? UPDATE_URL : CREATE_URL;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.status === 'success') {
                closeModal(productModal);
                fetchProducts();
            } else {
                console.error('Operation failed:', result.message);
            }
        } catch (error) {
            console.error('Error saving product:', error);
        }
    });

    productsTbody.addEventListener('click', async (e) => {
        if (e.target.closest('.edit-btn')) {
            const productId = e.target.closest('.edit-btn').dataset.id;
            try {
                const response = await fetch(`${API_URL}?product_id=${productId}`);
                const { data } = await response.json();
                
                document.getElementById('product-id').value = data.product_id;
                document.getElementById('product-name').value = data.name;
                document.getElementById('product-description').value = data.description;
                document.getElementById('product-price').value = data.price;
                document.getElementById('product-stock').value = data.stock_quantity;
                
                modalTitle.textContent = 'Edit Product';
                saveBtn.textContent = 'Update';
                openModal(productModal);
            } catch (error) {
                console.error('Error fetching product data:', error);
            }
        } else if (e.target.closest('.delete-btn')) {
            const productId = e.target.closest('.delete-btn').dataset.id;
            document.getElementById('delete-product-id').value = productId;
            openModal(deleteProductModal);
        }
    });

    deleteProductForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const productId = document.getElementById('delete-product-id').value;

        try {
            const response = await fetch(DELETE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            });
            const result = await response.json();

            if (result.status === 'success') {
                closeModal(deleteProductModal);
                fetchProducts();
            } else {
                console.error('Deletion failed:', result.message);
            }
        } catch (error) {
            console.error('Error deleting product:', error);
        }
    });

    fetchProducts();
});
